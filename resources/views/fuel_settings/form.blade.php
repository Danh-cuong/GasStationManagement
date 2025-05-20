@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3> {{ $setting->exists ? 'Chỉnh sửa' : 'Thêm mới' }} Cài đặt nhiên liệu</h3>
        </div>
        <div class="card-body p-0 container pt-3" style="min-height: 95vh">
            <form method="POST"
                action="{{ $setting->exists ? route('fuel-settings.update', $setting) : route('fuel-settings.store') }}">
                @csrf
                @if ($setting->exists)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Loại nhiên liệu</label>
                    <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror" required>
                        <option value="">-- Chọn nhiên liệu --</option>
                        @foreach ($fuelTypes as $code => $label)
                            <option value="{{ $code }}"
                                {{ old('fuel_type', $setting->fuel_type) == $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('fuel_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Tồn đầu kỳ (Lít)</label>
                        <input type="number" step="0.001" name="start_inv"
                            class="form-control @error('start_inv') is-invalid @enderror"
                            value="{{ old('start_inv', $setting->start_inv) }}" required>
                        @error('start_inv')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hao hụt Nhập (%)</label>
                        <input type="number" step="0.0001" name="import_loss_rate"
                            class="form-control @error('import_loss_rate') is-invalid @enderror"
                            value="{{ old('import_loss_rate', $setting->import_loss_rate) }}" required>
                        @error('import_loss_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hao hụt Xuất (%)</label>
                        <input type="number" step="0.0001" name="export_loss_rate"
                            class="form-control @error('export_loss_rate') is-invalid @enderror"
                            value="{{ old('export_loss_rate', $setting->export_loss_rate) }}" required>
                        @error('export_loss_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    {{ $setting->exists ? 'Cập nhật' : 'Tạo mới' }}
                </button>
                <a href="{{ route('fuel-settings.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
@endsection
