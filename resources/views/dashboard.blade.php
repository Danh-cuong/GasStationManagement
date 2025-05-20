@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <h3>Dashboard</h3>
        </div>
        <div class="card-body p-0" style="min-height: 95vh">
            <div class="container py-5">

                @if (auth()->user()->hasRole('admin'))
                    <style>
                        .fuel-tabs {
                            background: #f0f0f5;
                            padding: .5rem;
                            border-radius: .75rem;
                            margin-bottom: 1rem;
                        }

                        .info-section {
                            background: #fafafb;
                            border-radius: .75rem;
                            padding: 1rem;
                        }

                        .medal {
                            font-size: 2.5rem;
                            display: block;
                        }

                        .medal.gold {
                            color: #ffc107;
                        }

                        .medal.silver {
                            color: #c0c0c0;
                        }

                        .medal.bronze {
                            color: #cd7f32;
                        }
                    </style>

                    <div class="container py-5">
                        <div class="row g-4">

                            {{-- TOP SẢN LƯỢNG (LÍT) --}}
                            <div class="col-md-6">
                                <div class="fuel-tabs">
                                    <ul class="nav nav-pills justify-content-center" id="litTab" role="tablist">
                                        @foreach ($fuelNames as $i => $fuelName)
                                            @php $code = Str::slug($fuelName,'_'); @endphp
                                            <li class="nav-item mx-1" role="presentation">
                                                <button class="nav-link @if ($i === 0) active @endif"
                                                    id="lit-tab-{{ $code }}" data-bs-toggle="pill"
                                                    data-bs-target="#lit-pane-{{ $code }}" type="button"
                                                    role="tab">{{ $fuelName }}</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    @foreach ($fuelNames as $i => $fuelName)
                                        @php
                                            $code = Str::slug($fuelName, '_');
                                            $list = $topsByFuel[$fuelName]->lit;
                                            $ordered = collect([
                                                $list->get(1) ?? null,
                                                $list->get(0) ?? null,
                                                $list->get(2) ?? null,
                                            ]);
                                        @endphp
                                        <div class="tab-pane fade @if ($i === 0) show active @endif"
                                            id="lit-pane-{{ $code }}" role="tabpanel">
                                            <div class="info-section">
                                                <h5 class="mb-4 text-center">
                                                    Top sản lượng (Lít) — {{ $fuelName }}
                                                </h5>
                                                <div class="row text-center align-items-end mb-3">
                                                    @foreach ($ordered as $pos => $r)
                                                        <div class="col-4">
                                                            @if ($r)
                                                                @php
                                                                    $cls =
                                                                        $pos === 1
                                                                            ? 'gold'
                                                                            : ($pos === 0
                                                                                ? 'silver'
                                                                                : 'bronze');
                                                                    $emoji =
                                                                        $pos === 1 ? '🥇' : ($pos === 0 ? '🥈' : '🥉');
                                                                @endphp
                                                                <span
                                                                    class="medal {{ $cls }}">{{ $emoji }}</span>
                                                                <div class="fs-4">
                                                                    {{ number_format($r->total, 3, '.', ',') }}
                                                                    L</div>
                                                                <small class="text-muted">{{ $r->name }}</small>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if ($list->count() > 3)
                                                    <hr>
                                                    @foreach ($list->slice(3) as $idx => $r)
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <div>#{{ $idx + 1 }} {{ $r->name }}</div>
                                                            <div>{{ number_format($r->total, 3, '.', ',') }} L</div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- TOP DOANH THU --}}
                            <div class="col-md-6">
                                <div class="fuel-tabs">
                                    <ul class="nav nav-pills justify-content-center" id="moneyTab" role="tablist">
                                        @foreach ($fuelNames as $i => $fuelName)
                                            @php $code = Str::slug($fuelName,'_'); @endphp
                                            <li class="nav-item mx-1" role="presentation">
                                                <button class="nav-link @if ($i === 0) active @endif"
                                                    id="money-tab-{{ $code }}" data-bs-toggle="pill"
                                                    data-bs-target="#money-pane-{{ $code }}" type="button"
                                                    role="tab">{{ $fuelName }}</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    @foreach ($fuelNames as $i => $fuelName)
                                        @php
                                            $code = Str::slug($fuelName, '_');
                                            $list = $topsByFuel[$fuelName]->money;
                                            $ordered = collect([
                                                $list->get(1) ?? null,
                                                $list->get(0) ?? null,
                                                $list->get(2) ?? null,
                                            ]);
                                        @endphp
                                        <div class="tab-pane fade @if ($i === 0) show active @endif"
                                            id="money-pane-{{ $code }}" role="tabpanel">
                                            <div class="info-section">
                                                <h5 class="mb-4 text-center">
                                                    Top doanh thu — {{ $fuelName }}
                                                </h5>
                                                <div class="row text-center align-items-end mb-3">
                                                    @foreach ($ordered as $pos => $r)
                                                        <div class="col-4">
                                                            @if ($r)
                                                                @php
                                                                    $cls =
                                                                        $pos === 1
                                                                            ? 'gold'
                                                                            : ($pos === 0
                                                                                ? 'silver'
                                                                                : 'bronze');
                                                                    $emoji =
                                                                        $pos === 1 ? '🥇' : ($pos === 0 ? '🥈' : '🥉');
                                                                @endphp
                                                                <span
                                                                    class="medal {{ $cls }}">{{ $emoji }}</span>
                                                                <div class="fs-4">
                                                                    {{ number_format($r->total, 0, ',', '.') }} ₫</div>
                                                                <small class="text-muted">{{ $r->name }}</small>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if ($list->count() > 3)
                                                    <hr>
                                                    @foreach ($list->slice(3) as $idx => $r)
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <div>#{{ $idx + 1 }} {{ $r->name }}</div>
                                                            <div>{{ number_format($r->total, 0, ',', '.') }} ₫</div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <hr>

                        {{-- DANH SÁCH CỬA HÀNG --}}
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên cửa hàng</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($CHs as $ch)
                                        <tr>
                                            <td>{{ $ch->name }}</td>
                                            <td class="{{ $ch->status ? 'text-success' : 'text-danger' }}">
                                                {{ $ch->status ? 'Đang hoạt động' : 'Dừng hoạt động' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <form method="GET" class="row g-3 align-items-end mb-5">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="from_date" class="form-control" value="{{ $from }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="to_date" class="form-control" value="{{ $to }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
                        </div>
                    </form>

                    <div class="row gy-4">
                        {{-- Tổng lít theo fuelName --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white">
                                    Tổng Sản Lượng theo loại nhiên liệu ({{ $from }} → {{ $to }})
                                </div>
                                <div class="card-body">
                                    <canvas id="litChart"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Tổng tiền theo fuelName --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    Tổng Tiền theo loại nhiên liệu ({{ $from }} → {{ $to }})
                                </div>
                                <div class="card-body">
                                    <canvas id="moneyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        const stats = @json($stats);

                        // Labels = fuelName
                        const labels = stats.map(s => s.name);
                        const dataLit = stats.map(s => s.total_lit);
                        const dataMoney = stats.map(s => s.total_money);

                        new Chart(document.getElementById('litChart'), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Tổng Sản Lượng',
                                    data: dataLit,
                                    backgroundColor: 'rgba(0,123,255,0.6)',
                                    borderColor: 'rgba(0,123,255,1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        new Chart(document.getElementById('moneyChart'), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Tổng Tiền (VND)',
                                    data: dataMoney,
                                    backgroundColor: 'rgba(40,167,69,0.6)',
                                    borderColor: 'rgba(40,167,69,1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: v => v.toLocaleString() + '₫'
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>
@endsection
