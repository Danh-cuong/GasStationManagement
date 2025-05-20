@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-3">Nhập hàng<br><small>{{ $from }} → {{ $to }}</small></h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('admin.stores.entries.download', ['from_date' => $from, 'to_date' => $to]) }}"
                        class="btn btn-danger">Tải PDF</a>
                    <a href="{{ route('admin.stores.index', ['from_date' => $from, 'to_date' => $to]) }}"
                        class="btn btn-secondary">Quay lại</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">STT</th>
                                <th rowspan="2">Cửa hàng</th>
                                <th colspan="2">Chứng từ</th>
                                <th rowspan="2">Loại nhiên liệu</th>
                                <th rowspan="2">ĐVT</th>
                                <th rowspan="2">SL</th>
                                <th rowspan="2">Giá</th>
                                <th rowspan="2">VAT %</th>
                                <th rowspan="2">Tiền hàng</th>
                            </tr>
                            <tr>
                                <th>Thời gian nhập</th>
                                <th>Mã chứng từ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $stt = 1; @endphp
                            @foreach ($stores as $s)
                                @foreach ($s['entries'] as $i => $e)
                                    <tr>
                                        @if ($i === 0)
                                            <td rowspan="{{ count($s['entries']) }}">{{ $stt }}</td>
                                            <td rowspan="{{ count($s['entries']) }}">{{ $s['store'] }}</td>
                                        @endif

                                        <td>{{ $e['time'] }}</td>
                                        <td>{{ $e['doc'] }}</td>
                                        <td>{{ $e['fuel'] }}</td>
                                        <td>{{ $e['unit'] }}</td>
                                        <td>{{ number_format($e['qty'], 3, ',', '.') }}</td>
                                        <td>{{ number_format($e['price'], 2, ',', '.') }}</td>
                                        <td>{{ number_format($e['vat'], 2, ',', '.') }}</td>
                                        <td>{{ number_format($e['total'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                {{-- Tổng từng cửa hàng --}}
                                <tr class="fw-bold">
                                    <td colspan="6">Tổng</td>
                                    <td>{{ number_format($s['sum_qty'], 3, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                    <td>{{ number_format($s['sum_tot'], 2, ',', '.') }}</td>
                                </tr>
                                @php $stt++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
