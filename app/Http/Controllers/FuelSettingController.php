<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\FuelSetting;

class FuelSettingController extends Controller
{
    // 1. Show form
    public function index()
    {
        // Lấy tất cả nhân viên có cửa hàng
        $employees = Employee::all();
        $fuelTypes = [
            'RON95_III'=>'Xăng RON 95-III',
            'E5_92_II'=>'Xăng E5 RON 92-II',
            'DO05S_II'=>'Dầu DO 0,05S-II',
        ];

        // Load hoặc tạo mặc định
        $settings = FuelSetting::all()
            ->keyBy(fn($s) => $s->employee_id.'-'.$s->fuel_type);

        return view('fuel_settings.index_admin', compact('employees','fuelTypes','settings'));
    }

    // 2. Xử lý update hàng loạt
    public function updateAll(Request $r)
    {
        // expected input: settings[employee_id][fuel_code][start_inv|import_loss_rate|export_loss_rate]
        $data = $r->validate([
            'settings' => 'required|array',
            'settings.*.*.start_inv'        => 'required|numeric|min:0',
            'settings.*.*.import_loss_rate' => 'required|numeric|min:0',
            'settings.*.*.export_loss_rate' => 'required|numeric|min:0',
        ]);

        foreach ($data['settings'] as $empId => $fuelArr) {
            foreach ($fuelArr as $code => $vals) {
                FuelSetting::updateOrCreate(
                    ['employee_id'=>$empId,'fuel_type'=>$code],
                    [
                      'start_inv'        => $vals['start_inv'],
                      'import_loss_rate' => $vals['import_loss_rate'],
                      'export_loss_rate' => $vals['export_loss_rate'],
                    ]
                );
            }
        }

        return back()->with('success','Cập nhật thiết lập tồn kho thành công.');
    }
}
