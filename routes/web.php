<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FuelEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FuelSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('index');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('post-login', [AuthController::class, 'login'])->name('login.post');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('tanks', TankController::class)->only(['index', 'show']);

    Route::get('pumps',[PumpController::class, 'index'])->name('pumps.index');
    Route::get('pumps/{pump}/logs',[PumpController::class, 'showLogsForm'])->name('pumps.logs.form');
    Route::get('pumps/{pump}/logs/list', [PumpController::class, 'logs'])->name('pumps.logs.list');

    Route::get('pumps/{pump}/logs/export-csv', [PumpController::class, 'exportCsv'])->name('pumps.logs.exportCsv');
    Route::get('pumps/{pump}/logs/export-pdf', [PumpController::class, 'exportPdf'])->name('pumps.logs.exportPdf');

    Route::get('pumps/my/logs', [PumpController::class, 'myLogsForm'])->name('pumps.my.logs.form');
    Route::get('pumps/my/logs/list', [PumpController::class, 'myLogs'])->name('pumps.my.logs.list');

    Route::get('dashboard/logs', [PumpController::class, 'dashboardLogs'])->name('dashboard.logs');

    Route::get('overview', [PumpController::class, 'checkOverview'])->name('overview.index');
    Route::post('overview/update', [PumpController::class, 'updateOverview'])->name('overview.update');

     // Form nhập hàng
    Route::get ('entries/create',[FuelEntryController::class,'create'])->name('entries.create');
    Route::post('entries',[FuelEntryController::class,'store'])->name('entries.store');
 
     // Thống kê nhập hàng
    Route::get ('entries/stats',[FuelEntryController::class,'statsForm'])->name('entries.stats.form');
    Route::get ('entries/stats/results',[FuelEntryController::class,'stats'])->name('entries.stats');
    Route::get('entries/{entry}/edit', [FuelEntryController::class,'edit'])->name('entries.edit');
    Route::put('entries/{entry}', [FuelEntryController::class,'update'])->name('entries.update');
    Route::delete('entries/{entry}', [FuelEntryController::class,'destroy'])->name('entries.destroy');

     Route::get('reports/inventory',[ReportController::class,'inventory'])->name('reports.inventory');
     Route::get('reports/inventory/pdf', [ReportController::class,'inventoryPdf'])->name('reports.inventory.pdf');

    Route::get('fuel-settings',[ReportController::class,'fuelSettingsIndex'])->name('fuel-settings.index');
    Route::get('fuel-settings/create',[ReportController::class,'fuelSettingsCreate'])->name('fuel-settings.create');
    Route::post('fuel-settings',[ReportController::class,'fuelSettingsStore'])->name('fuel-settings.store');
    Route::get('fuel-settings/{setting}/edit',[ReportController::class,'fuelSettingsEdit'])->name('fuel-settings.edit');
    Route::put('fuel-settings/{setting}',[ReportController::class,'fuelSettingsUpdate'])->name('fuel-settings.update');
    Route::delete('fuel-settings/{setting}',[ReportController::class,'fuelSettingsDestroy'])->name('fuel-settings.destroy');
});

Route::middleware(['auth'])->prefix('admin')->group(function(){
    Route::resource('employees', EmployeeController::class)->except(['show']);
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('users',[UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('users/create',[UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('users',[UserManagementController::class, 'store'])->name('admin.users.store');

    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('users/{user}/revoke', [UserManagementController::class, 'revokeEmployee'])->name('admin.users.revoke');

    Route::get('password/change',[UserManagementController::class, 'showChangeForm'])->name('employee.password.change');
    Route::post('password/change',[UserManagementController::class, 'changePassword'])->name('employee.password.update');

    Route::post('users/{user}/assign', [UserManagementController::class, 'assignEmployee'])->name('admin.users.assign');

    //add vòi bơm, role nhân viên
    Route::get('users/{user}/assign-pump', [UserManagementController::class, 'showAssignPumpForm'])->name('admin.users.assign.pump.form');
    Route::post('users/{user}/assign', [UserManagementController::class, 'assignPump'])->name('admin.users.assign');

    Route::get('overview', [PumpController::class, 'adminOverview'])->name('admin.overview.index');
    Route::post('overview/update', [PumpController::class, 'adminUpdateOverview'])->name('admin.overview.update');

    Route::get('overview/pumps', [PumpController::class, 'getPumpsByEmployee'])->name('admin.overview.pumps');
    Route::get('overview/export-csv', [PumpController::class, 'exportCsv'])->name('overview.exportCsv');
    Route::get('admin/overview/export-csv', [PumpController::class, 'adminExportCsv'])->name('admin.overview.exportCsv');
    Route::get('pumps/{pump}/logs/export-csv', [PumpController::class, 'UserExportCsv'])->name('pumps.logs.exportCsv');
    Route::get('pumps/{pump}/logs/export-pdf', [PumpController::class, 'UserExportPdf'])->name('pumps.logs.exportPdf');

    Route::post('pumps/{pump}/logs/row-pdf',[PumpController::class, 'exportRowPdf'])->name('pumps.logs.rowPdf');    
    
    //cập nhật
    Route::post('/pumps/batch-update-overview', [PumpController::class, 'batchUpdateOverview'])->name('pumps.batchUpdateOverview');

    Route::get('stores', [ReportController::class,'storeIndex'])->name('admin.stores.index');
    Route::get('admin/stores/inventory/preview', [ReportController::class,'previewInventory'])->name('admin.stores.inventory.preview');
    Route::get('admin/stores/inventory/download', [ReportController::class,'downloadInventory'])->name('admin.stores.inventory.download'); 
    // — Nhập hàng —
    Route::get('entries/preview',  [ReportController::class,'previewEntries'])->  name('admin.stores.entries.preview');
    Route::get('entries/download', [ReportController::class,'downloadEntries'])-> name('admin.stores.entries.download');

    // — Tổng sản lượng —
    Route::get('production/preview',  [ReportController::class,'previewProduction'])->  name('admin.stores.production.preview');
    Route::get('production/download', [ReportController::class,'downloadProduction'])-> name('admin.stores.production.download');

    // — Lợi nhuận —
    Route::get('profit/preview',  [ReportController::class,'previewProfit'])->  name('admin.stores.profit.preview');
    Route::get('profit/download', [ReportController::class,'downloadProfit'])-> name('admin.stores.profit.download');
    
    Route::get('fuel-settings-all', [FuelSettingController::class,'index'])->name('admin.fuel_settings.index');
    Route::post('fuel-settings-all/update-all', [FuelSettingController::class,'updateAll'])->name('admin.fuel_settings.updateAll');
}); 