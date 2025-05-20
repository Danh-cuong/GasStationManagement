@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3>Quản lý CH</h3>
            </div>
            @if (!empty($missingPairs))
                <div class="modal fade" id="missingLogModal" tabindex="-1" aria-labelledby="missingLogModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title" id="missingLogModalLabel">
                                    ⚠️ Cảnh báo thiếu dữ liệu
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <p class="mb-2">Dữ liệu còn thiếu tính từ ngày 01/04/2025:</p>
                                    <ul>
                                        @foreach ($missingPairs as $pair)
                                            <li class="mb-2">
                                                <b>{{ $pair['employee_name'] }}</b> —
                                                Pump <b>{{ $pair['pump_name'] }}</b>
                                                (<b>{{ $pair['fuel_name'] }}</b>)
                                                <span class="badge bg-danger">Thiếu {{ $pair['missing_count'] }} ngày dữ
                                                    liệu.</span>
                                                <form method="POST" action="{{ route('pumps.batchUpdateOverview') }}"
                                                    class="d-inline ms-2">
                                                    @csrf
                                                    <input type="hidden" name="employee_id"
                                                        value="{{ $pair['employee_id'] }}">
                                                    <input type="hidden" name="pump_id" value="{{ $pair['pump_id'] }}">
                                                    <input type="hidden" name="fuel_type" value="{{ $pair['fuel_type'] }}">
                                                    @foreach ($pair['missing_dates'] as $d)
                                                        <input type="hidden" name="dates[]" value="{{ $d }}">
                                                    @endforeach
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Cập nhật {{ $pair['missing_count'] }} ngày còn thiếu
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var modal = new bootstrap.Modal(document.getElementById('missingLogModal'));
                        modal.show();
                    });
                </script>
            @endif

            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control"
                            required>
                    </div>
                    <div class="col-md-12 d-flex gap-2">
                        <button type="submit" formaction="{{ route('admin.stores.inventory.preview') }}"
                            class="btn btn-outline-info flex-fill">
                            Nhập - Xuất - Tồn
                        </button>
                        <button type="submit" formaction="{{ route('admin.stores.entries.preview') }}"
                            class="btn btn-outline-success flex-fill">
                            Nhập hàng
                        </button>
                        <button type="submit" formaction="{{ route('admin.stores.production.preview') }}"
                            class="btn btn-outline-warning flex-fill">
                            Tổng sản lượng
                        </button>
                        <button type="submit" formaction="{{ route('admin.stores.profit.preview') }}"
                            class="btn btn-outline-danger flex-fill">
                            Lợi nhuận
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
