<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    protected $employeeRole;
    public $apiHelper;

    public function __construct()
    {
        $this->employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $this->apiHelper = new ListFunctionController();
    }

    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|unique:users,name',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($this->employeeRole);

        return redirect()->route('admin.users.assign.pump.form', $user->id)
                        ->with('status', "Tạo user {$user->name} thành công với role employee. Vui lòng gán pump cho user.");
    }


    public function destroy(User $user)
    {
        // Không xóa admin
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')->with('status', "Không thể xóa admin");
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', "Xóa user {$user->name} thành công");
    }

    public function revokeEmployee(User $user)
    {
        if ($user->hasRole('employee')) {
            $user->removeRole('employee');
            return redirect()->route('admin.users.index')->with('status', "Hủy quyền employee của {$user->name} thành công");
        }

        return redirect()->route('admin.users.index')->with('status', "{$user->name} không có role employee");
    }


    public function showChangeForm()
    {
        return view('users.change_password');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Mật khẩu hiện tại không đúng',
            ]);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('employee.password.change')->with('status', 'Đổi mật khẩu thành công');
    }

    public function assignEmployee(User $user)
    {
        if (! $user->hasRole('employee')) {
            $user->assignRole($this->employeeRole);
            return redirect()->route('admin.users.index')
                             ->with('status', "Đã cấp quyền employee cho {$user->name}");
        }

        return redirect()->route('admin.users.index')->with('status', "{$user->name} đã có quyền employee");
    }

    public function showAssignPumpForm(User $user)
    {
        $token = session('access_token');
        $url = env('API_APP') . 'integration/pumps';

        $pumps = $this->apiHelper->getAPIAuth($url, [], $token);
        if (!is_array($pumps)) {
            $pumps = [];
        }

        $employees = Employee::all();

        return view('users.assign_pump', compact('user', 'pumps', 'employees'));
    }


    public function assignPump(Request $request, User $user)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employee,id',
        ]);

        $user->employee_id = $data['employee_id'];
        $user->save();

        return redirect()->route('admin.users.index')
                        ->with('status', "Đã gán profile nhân viên (ID: {$data['employee_id']}) cho user {$user->name}");
    }

    
}
