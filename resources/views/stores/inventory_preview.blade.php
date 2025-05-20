@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-3">Nhập - Xuất -Tồn<br><small>{{ $from }} → {{ $to }}</small></h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('admin.stores.inventory.download', ['from_date' => $from, 'to_date' => $to]) }}"
                        class="btn btn-danger">Tải PDF</a>
                    <a href="{{ route('admin.stores.index', ['from_date' => $from, 'to_date' => $to]) }}"
                        class="btn btn-secondary">Quay lại</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Cửa hàng</th>
                                <th>Hàng hoá</th>
                                <th>Tồn đầu kỳ</th>
                                <th>Nhập kỳ</th>
                                <th>Xuất kỳ</th>
                                <th>Hao hụt nhập</th>
                                <th>Hao hụt xuất</th>
                                <th>Tồn cuối kỳ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $r)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $r['emp']->name }}</td>
                                    <td>{{ $r['fuel'] }}</td>
                                    <td>{{ number_format($r['si'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($r['imp'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($r['exp'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($r['li'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($r['le'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($r['ei'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td colspan="3">Tổng cộng</td>
                                <td>{{ number_format($totals['start_inv'], 2, ',', '.') }}</td>
                                <td>{{ number_format($totals['imp_period'], 2, ',', '.') }}</td>
                                <td>{{ number_format($totals['exp_period'], 2, ',', '.') }}</td>
                                <td>{{ number_format($totals['loss_imp'], 2, ',', '.') }}</td>
                                <td>{{ number_format($totals['loss_exp'], 2, ',', '.') }}</td>
                                <td>{{ number_format($totals['end_inv'], 2, ',', '.') }}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
