<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\FuelEntry;
use App\Models\DailyPumpLog;
use App\Models\FuelSetting;
use App\Models\Employee;
use PDF;
use Auth;

class ReportController extends Controller
{
    protected $fuelTypes = [
        'RON95_III' => 'Xăng RON 95-III',
        'E5_92_II'  => 'Xăng E5 RON 92-II',
        'DO05S_II'  => 'Dầu DO 0,05S-II',
    ];

     public function fuelSettingsIndex()
     {
         $settings = FuelSetting::orderBy('employee_id')
                         ->orderBy('fuel_type')
                         ->get();
         return view('fuel_settings.index', [
             'settings'  => $settings,
             'fuelTypes' => $this->fuelTypes,
         ]);
     }
 
    protected function getMissingLogPairsBatch($fromDate = '2025-04-01')
    {
        $toDate = Carbon::now('Asia/Ho_Chi_Minh')->subDay()->toDateString();

        $employees = Employee::all();
        $result = [];

        foreach ($employees as $emp) {
            $pumps = app(\App\Http\Controllers\ListFunctionController::class)
                ->getAPIAuth(rtrim($emp->url, '/') . '/integration/pumps', [], session('access_token'));
            if (!is_array($pumps)) $pumps = [];

            foreach ($pumps as $pump) {
                $fuelMap = [
                    'Xăng RON 95-III'   => 'RON95_III',
                    'Dầu DO 0,05S-II'   => 'DO05S_II',
                    'Xăng E5 RON 92-II' => 'E5_92_II',
                    'DO 0,05S-II'       =>  'DO05S_II'
                ];
                $fuelName = $pump->fuelName ?? '';
                $fuelType = $fuelMap[$fuelName] ?? null;
                if (!$fuelType) continue;

                $allDates = [];
                $dt = Carbon::parse($fromDate);
                $end = Carbon::parse($toDate);
                while ($dt->lte($end)) {
                    $allDates[] = $dt->toDateString();
                    $dt->addDay();
                }
                $loggedDates = DailyPumpLog::where('employee_id', $emp->id)
                    ->where('pump_id', $pump->id)
                    ->where('fuel_type', $fuelType)
                    ->whereBetween('log_date', [$fromDate, $toDate])
                    ->pluck('log_date')
                    ->toArray();

                $missing = array_diff($allDates, $loggedDates);

                if (!empty($missing)) {
                    $result[] = [
                        'employee_id'   => $emp->id,
                        'employee_name' => $emp->name,
                        'pump_id'       => $pump->id,
                        'pump_name'     => $pump->name ?? $pump->id,
                        'fuel_type'     => $fuelType,
                        'fuel_name'     => $fuelName,
                        'missing_dates' => array_values($missing),
                        'missing_count' => count($missing),
                    ];
                }
            }
        }
        return $result;
    }



     public function fuelSettingsCreate()
     {
         return view('fuel_settings.form', [
             'setting'   => new FuelSetting,
             'fuelTypes' => $this->fuelTypes,
         ]);
     }
 
     public function fuelSettingsStore(Request $request)
    {
        $data = $request->validate([
            'fuel_type'        => 'required|in:'.implode(',',array_keys($this->fuelTypes)),
            'start_inv'        => 'required|numeric|min:0',
            'import_loss_rate' => 'required|numeric|min:0|max:1',
            'export_loss_rate' => 'required|numeric|min:0|max:1',
        ]);

        FuelSetting::create([
            'employee_id'      => Auth::user()->employee_id,
            'fuel_type'        => $data['fuel_type'],
            'start_inv'        => $data['start_inv'],
            'import_loss_rate' => $data['import_loss_rate'],
            'export_loss_rate' => $data['export_loss_rate'],
        ]);

        return redirect()->route('fuel-settings.index')
                         ->with('status','Đã thêm cài đặt thành công.');
    }
 
     public function fuelSettingsEdit(FuelSetting $setting)
     {
         return view('fuel_settings.form', [
             'setting'   => $setting,
             'fuelTypes' => $this->fuelTypes,
         ]);
     }
 
     public function fuelSettingsUpdate(Request $request, FuelSetting $setting)
    {
        $data = $request->validate([
            'fuel_type'        => 'required|in:'.implode(',',array_keys($this->fuelTypes)),
            'start_inv'        => 'required|numeric|min:0',
            'import_loss_rate' => 'required|numeric|min:0|max:1',
            'export_loss_rate' => 'required|numeric|min:0|max:1',
        ]);

        $setting->update([
            'fuel_type'        => $data['fuel_type'],
            'start_inv'        => $data['start_inv'],
            'import_loss_rate' => $data['import_loss_rate'],
            'export_loss_rate' => $data['export_loss_rate'],
        ]);

        return redirect()->route('fuel-settings.index')
                         ->with('status','Cập nhật cài đặt thành công.');
    }

    public function fuelSettingsDestroy(FuelSetting $setting)
    {
         $setting->delete();
 
         return redirect()->route('fuel-settings.index')
                          ->with('status','Đã xóa cài đặt.');
    }

    public function inventory(Request $request)
    {
        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to   = $request->input('to_date',   now()->toDateString());
        $user = Auth::user();

        $fuelTypes = [
            'RON95_III' => 'Xăng RON 95-III',
            'E5_92_II'  => 'Xăng E5 RON 92-II',
            'DO05S_II'  => 'Dầu DO 0,05S-II',
        ];

        $initialDate = '2025-04-01';

        $rows = [];

        foreach ($fuelTypes as $code => $label) {
            $setting = FuelSetting::where('employee_id', $user->employee_id)
                ->where('fuel_type', $code)
                ->first();

            $baseStartInv     = $setting->start_inv        ?? 50000;
            $importLossRate   = $setting->import_loss_rate ?? 0.0012;
            $exportLossRate   = $setting->export_loss_rate ?? 0.0006;

            if ($from <= $initialDate) {
                $startInv = $baseStartInv;
            } else {
                $importBefore = FuelEntry::where('fuel_type', $code)->where('employee_id', $user->employee_id)
                    ->whereBetween('entry_time', ["{$initialDate} 00:00:00", "{$from} 23:59:59"])
                    ->sum('quantity');
                $exportBefore = DailyPumpLog::where('fuel_type', $code)->where('employee_id', $user->employee_id)
                    ->whereBetween('log_date', [$initialDate, $from])
                    ->sum('lit');

                $startInv = $baseStartInv + $importBefore - $exportBefore;
            }

            $impPeriod = FuelEntry::where('fuel_type', $code)->where('employee_id', $user->employee_id)
                ->whereBetween('entry_time', ["{$from} 00:00:00", "{$to} 23:59:59"])
                ->sum('quantity');
            $expPeriod = DailyPumpLog::where('fuel_type', $code)->where('employee_id', $user->employee_id)
                ->whereBetween('log_date', [$from, $to])
                ->sum('lit');

            $lossImp = $impPeriod * $importLossRate;
            $lossExp = $expPeriod * $exportLossRate;
            $endInv  = $startInv + $impPeriod - $expPeriod - $lossImp - $lossExp;

            $rows[] = [
                'label'       => $label,
                'start_inv'   => $startInv,
                'imp_period'  => $impPeriod,
                'exp_period'  => $expPeriod,
                'loss_imp'    => $lossImp,
                'loss_exp'    => $lossExp,
                'end_inv'     => $endInv,
            ];
        }

        return view('reports.inventory', compact('from', 'to', 'rows'));
    }

    /**
     * Xuất PDF Báo cáo Nhập – Xuất – Tồn
     */
    public function inventoryPdf(Request $request)
    {
        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to   = $request->input('to_date',   now()->toDateString());
        $user = Auth::user();

        $fuelTypes = [
            'RON95_III' => 'Xăng RON 95-III',
            'E5_92_II'  => 'Xăng E5 RON 92-II',
            'DO05S_II'  => 'Dầu DO 0,05S-II',
        ];

        $rows = [];

        foreach ($fuelTypes as $code => $label) {
            $setting = FuelSetting::where('employee_id', $user->employee_id)
                         ->where('fuel_type', $code)
                         ->first();

            $start_inv        = $setting->start_inv        ?? 50000;
            $import_loss_rate = $setting->import_loss_rate ?? 0.0012;
            $export_loss_rate = $setting->export_loss_rate ?? 0.0006;

            $imp_period = FuelEntry::where('fuel_type', $code)
                ->whereBetween('entry_time', ["{$from} 00:00:00", "{$to} 23:59:59"])
                ->sum('quantity');

            $exp_period = DailyPumpLog::where('fuel_type', $code)
                ->whereBetween('log_date', [$from, $to])
                ->sum('lit');

            $loss_imp = $imp_period * $import_loss_rate;
            $loss_exp = $exp_period * $export_loss_rate;

            $end_inv = $start_inv + $imp_period - $exp_period - $loss_imp - $loss_exp;

            $rows[] = [
                'label'       => $label,
                'start_inv'   => $start_inv,
                'imp_period'  => $imp_period,
                'exp_period'  => $exp_period,
                'loss_imp'    => $loss_imp,
                'loss_exp'    => $loss_exp,
                'end_inv'     => $end_inv,
            ];
        }

        $pdf = PDF::loadView('reports.inventory_pdf', compact('from','to','rows'))
                  ->setPaper('a4','landscape');

        return $pdf->download("BaoCao_NhapXuatTon_{$from}_{$to}.pdf");
    }

    public function loss(Request $request){

    }


    public function storeIndex(Request $request)
    {
        $missingPairs = $this->getMissingLogPairsBatch();

        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to   = $request->input('to_date',   now()->toDateString());
        return view('stores.index', compact('from','to','missingPairs'));
    }

    // ----- Inventory -----
    public function previewInventory(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date;
        $to   = $r->to_date;

        // Tạo data giống download
        list($rows,$totals) = $this->buildInventoryData($from,$to);

        return view('stores.inventory_preview', compact('from','to','rows','totals'));
    }

    public function downloadInventory(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date;
        $to   = $r->to_date;
        list($rows,$totals) = $this->buildInventoryData($from,$to);

        $pdf = PDF::loadView('stores.inventory_pdf', compact('from','to','rows','totals'))
                  ->setPaper('a4','landscape');
        return $pdf->download("inventory_{$from}_to_{$to}.pdf");
    }

    // ----- Entries -----
    public function previewEntries(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date; $to = $r->to_date;
        // build
        $stores = $this->buildEntriesData($from,$to);
        return view('stores.entries_preview', compact('from','to','stores'));
    }

    public function downloadEntries(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date; $to = $r->to_date;
        $stores = $this->buildEntriesData($from,$to);

        $pdf = PDF::loadView('stores.entries_pdf', compact('from','to','stores'))
                  ->setPaper('a4','landscape');
        return $pdf->download("entries_{$from}_to_{$to}.pdf");
    }

    // ----- Production -----
    public function previewProduction(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date; $to = $r->to_date;
        $rows = $this->buildProductionData($from,$to);
        return view('stores.production_preview', compact('from','to','rows'));
    }

    public function downloadProduction(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date; $to = $r->to_date;
        $rows = $this->buildProductionData($from,$to);

        $pdf = PDF::loadView('stores.production_pdf', compact('from','to','rows'))
                  ->setPaper('a4','landscape');
        return $pdf->download("production_{$from}_to_{$to}.pdf");
    }

    // ----- Profit -----
    public function previewProfit(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date; $to = $r->to_date;
        $rows = $this->buildProfitData($from,$to);
        return view('stores.profit_preview', compact('from','to','rows'));
    }

    public function downloadProfit(Request $r)
    {
        $r->validate([
            'from_date'=>'required|date',
            'to_date'  =>'required|date|after_or_equal:from_date',
        ]);
        $from = $r->from_date; $to = $r->to_date;
        $rows = $this->buildProfitData($from,$to);

        $pdf = PDF::loadView('stores.profit_pdf', compact('from','to','rows'))
                  ->setPaper('a4','landscape');
        return $pdf->download("profit_{$from}_to_{$to}.pdf");
    }

    // === Helpers: build data for each report ===

    protected function buildInventoryData($from, $to)
    {
        // Định nghĩa các loại nhiên liệu
        $fuelTypes = [
            'RON95_III' => 'Xăng RON 95-III',
            'E5_92_II'  => 'Xăng E5 RON 92-II',
            'DO05S_II'  => 'Dầu DO 0,05S-II',
        ];

        $rows = [];
        // Khởi tạo tổng
        $totals = [
            'start_inv'  => 0,
            'imp_period' => 0,
            'exp_period' => 0,
            'loss_imp'   => 0,
            'loss_exp'   => 0,
            'end_inv'    => 0,
        ];

        foreach (Employee::all() as $emp) {
            // Lấy setting chung (nếu bạn lưu start_inv per fuel thì filter thêm fuel_type)
            $setting = FuelSetting::where('employee_id', $emp->id)->first();
            $siDefault = $setting->start_inv ?? 50000;
            $importLossRate = $setting->import_loss_rate ?? 0.0012;
            $exportLossRate = $setting->export_loss_rate ?? 0.0006;

            foreach ($fuelTypes as $code => $label) {
                // Số dư đầu kỳ (nếu per-fuel store thì lấy theo $code, ở đây dùng chung)
                $si  = $siDefault;
                // Nhập trong kỳ
                $imp = FuelEntry::where('employee_id', $emp->id)
                    ->where('fuel_type', $code)
                    ->whereBetween('entry_time', ["{$from} 00:00:00", "{$to} 23:59:59"])
                    ->sum('quantity');
                // Xuất trong kỳ
                $exp = DailyPumpLog::where('employee_id', $emp->id)
                    ->where('fuel_type', $code)
                    ->whereBetween('log_date', [$from, $to])
                    ->sum('lit');
                // Hao hụt
                $li = $imp * $importLossRate;
                $le = $exp * $exportLossRate;
                // Tồn cuối
                $ei = $si + $imp - $exp - $li - $le;

                // Đẩy vào rows
                $rows[] = [
                    'emp'  => $emp,
                    'fuel' => $label,
                    'si'   => $si,
                    'imp'  => $imp,
                    'exp'  => $exp,
                    'li'   => $li,
                    'le'   => $le,
                    'ei'   => $ei,
                ];

                // Cộng dồn tổng
                $totals['start_inv']  += $si;
                $totals['imp_period'] += $imp;
                $totals['exp_period'] += $exp;
                $totals['loss_imp']   += $li;
                $totals['loss_exp']   += $le;
                $totals['end_inv']    += $ei;
            }
        }

        return [$rows, $totals];
    }


    protected function buildEntriesData($from,$to)
    {
        $fuelTypes = [
            'RON95_III'=>'Xăng RON 95-III',
            'E5_92_II'=>'Xăng E5 RON 92-II',
            'DO05S_II'=>'Dầu DO 0,05S-II',
        ];
        $stores=[];
        foreach(Employee::all() as $emp){
            $ents = FuelEntry::where('employee_id',$emp->id)
                ->whereBetween('entry_time',["{$from} 00:00:00","{$to} 23:59:59"])
                ->orderBy('entry_time')
                ->get()
                ->map(fn($e)=>[
                    'time'=>Carbon::parse($e->entry_time)->format('Y-m-d H:i'),
                    'doc'=>$e->document_code?:'-',
                    'fuel'=>$fuelTypes[$e->fuel_type]??$e->fuel_type,
                    'unit'=>$e->unit_type,
                    'qty'=>$e->quantity,
                    'price'=>$e->price,
                    'vat'=>$e->vat_percentage,
                    'total'=>$e->quantity*$e->price*(1+$e->vat_percentage/100),
                ]);
            if($ents->isEmpty()) continue;
            $stores[]=[
                'store'=>$emp->name,
                'entries'=>$ents,
                'sum_qty'=>$ents->sum('qty'),
                'sum_tot'=>$ents->sum('total'),
            ];
        }
        return $stores;
    }

    protected function buildProductionData($from,$to)
    {
        $rows=[];
        foreach(Employee::all() as $emp){
            $lit = DailyPumpLog::where('employee_id',$emp->id)
                     ->whereBetween('log_date',[$from,$to])->sum('lit');
            $rev = DailyPumpLog::where('employee_id',$emp->id)
                     ->whereBetween('log_date',[$from,$to])->sum('money');
            $rows[]=['store'=>$emp->name,'lit'=>$lit,'revenue'=>$rev];
        }
        return $rows;
    }

    protected function buildProfitData($from,$to)
    {
        $fuelTypes = [
            'RON95_III'=>'Xăng RON 95-III',
            'E5_92_II'=>'Xăng E5 RON 92-II',
            'DO05S_II'=>'Dầu DO 0,05S-II',
        ];
        $rows=[];
        foreach(Employee::all() as $emp){
            foreach($fuelTypes as $code=>$lbl){
                $impRev = FuelEntry::where('employee_id',$emp->id)
                    ->where('fuel_type',$code)
                    ->whereBetween('entry_time',["{$from} 00:00:00","{$to} 23:59:59"])
                    ->sum(\DB::raw('quantity*price'));
                $expRev = DailyPumpLog::where('employee_id',$emp->id)
                    ->where('fuel_type',$code)
                    ->whereBetween('log_date',[$from,$to])
                    ->sum('money');
                $rows[]=[
                    'store'=>$emp->name,
                    'fuel'=>$lbl,
                    'impRev'=>$impRev,
                    'expRev'=>$expRev,
                    'profit'=>$expRev-$impRev,
                ];
            }
        }
        return $rows;
    }
}
