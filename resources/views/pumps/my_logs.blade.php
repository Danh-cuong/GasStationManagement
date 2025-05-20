@extends('layouts.app')

@section('content')
    <h1>Log của pump (ID: {{ $pumpId }})</h1>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form action="{{ route('pumps.my.logs.list') }}" method="GET" class="mb-3">
        @csrf
        <div class="form-group">
            <label for="fromTime">Từ thời gian:</label>
            <input type="text" id="fromTime" name="fromTime" class="form-control" value="{{ $fromTime ?? '' }}"
                placeholder="Y-m-d H:i:s">
        </div>
        <div class="form-group">
            <label for="toTime">Đến thời gian:</label>
            <input type="text" id="toTime" name="toTime" class="form-control" value="{{ $toTime ?? '' }}"
                placeholder="Y-m-d H:i:s">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Xem log</button>
    </form>

    @if (isset($transactions))
        <h3>Tổng: {{ $totals['totalTrans'] }} giao dịch, {{ $totals['totalMoney'] }} tiền, {{ $totals['totalMillis'] }}
            millis</h3>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thời gian</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction['id'] ?? '' }}</td>
                        <td>{{ $transaction['dateTimeCreated'] ?? '' }}</td>
                        <td><!-- Hiển thị chi tiết giao dịch --></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }}
    @endif
@endsection
