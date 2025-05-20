<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Auth;
use App\Models\User;
use App\Models\Employee;
use App\Models\DailyPumpLog;
use Carbon\Carbon;
use Pdf;

class PumpController extends Controller
{
    protected $apiHelper;

    public function __construct()
    {
        $this->apiHelper = new ListFunctionController();
    }

    public function index()
{
    $token = session('access_token');
    $user  = Auth::user();

    if ($user->hasRole('admin')) {
        $basePumpsUrl = env('API_APP') . 'integration/pumps';
    } else {
        $emp = Employee::find($user->employee_id);
        $basePumpsUrl = rtrim($emp->url, '/') . '/integration/pumps';
    }
    $pumps = $this->apiHelper->getAPIAuth($basePumpsUrl, [], $token);
    if (!is_array($pumps)) {
        $pumps = [];
    }

    $now    = Carbon::now('Asia/Ho_Chi_Minh');
    $from   = $now->copy()->subHours(2)->format('Y-m-d H:i:s');
    $to     = $now->format('Y-m-d H:i:s');

    $logsByPump = [];

    foreach ($pumps as $pump) {
        if ($user->hasRole('admin')) {
            $baseTrans = env('API_APP') . 'integration/transactions';
        } else {
            $baseTrans = rtrim($emp->url, '/') . '/integration/transactions';
        }

        $query = http_build_query([
            'searchType' => 1,
            'pumpId'     => $pump->id,
            'fromTime'   => $from,
            'toTime'     => $to,
        ]);
        $transUrl = "{$baseTrans}?{$query}";

        $resp = $this->apiHelper->getAPIAuth($transUrl, [], $token);
        $latest = null;

        if (is_object($resp) && is_array($resp->transactions) && count($resp->transactions) > 0) {
            usort($resp->transactions, function($a, $b){
                return strtotime($b->dateTimeCreated) - strtotime($a->dateTimeCreated);
            });
            $latest = $resp->transactions[0];
        }

        $logsByPump[$pump->id] = $latest;
    }

    // dd($logsByPump);
    return view('pumps.index', compact('pumps', 'logsByPump'));
}
    public function show($id)
    {
        $token = session('access_token');
        $url   = env('API_APP') . "integration/pumps/{$id}";
        $pump  = $this->apiHelper->getAPIAuth($url, [], $token);

        if (!is_object($pump)) {
            abort(404, 'Không tìm thấy trụ bơm');
        }

        return view('pumps.show', compact('pump'));
    }


    public function showLogsForm($pumpId)
    {
        return view('pumps.logs', [
            'pumpId' => $pumpId,
            'logs'   => null,
            'fromTime' => old('fromTime'),
            'toTime'   => old('toTime'),
        ]);
    }

    public function logs(Request $request, $pumpId)
    {
        $validatedData = $request->validate([
            'fromTime' => 'required|date_format:Y-m-d\TH:i',
            'toTime'   => 'required|date_format:Y-m-d\TH:i|after_or_equal:fromTime',
        ]);

        $fromTimeFormatted = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validatedData['fromTime'])
                                    ->format('Y-m-d H:i:s');
        $toTimeFormatted = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validatedData['toTime'])
                                    ->format('Y-m-d H:i:s');

        $token = session('access_token');
        $base = env('API_APP') . 'integration/transactions';
        $query = http_build_query([
            'searchType' => 1,
            'pumpId'     => $pumpId,
            'fromTime'   => $fromTimeFormatted,
            'toTime'     => $toTimeFormatted,
        ]);
        $url = "{$base}?{$query}";

        $response = $this->apiHelper->getAPIAuth($url, [], $token);

        $totals = ['totalMillis'=>0, 'totalMoney'=>0, 'totalTrans'=>0];
        $transactions = [];
        if (is_object($response)) {
            $totals = [
                'totalMillis' => $response->totalMillis ?? 0,
                'totalMoney'  => $response->totalMoney  ?? 0,
                'totalTrans'  => $response->totalTrans  ?? 0,
            ];
            $transactions = is_array($response->transactions) ? $response->transactions : [];
        }

        $collection = collect($transactions);
        $sorted = $collection->sortByDesc(function($item) {
            return strtotime($item->dateTimeCreated);
        })->values();

        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pumps.logs', [
            'pumpId'       => $pumpId,
            'fromTime'     => $validatedData['fromTime'],
            'toTime'       => $validatedData['toTime'],
            'totals'       => $totals,
            'transactions' => $paginator,
        ]);
    }


    public function dashboardLogs(Request $request)
    {
        $data = $request->validate([
            'fromTime' => 'required|date_format:Y-m-d H:i:s',
            'toTime'   => 'required|date_format:Y-m-d H:i:s|after_or_equal:fromTime',
        ]);

        $user = Auth::user();
        if (!$user->pump_id) {
            return response()->json(['error' => 'Tài khoản chưa được gán pump'], 400);
        }
        $pumpId = $user->pump_id;

        $token = session('access_token');

        if ($user->hasRole('admin')) {
            $base = rtrim(env('API_APP'), '/') . '/integration/transactions';
        } else {
            if (!$user->employee_id) {
                return response()->json(['error' => 'Tài khoản chưa được gán nhân viên'], 400);
            }
            $employee = Employee::find($user->employee_id);
            if (!$employee || !$employee->url) {
                return response()->json(['error' => 'Thông tin nhân viên không hợp lệ'], 400);
            }
            $base = rtrim($employee->url, '/') . '/integration/transactions';
        }

        $query = http_build_query([
            'searchType' => 1,
            'pumpId'     => $pumpId,
            'fromTime'   => $data['fromTime'],
            'toTime'     => $data['toTime'],
        ]);
        $url = "{$base}?{$query}";

        $apiResponse = $this->apiHelper->getAPIAuth($url, [], $token);

        if (!$apiResponse) {
            return response()->json(['error' => 'Không thể lấy dữ liệu giao dịch'], 500);
        }

        return response()->json($apiResponse);
    }


    public function checkOverview(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $basePumpsUrl = env('API_APP') . 'integration/pumps';
        } else {
            $employee = Employee::find($user->employee_id);
            $basePumpsUrl = rtrim($employee->url, '/') . '/integration/pumps';
        }
        $token = session('access_token');
        $pumps = $this->apiHelper->getAPIAuth($basePumpsUrl, [], $token);
        if (!is_array($pumps)) {
            $pumps = [];
        }

        $selectedPump = $request->input('pump_id');
        $fromDate     = $request->input('from_date');
        $toDate       = $request->input('to_date');
    
        $logs = collect();
        if ($selectedPump && $fromDate && $toDate) {
            $logs = DailyPumpLog::where('pump_id', $selectedPump)
                ->where('employee_id', $user->employee_id)
                ->whereBetween('log_date', [$fromDate, $toDate])
                ->orderBy('log_date', 'desc')
                ->get();
        }
    
        return view('pumps.overview', compact(
            'pumps', 'logs', 'selectedPump', 'fromDate', 'toDate'
        ));
    }

    public function updateOverview(Request $request)
    {
        $request->validate([
            'pump_id' => 'required|integer',
        ]);
        $pumpId = $request->input('pump_id');
        $user   = Auth::user();

        $tz         = 'Asia/Ho_Chi_Minh';
        $targetDate = Carbon::now($tz)->subDay()->toDateString();

        $exists = DailyPumpLog::where('pump_id', $pumpId)
                    ->where('employee_id', $user->employee_id)
                    ->where('log_date', $targetDate)
                    ->exists();
        if ($exists) {
            return redirect()->back()
                ->with('status', "Dữ liệu tổng log đã được cập nhật cho ngày {$targetDate}");
        }

        $fromTime = "{$targetDate} 00:00:00";
        $toTime   = "{$targetDate} 23:59:59";

        if ($user->hasRole('admin')) {
            $basePump  = env('API_APP') . 'integration/pumps';
            $baseTrans = env('API_APP') . 'integration/transactions';
        } else {
            $emp       = Employee::findOrFail($user->employee_id);
            $basePump  = rtrim($emp->url, '/') . '/integration/pumps';
            $baseTrans = rtrim($emp->url, '/') . '/integration/transactions';
        }
        $token = session('access_token');

        $allPumps = $this->apiHelper->getAPIAuth($basePump, [], $token);
        if (! is_array($allPumps)) {
            $allPumps = [];
        }
        $pumpObj  = collect($allPumps)->firstWhere('id', $pumpId);
        $fuelName = $pumpObj->fuelName ?? '';

        $fuelMap = [
            'Xăng RON 95-III'   => 'RON95_III',
            'Dầu DO 0,05S-II'   => 'DO05S_II',
            'Xăng E5 RON 92-II' => 'E5_92_II',
            'DO 0,05S-II'       =>  'DO05S_II'
        ];
        
        $fuelTypeCode = $map[$fuelName] ?? null;
        if (! $fuelTypeCode) {
            return redirect()->back()
                ->withErrors(['pump_id' => "Không xác định được loại nhiên liệu cho pump #{$pumpId}."]);
        }

        $query = http_build_query([
            'searchType' => 1,
            'pumpId'     => $pumpId,
            'fromTime'   => $fromTime,
            'toTime'     => $toTime,
        ]);
        $resp = $this->apiHelper->getAPIAuth("{$baseTrans}?{$query}", [], $token);

        $sumMoney     = 0;
        $sumMillis    = 0;
        $sumLit       = 0;
        $dailyTotalF3 = 0;
        $latestTs     = null;

        if (is_object($resp) && is_array($resp->transactions)) {
            foreach ($resp->transactions as $t) {
                $money   = $t->money       ?? 0;
                $price   = $t->price       ?? 0;
                $millis  = $t->millis      ?? 0;
                $totalF3 = $t->totalF3     ?? 0;
                $dtStr   = $t->dateTimeCreated ?? null;

                $sumMoney  += $money;
                $sumMillis += $millis;
                $sumLit    += ($price > 0) ? ($money / $price) : 0;

                if ($dtStr) {
                    $ts = strtotime($dtStr);
                    if ($latestTs === null || $ts > $latestTs) {
                        $latestTs     = $ts;
                        $dailyTotalF3 = $totalF3;
                    }
                }
            }
        }

        DailyPumpLog::create([
            'log_date'   => $targetDate,
            'employee_id'=> $user->employee_id,
            'pump_id'    => $pumpId,
            'fuel_type'  => $fuelTypeCode,  // cột ENUM
            'money'      => $sumMoney,
            'millis'     => $sumMillis,
            'total_f3'   => $dailyTotalF3,
            'lit'        => $sumLit,
        ]);

        return redirect()->back()
            ->with('status', "Đã cập nhật tổng log cho ngày {$targetDate}");
    }

    //phần này để cập nhật 30 ngày
    // public function updateOverview(Request $request)
    // {
    //     $request->validate([
    //         'pump_id' => 'required|integer',
    //     ]);
    //     $pumpId = $request->input('pump_id');
    //     $user   = Auth::user();

    //     $tz   = 'Asia/Ho_Chi_Minh';
    //     $end  = Carbon::now($tz)->subDay();        // hôm qua
    //     $start= $end->copy()->subDays(29);         // 29 ngày trước đó

    //     if ($user->hasRole('admin')) {
    //         $basePump  = env('API_APP') . 'integration/pumps';
    //         $baseTrans = env('API_APP') . 'integration/transactions';
    //     } else {
    //         $emp       = Employee::findOrFail($user->employee_id);
    //         $basePump  = rtrim($emp->url, '/') . '/integration/pumps';
    //         $baseTrans = rtrim($emp->url, '/') . '/integration/transactions';
    //     }
    //     $token = session('access_token');

    //     $fuelMap = [
    //         'Xăng RON 95-III'   => 'RON95_III',
    //         'Dầu DO 0,05S-II'   => 'DO05S_II',
    //         'Xăng E5 RON 92-II' => 'E5_92_II',
    //         'DO 0,05S-II'       =>  'DO05S_II'
    //     ];

    //     $allPumps = $this->apiHelper->getAPIAuth($basePump, [], $token);
    //     if (!is_array($allPumps)) {
    //         $allPumps = [];
    //     }
    //     // dd($allPumps);
    //     $pumpObj = collect($allPumps)->firstWhere('id', $pumpId);
    //     $fuelName= $pumpObj->fuelName ?? null;
    //     $fuelType= $fuelName && isset($fuelMap[$fuelName]) 
    //                 ? $fuelMap[$fuelName] 
    //                 : null;

    //     if (!$fuelType) {
    //         return redirect()->back()
    //             ->withErrors(['pump_id' => "Không xác định được loại nhiên liệu cho pump #{$pumpId}."]);
    //     }

    //     for ($date = $start; $date->lte($end); $date->addDay()) {
    //         $day = $date->toDateString(); // YYYY-MM-DD

    //         $exists = DailyPumpLog::where('pump_id',$pumpId)
    //                     ->where('employee_id',$user->employee_id)
    //                     ->where('log_date',$day)
    //                     ->exists();
    //         if ($exists) {
    //             continue;
    //         }

    //         // Tính from/to time
    //         $fromTime = "{$day} 00:00:00";
    //         $toTime   = "{$day} 23:59:59";

    //         // Gọi API transactions
    //         $query = http_build_query([
    //             'searchType'=>1,
    //             'pumpId'    =>$pumpId,
    //             'fromTime'  =>$fromTime,
    //             'toTime'    =>$toTime,
    //         ]);
    //         $resp = $this->apiHelper->getAPIAuth("{$baseTrans}?{$query}", [], $token);

    //         // Tính tổng
    //         $sumMoney     = 0;
    //         $sumMillis    = 0;
    //         $sumLit       = 0;
    //         $dailyTotalF3 = 0;
    //         $latestTs     = null;

    //         if (is_object($resp) && is_array($resp->transactions)) {
    //             foreach ($resp->transactions as $t) {
    //                 $money   = $t->money       ?? 0;
    //                 $price   = $t->price       ?? 0;
    //                 $millis  = $t->millis      ?? 0;
    //                 $totalF3 = $t->totalF3     ?? 0;
    //                 $dtStr   = $t->dateTimeCreated ?? null;

    //                 $sumMoney  += $money;
    //                 $sumMillis += $millis;
    //                 $sumLit    += ($price > 0) ? ($money / $price) : 0;

    //                 if ($dtStr) {
    //                     $ts = strtotime($dtStr);
    //                     if ($latestTs === null || $ts > $latestTs) {
    //                         $latestTs     = $ts;
    //                         $dailyTotalF3 = $totalF3;
    //                     }
    //                 }
    //             }
    //         }

    //         // Lưu vào DB
    //         DailyPumpLog::create([
    //             'log_date'   => $day,
    //             'employee_id'=> $user->employee_id,
    //             'pump_id'    => $pumpId,
    //             'fuel_type'  => $fuelType,
    //             'money'      => $sumMoney,
    //             'millis'     => $sumMillis,
    //             'total_f3'   => $dailyTotalF3,
    //             'lit'        => $sumLit,
    //         ]);
    //     }

    //     return redirect()->back()
    //         ->with('status', "Đã cập nhật tổng log cho 30 ngày từ {$start->toDateString()} đến {$end->toDateString()}");
    // }

    public function adminOverview(Request $request)
    {
        $employees = Employee::all();

        $selectedEmployee = $request->input('employee_id');
        $pumps = [];
        if ($selectedEmployee) {
            $emp = Employee::find($selectedEmployee);
            if ($emp) {
                $token = session('access_token');
                $basePumpsUrl = rtrim($emp->url, '/') . '/integration/pumps';
                $pumps = app(ListFunctionController::class)
                         ->getAPIAuth($basePumpsUrl, [], $token) 
                         ?: [];
            }
        }

        $selectedPump = $request->input('pump_id');
        $fromDate     = $request->input('from_date');
        $toDate       = $request->input('to_date');

        $logs = collect();
        if ($selectedEmployee && $selectedPump && $fromDate && $toDate) {
            $logs = DailyPumpLog::where('employee_id', $selectedEmployee)
                ->where('pump_id', $selectedPump)
                ->whereBetween('log_date', [$fromDate, $toDate])
                ->orderBy('log_date', 'desc')
                ->get();
        }

        return view('pumps.overview_admin', compact(
            'employees','pumps','logs',
            'selectedEmployee','selectedPump','fromDate','toDate'
        ));
    }

    public function adminUpdate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'pump_id'     => 'required|integer',
        ]);

        $employeeId = $request->input('employee_id');
        $pumpId     = $request->input('pump_id');

        $emp = Employee::findOrFail($employeeId);
        $tz = 'Asia/Ho_Chi_Minh';
        $yesterday = Carbon::now($tz)->subDay()->startOfDay();
        $startDate = $yesterday->copy()->subDays(29);

        $token     = session('access_token');
        $baseTrans = rtrim($emp->url, '/') . '/integration/transactions';

        for ($date = $startDate; $date->lte($yesterday); $date->addDay()) {
            $logDate = $date->toDateString();

            if (DailyPumpLog::where('employee_id', $employeeId)
                ->where('pump_id', $pumpId)
                ->where('log_date', $logDate)
                ->exists()) {
                continue;
            }

            $fromTime = "$logDate 00:00:00";
            $toTime   = "$logDate 23:59:59";
            $query    = http_build_query([
                'searchType' => 1,
                'pumpId'     => $pumpId,
                'fromTime'   => $fromTime,
                'toTime'     => $toTime,
            ]);
            $resp = app(ListFunctionController::class)
                    ->getAPIAuth("{$baseTrans}?{$query}", [], $token);

            $sumMoney     = 0;
            $sumMillis    = 0;
            $sumLit       = 0;
            $dailyTotalF3 = 0;
            $latestTs     = null;

            if (is_object($resp) && is_array($resp->transactions)) {
                foreach ($resp->transactions as $t) {
                    $money   = $t->money       ?? 0;
                    $price   = $t->price       ?? 0;
                    $millis  = $t->millis      ?? 0;
                    $totalF3 = $t->totalF3     ?? 0;
                    $dtStr   = $t->dateTimeCreated ?? null;

                    $lit = ($price > 0) ? ($money / $price) : 0;

                    $sumMoney  += $money;
                    $sumMillis += $millis;
                    $sumLit    += $lit;

                    if ($dtStr) {
                        $ts = strtotime($dtStr);
                        if ($latestTs === null || $ts > $latestTs) {
                            $latestTs     = $ts;
                            $dailyTotalF3 = $totalF3;
                        }
                    }
                }
            }

            DailyPumpLog::create([
                'log_date'    => $logDate,
                'employee_id' => $employeeId,
                'pump_id'     => $pumpId,
                'money'       => $sumMoney,
                'millis'      => $sumMillis,
                'total_f3'    => $dailyTotalF3,
                'lit'         => $sumLit,
            ]);
        }

        return redirect()->back()
                        ->with('status', 'Đã cập nhật tổng log 30 ngày cho employee #' . $employeeId);
    }

    public function getPumpsByEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:employee,id',
        ]);

        $emp = Employee::find($request->employee_id);
        $token = session('access_token');
        $basePumpsUrl = rtrim($emp->url, '/') . '/integration/pumps';

        $pumps = $this->apiHelper->getAPIAuth($basePumpsUrl, [], $token);
        if (!is_array($pumps)) {
            $pumps = [];
        }

        return response()->json(array_map(function($p){
            return [
                'id'       => $p->id,
                'fuelName' => $p->fuelName,
            ];
        }, $pumps));
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'pump_id'   => 'required|integer',
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $pumpId   = $request->pump_id;
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;

        $logs = DailyPumpLog::where('pump_id', $pumpId)
            ->whereBetween('log_date', [$fromDate, $toDate])
            ->orderBy('log_date', 'desc')
            ->get(['log_date','lit','money','millis','total_f3']);

        $fileName = "logs_pump_{$pumpId}_{$fromDate}_to_{$toDate}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Ngày','Lít','Tiền (VND)','Millis','Tổng Lít F3']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->log_date,
                    number_format($log->lit,3,'.',''),
                    number_format($log->money,2,'.',''),
                    $log->millis,
                    number_format($log->total_f3,3,'.',''),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function adminExportCsv(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:employee,id',
            'pump_id'     => 'required|integer',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
        ]);

        $employeeId = $request->employee_id;
        $pumpId     = $request->pump_id;
        $fromDate   = $request->from_date;
        $toDate     = $request->to_date;

        $logs = DailyPumpLog::where('employee_id', $employeeId)
            ->where('pump_id', $pumpId)
            ->whereBetween('log_date', [$fromDate, $toDate])
            ->orderBy('log_date', 'desc')
            ->get(['log_date','lit','money','millis','total_f3']);

        $fileName = "admin_logs_emp{$employeeId}_pump{$pumpId}_{$fromDate}_to_{$toDate}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Ngày','Lít','Tiền (VND)','Millis','Tổng Lít F3']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->log_date,
                    number_format($log->lit, 3, '.', ''),
                    number_format($log->money, 2, '.', ''),
                    $log->millis,
                    number_format($log->total_f3, 3, '.', ''),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function UserExportCsv(Request $request, $pumpId)
    {
        $r = $request->merge(['pumpIdParam' => $pumpId]);
        $response = $this->logs($r, $pumpId);

        $data = $this->fetchTransactions($request, $pumpId);

        $fileName = "pump_{$pumpId}_logs_{$data['from']}_to_{$data['to']}.csv";
        $logs     = $data['transactions'];

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($logs) {
            $handle = fopen('php://output','w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Thời gian','ID giao dịch','Giá','Lít','Tiền','Millis','Tổng F3']);
            foreach($logs as $tx){
                fputcsv($handle, [
                    $tx->dateTimeCreated,
                    $tx->id,
                    $tx->price,
                    number_format($tx->money / $tx->price, 3, '.', ''),
                    $tx->money,
                    $tx->millis,
                    $tx->totalF3,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function UserExportPdf(Request $request, $pumpId)
    {
        $data = $this->fetchTransactions($request, $pumpId);
        $totals       = $data['totals'];
        $transactions = $data['transactions'];
    
        $token    = session('access_token');
        $listUrl  = env('API_APP') . 'integration/pumps';
        $allPumps = $this->apiHelper->getAPIAuth($listUrl, [], $token);

        if (! is_array($allPumps)) {
            $allPumps = [];
        }

        $pumpObj = collect($allPumps)->firstWhere('id', $pumpId);

        $fuelName = $pumpObj->fuelName ?? '';
    
        // dd($pumpId, $allPumps, $fuelName,$pumpObj);
        $viewData = [
            'pumpId'       => $pumpId,
            'fuelName'     => $fuelName,
            'from'         => $data['from'],
            'to'           => $data['to'],
            'totals'       => $totals,
            'transactions' => $transactions,
        ];
    
        $pdf = Pdf::loadView('pumps.logs_invoice', $viewData)
                  ->setPaper('a4', 'portrait');
    
        return $pdf->download("invoice_pump_{$pumpId}.pdf");
    }

    protected function fetchTransactions(Request $request, $pumpId)
    {
        $validated = $request->validate([
            'fromTime' => 'required|date_format:Y-m-d\TH:i',
            'toTime'   => 'required|date_format:Y-m-d\TH:i|after_or_equal:fromTime',
        ]);

        $from = Carbon::createFromFormat('Y-m-d\TH:i', $validated['fromTime'])
                      ->format('Y-m-d H:i:s');
        $to   = Carbon::createFromFormat('Y-m-d\TH:i', $validated['toTime'])
                      ->format('Y-m-d H:i:s');

        $token = session('access_token');
        $base  = env('API_APP') . 'integration/transactions';
        $query = http_build_query([
            'searchType'=>1,
            'pumpId'    =>$pumpId,
            'fromTime'  =>$from,
            'toTime'    =>$to
        ]);
        $resp = $this->apiHelper->getAPIAuth("{$base}?{$query}", [], $token);

        $totals = ['totalMillis'=>0,'totalMoney'=>0,'totalTrans'=>0];
        $txs    = [];

        if (is_object($resp)) {
            $totals = [
                'totalMillis'=>$resp->totalMillis  ?? 0,
                'totalMoney' =>$resp->totalMoney   ?? 0,
                'totalTrans' =>$resp->totalTrans   ?? 0,
            ];
            $txs = is_array($resp->transactions) ? $resp->transactions : [];
        }

        $collection = collect($txs)->sortByDesc(fn($t)=>strtotime($t->dateTimeCreated))->values();
        $perPage = 1000;
        $paginator = new LengthAwarePaginator(
            $collection->all(),
            $collection->count(),
            $perPage,
            1
        );
        return [
            'from'         => $validated['fromTime'],
            'to'           => $validated['toTime'],
            'totals'       => $totals,
            'transactions' => $collection->all(),
        ];
    }

   public function exportRowPdf(Request $request, $pumpId)
    {
        $data = $request->validate([
            'tx_data' => 'required|string',
        ])['tx_data'];

        $tx = json_decode(base64_decode($data));
        if (! $tx) {
            abort(400, 'Dữ liệu không hợp lệ');
        }

        $token   = session('access_token');
        $listUrl = env('API_APP') . 'integration/pumps';
        $allPumps = $this->apiHelper->getAPIAuth($listUrl, [], $token);
        if (! is_array($allPumps)) {
            $allPumps = [];
        }
        $pumpObj  = collect($allPumps)->firstWhere('id', $pumpId);
        $fuelName = $pumpObj->fuelName ?? '';

        $from = Carbon::parse($tx->dateTimeCreated)->toDateTimeString();

        $viewData = [
            'pumpId'       => $pumpId,
            'fuelName'     => $fuelName,
            'from'         => $from,
            'to'           => $from,
            'transactions' => [ $tx ],
            'totals'       => [
                'totalMillis' => $tx->millis,
                'totalMoney'  => $tx->money,
                'totalTrans'  => 1,
            ],
        ];

        $pdf = PDF::loadView('pumps.logs_invoice', $viewData)
                ->setPaper('a4', 'portrait');

        return $pdf->download("invoice_pump_{$pumpId}_tx_{$tx->id}.pdf");
    }

    public function batchUpdateOverview(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'pump_id'     => 'required|integer',
            'fuel_type'   => 'required|string',
            'dates'       => 'required|array',
            'dates.*'     => 'required|date',
        ]);

        $employee_id = $request->input('employee_id');
        $pump_id = $request->input('pump_id');
        $fuel_type = $request->input('fuel_type');
        $dates = $request->input('dates');

        $emp = Employee::findOrFail($employee_id);
        $token = session('access_token');
        $baseTrans = rtrim($emp->url, '/') . '/integration/transactions';

        $created = 0;
        foreach ($dates as $targetDate) {
            $exists = DailyPumpLog::where('pump_id', $pump_id)
                ->where('employee_id', $employee_id)
                ->where('fuel_type', $fuel_type)
                ->where('log_date', $targetDate)
                ->exists();
            if ($exists) continue;

            $fromTime = "{$targetDate} 00:00:00";
            $toTime   = "{$targetDate} 23:59:59";
            $query = http_build_query([
                'searchType' => 1,
                'pumpId'     => $pump_id,
                'fromTime'   => $fromTime,
                'toTime'     => $toTime,
            ]);
            $resp = app(ListFunctionController::class)
                ->getAPIAuth("{$baseTrans}?{$query}", [], $token);

            $sumMoney     = 0;
            $sumMillis    = 0;
            $sumLit       = 0;
            $dailyTotalF3 = 0;
            $latestTs     = null;

            if (is_object($resp) && is_array($resp->transactions)) {
                foreach ($resp->transactions as $t) {
                    $money   = $t->money       ?? 0;
                    $price   = $t->price       ?? 0;
                    $millis  = $t->millis      ?? 0;
                    $totalF3 = $t->totalF3     ?? 0;
                    $dtStr   = $t->dateTimeCreated ?? null;

                    $sumMoney  += $money;
                    $sumMillis += $millis;
                    $sumLit    += ($price > 0) ? ($money / $price) : 0;

                    if ($dtStr) {
                        $ts = strtotime($dtStr);
                        if ($latestTs === null || $ts > $latestTs) {
                            $latestTs     = $ts;
                            $dailyTotalF3 = $totalF3;
                        }
                    }
                }
            }

            DailyPumpLog::create([
                'log_date'    => $targetDate,
                'employee_id' => $employee_id,
                'pump_id'     => $pump_id,
                'fuel_type'   => $fuel_type,
                'money'       => $sumMoney,
                'millis'      => $sumMillis,
                'total_f3'    => $dailyTotalF3,
                'lit'         => $sumLit,
            ]);
            $created++;
        }

        return back()->with('status', "Đã cập nhật tổng log cho {$created} ngày còn thiếu của bộ này.");
    }

}
