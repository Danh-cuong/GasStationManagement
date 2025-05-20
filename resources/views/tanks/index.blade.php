@extends('layouts.app')

@section('content')
    <style>
        .tanks-wrapper {
            white-space: nowrap;
        }

        .tank-card {
            flex: 0 0 auto;
            width: 320px;
            background: linear-gradient(135deg, #e6e5d8, #c5c4aa);
            border-radius: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tank-header {
            background: #fff;
            padding: 0.75rem;
            border-bottom: 1px solid #ccc;
            font-weight: 600;
        }

        .tank-body {
            padding: 1rem;
            display: flex;
            background: transparent;
        }

        .tank-graphic {
            position: relative;
            width: 100px;
            height: 140px;
            background: #ddd;
            border-radius: 0 1rem 1rem 0;
            overflow: hidden;
        }

        .tank-graphic .tank-level {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #0d6efd;
        }

        .tank-graphic .tank-perc {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-weight: bold;
        }

        .tank-info p {
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        .tank-info .text-danger {
            font-weight: 600;
        }
    </style>
    <div class="">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Thông tin bồn</h3>
            </div>
            <div class="card-body p-0" style="min-height: 95vh">
                <div class="tanks-wrapper d-flex flex-nowrap overflow-auto p-3 gap-3">
                    @forelse($tanks as $tank)
                        <div class="tank-card">
                            <div class="tank-header text-center">
                                {{ $tank->name }} ({{ $tank->description ?: '–' }})
                            </div>
                            <div class="tank-body d-flex">
                                <div class="tank-graphic flex-shrink-0">
                                    <div class="tank-level" style="height: {{ $tank->level }}%;"></div>
                                    <div class="tank-perc">{{ $tank->level }}%</div>
                                </div>
                                <div class="tank-info ps-3">
                                    <p><strong>Lượng NL:</strong> <span
                                            class="text-danger">{{ number_format($tank->volume) }} L</span></p>
                                    <p><strong>DT ngày:</strong><br>
                                        <span class="text-danger">{{ number_format($tank->dailyLitres ?? 0, 3, '.', ',') }}
                                            L</span><br>
                                        <span class="text-danger">{{ number_format($tank->dailyMoney ?? 0, 0, ',', '.') }}
                                            Đ</span>
                                    </p>
                                    <a href="{{ route('tanks.show', $tank->id) }}" class="btn btn-primary btn-sm mt-2">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-3">Không có dữ liệu bồn.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
