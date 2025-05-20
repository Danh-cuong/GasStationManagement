@extends('layouts.app')

@section('content')

    <div class="">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Log giao dịch trụ bơm #{{ $pumpId }}</h3>
            </div>
            <div class="card-body p-0" style="min-height: 95vh">
                <div class="container-fluid pt-3">
                    <form method="GET" action="{{ route('pumps.logs.list', $pumpId) }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="fromTime" class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" id="fromTime" name="fromTime"
                                class="form-control @error('fromTime') is-invalid @enderror"
                                value="{{ old('fromTime', \Carbon\Carbon::parse($fromTime ?? now())->format('Y-m-d\TH:i')) }}"
                                required>
                            @error('fromTime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="toTime" class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" id="toTime" name="toTime"
                                class="form-control @error('toTime') is-invalid @enderror"
                                value="{{ old('toTime', \Carbon\Carbon::parse($toTime ?? now())->format('Y-m-d\TH:i')) }}"
                                required>
                            @error('toTime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Xem log</button>
                        </div>
                    </form>


                    @isset($transactions)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-white bg-info">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Tổng giao dịch</h6>
                                        <p class="display-6 mb-0">{{ $totals['totalTrans'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-success">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Tổng tiền</h6>
                                        <p class="display-6 mb-0">{{ number_format($totals['totalMoney']) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-warning">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Tổng thời gian bơm (millis)</h6>
                                        <p class="display-6 mb-0">{{ number_format($totals['totalMillis']) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('pumps.logs.exportCsv', $pumpId) }}?fromTime={{ $fromTime }}&toTime={{ $toTime }}"
                                    class="btn btn-success w-100">
                                    Xuất CSV
                                </a>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('pumps.logs.exportPdf', $pumpId) }}?fromTime={{ $fromTime }}&toTime={{ $toTime }}"
                                    class="btn btn-danger w-100">
                                    Xuất PDF
                                </a>
                            </div>
                        </div>

                        @if (empty($transactions))
                            <div class="alert alert-info">Không có giao dịch trong khoảng thời gian này.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            {{-- <th>#</th> --}}
                                            <th>Thời gian tạo</th>
                                            <th>ID giao dịch</th>
                                            <th>Giá</th>
                                            <th>Lít</th>
                                            <th>Tổng tiền</th>
                                            <th>Thời gian bơm</th>
                                            <th>Số giao dịch</th>
                                            <th>Số tổng</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $tx)
                                            <tr>
                                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                                <td>{{ $tx->dateTimeCreated }}</td>
                                                <td>{{ $tx->id }}</td>
                                                <td>{{ number_format($tx->price) }}</td>
                                                <td>{{ number_format($tx->money / $tx->price) }}</td>
                                                <td>{{ number_format($tx->money) }}</td>
                                                <td>{{ number_format($tx->millis) }}</td>
                                                <td>{{ $tx->pickUpTimes }}</td>
                                                <td>{{ number_format($tx->totalF3) }}</td>
                                                <td class="text-center">
                                                    <form method="POST" action="{{ route('pumps.logs.rowPdf', $pumpId) }}">
                                                        @csrf
                                                        <input type="hidden" name="tx_data"
                                                            value="{{ base64_encode(json_encode($tx)) }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            PDF
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center">
                                    {{ $transactions->links('pagination::bootstrap-5') }}
                                </div>

                            </div>
                        @endif
                    @endisset
                </div>
            </div>
        </div>
    </div>


@endsection
