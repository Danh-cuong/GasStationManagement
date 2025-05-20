@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Quản lý Users</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tạo User mới</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Chức vụ</th>
                    {{-- <th>Vòi bơm</th> --}}
                    <th>Created At</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        {{-- <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-secondary">{{ $role->name }}</span>
                            @endforeach
                        </td> --}}
                        <td>
                            @if ($user->hasRole('employee'))
                                Nhân viên số {{ $user->employee_id }}
                            @endif
                        </td>
                        {{-- <td>
                            @if ($user->hasRole('employee'))
                                Vòi bơm số {{ $user->pump_id }}
                            @endif
                        </td> --}}

                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-nowrap">
                            @unless ($user->hasRole('admin'))
                                {{-- @if ($user->pump_id == 0)
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('admin.users.assign.pump.form', $user->id) }}">Vòi
                                        bơm</a>
                                @endif --}}
                                @if ($user->hasRole('employee'))
                                    <form action="{{ route('admin.users.revoke', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning"
                                            onclick="return confirm('Xác nhận hủy quyền employee của {{ $user->name }}?')">
                                            Hủy quyền
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.assign', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Cấp quyền employee cho {{ $user->name }}?')">
                                            Cấp quyền
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Xác nhận xóa user {{ $user->name }}?')">
                                        Xóa
                                    </button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
@endsection
