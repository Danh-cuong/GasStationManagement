@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <h3>Thống kê Nhập hàng</h3>
        </div>
        <div class="card-body p-0 container pt-3" style="min-height: 95vh">
            <form method="GET" action="{{ route('entries.stats') }}" class="row g-3 mb-5">
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control"
                        value="{{ $data['from_date'] ?? now()->subWeek()->toDateString() }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control"
                        value="{{ $data['to_date'] ?? now()->toDateString() }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại nhiên liệu</label>
                    <select name="fuel_type" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach ($fuelTypes as $code => $label)
                            <option value="{{ $code }}" {{ ($data['fuel_type'] ?? '') == $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái chứng từ</label>
                    <select name="has_document" class="form-select">
                        <option value="all">Tất cả</option>
                        <option value="with" {{ ($data['has_document'] ?? '') == 'with' ? 'selected' : '' }}>
                            Có
                        </option>
                        <option value="without"{{ ($data['has_document'] ?? '') == 'without' ? 'selected' : '' }}>
                            Không
                        </option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-primary">Xem kết quả</button>
                </div>
            </form>

            @isset($entries)
                <div class="alert alert-info">
                    Tổng số lượng nhập: <strong>{{ number_format($totalQty, 3, '.', ',') }}</strong>
                </div>

                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr class="table-light text-center">
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        STT</th>
                                    <th colspan="2" class="text-white" style="background-color: #008fe5">Chứng từ</th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        Loại nhiên liệu
                                    </th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        Đơn vị</th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        Số lượng</th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        Giá</th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        VAT %</th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">
                                        Tiền hàng</th>
                                    <th rowspan="2" class="text-white"
                                        style="vertical-align: middle; background-color: #008fe5">Hành động</th>
                                </tr>
                                <tr class="table-light text-center">
                                    <th class="text-white" style="vertical-align: middle; background-color: #008fe5">Thời gian
                                        nhập
                                    </th>
                                    <th class="text-white" style="vertical-align: middle; background-color: #008fe5">Mã chứng từ
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entries as $idx => $e)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td class="text-center">{{ $e->entry_time->format('Y-m-d H:i') }}</td>
                                        <td class="text-center">{{ $e->document_code ?? '-' }}</td>
                                        <td>{{ $fuelTypes[$e->fuel_type] }}</td>
                                        <td>{{ $unitTypes[$e->unit_type] }}</td>
                                        <td class="text-end">{{ number_format($e->quantity, 3, '.', ',') }}</td>
                                        <td class="text-end">{{ number_format($e->price, 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($e->vat_percentage, 2, '.', ',') }}</td>
                                        <td>{{ number_format($e->price * $e->quantity + (($e->price * $e->quantity) / 100) * $e->vat_percentage, 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('entries.edit', $e->id) }}" class="btn btn-sm btn-primary me-1">
                                                Sửa
                                            </a>

                                            <form action="{{ route('entries.destroy', $e->id) }}" method="POST"
                                                style="display: inline-block;"
                                                onsubmit="return confirm('Bạn có chắc muốn xoá bản ghi này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Xoá
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endisset
        </div>
    </div>
@endsection
