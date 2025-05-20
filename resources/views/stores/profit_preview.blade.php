@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-3">BÁO CÁO LỢI NHUẬN THEO CỬA HÀNG<br>
                    <small>{{ $from }} → {{ $to }}</small>
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('admin.stores.profit.download', ['from_date' => $from, 'to_date' => $to]) }}"
                        class="btn btn-danger">Tải PDF</a>
                    <a href="{{ route('admin.stores.index', ['from_date' => $from, 'to_date' => $to]) }}"
                        class="btn btn-secondary">Quay lại</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Cửa hàng</th>
                                <th>Hàng hoá</th>
                                <th>Nhập trong kỳ<br>Doanh thu</th>
                                <th>Xuất trong kỳ<br>Doanh thu</th>
                                <th>Lợi nhuận</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $stt = 1;
                                $groups = collect($rows)->groupBy('store');
                            @endphp

                            @foreach ($groups as $storeName => $group)
                                @foreach ($group as $i => $r)
                                    <tr>
                                        @if ($i === 0)
                                            <td rowspan="{{ count($group) }}">{{ $stt }}</td>
                                            <td rowspan="{{ count($group) }}">{{ $storeName }}</td>
                                        @endif

                                        <td class="text-start">{{ $r['fuel'] }}</td>
                                        <td class="text-end">{{ number_format($r['impRev'], 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($r['expRev'], 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($r['profit'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                @php $stt++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
