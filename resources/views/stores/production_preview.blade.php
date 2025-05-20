@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-3">Tổng sản lượng & Doanh thu<br><small>{{ $from }} → {{ $to }}</small>
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('admin.stores.production.download', ['from_date' => $from, 'to_date' => $to]) }}"
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
                                <th>Tổng Lít</th>
                                <th>Doanh thu (₫)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $r)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $r['store'] }}</td>
                                    <td>{{ number_format($r['lit'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($r['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
