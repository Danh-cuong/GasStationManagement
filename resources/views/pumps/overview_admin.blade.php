@extends('layouts.app')

@section('content')
    <div class="">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Admin — Check Log Tổng Quan</h3>
            </div>
            <div class="card-body p-0" style="min-height: 95vh">
                <div class="container-fluid pt-5">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="GET" action="{{ route('admin.overview.index') }}" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Mã thiết bị TĐH</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Chọn mã thiết bị TĐH --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}"
                                        {{ isset($selectedEmployee) && $selectedEmployee == $e->id ? 'selected' : '' }}>
                                        {{ $e->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Chọn Vòi bơm</label>
                            <select name="pump_id" class="form-select" required>
                                <option value="">-- Chọn vòi bơm --</option>
                                @foreach ($pumps as $pump)
                                    <option value="{{ $pump->id }}"
                                        {{ isset($selectedPump) && $selectedPump == $pump->id ? 'selected' : '' }}>
                                        Vòi {{ $pump->id }} – {{ $pump->fuelName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="from_date" class="form-control"
                                value="{{ $fromDate ?? now()->subDay()->toDateString() }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="to_date" class="form-control"
                                value="{{ $toDate ?? now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100">Tìm kiếm</button>
                        </div>
                    </form>

                    <div class="row g-3 mb-4">
                        @if ($logs->isNotEmpty())
                            <div class="col-md-3">
                                <a href="{{ route('admin.overview.exportCsv', [
                                    'employee_id' => $selectedEmployee,
                                    'pump_id' => $selectedPump,
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]) }}"
                                    class="btn btn-success w-100 {{ empty($logs) ? 'disabled' : '' }}"
                                    @if (empty($logs)) aria-disabled="true" @endif>
                                    Xuất CSV
                                </a>
                            </div>
                        @endif
                        {{-- <div class="col-md-3">

                            <form method="POST" action="{{ route('admin.overview.update') }}" class="mb-4">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ $selectedEmployee ?? '' }}">
                                <input type="hidden" name="pump_id" value="{{ $selectedPump ?? '' }}">
                                <button class="btn btn-warning">
                                    Cập nhật tổng log
                                </button>
                            </form>
                        </div> --}}
                    </div>

                    @if ($logs->isNotEmpty())
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
                                        <tr>
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
                        <div class="alert alert-info">Không có dữ liệu log trong khoảng đã chọn.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const empSelect = document.querySelector('select[name="employee_id"]');
            const pumpSelect = document.querySelector('select[name="pump_id"]');

            empSelect.addEventListener('change', function() {
                const empId = this.value;
                pumpSelect.innerHTML = '<option>Đang tải...</option>';
                if (!empId) {
                    pumpSelect.innerHTML = '<option value="">-- Chọn vòi bơm --</option>';
                    return;
                }
                fetch(`{{ route('admin.overview.pumps') }}?employee_id=${empId}`)
                    .then(res => res.json())
                    .then(data => {
                        let html = '<option value="">-- Chọn vòi bơm --</option>';
                        data.forEach(p => {
                            html +=
                                `<option value="${p.id}">Vòi ${p.id} – ${p.fuelName}</option>`;
                        });
                        pumpSelect.innerHTML = html;
                    })
                    .catch(() => {
                        pumpSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                    });
            });

            // Nếu đã có selectedEmployee khi load trang, kích hoạt load lần đầu
            @if (isset($selectedEmployee))
                empSelect.dispatchEvent(new Event('change'));
            @endif
        });
    </script>
@endsection
