@extends('layouts.app')

@section('title', 'Detail Dokumen Kontrak')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-contract me-2"></i>Detail Dokumen Kontrak</span>
                        <div>
                            <a href="{{ route('dokumen-kontrak.edit', $dokumenKontrak->Id) }}"
                                class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('dokumen-kontrak.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card border-secondary h-100">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Dokumen</h5>
                                    </div>
                                    <div class="card-body">

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Karyawan</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <div class="form-control">
                                                        {{ $dokumenKontrak->karyawan->NamaKry ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tanggal Masuk field -->
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Tanggal Masuk</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-calendar-check"></i></span>
                                                    <div class="form-control">
                                                        {{ $dokumenKontrak->karyawan->TglMsk ? $dokumenKontrak->karyawan->TglMsk->format('d/m/Y') : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Masa Kerja field -->
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Masa Kerja</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-business-time"></i></span>
                                                    <div class="form-control" id="masaKerjaDisplay">
                                                        <!-- This will be filled by JavaScript -->
                                                        <div class="spinner-border spinner-border-sm text-secondary"
                                                            role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Kategori Dokumen</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                                    <div class="form-control">
                                                        {{ $dokumenKontrak->kategoriDok->KategoriDok ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Jenis Dokumen</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                                    <div class="form-control">{{ $dokumenKontrak->JenisDok }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">No. Registrasi</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    <div class="form-control">{{ $dokumenKontrak->NoRegDok }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Keterangan</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                                    <div class="form-control min-height-100">
                                                        {{ $dokumenKontrak->KetDok ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Validity and Date Information -->
                            <div class="col-md-6">
                                <div class="card border-secondary h-100">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku & Tanggal
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Validasi Dokumen</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        @if ($dokumenKontrak->ValidasiDok === 'Tetap')
                                                            <i class="fas fa-infinity text-primary"></i>
                                                        @else
                                                            <i class="fas fa-sync text-success"></i>
                                                        @endif
                                                    </span>
                                                    <div class="form-control">{{ $dokumenKontrak->ValidasiDok }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Tanggal Terbit</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-calendar-plus"></i></span>
                                                    <div class="form-control">
                                                        {{ $dokumenKontrak->TglTerbitDok ? $dokumenKontrak->TglTerbitDok->format('d/m/Y') : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Tanggal Berakhir</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-calendar-times"></i></span>
                                                    <div class="form-control">
                                                        @if ($dokumenKontrak->TglBerakhirDok)
                                                            <span
                                                                class="{{ $dokumenKontrak->is_expired ? 'text-danger fw-bold' : '' }}">
                                                                {{ $dokumenKontrak->TglBerakhirDok->format('d/m/Y') }}
                                                                @if ($dokumenKontrak->is_expired)
                                                                    <i class="fas fa-exclamation-circle text-danger ms-1"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Sudah kedaluwarsa"></i>
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Masa Berlaku</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-hourglass-half"></i></span>
                                                    <div class="form-control">{{ $dokumenKontrak->MasaBerlaku }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Tanggal Pengingat</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-bell"></i></span>
                                                    <div class="form-control">
                                                        {{ $dokumenKontrak->TglPengingat ? $dokumenKontrak->TglPengingat->format('d/m/Y') : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Masa Pengingat</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                    <div class="form-control">{{ $dokumenKontrak->MasaPengingat }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Information -->
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary bg-opacity-25 text-white">
                                <h5 class="mb-0"><i class="fas fa-file me-2"></i>Dokumen & Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Status Dokumen</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        @if ($dokumenKontrak->StatusDok === 'Berlaku')
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @else
                                                            <i class="fas fa-times-circle text-danger"></i>
                                                        @endif
                                                    </span>
                                                    <div class="form-control">
                                                        @if ($dokumenKontrak->StatusDok === 'Berlaku')
                                                            <span class="badge bg-success">Berlaku</span>
                                                        @else
                                                            <span class="badge bg-danger">Tidak Berlaku</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">File Dokumen</label>
                                            <div class="info-value">
                                                @if ($dokumenKontrak->FileDok)
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('dokumen-kontrak.viewDocument', $dokumenKontrak->Id) }}"
                                                            class="btn btn-info" target="_blank">
                                                            <i class="fas fa-eye me-1"></i> Lihat
                                                        </a>
                                                    </div>
                                                    <div class="mt-2 small">
                                                        <i class="fas fa-file me-1"></i> {{ $dokumenKontrak->FileDok }}
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Tidak ada file
                                                        dokumen yang tersedia.
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

        .min-height-100 {
            min-height: 100px !important;
            align-items: flex-start !important;
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Calculate masa kerja automatically
            const tglMskText = document.querySelector('.info-group:nth-child(2) .form-control').textContent.trim();
            calculateMasaKerja(tglMskText);
        });

        // Function to calculate masa kerja from tanggal masuk
        // Function to calculate masa kerja from tanggal masuk
        function calculateMasaKerja(tglMskText) {
            const masaKerjaDisplay = document.getElementById('masaKerjaDisplay');

            // Check if date is available
            if (tglMskText === '-') {
                masaKerjaDisplay.textContent = '-';
                return;
            }

            // Parse the date in format dd/mm/yyyy
            const parts = tglMskText.split('/');
            if (parts.length !== 3) {
                masaKerjaDisplay.textContent = '-';
                return;
            }

            const joinDate = new Date(parts[2], parts[1] - 1, parts[0]); // Year, month (0-indexed), day
            const today = new Date();

            // Set time to midnight to avoid time-of-day issues
            today.setHours(0, 0, 0, 0);
            joinDate.setHours(0, 0, 0, 0);

            // Get years difference
            let years = today.getFullYear() - joinDate.getFullYear();

            // Create a date with the same month and day from the join date but in the current year
            const currentYearJoinDate = new Date(today.getFullYear(), joinDate.getMonth(), joinDate.getDate());

            // If the anniversary hasn't occurred yet this year, subtract a year
            // But only if years is greater than 0 to avoid negative years
            if (today < currentYearJoinDate && years > 0) {
                years--;
            }

            // Calculate months
            let months = today.getMonth() - joinDate.getMonth();
            if (months < 0) {
                months += 12;
            }

            // Adjust for day of month
            if (today.getDate() < joinDate.getDate()) {
                months--;
                if (months < 0) {
                    months += 12;
                    // Only decrement years if it would not make years negative
                    if (years > 0) {
                        years--;
                    }
                }
            }

            // Calculate remaining days
            let days = today.getDate() - joinDate.getDate();
            if (days < 0) {
                // Get the last day of the previous month
                const lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += lastMonth.getDate();
            }

            // Format: "X tahun Y bulan Z hari"
            masaKerjaDisplay.textContent = `${years} tahun ${months} bulan ${days} hari`;
        }
    </script>
@endpush
