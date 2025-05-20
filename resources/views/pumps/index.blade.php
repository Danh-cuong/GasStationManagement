@extends('layouts.app')

@section('content')
    <style>
        .pump-wrapper {
            width: 200px;
            border-radius: 6px;
            overflow: hidden;

            background-image: url('{{ asset('images/trubom2.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100% 100%;
        }

        .voibom-outside {
            width: 40px;
            height: auto;
            margin-top: 6.5rem;
            scale: 2
        }

        .pump-wrapper .overlay {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .pump-top {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #88b7f7;
            padding: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            border-radius: 6px;
        }

        .pump-top>span {
            width: 100%;
            text-align: center;
            margin: 0.2rem 0;
        }

        .pump-hose {
            color: #ffffff;
        }

        .pump-fuel-type {
            color: #ffffff;
        }

        .pump-handle {
            color: #dc3545;
        }

        .pump-screen {
            background: white;
            padding: 1rem;
            border: 5px solid #a0a0a0;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #333;
            text-align: center;
        }

        .pump-screen .pump-line {
            margin: 0.2rem 0;
            padding: 0.3rem 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .pump-bottom {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pump-bottom a.btn {
            font-size: 0.8rem;
        }
    </style>

    <div>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Thông tin trụ bơm</h3>
            </div>
            <div class="card-body p-0" style="min-height: 95vh">
                @if (empty($pumps))
                    <div class="alert alert-warning fw-bold">Không có dữ liệu trụ bơm.</div>
                @else
                    <div class="row g-4 mb-5 pt-5">
                        @foreach ($pumps as $pump)
                            @php
                                $log = $logsByPump[$pump->id] ?? null;
                                $millis = $log->millis ?? 0;
                                $price = $log->price ?? 0;
                                $money = $log->money ?? 0;
                                $lit = $price > 0 ? $money / $price : 0;
                            @endphp

                            <div class="col-12 col-md-6 col-lg-4 d-flex justify-content-center">
                                <div class="d-flex align-items-start">
                                    <div class="pump-wrapper position-relative">
                                        <div class="overlay">
                                            <div class="pump-top">
                                                <span class="pump-hose">Vòi {{ $pump->id }}</span>
                                                <span class="pump-fuel-type">{{ $pump->fuelName }}</span>
                                            </div>
                                            <div class="pump-screen mt-2">
                                                <p class="pump-line">
                                                    <strong>Millis:</strong>
                                                    {{ number_format($millis) }}
                                                </p>
                                                <p class="pump-line">
                                                    <strong>Giá:</strong>
                                                    {{ number_format($price, 0, ',', '.') }} VND
                                                </p>
                                                <p class="pump-line">
                                                    <strong>Lít:</strong>
                                                    {{ number_format($lit, 3, '.', ',') }}
                                                </p>
                                                <p class="pump-line">
                                                    <strong>Tiền:</strong>
                                                    {{ number_format($money, 0, ',', '.') }} VND
                                                </p>
                                            </div>
                                            <div class="pump-bottom">
                                                <a href="{{ route('pumps.logs.form', $pump->id) }}"
                                                    class="btn btn-danger btn-sm">
                                                    Xem log
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <img src="{{ asset('images/voibom.png') }}" alt="Vòi bơm" class="voibom-outside ms-2">
                                </div>
                            </div>
                        @endforeach

                    </div>
                    <br><br>
                @endif
            </div>
        </div>
    </div>

    {{-- <div class="bg-light">
        <div class="row">
            <h3 class="mb-4 text-center pt-3">Danh sách trụ bơm</h3>
        </div>

        @if (empty($pumps))
            <div class="alert alert-warning fw-bold">Không có dữ liệu trụ bơm.</div>
        @else
            <div class="row g-4 mb-5">
                @foreach ($pumps as $pump)
                    <div class="col-12 col-md-6 col-lg-4 d-flex justify-content-center">
                        <div class="d-flex align-items-start">
                            <div class="pump-wrapper position-relative">
                                <div class="overlay">
                                    <div class="pump-top">
                                        <span class="pump-hose">Vòi {{ $pump->id }}</span>
                                        <span class="pump-fuel-type">{{ $pump->fuelName }}</span>
                                    </div>
                                    <div class="pump-screen mt-2">
                                        <p class="pump-line"><strong>Tiền:</strong>
                                            {{ number_format($pump->money ?? 0, 0, ',', '.') }} VND</p>
                                        <p class="pump-line"><strong>Lít:</strong>
                                            {{ number_format($pump->milliLitres ?? 0, 3, '.', ',') }}</p>
                                        <p class="pump-line"><strong>Giá:</strong>
                                            {{ number_format($pump->price ?? 0, 0, ',', '.') }} VND</p>
                                        <p class="pump-line"><strong>Tổng:</strong>
                                            {{ number_format($pump->total ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="pump-bottom">
                                        <a href="{{ route('pumps.logs.form', $pump->id) }}" class="btn btn-danger btn-sm">
                                            Xem log
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <img src="{{ asset('images/voibom.png') }}" alt="Vòi bơm" class="voibom-outside ms-2">
                        </div>
                    </div>
                @endforeach
            </div>
            <br><br>
        @endif
    </div> --}}
@endsection
