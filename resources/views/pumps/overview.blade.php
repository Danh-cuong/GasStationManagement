@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Check Log Tổng Quan Theo Mã TĐH</h3>
        </div>
        <div class="card-body p-0" style="min-height: 95vh">
            <div class="container-fluid pt-5">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="GET" action="{{ route('overview.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="pump_id" class="form-label">Chọn Vòi bơm</label>
                        <select id="pump_id" name="pump_id" class="form-select" required>
                            <option value="">-- Chọn vòi bơm --</option>
                            @foreach ($pumps as $pump)
                                <option value="{{ $pump->id }}"
                                    {{ isset($selectedPump) && $selectedPump == $pump->id ? 'selected' : '' }}>
                                    Vòi {{ $pump->id }} – {{ $pump->fuelName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">Từ ngày</label>
                        <input type="date" id="from_date" name="from_date" class="form-control"
                            value="{{ old('from_date', $fromDate ?? now()->subDay()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label">Đến ngày</label>
                        <input type="date" id="to_date" name="to_date" class="form-control"
                            value="{{ old('to_date', $toDate ?? now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2 w-100">Tìm kiếm</button>
                    </div>
                </form>

                <div class="row g-3 mb-4">
                    @if (!empty($logs) && $logs->isNotEmpty())
                        <div class="col-md-3">
                            <a href="{{ route('overview.exportCsv', [
                                'pump_id' => $selectedPump,
                                'from_date' => $fromDate,
                                'to_date' => $toDate,
                            ]) }}"
                                class="btn btn-success w-100 {{ empty($logs) ? 'disabled' : '' }}"
                                @if (empty($logs)) aria-disabled="true" @endif>
                                Xuất Excel
                            </a>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <form method="POST" action="{{ route('overview.update') }}">
                            @csrf
                            <input type="hidden" name="pump_id" value="{{ $selectedPump ?? '' }}">
                            <button type="submit" class="btn btn-warning w-100">
                                Cập nhật dữ liệu tổng log
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Hiển thị các ô tổng --}}
                @if (!empty($logs) && $logs->isNotEmpty())
                    @php
                        $totalTransactions = $logs->count();
                        $totalMoney = $logs->sum('money');
                        $totalLit = $logs->sum('lit');
                        $totalMillis = $logs->sum('millis');
                    @endphp

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-info h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Số ngày</h6>
                                    <h2 class="card-text">{{ number_format($totalTransactions) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Tổng tiền</h6>
                                    <h2 class="card-text">{{ number_format($totalMoney, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Tổng lít</h6>
                                    <h2 class="card-text">{{ number_format($totalLit, 3, '.', ',') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-danger h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Tổng millis</h6>
                                    <h2 class="card-text">{{ number_format($totalMillis) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($logs) && $logs->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-white" style="background-color: #008fe5">Ngày</th>
                                    <th class="text-white" style="background-color: #008fe5">Lít</th>
                                    <th class="text-white" style="background-color: #008fe5">Tiền (VND)</th>
                                    <th class="text-white" style="background-color: #008fe5">Millis</th>
                                    <th class="text-white" style="background-color: #008fe5">Tổng Lít F3</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr class="text-center">
                                        <td>{{ $log->log_date }}</td>
                                        <td>{{ number_format($log->lit, 3, '.', ',') }}</td>
                                        <td>{{ number_format($log->money, 2, ',', '.') }}</td>
                                        <td>{{ number_format($log->millis) }}</td>
                                        <td>{{ number_format($log->total_f3, 3, '.', ',') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                @elseif(isset($selectedPump))
                    <div class="alert alert-info">Không có dữ liệu log trong khoảng thời gian đã chọn.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
