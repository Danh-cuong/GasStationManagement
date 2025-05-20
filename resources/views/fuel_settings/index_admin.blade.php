@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Quản lý thiết lập tồn kho & hao hụt</h3>
        </div>
        <div class="card-body container pt-3" style="min-height: 95vh">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.fuel_settings.updateAll') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">Cửa hàng</th>
                                <th rowspan="2">Loại nhiên liệu</th>
                                <th rowspan="2">Tồn đầu kỳ (L)</th>
                                <th colspan="2">Hao hụt</th>
                            </tr>
                            <tr>
                                <th>Nhập (%)</th>
                                <th>Xuất (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $emp)
                                @foreach ($fuelTypes as $code => $label)
                                    @php
                                        $key = $emp->id . '-' . $code;
                                        $s = $settings[$key] ?? null;
                                    @endphp
                                    <tr>
                                        @if ($loop->first)
                                            <td rowspan="{{ count($fuelTypes) }}">{{ $emp->name }}</td>
                                        @endif

                                        <td class="text-start">{{ $label }}</td>
                                        {{-- start_inv --}}
                                        <td>
                                            <input type="number"
                                                name="settings[{{ $emp->id }}][{{ $code }}][start_inv]"
                                                class="form-control form-control-sm"
                                                value="{{ old("settings.$emp->id.$code.start_inv", $s->start_inv ?? 50000) }}"
                                                step="0.01" min="0">
                                        </td>
                                        {{-- import_loss_rate --}}
                                        <td>
                                            <input type="number"
                                                name="settings[{{ $emp->id }}][{{ $code }}][import_loss_rate]"
                                                class="form-control form-control-sm"
                                                value="{{ old("settings.$emp->id.$code.import_loss_rate", $s->import_loss_rate ?? 0.0012) }}"
                                                step="0.0001" min="0">
                                        </td>
                                        {{-- export_loss_rate --}}
                                        <td>
                                            <input type="number"
                                                name="settings[{{ $emp->id }}][{{ $code }}][export_loss_rate]"
                                                class="form-control form-control-sm"
                                                value="{{ old("settings.$emp->id.$code.export_loss_rate", $s->export_loss_rate ?? 0.0006) }}"
                                                step="0.0001" min="0">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
