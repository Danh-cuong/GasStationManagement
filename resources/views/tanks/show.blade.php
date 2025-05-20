@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $tank->name }}</h4>
                        <a href="{{ route('tanks.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                    <div class="card-body">

                        <div class="row mb-4">
                            <div class="col-6">
                                <p class="mb-2"><i class="bi bi-upc-scan me-1"></i><strong>ID:</strong>
                                    {{ $tank->id }}</p>
                                <p class="mb-2"><i class="bi bi-card-text me-1"></i><strong>Mô tả:</strong>
                                    {{ $tank->description ?: '—' }}</p>
                                <p class="mb-2"><i class="bi bi-fuel-pump me-1"></i><strong>Fuel ID:</strong>
                                    {{ $tank->fuelId }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><i class="bi bi-box-seam me-1"></i><strong>Capacity:</strong>
                                    {{ number_format($tank->capacity) }} L</p>
                                <p class="mb-2"><i class="bi bi-droplet-half me-1"></i><strong>Level:</strong>
                                    {{ $tank->level }}%</p>
                                <p class="mb-2"><i class="bi bi-bar-chart-line me-1"></i><strong>Volume:</strong>
                                    {{ number_format($tank->volume) }} L</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Mức nhiên liệu</h6>
                            <div class="progress" style="height: 1.5rem;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $tank->level }}%;"
                                    aria-valuenow="{{ $tank->level }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $tank->level }}%
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <p class="mb-2"><i class="bi bi-water me-1"></i><strong>Water Level:</strong>
                                    {{ $tank->waterLevel }}%</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><i class="bi bi-water me-1"></i><strong>Water Volume:</strong>
                                    {{ number_format($tank->waterVolume) }} L</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="mb-2"><i class="bi bi-thermometer-half me-1"></i><strong>Temperature:</strong>
                                {{ $tank->temperature }}°C</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection
