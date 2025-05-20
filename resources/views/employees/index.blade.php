@extends('layouts.app')

@section('content')
    <div class="">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Thông tin mã cửa hàng</h3>
            </div>
            <div class="card-body p-0 container-fluid pt-3" style="min-height: 95vh">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <a href="{{ route('employees.create') }}" class="btn btn-primary mb-3">
                    Thêm mới mã cửa hàng
                </a>

                @if ($employees->isEmpty())
                    <div class="alert alert-warning">Chưa có mã thiết bị cửa hàng nào.</div>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mã thiết bị cửa hàng</th>
                                <th>Client ID</th>
                                <th>Client Secret</th>
                                <th>URL</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $emp)
                                <tr>
                                    <td>{{ $emp->id }}</td>
                                    <td>{{ $emp->name }}</td>
                                    <td>{{ $emp->client_id }}</td>
                                    <td>{{ $emp->client_secret }}</td>
                                    <td>{{ $emp->url }}</td>
                                    @if ($emp->status == 0)
                                        <td style="color: red">Dừng hoạt động</td>
                                    @else
                                        <td style="color: green">Đang hoạt động</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-warning">Sửa</a>
                                        <form action="{{ route('employees.destroy', $emp) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Xác nhận xóa?')"
                                                class="btn btn-sm btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $employees->links() }}
                @endif
            </div>
        </div>
    </div>
@endsection
