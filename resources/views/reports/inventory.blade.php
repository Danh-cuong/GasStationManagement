@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-4">Báo cáo Nhập - Xuất - Tồn</h3>
        </div>
        <div class="card-body container pt-3" style="min-height: 95vh">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Xem báo cáo</button>
                </div>
            </form>

            <div class="mb-4">
                <a href="{{ route('reports.inventory.pdf', ['from_date' => $from, 'to_date' => $to]) }}"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-printer me-1"></i> Xuất PDF
                </a>
            </div>

            @php
                $lowStock = collect($rows)->contains(fn($r) => $r['end_inv'] < 10000);
            @endphp

            {{-- chèn modal --}}
            <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-warning">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="lowStockModalLabel">Cảnh báo tồn kho</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            Tồn kho sắp hết, vui lòng nhập thêm hàng vào kho.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                STT</th>
                            <th rowspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                Hàng hóa</th>

                            <th rowspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                Tồn đầu kỳ<br>(LÍT)</th>
                            <th rowspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                Nhập trong kỳ<br>(LÍT)</th>
                            <th rowspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                Xuất trong kỳ<br>(LÍT)</th>

                            <th colspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                Hao hụt (LÍT)</th>

                            <th rowspan="2" class="text-white" style="vertical-align: middle; background-color: #008fe5">
                                Tồn cuối kỳ<br>(LÍT)</th>
                        </tr>
                        <tr>
                            <th class="text-white" style="vertical-align: middle; background-color: #008fe5">Nhập</th>
                            <th class="text-white" style="vertical-align: middle; background-color: #008fe5">Xuất</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $i => $r)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-start">{{ $r['label'] }}</td>
                                <td>{{ number_format($r['start_inv'], 2, '.', ',') }}</td>
                                <td>{{ number_format($r['imp_period'], 2, '.', ',') }}</td>
                                <td>{{ number_format($r['exp_period'], 2, '.', ',') }}</td>
                                <td>{{ number_format($r['loss_imp'], 2, '.', ',') }}</td>
                                <td>{{ number_format($r['loss_exp'], 2, '.', ',') }}</td>
                                <td>{{ number_format($r['end_inv'], 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">Tổng</th>
                            <th>{{ number_format(collect($rows)->sum('start_inv'), 2, '.', ',') }}</th>
                            <th>{{ number_format(collect($rows)->sum('imp_period'), 2, '.', ',') }}</th>
                            <th>{{ number_format(collect($rows)->sum('exp_period'), 2, '.', ',') }}</th>
                            <th>{{ number_format(collect($rows)->sum('loss_imp'), 2, '.', ',') }}</th>
                            <th>{{ number_format(collect($rows)->sum('loss_exp'), 2, '.', ',') }}</th>
                            <th>{{ number_format(collect($rows)->sum('end_inv'), 2, '.', ',') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($lowStock)
                var lowStockModal = new bootstrap.Modal(document.getElementById('lowStockModal'));
                lowStockModal.show();
            @endif
        });
    </script>
@endsection
