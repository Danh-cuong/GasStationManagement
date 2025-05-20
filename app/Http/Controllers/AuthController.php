<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use DB;

class AuthController extends Controller
{
    protected $apiHelper;

    public function __construct()
    {
        $this->apiHelper = new ListFunctionController();
    }

    public function index(){
        return redirect()->route('dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name'     => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('admin')) {
                $clientId     = env('CLIENT_ID');
                $clientSecret = env('CLIENT_SECRET');
                $urlBase      = env('API_APP');
            } else {
                if (!$user->employee_id) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->withErrors(['name' => 'Tài khoản của bạn chưa được gán nhân viên. Vui lòng liên hệ quản trị để được hỗ trợ.']);
                }

                $employee = Employee::find($user->employee_id);
                if (!$employee) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->withErrors(['name' => 'Tài khoản của bạn chưa được gán nhân viên hợp lệ. Vui lòng liên hệ quản trị để được hỗ trợ.']);
                }

                $clientId     = $employee->client_id;
                $clientSecret = $employee->client_secret;

                $urlBase      = $employee->url;
            }

            $getAPI = new ListFunctionController();
            $dataSubmit = [
                "clientId"     => $clientId,
                "clientSecret" => $clientSecret,
            ];

            $urlCall = $urlBase . "integration/access-token";
            $apiResponse = $getAPI->postAPIToken($urlCall, $dataSubmit);

            if (isset($apiResponse->accessToken)) {
                session(['access_token' => $apiResponse->accessToken]);
            }

            if ($user->hasRole('admin')) {
                return redirect()->intended('/dashboard')
                                ->with('status', 'Chào mừng Admin!');
            }

            return redirect()->intended('/dashboard')
                            ->with('status', 'Đăng nhập thành công!');
        }

        throw ValidationException::withMessages([
            'name' => __('Tên tài khoản hoặc mật khẩu không đúng'),
        ]);
    }


    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $now          = Carbon::now('Asia/Ho_Chi_Minh');
            $startOfMonth = $now->copy()->startOfMonth()->toDateString();
            $endOfMonth   = $now->copy()->endOfMonth()->toDateString();
    
            $fuelNames = [
                'Xăng RON 95-III',
                'Dầu DO 0,05S-II',
                'Xăng E5 RON 92-II',
            ];
            $employees   = \App\Models\Employee::all();
            $topsByFuel  = [];
    
            foreach ($fuelNames as $fuelName) {
                $litResults   = collect();
                $moneyResults = collect();
    
                foreach ($employees as $emp) {
                    $tokenResp = app(ListFunctionController::class)
                        ->postAPIToken(
                            rtrim($emp->url, '/') . '/integration/access-token',
                            ['clientId'=>$emp->client_id,'clientSecret'=>$emp->client_secret]
                        );
                    $token = $tokenResp->accessToken ?? null;
    
                    $sumLit   = 0;
                    $sumMoney = 0;
    
                    if ($token) {
                        $pumps = app(ListFunctionController::class)
                            ->getAPIAuth(rtrim($emp->url, '/') . '/integration/pumps', [], $token);
    
                        if (is_array($pumps)) {
                            $pumpIds = array_map(
                                fn($p)=> $p->id,
                                array_filter($pumps, fn($p)=>
                                    strcasecmp(trim($p->fuelName), trim($fuelName))===0
                                )
                            );
    
                            $transBase = rtrim($emp->url, '/') . '/integration/transactions';
                            foreach ($pumpIds as $pumpId) {
                                $query = http_build_query([
                                    'searchType'=>1,
                                    'pumpId'    =>$pumpId,
                                    'fromTime'  =>"{$startOfMonth} 00:00:00",
                                    'toTime'    =>"{$endOfMonth} 23:59:59",
                                ]);
                                $resp = app(ListFunctionController::class)
                                    ->getAPIAuth("{$transBase}?{$query}", [], $token);
    
                                if (is_object($resp) && is_array($resp->transactions)) {
                                    foreach ($resp->transactions as $t) {
                                        $m = $t->money  ?? 0;
                                        $p = $t->price  ?? 0;
                                        $sumMoney += $m;
                                        if ($p>0) {
                                            $sumLit += $m/$p;
                                        }
                                    }
                                }
                            }
                        }
                    }
    
                    $litResults->push((object)['name'=>$emp->name,'total'=>$sumLit]);
                    $moneyResults->push((object)['name'=>$emp->name,'total'=>$sumMoney]);
                }
    
                $topLit   = $litResults->sortByDesc('total')->values()->take(5);
                $topMoney = $moneyResults->sortByDesc('total')->values()->take(5);
    
                $topsByFuel[$fuelName] = (object)[
                    'name'  => $fuelName,
                    'lit'   => $topLit,
                    'money' => $topMoney,
                ];
            }
    
            $CHs = Employee::all();
            return view('dashboard', compact('now','topsByFuel','fuelNames','CHs'));
        } else {
           $tz   = 'Asia/Ho_Chi_Minh';
            $from = $request->input('from_date', Carbon::now($tz)->subDays(7)->toDateString());
            $to   = $request->input('to_date',   Carbon::now($tz)->toDateString());

            $emp       = Employee::findOrFail($user->employee_id);
            $basePump  = rtrim($emp->url, '/') . '/integration/pumps';
            $baseTrans = rtrim($emp->url, '/') . '/integration/transactions';
            $token     = session('access_token');

            $allPumps = $this->apiHelper->getAPIAuth($basePump, [], $token);
            if (! is_array($allPumps)) {
                $allPumps = [];
            }

            $fuelTotals = [];
            foreach ($allPumps as $pump) {
                $pumpId   = $pump->id;
                $fuelName = $pump->fuelName ?? 'Unknown';

                $query = http_build_query([
                    'searchType'=>1,
                    'pumpId'    =>$pumpId,
                    'fromTime'  =>"$from 00:00:00",
                    'toTime'    =>"$to 23:59:59",
                ]);
                $resp = $this->apiHelper->getAPIAuth("{$baseTrans}?{$query}", [], $token);

                $sumLit   = 0;
                $sumMoney = 0;
                if (is_object($resp) && is_array($resp->transactions)) {
                    foreach ($resp->transactions as $t) {
                        $money = $t->money ?? 0;
                        $price = $t->price ?? 0;
                        $sumMoney += $money;
                        if ($price > 0) {
                            $sumLit += $money / $price;
                        }
                    }
                }

                if (!isset($fuelTotals[$fuelName])) {
                    $fuelTotals[$fuelName] = ['lit'=>0,'money'=>0];
                }
                $fuelTotals[$fuelName]['lit']   += $sumLit;
                $fuelTotals[$fuelName]['money'] += $sumMoney;
            }

            $stats = collect($fuelTotals)
                ->map(fn($vals,$fuelName) => [
                    'name'        => $fuelName,
                    'total_lit'   => round($vals['lit'],3),
                    'total_money' => round($vals['money'],2),
                ])
                ->values()
                ->all();
            return view('dashboard', compact('from','to','stats'));

        }
    }

    protected function fetchTransactions(Request $request, $pumpId, $baseUrl)
    {
        $validated = $request->validate([
            'fromTime' => 'required|date_format:Y-m-d\TH:i',
            'toTime'   => 'required|date_format:Y-m-d\TH:i|after_or_equal:fromTime',
        ]);
        $from = Carbon::createFromFormat('Y-m-d\TH:i',$validated['fromTime'])
                      ->format('Y-m-d H:i:s');
        $to   = Carbon::createFromFormat('Y-m-d\TH:i',$validated['toTime'])
                      ->format('Y-m-d H:i:s');

        $token = session('access_token');
        $query = http_build_query([
            'searchType'=>1,
            'pumpId'    =>$pumpId,
            'fromTime'  =>$from,
            'toTime'    =>$to,
        ]);
        $resp = app(\App\Http\Controllers\ListFunctionController::class)
            ->getAPIAuth("{$baseUrl}/integration/transactions?{$query}",[], $token);

        $totals = ['totalMillis'=>0,'totalMoney'=>0,'totalTrans'=>0];
        $txs = [];
        if (is_object($resp)) {
            $totals = [
                'totalMillis'=>$resp->totalMillis??0,
                'totalMoney' =>$resp->totalMoney ??0,
                'totalTrans' =>$resp->totalTrans ??0,
            ];
            $txs = is_array($resp->transactions) ? $resp->transactions : [];
        }

        // sort + manual paginate for simplicity
        $collection = collect($txs)->sortByDesc(fn($t)=>strtotime($t->dateTimeCreated))->values();
        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $items   = $collection->slice(($page-1)*$perPage,$perPage)->all();
        $paginator = new LengthAwarePaginator($items,$collection->count(),$perPage,$page,[
            'path'=>url()->current(),
            'query'=>$request->query()
        ]);

        return ['transactions'=>$paginator,'totals'=>$totals];
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                         ->with('status', 'Bạn đã đăng xuất.');
    }

}
