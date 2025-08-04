@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-building me-2"></i>Detail Perusahaan</span>
                        <div>
                            <a href="{{ route('perusahaan.edit', $perusahaan->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('perusahaan.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Informasi Perusahaan -->
                            <div class="col-md-6">
                                <div class="card h-100 border-secondary">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Informasi Perusahaan</h5>
                                    </div>
                                    <div class="card-body">

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Nama Perusahaan</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                    <div class="form-control">{{ $perusahaan->NamaPrsh }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Alamat</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                    <div class="form-control text-break" style="min-height: 60px; white-space: pre-line;">
                                                        {{ $perusahaan->AlamatPrsh }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Telepon</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    <div class="form-control">{{ $perusahaan->TelpPrsh }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($perusahaan->TelpPrsh2)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Telepon 2</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                                        <div class="form-control">{{ $perusahaan->TelpPrsh2 }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Email</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <div class="form-control">{{ $perusahaan->EmailPrsh }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($perusahaan->EmailPrsh2)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Email 2</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                        <div class="form-control">{{ $perusahaan->EmailPrsh2 }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($perusahaan->WebPrsh)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Website</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                        <div class="form-control">
                                                            <a href="{{ $perusahaan->WebPrsh }}" target="_blank">{{ $perusahaan->WebPrsh }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Bisnis dan Manajemen -->
                            <div class="col-md-6">
                                <div class="card h-100 border-secondary">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Informasi Bisnis & Manajemen</h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($perusahaan->TglBerdiri)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Tanggal Berdiri</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                        <div class="form-control">{{ $perusahaan->TglBerdiri->format('d-m-Y') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Bidang Usaha</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                                    <div class="form-control">{{ $perusahaan->BidangUsh }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($perusahaan->IzinUsh)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Izin Usaha</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                                        <div class="form-control">{{ $perusahaan->IzinUsh }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($perusahaan->GolonganUsh)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Golongan Usaha</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                                        <div class="form-control">{{ $perusahaan->GolonganUsh }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Direktur Utama</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                    <div class="form-control">{{ $perusahaan->DirekturUtm }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($perusahaan->Direktur)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Direktur</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                        <div class="form-control">{{ $perusahaan->Direktur }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($perusahaan->KomisarisUtm)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Komisaris Utama</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                        <div class="form-control">{{ $perusahaan->KomisarisUtm }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($perusahaan->Komisaris)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Komisaris</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                        <div class="form-control">{{ $perusahaan->Komisaris }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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