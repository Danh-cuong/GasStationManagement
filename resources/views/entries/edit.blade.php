@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Chỉnh sửa chứng từ</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('entries.update', $entry->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Mã chứng từ</label>
                    <input type="text" name="document_code"
                        class="form-control @error('document_code') is-invalid @enderror"
                        value="{{ old('document_code', $entry->document_code) }}">
                    @error('document_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('entries.stats.form') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
@endsection
