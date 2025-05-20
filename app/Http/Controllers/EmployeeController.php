<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|unique:employee,name',
            'client_id'     => 'required|string',
            'client_secret' => 'required|string',
            'url'           => 'required|url',
            'status'        => 'required',
        ]);

        Employee::create($data);

        return redirect()->route('employees.index')
                         ->with('status', 'Thêm mới key nhân viên thành công');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name'          => 'required|string|unique:employee,name,'.$employee->id,
            'url'           => 'required|url',
            'status'           => 'required',
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')
                         ->with('status', 'Cập nhật key nhân viên thành công');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
                         ->with('status', 'Xóa key nhân viên thành công');
    }
}
