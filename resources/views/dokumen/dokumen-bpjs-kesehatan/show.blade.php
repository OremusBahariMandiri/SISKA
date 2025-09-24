@extends('layouts.app')

@section('title', 'Detail Dokumen BPJS Kesehatan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-medical me-2"></i>Detail Dokumen BPJS Kesehatan</span>
                        <div>
                            <a href="{{ route('dokumen-bpjs-kesehatan.edit', $dokumenBpjsKesehatan->id) }}" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('dokumen-bpjs-kesehatan.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Nav tabs for form sections -->
                        <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-karyawan-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-karyawan" type="button" role="tab"
                                    aria-controls="info-karyawan" aria-selected="true">
                                    <i class="fas fa-user me-1"></i> Informasi Karyawan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="iuran-bpjs-tab" data-bs-toggle="tab"
                                    data-bs-target="#iuran-bpjs" type="button" role="tab"
                                    aria-controls="iuran-bpjs" aria-selected="false">
                                    <i class="fas fa-money-bill-wave me-1"></i> Iuran BPJS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="info-tambahan-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-tambahan" type="button" role="tab"
                                    aria-controls="info-tambahan" aria-selected="false">
                                    <i class="fas fa-info-circle me-1"></i> Informasi Tambahan
                                </button>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Tab 1: Informasi Karyawan -->
                            <div class="tab-pane fade show active" id="info-karyawan" role="tabpanel"
                                aria-labelledby="info-karyawan-tab">

                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Dokumen</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="IdKodeA04" class="form-label fw-bold">Karyawan</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control bg-light" id="IdKodeA04" value="{{ $dokumenBpjsKesehatan->karyawan->NamaKry ?? '-' }} - {{ $dokumenBpjsKesehatan->karyawan->NrkKry ?? '-' }}" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="KategoriDok" class="form-label fw-bold">Kategori Dokumen</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                                <input type="text" class="form-control bg-light" id="KategoriDok" value="{{ $dokumenBpjsKesehatan->kategori->KategoriDok ?? '-' }}" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="JenisDok" class="form-label fw-bold">Jenis Dokumen</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                                <input type="text" class="form-control bg-light" id="JenisDok" value="{{ $dokumenBpjsKesehatan->JenisDok ?? '-' }}" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="NoRegDok" class="form-label fw-bold">Nomor Registrasi</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-control bg-light" id="NoRegDok" value="{{ $dokumenBpjsKesehatan->NoRegDok ?? '-' }}" disabled>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Iuran BPJS -->
                            <div class="tab-pane fade" id="iuran-bpjs" role="tabpanel"
                                aria-labelledby="iuran-bpjs-tab">

                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informasi Upah</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="UpahKtrKry" class="form-label fw-bold">Upah Kotor</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="UpahKtrKry" value="{{ number_format($dokumenBpjsKesehatan->UpahKtrKry, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="UpahBrshKry" class="form-label fw-bold">Upah Bersih</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="UpahBrshKry" value="{{ number_format($dokumenBpjsKesehatan->UpahBrshKry, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-info mb-3">
                                            <div class="card-header bg-info bg-opacity-25 text-dark">
                                                <h6 class="mb-0"><i class="fas fa-building me-2"></i>Iuran Perusahaan</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label for="IuranPrshPersen" class="form-label fw-bold">Persentase</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control bg-light" id="IuranPrshPersen" value="{{ $dokumenBpjsKesehatan->IuranPrshPersen }}" disabled>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="IuranPrshRp" class="form-label fw-bold">Nominal</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="IuranPrshRp" value="{{ number_format($dokumenBpjsKesehatan->IuranPrshRp, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning bg-opacity-25 text-dark">
                                                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Iuran Karyawan</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label for="IuranKryPersen" class="form-label fw-bold">Persentase</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control bg-light" id="IuranKryPersen" value="{{ $dokumenBpjsKesehatan->IuranKryPersen }}" disabled>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="IuranKryRp" class="form-label fw-bold">Nominal</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="IuranKryRp" value="{{ number_format($dokumenBpjsKesehatan->IuranKryRp, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Iuran Tambahan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="IuranKry1Rp" class="form-label fw-bold">Iuran Karyawan 1</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="IuranKry1Rp" value="{{ $dokumenBpjsKesehatan->IuranKry1Rp ? number_format($dokumenBpjsKesehatan->IuranKry1Rp, 0, ',', '.') : '-' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="IuranKry2Rp" class="form-label fw-bold">Iuran Karyawan 2</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="IuranKry2Rp" value="{{ $dokumenBpjsKesehatan->IuranKry2Rp ? number_format($dokumenBpjsKesehatan->IuranKry2Rp, 0, ',', '.') : '-' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="IuranKry3Rp" class="form-label fw-bold">Iuran Karyawan 3</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="IuranKry3Rp" value="{{ $dokumenBpjsKesehatan->IuranKry3Rp ? number_format($dokumenBpjsKesehatan->IuranKry3Rp, 0, ',', '.') : '-' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-success mb-4">
                                    <div class="card-header bg-success bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Total Iuran</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="JmlPrshRp" class="form-label fw-bold">Jumlah Iuran Perusahaan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="JmlPrshRp" value="{{ number_format($dokumenBpjsKesehatan->JmlPrshRp, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="JmlKryRp" class="form-label fw-bold">Jumlah Iuran Karyawan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light" id="JmlKryRp" value="{{ number_format($dokumenBpjsKesehatan->JmlKryRp, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="TotIuran" class="form-label fw-bold">Total Iuran</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light fw-bold text-success" id="TotIuran" value="{{ number_format($dokumenBpjsKesehatan->TotIuran, 0, ',', '.') }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Informasi Tambahan -->
                            <div class="tab-pane fade" id="info-tambahan" role="tabpanel"
                                aria-labelledby="info-tambahan-tab">

                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku & Tanggal</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="TglTerbitDok" class="form-label fw-bold">Tanggal Terbit</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                                                        <input type="text" class="form-control bg-light" id="TglTerbitDok" value="{{ $dokumenBpjsKesehatan->TglTerbitDok ? date('d-m-Y', strtotime($dokumenBpjsKesehatan->TglTerbitDok)) : '-' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="TglBerakhirDok" class="form-label fw-bold">Tanggal Berakhir</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                                                        <input type="text" class="form-control bg-light" id="TglBerakhirDok" value="{{ $dokumenBpjsKesehatan->TglBerakhirDok ? date('d-m-Y', strtotime($dokumenBpjsKesehatan->TglBerakhirDok)) : 'Tetap' }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="MasaBerlaku" class="form-label fw-bold">Masa Berlaku</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hourglass-half"></i></span>
                                                <input type="text" class="form-control bg-light" id="MasaBerlaku" value="{{ $dokumenBpjsKesehatan->MasaBerlaku ?? 'Tetap' }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>File Dokumen</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($dokumenBpjsKesehatan->FileDok)
                                            <div class="form-group">
                                                <label class="form-label fw-bold">File Dokumen</label>
                                                <div class="card bg-light p-3 border">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            @php
                                                                $fileExt = pathinfo($dokumenBpjsKesehatan->FileDok, PATHINFO_EXTENSION);
                                                                $iconClass = 'fas fa-file fa-2x text-secondary';

                                                                if ($fileExt === 'pdf') {
                                                                    $iconClass = 'fas fa-file-pdf fa-2x text-danger';
                                                                } elseif (in_array($fileExt, ['jpg', 'jpeg', 'png'])) {
                                                                    $iconClass = 'fas fa-file-image fa-2x text-primary';
                                                                }
                                                            @endphp
                                                            <i class="{{ $iconClass }}"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 text-truncate">{{ basename($dokumenBpjsKesehatan->FileDok) }}</h6>
                                                            <div class="btn-group">
                                                                <a href="{{ route('dokumen-bpjs-kesehatan.viewDocument', $dokumenBpjsKesehatan->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                                        <i class="fas fa-eye me-1"></i> Lihat
                                                                </a>
                                                                {{-- <a href="{{ route('dokumen-bpjs-kesehatan.download', $dokumenBpjsKesehatan->id) }}" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-download me-1"></i> Unduh
                                                                </a> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-1"></i> Tidak ada file dokumen yang tersedia.
                                            </div>
                                        @endif
                                        <div class="form-group mb-3">
                                            <label for="KetDok" class="form-label fw-bold">Keterangan Dokumen</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                                <textarea class="form-control bg-light" id="KetDok" rows="3" disabled>{{ $dokumenBpjsKesehatan->KetDok ?? '-' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Status Dokumen</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Status</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                <input type="text" class="form-control
                                                    @if($dokumenBpjsKesehatan->StatusDok == 'Berlaku')
                                                        bg-success bg-opacity-10 text-success fw-bold
                                                    @else
                                                        bg-danger bg-opacity-10 text-danger fw-bold
                                                    @endif"
                                                    value="{{ $dokumenBpjsKesehatan->StatusDok ?? '-' }}" disabled>
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

        .form-label {
            margin-bottom: 0.3rem;
        }

        .card {
            margin-bottom: 1rem;
            transition: all 0.3s;
            border-radius: 0.5rem;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            font-weight: 600;
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        /* Form field styling for disabled mode */
        .form-control:disabled,
        .form-control[readonly] {
            background-color: #f8f9fa;
            opacity: 1;
            cursor: default;
            border: 1px solid #dee2e6;
        }

        .form-control:disabled:focus,
        .form-control[readonly]:focus {
            box-shadow: none;
        }

        /* Nav tabs styling */
        .nav-tabs .nav-link {
            color: #6c757d;
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
            font-weight: bold;
        }

        .tab-content {
            padding-top: 1rem;
        }

        /* Tab content animation */
        .tab-pane.fade {
            transition: opacity 0.3s ease;
        }

        .tab-pane.show {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Special status color styling */
        .text-success {
            color: #198754 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* Input group styling */
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tabs
            const triggerTabList = [].slice.call(document.querySelectorAll('#formTabs button'));
            triggerTabList.forEach(function(triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);

                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });

            // Handle direct tab access from URL hash
            if (window.location.hash) {
                const tabId = window.location.hash.replace('#', '');
                const tab = document.querySelector(`#${tabId}-tab`);
                if (tab) {
                    const tabTrigger = new bootstrap.Tab(tab);
                    tabTrigger.show();
                }
            }

            // Update URL hash when tab changes
            triggerTabList.forEach(function(triggerEl) {
                triggerEl.addEventListener('shown.bs.tab', function(event) {
                    const tabId = event.target.getAttribute('aria-controls');
                    history.replaceState(null, null, `#${tabId}`);
                });
            });
        });
    </script>
@endpush