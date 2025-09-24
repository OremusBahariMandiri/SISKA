@extends('layouts.app')

@section('title', 'Detail Wilayah Kerja')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Detail Wilayah Kerja</span>
                        <div>
                            <a href="{{ route('wilayah-kerja.edit', $wilayahKerja->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('wilayah-kerja.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card border-secondary mb-4">
                            <div class="card-header bg-secondary bg-opacity-25 text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Wilayah Kerja</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Golongan</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                            <div class="form-control">{{ $wilayahKerja->GolonganWilker }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Nama Wilayah Kerja</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <div class="form-control">{{ $wilayahKerja->WilayahKerja }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Singkatan</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                            <div class="form-control">{{ $wilayahKerja->SingkatanWilker }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-header {
            font-weight: 600;
        }

        .info-label {
            margin-bottom: 0.3rem;
            display: block;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-value .form-control {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            min-height: 38px;
        }

        .card {
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush