@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>{{ isset($employee) ? 'Sửa' : 'Thêm mới' }} Mã thiết bị cửa hàng</h3>
        </div>
        <div class="card-body p-0 container pt-3" style="min-height: 95vh">
            <div class="container">
                <form method="POST"
                    action="{{ isset($employee) ? route('employees.update', $employee) : route('employees.store') }}">
                    @csrf
                    @if (isset($employee))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Tên mã thiết bị cửa hàng</label>
                        <input name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $employee->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client ID</label>
                        <input name="client_id" class="form-control @error('client_id') is-invalid @enderror"
                            value="{{ old('client_id', $employee->client_id ?? '') }}" required>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client Secret</label>
                        <input name="client_secret" class="form-control @error('client_secret') is-invalid @enderror"
                            value="{{ old('client_secret', $employee->client_secret ?? '') }}" required>
                        @error('client_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input name="url" type="url" class="form-control @error('url') is-invalid @enderror"
                            value="{{ old('url', $employee->url ?? '') }}" required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="1" {{ old('status', $employee->status ?? '') == 1 ? 'selected' : '' }}>Đang
                                hoạt động</option>
                            <option value="0" {{ old('status', $employee->status ?? '') == 0 ? 'selected' : '' }}>Dừng
                                hoạt động</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <button class="btn btn-success">{{ isset($employee) ? 'Cập nhật' : 'Tạo mới' }}</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection
