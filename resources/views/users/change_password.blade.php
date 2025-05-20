@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Đổi mật khẩu</h3>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('employee.password.update') }}">
            @csrf

            <div class="mb-3">
                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                <input type="password" id="current_password" name="current_password"
                    class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới</label>
                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    required>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
        </form>
    </div>
@endsection
