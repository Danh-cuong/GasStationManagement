@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Gán Pump & Nhân viên cho User: {{ $user->name }}</h3>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.users.assign', $user->id) }}">
            @csrf

            {{-- <div class="mb-3">
                <label for="pump_id" class="form-label">Chọn Pump:</label>
                <select name="pump_id" id="pump_id" class="form-control" required>
                    @foreach ($pumps as $pump)
                        <option value="{{ $pump->id }}">
                            {{ $pump->name }} (ID: {{ $pump->id }})
                        </option>
                    @endforeach
                </select>
            </div> --}}

            <div class="mb-3">
                <label for="employee_id" class="form-label">Chọn Nhân viên:</label>
                <select name="employee_id" id="employee_id" class="form-control" required>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->name }} (Client ID: {{ $employee->client_id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Gán cho User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
@endsection
