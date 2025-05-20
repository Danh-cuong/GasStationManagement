@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Quản lý Fuel Settings</h3>
        </div>
        <div class="card-body p-0 container pt-3" style="min-height: 95vh">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <a href="{{ route('fuel-settings.create') }}" class="btn btn-primary mb-3">Thêm mới</a>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Fuel Type</th>
                            <th>Tồn đầu kỳ (Lít)</th>
                            <th>Hao hụt Nhập (%)</th>
                            <th>Hao hụt Xuất (%)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($settings as $i => $s)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td class="text-center">{{ $s->employee_id }}</td>
                                <td>{{ $fuelTypes[$s->fuel_type] }}</td>
                                <td class="text-end">{{ number_format($s->start_inv, 3) }}</td>
                                <td class="text-end">{{ $s->import_loss_rate }}</td>
                                <td class="text-end">{{ $s->export_loss_rate }}</td>
                                <td class="text-center">
                                    <a href="{{ route('fuel-settings.edit', $s) }}" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="{{ route('fuel-settings.destroy', $s) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xoá cài đặt?')">
                                            Xoá
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
