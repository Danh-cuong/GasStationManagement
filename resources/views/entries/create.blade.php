@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Nhập Hàng</h3>
        </div>
        <div class="card-body p-0 container pt-3" style="min-height: 95vh">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('entries.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Thời gian nhập</label>
                    <input type="datetime-local" name="entry_time"
                        class="form-control @error('entry_time') is-invalid @enderror" value="{{ old('entry_time') }}"
                        required>
                    @error('entry_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Loại nhiên liệu</label>
                    <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror" required>
                        <option value="">-- Chọn nhiên liệu --</option>
                        @foreach ($fuelTypes as $code => $label)
                            <option value="{{ $code }}" {{ old('fuel_type') == $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('fuel_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Đơn vị tính</label>
                    <select name="unit_type" class="form-select @error('unit_type') is-invalid @enderror" required>
                        <option value="">-- Chọn đơn vị --</option>
                        @foreach ($unitTypes as $code => $label)
                            <option value="{{ $code }}" {{ old('unit_type') == $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Giá</label>
                        <input type="number" step="0.01" name="price"
                            class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">VAT %</label>
                        <input type="number" step="0.01" name="vat_percentage"
                            class="form-control @error('vat_percentage') is-invalid @enderror"
                            value="{{ old('vat_percentage') }}" required>
                        @error('vat_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số lượng nhập</label>
                        <input type="number" step="0.001" name="quantity"
                            class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}"
                            required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mã chứng từ (nếu có)</label>
                    <input type="text" name="document_code"
                        class="form-control @error('document_code') is-invalid @enderror"
                        value="{{ old('document_code') }}">
                    @error('document_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-success">Lưu nhập hàng</button>
            </form>
        </div>
    </div>
@endsection
