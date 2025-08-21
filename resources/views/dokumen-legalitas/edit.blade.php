@extends('layouts.app')

@section('title', 'Edit Dokumen Legalitas')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Dokumen Legalitas</span>
                        <a href="{{ route('dokumen-legalitas.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('dokumen-legalitas.update', $dokumenLegalitas->Id) }}" method="POST"
                            id="dokumenLegalitasForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Grouped form sections with cards -->
                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Dokumen</h5>
                                        </div>
                                        <div class="card-body">
                                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                                value="{{ $dokumenLegalitas->IdKode }}" hidden readonly>

                                            <div class="form-group mb-3">
                                                <label for="NoRegDok" class="form-label fw-bold">Nomor Registrasi <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('NoRegDok') is-invalid @enderror"
                                                        id="NoRegDok" name="NoRegDok"
                                                        value="{{ old('NoRegDok', $dokumenLegalitas->NoRegDok) }}"
                                                        placeholder="Masukkan nomor registrasi dokumen" required>
                                                    @error('NoRegDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="IdKodeA04" class="form-label fw-bold">Karyawan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>

                                                    <!-- Custom searchable dropdown for Karyawan -->
                                                    <div class="custom-select-container @error('IdKodeA04') is-invalid @enderror"
                                                        style="flex: 1 1 auto; width: 1%;" id="karyawanContainer">
                                                        <input type="text" id="karyawanSearch"
                                                            class="form-control custom-select-search"
                                                            placeholder="Cari karyawan..." autocomplete="off"
                                                            value="{{ $dokumenLegalitas->karyawan->NamaKry ?? '' }}">

                                                        <div class="custom-select-dropdown" id="karyawanDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="karyawanFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Karyawan --">--
                                                                    Pilih Karyawan --</div>
                                                                @foreach ($karyawan as $employee)
                                                                    <div class="custom-select-option {{ old('IdKodeA04', $dokumenLegalitas->IdKodeA04) == $employee->IdKode ? 'selected' : '' }}"
                                                                        data-value="{{ $employee->IdKode }}"
                                                                        data-display="{{ $employee->NamaKry }}">
                                                                        {{ $employee->NamaKry }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA04" id="IdKodeA04"
                                                            value="{{ old('IdKodeA04', $dokumenLegalitas->IdKodeA04) }}"
                                                            required>
                                                    </div>

                                                    @error('IdKodeA04')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="KategoriDok" class="form-label fw-bold">Kategori Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-folder"></i></span>

                                                    <!-- Custom searchable dropdown for Kategori -->
                                                    <div class="custom-select-container @error('KategoriDok') is-invalid @enderror"
                                                        style="flex: 1 1 auto; width: 1%;" id="kategoriContainer">
                                                        <input type="text" id="kategoriSearch"
                                                            class="form-control custom-select-search"
                                                            placeholder="Cari kategori..." autocomplete="off"
                                                            value="{{ $dokumenLegalitas->kategori->KategoriDok ?? '' }}">

                                                        <div class="custom-select-dropdown" id="kategoriDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="kategoriFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Kategori --">--
                                                                    Pilih Kategori --</div>
                                                                @foreach ($kategoriDokumen as $kategori)
                                                                    <div class="custom-select-option {{ old('KategoriDok', $dokumenLegalitas->KategoriDok) == $kategori->IdKode ? 'selected' : '' }}"
                                                                        data-value="{{ $kategori->IdKode }}"
                                                                        data-display="{{ $kategori->KategoriDok }}">
                                                                        {{ $kategori->KategoriDok }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="KategoriDok" id="KategoriDok"
                                                            value="{{ old('KategoriDok', $dokumenLegalitas->KategoriDok) }}"
                                                            required>
                                                    </div>

                                                    @error('KategoriDok')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="JenisDok" class="form-label fw-bold">Jenis Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-file-alt"></i></span>

                                                    <!-- Custom searchable dropdown for Jenis Dokumen -->
                                                    <div class="custom-select-container @error('JenisDok') is-invalid @enderror"
                                                        style="flex: 1 1 auto; width: 1%;" id="jenisContainer">
                                                        <input type="text" id="jenisSearch"
                                                            class="form-control custom-select-search"
                                                            placeholder="Cari jenis dokumen..." autocomplete="off"
                                                            value="{{ old('JenisDok', $dokumenLegalitas->JenisDok) }}">

                                                        <div class="custom-select-dropdown" id="jenisDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="jenisFilterInput"
                                                                    class="form-control"
                                                                    placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options" id="jenisOptions">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value=""
                                                                    data-display="-- Pilih Jenis Dokumen --">--
                                                                    Pilih Jenis Dokumen --</div>
                                                                <!-- Options will be populated dynamically based on selected category -->
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="JenisDok" id="JenisDok"
                                                            value="{{ old('JenisDok', $dokumenLegalitas->JenisDok) }}"
                                                            required>
                                                    </div>

                                                    @error('JenisDok')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="KetDok" class="form-label fw-bold">Keterangan
                                                    Dokumen</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-align-left"></i></span>
                                                    <textarea class="form-control @error('KetDok') is-invalid @enderror" id="KetDok" name="KetDok" rows="3"
                                                        placeholder="Masukkan keterangan dokumen">{{ old('KetDok', $dokumenLegalitas->KetDok) }}</textarea>
                                                    @error('KetDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Validity and Date Information -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku &
                                                Tanggal</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Validasi Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="bg-light p-2 rounded">
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input validasi-dokumen @error('ValidasiDok') is-invalid @enderror"
                                                            type="radio" name="ValidasiDok" id="tetap"
                                                            value="Tetap"
                                                            {{ old('ValidasiDok', $dokumenLegalitas->ValidasiDok) == 'Tetap' ? 'checked' : '' }}
                                                            required>
                                                        <label class="form-check-label" for="tetap">
                                                            <i class="fas fa-infinity text-primary me-1"></i>Tetap
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input validasi-dokumen @error('ValidasiDok') is-invalid @enderror"
                                                            type="radio" name="ValidasiDok" id="perpanjangan"
                                                            value="Perpanjangan"
                                                            {{ old('ValidasiDok', $dokumenLegalitas->ValidasiDok) == 'Perpanjangan' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perpanjangan">
                                                            <i class="fas fa-sync text-success me-1"></i>Perpanjangan
                                                        </label>
                                                    </div>
                                                    @error('ValidasiDok')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TglTerbitDok" class="form-label fw-bold">Tanggal
                                                            Terbit <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-plus"></i></span>
                                                            <input type="date"
                                                                class="form-control tanggal-input @error('TglTerbitDok') is-invalid @enderror"
                                                                id="TglTerbitDok" name="TglTerbitDok"
                                                                value="{{ old('TglTerbitDok', $dokumenLegalitas->TglTerbitDok ? $dokumenLegalitas->TglTerbitDok->format('Y-m-d') : '') }}"
                                                                required>
                                                            @error('TglTerbitDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3 tgl-berakhir-group">
                                                        <label for="TglBerakhirDok" class="form-label fw-bold">Tanggal
                                                            Berakhir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-times"></i></span>
                                                            <input type="date"
                                                                class="form-control tanggal-input @error('TglBerakhirDok') is-invalid @enderror"
                                                                id="TglBerakhirDok" name="TglBerakhirDok"
                                                                value="{{ old('TglBerakhirDok', $dokumenLegalitas->TglBerakhirDok ? $dokumenLegalitas->TglBerakhirDok->format('Y-m-d') : '') }}">
                                                            @error('TglBerakhirDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="MasaBerlaku" class="form-label fw-bold">Masa Berlaku</label>
                                                <input type="text" class="form-control bg-light" id="MasaBerlaku"
                                                    name="MasaBerlaku"
                                                    value="{{ old('MasaBerlaku', $dokumenLegalitas->MasaBerlaku) }}"
                                                    readonly>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Masa berlaku akan dihitung
                                                    otomatis
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 tgl-pengingat-group">
                                                <label for="TglPengingat" class="form-label fw-bold">Tanggal
                                                    Pengingat</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-bell"></i></span>
                                                    <input type="date"
                                                        class="form-control tanggal-input @error('TglPengingat') is-invalid @enderror"
                                                        id="TglPengingat" name="TglPengingat"
                                                        value="{{ old('TglPengingat', $dokumenLegalitas->TglPengingat ? $dokumenLegalitas->TglPengingat->format('Y-m-d') : '') }}">
                                                    @error('TglPengingat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="MasaPengingat" class="form-label fw-bold">Masa
                                                    Pengingat</label>
                                                <input type="text" class="form-control bg-light" id="MasaPengingat"
                                                    name="MasaPengingat"
                                                    value="{{ old('MasaPengingat', $dokumenLegalitas->MasaPengingat) }}"
                                                    readonly>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Masa pengingat akan dihitung
                                                    otomatis
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="card border-secondary mt-4">
                                <div class="card-header bg-secondary bg-opacity-25 text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="FileDok" class="form-label fw-bold">File Dokumen</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-file-upload"></i></span>
                                                    <input type="file"
                                                        class="form-control @error('FileDok') is-invalid @enderror"
                                                        id="FileDok" name="FileDok">
                                                    @error('FileDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Format: PDF, JPG, JPEG, PNG.
                                                </div>
                                                @if ($dokumenLegalitas->FileDok)
                                                    <div class="mt-2">
                                                        <p class="mb-1">File saat ini:</p>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-info me-2">
                                                                <i class="fas fa-file-alt"></i>
                                                            </span>
                                                            <span
                                                                class="text-truncate">{{ $dokumenLegalitas->FileDok }}</span>
                                                            <a href="{{ route('dokumen-legalitas.viewDocument', $dokumenLegalitas->Id) }}"
                                                                class="btn btn-sm btn-success ms-2" target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="StatusDok" class="form-label fw-bold">Status Dokumen</label>
                                                <div class="bg-light p-2 rounded">
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input @error('StatusDok') is-invalid @enderror"
                                                            type="radio" name="StatusDok" id="berlaku"
                                                            value="Berlaku"
                                                            {{ old('StatusDok', $dokumenLegalitas->StatusDok) == 'Berlaku' ? 'checked' : '' }}
                                                            required>
                                                        <label class="form-check-label" for="berlaku">
                                                            <i class="fas fa-check-circle text-success me-1"></i>Berlaku
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input @error('StatusDok') is-invalid @enderror"
                                                            type="radio" name="StatusDok" id="tidak_berlaku"
                                                            value="Tidak Berlaku"
                                                            {{ old('StatusDok', $dokumenLegalitas->StatusDok) == 'Tidak Berlaku' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tidak_berlaku">
                                                            <i class="fas fa-times-circle text-danger me-1"></i>Tidak
                                                            Berlaku
                                                        </label>
                                                    </div>
                                                    @error('StatusDok')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 col-md-4 mx-auto mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .text-danger {
            font-weight: bold;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .invalid-feedback {
            display: block;
        }

        /* Base Custom Select Styles */
        .custom-select-container {
            position: relative !important;
        }

        .custom-select-search {
            cursor: pointer;
            background-color: #fff;
        }

        /* Dropdown Portal - akan ditambahkan ke body */
        .dropdown-portal {
            position: fixed;
            z-index: 9999999;
            /* Sangat tinggi */
            display: none;
        }

        .dropdown-portal.active {
            display: block;
        }

        /* Dropdown Content */
        .custom-select-dropdown {
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .custom-select-search-wrapper {
            position: sticky;
            top: 0;
            background: white;
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .custom-select-options {
            max-height: 250px;
            overflow-y: auto;
        }

        .custom-select-option {
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .custom-select-option:hover {
            background-color: #f8f9fa;
        }

        .custom-select-option.selected {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .custom-select-option.empty-option {
            color: #6c757d;
            font-style: italic;
        }

        .custom-select-option.hidden {
            display: none;
        }

        /* Improved input styling when focused */
        .custom-select-container.open .custom-select-search {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Enhanced validation styles */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        /* Additional alert styles */
        .alert-danger {
            border-left: 4px solid #842029;
        }

        .alert-danger .alert-heading {
            color: #842029;
            margin-bottom: 0.5rem;
        }

        /* Animation for dropdown */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -10px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .dropdown-portal.active {
            animation: fadeInDown 0.2s ease-out forwards;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data jenisDokumenByKategori dari controller
            const jenisDokumenByKategori = @json($jenisDokumenByKategori);

            // Create portal elements for each dropdown
            function createPortals() {
                const selectContainers = document.querySelectorAll('.custom-select-container');

                selectContainers.forEach(container => {
                    const prefix = container.id.replace('Container', '');
                    const dropdown = document.getElementById(`${prefix}Dropdown`);

                    // Create portal if not exists
                    if (!document.getElementById(`${prefix}Portal`)) {
                        const portal = document.createElement('div');
                        portal.id = `${prefix}Portal`;
                        portal.className = 'dropdown-portal';

                        // Move dropdown to portal
                        if (dropdown) {
                            // Clone the dropdown to preserve event listeners
                            const clonedDropdown = dropdown.cloneNode(true);
                            portal.appendChild(clonedDropdown);
                            document.body.appendChild(portal);

                            // Remove original dropdown
                            dropdown.parentNode.removeChild(dropdown);
                        }
                    }
                });
            }

            // Initialize custom select with portal approach
            function initCustomSelect(prefix, hiddenInputId) {
                const searchInput = document.getElementById(`${prefix}Search`);
                const container = document.getElementById(`${prefix}Container`);
                const portal = document.getElementById(`${prefix}Portal`) || createPortalForSelect(prefix);
                const dropdown = portal.querySelector(`.custom-select-dropdown`);
                const filterInput = portal.querySelector(`#${prefix}FilterInput`);
                const options = portal.querySelectorAll('.custom-select-option');
                const hiddenInput = document.getElementById(hiddenInputId);

                // Position portal function
                function positionPortal() {
                    const rect = searchInput.getBoundingClientRect();
                    portal.style.top = `${rect.bottom}px`;
                    portal.style.left = `${rect.left}px`;
                    portal.style.width = `${rect.width}px`;
                }

                // Initialize selected value if exists
                const initialValue = hiddenInput.value;
                if (initialValue) {
                    const selectedOption = Array.from(options).find(option => option.dataset.value ===
                        initialValue);
                    if (selectedOption) {
                        searchInput.value = selectedOption.dataset.display;
                        selectedOption.classList.add('selected');
                    }
                }

                // Show dropdown when clicking on search input
                searchInput.addEventListener('click', function(e) {
                    e.stopPropagation();

                    // Close all other portals
                    document.querySelectorAll('.dropdown-portal.active').forEach(p => {
                        if (p.id !== `${prefix}Portal`) {
                            p.classList.remove('active');
                        }
                    });

                    // Toggle this portal
                    const isOpen = portal.classList.contains('active');

                    if (!isOpen) {
                        positionPortal();
                        portal.classList.add('active');
                        container.classList.add('open');

                        // Reset filter and focus
                        if (filterInput) {
                            filterInput.value = '';
                            setTimeout(() => filterInput.focus(), 50);
                        }

                        // Reset options visibility
                        options.forEach(option => option.classList.remove('hidden'));
                    } else {
                        portal.classList.remove('active');
                        container.classList.remove('open');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest(`#${prefix}Container`) &&
                        !e.target.closest(`#${prefix}Portal`)) {
                        portal.classList.remove('active');
                        container.classList.remove('open');
                    }
                });

                // Filter options as user types
                if (filterInput) {
                    filterInput.addEventListener('input', function() {
                        const filter = this.value.toLowerCase();
                        options.forEach(option => {
                            const text = option.textContent.toLowerCase();
                            if (text.includes(filter) || option.classList.contains(
                                    'empty-option')) {
                                option.classList.remove('hidden');
                            } else {
                                option.classList.add('hidden');
                            }
                        });
                    });
                }

                // Handle option selection
                options.forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.stopPropagation();

                        const value = this.dataset.value;
                        const display = this.dataset.display;

                        // Update hidden input and display
                        hiddenInput.value = value;
                        searchInput.value = display;

                        // Mark as selected
                        options.forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');

                        // Close dropdown
                        portal.classList.remove('active');
                        container.classList.remove('open');

                        // Remove invalid state if set
                        searchInput.classList.remove('is-invalid');
                        container.classList.remove('is-invalid');

                        // Trigger change event
                        const event = new Event('change', {
                            bubbles: true
                        });
                        hiddenInput.dispatchEvent(event);

                        // Specific actions for certain fields
                        if (prefix === 'kategori') {
                            updateJenisDokumen(value);
                        }
                    });
                });

                // Create portal if doesn't exist
                function createPortalForSelect(prefix) {
                    const origDropdown = document.getElementById(`${prefix}Dropdown`);

                    if (!origDropdown) return null;

                    const portal = document.createElement('div');
                    portal.id = `${prefix}Portal`;
                    portal.className = 'dropdown-portal';

                    // Clone dropdown to preserve structure
                    const clonedDropdown = origDropdown.cloneNode(true);
                    portal.appendChild(clonedDropdown);
                    document.body.appendChild(portal);

                    // Remove original
                    origDropdown.parentNode.removeChild(origDropdown);

                    return portal;
                }

                // Update position on scroll and resize
                window.addEventListener('resize', function() {
                    if (portal.classList.contains('active')) {
                        positionPortal();
                    }
                });

                window.addEventListener('scroll', function() {
                    if (portal.classList.contains('active')) {
                        positionPortal();
                    }
                });

                // Keyboard navigation
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.click();
                    } else if (e.key === 'Escape' && portal.classList.contains('active')) {
                        e.preventDefault();
                        portal.classList.remove('active');
                        container.classList.remove('open');
                    }
                });

                if (filterInput) {
                    filterInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            e.preventDefault();
                            portal.classList.remove('active');
                            container.classList.remove('open');
                        }
                    });
                }
            }

            // Function to update jenis dokumen options based on selected kategori
            function updateJenisDokumen(kategoriId) {
                const jenisPortal = document.getElementById('jenisPortal');
                if (!jenisPortal) return;

                const jenisOptions = jenisPortal.querySelector('#jenisOptions');
                const jenisSearch = document.getElementById('jenisSearch');
                const jenisHiddenInput = document.getElementById('JenisDok');

                // Keep the empty option
                const emptyOption = jenisOptions.querySelector('.empty-option');
                jenisOptions.innerHTML = '';
                jenisOptions.appendChild(emptyOption);

                // Save the current value for restoration later
                const currentValue = jenisHiddenInput.value;
                const currentDisplay = jenisSearch.value;

                // Reset jenis value if not keeping it
                if (!currentValue) {
                    jenisHiddenInput.value = '';
                    jenisSearch.value = '';
                }

                if (!kategoriId) return;

                // Get dokumen list for selected kategori
                const dokumenList = jenisDokumenByKategori[kategoriId] || [];

                // Add options - jenis dokumen already sorted by GolDok from controller
                dokumenList.forEach(dokumen => {
                    const option = document.createElement('div');
                    option.className = 'custom-select-option';
                    option.dataset.value = dokumen.JenisDok;
                    option.dataset.display = dokumen.JenisDok;
                    option.dataset.goldok = dokumen.GolDok || 999; // Add GolDok as data attribute
                    option.textContent = dokumen.JenisDok;

                    // Check if this option matches the current value
                    if (dokumen.JenisDok === currentValue) {
                        option.classList.add('selected');
                        jenisHiddenInput.value = currentValue;
                        jenisSearch.value = currentDisplay;
                    }

                    option.addEventListener('click', function(e) {
                        e.stopPropagation();

                        jenisHiddenInput.value = this.dataset.value;
                        jenisSearch.value = this.dataset.display;

                        const allOptions = jenisOptions.querySelectorAll('.custom-select-option');
                        allOptions.forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');

                        jenisPortal.classList.remove('active');
                        document.getElementById('jenisContainer').classList.remove('open');

                        jenisSearch.classList.remove('is-invalid');
                        document.getElementById('jenisContainer').classList.remove('is-invalid');

                        const event = new Event('change', {
                            bubbles: true
                        });
                        jenisHiddenInput.dispatchEvent(event);
                    });

                    jenisOptions.appendChild(option);
                });

                // Check if the current JenisDok exists in the new options
                let found = false;
                if (currentValue) {
                    dokumenList.forEach(dokumen => {
                        if (dokumen.JenisDok === currentValue) {
                            found = true;
                        }
                    });
                }

                // If not found in the new options, clear the inputs
                if (!found && currentValue) {
                    jenisHiddenInput.value = '';
                    jenisSearch.value = '';
                }
            }

            // Function to calculate and display validity period
            function hitungMasaBerlaku() {
                const validasiDok = document.querySelector('input[name="ValidasiDok"]:checked').value;
                const tglTerbit = document.getElementById('TglTerbitDok').value;
                const tglBerakhir = document.getElementById('TglBerakhirDok').value;
                const masaBerlakuField = document.getElementById('MasaBerlaku');

                if (validasiDok === 'Tetap') {
                    masaBerlakuField.value = 'Tetap';
                    return;
                }

                if (tglTerbit && tglBerakhir) {
                    const startDate = new Date(tglTerbit);
                    const endDate = new Date(tglBerakhir);

                    if (endDate < startDate) {
                        masaBerlakuField.value = 'Tanggal berakhir harus setelah tanggal terbit';
                        return;
                    }

                    // Calculate difference in years, months, and days
                    let years = endDate.getFullYear() - startDate.getFullYear();
                    let months = endDate.getMonth() - startDate.getMonth();
                    let days = endDate.getDate() - startDate.getDate();

                    if (days < 0) {
                        months--;
                        // Add days from previous month
                        const lastDayOfMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0).getDate();
                        days += lastDayOfMonth;
                    }

                    if (months < 0) {
                        years--;
                        months += 12;
                    }

                    let result = '';
                    if (years > 0) result += years + ' thn ';
                    if (months > 0) result += months + ' bln ';
                    if (days > 0) result += days + ' hri';

                    masaBerlakuField.value = result || '0 hri';
                } else {
                    masaBerlakuField.value = '';
                }
            }

            // Function to calculate and display reminder period
            function hitungMasaPengingat() {
                const tglPengingat = document.getElementById('TglPengingat').value;
                const tglBerakhir = document.getElementById('TglBerakhirDok').value;
                const masaPengingatField = document.getElementById('MasaPengingat');

                if (tglPengingat && tglBerakhir) {
                    const reminderDate = new Date(tglPengingat);
                    const expiryDate = new Date(tglBerakhir);

                    if (reminderDate > expiryDate) {
                        masaPengingatField.value = 'Tanggal pengingat harus sebelum tanggal berakhir';
                        return;
                    }

                    // Calculate difference in days
                    const diffTime = Math.abs(expiryDate - reminderDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    masaPengingatField.value = diffDays + ' hari';
                } else {
                    masaPengingatField.value = '-';
                }
            }

            // Function to toggle visibility of fields based on validity type
            function toggleFieldsVisibility() {
                const validasiDok = document.querySelector('input[name="ValidasiDok"]:checked').value;
                const tglBerakhirGroup = document.querySelector('.tgl-berakhir-group');
                const tglPengingatGroup = document.querySelector('.tgl-pengingat-group');
                const tglBerakhirInput = document.getElementById('TglBerakhirDok');
                const tglBerakhirLabel = tglBerakhirGroup.querySelector('.form-label');
                const tglPengingatInput = document.getElementById('TglPengingat');

                if (validasiDok === 'Tetap') {
                    tglBerakhirGroup.style.display = 'none';
                    tglPengingatGroup.style.display = 'none';
                    document.getElementById('TglBerakhirDok').value = '';
                    document.getElementById('TglPengingat').value = '';
                    document.getElementById('MasaBerlaku').value = 'Tetap';
                    document.getElementById('MasaPengingat').value = '-';

                    // Remove required attribute
                    tglBerakhirInput.removeAttribute('required');
                    tglPengingatInput.removeAttribute('required');
                } else {
                    tglBerakhirGroup.style.display = 'block';
                    tglPengingatGroup.style.display = 'block';

                    // Add required marker to label
                    if (!tglBerakhirLabel.innerHTML.includes('*')) {
                        tglBerakhirLabel.innerHTML = tglBerakhirLabel.innerHTML +
                            ' <span class="text-danger">*</span>';
                    }

                    // Add required attribute
                    tglBerakhirInput.setAttribute('required', 'required');

                    hitungMasaBerlaku();
                    hitungMasaPengingat();
                }
            }

            // Function to highlight missing fields
            function highlightMissingFields() {
                document.querySelectorAll('[required]').forEach(function(input) {
                    if (!input.value) {
                        input.classList.add('is-invalid');

                        // Handle custom select validation
                        if (input.type === 'hidden' && input.id.includes('IdKode') || input.id.includes(
                                'Dok')) {
                            const containerId = input.id.replace('IdKode', '').replace('Dok', '') +
                                'Container';
                            const container = document.getElementById(containerId);

                            if (container) {
                                container.classList.add('is-invalid');
                                const searchInput = container.querySelector('.custom-select-search');
                                if (searchInput) {
                                    searchInput.classList.add('is-invalid');
                                }
                            }
                        }

                        // Create error message if it doesn't exist
                        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains(
                                'invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Field ini wajib diisi';
                            input.parentNode.insertBefore(feedback, input.nextElementSibling);
                        }
                    }
                });

                // Animate invalid fields
                document.querySelectorAll('.is-invalid').forEach(element => {
                    element.classList.add('animate__animated', 'animate__shakeX');
                    setTimeout(() => {
                        element.classList.remove('animate__shakeX');
                    }, 1000);
                });
            }

            // Add event listeners for validity type
            document.querySelectorAll('.validasi-dokumen').forEach(function(radio) {
                radio.addEventListener('change', toggleFieldsVisibility);
            });

            // Add event listeners for dates
            document.querySelectorAll('.tanggal-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    if (this.id === 'TglTerbitDok' || this.id === 'TglBerakhirDok') {
                        hitungMasaBerlaku();
                    }
                    if (this.id === 'TglPengingat' || this.id === 'TglBerakhirDok') {
                        hitungMasaPengingat();
                    }
                });
            });

            // Form validation and submission
            const form = document.getElementById('dokumenLegalitasForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Close any open dropdowns
                    document.querySelectorAll('.dropdown-portal').forEach(portal => {
                        portal.classList.remove('active');
                    });
                    document.querySelectorAll('.custom-select-container').forEach(container => {
                        container.classList.remove('open');
                    });

                    // Highlight missing fields
                    highlightMissingFields();
                }
            });

            // Initialize
            createPortals();

            // Initialize each select
            initCustomSelect('karyawan', 'IdKodeA04');
            initCustomSelect('kategori', 'KategoriDok');
            initCustomSelect('jenis', 'JenisDok');

            // Initialize form state
            toggleFieldsVisibility();

            // Kategori change handler untuk update jenis dokumen
            document.getElementById('KategoriDok').addEventListener('change', function() {
                updateJenisDokumen(this.value);
            });

            // Initialize jenis dokumen if kategori is selected
            const oldKategori = document.getElementById('KategoriDok').value;
            if (oldKategori) {
                updateJenisDokumen(oldKategori);
            }

            // Initialize periods
            if (document.getElementById('TglTerbitDok').value && document.getElementById('TglBerakhirDok').value) {
                hitungMasaBerlaku();
            }

            if (document.getElementById('TglPengingat').value && document.getElementById('TglBerakhirDok').value) {
                hitungMasaPengingat();
            }

            // Add global escape key handler
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.dropdown-portal').forEach(portal => {
                        portal.classList.remove('active');
                    });
                    document.querySelectorAll('.custom-select-container').forEach(container => {
                        container.classList.remove('open');
                    });
                }
            });
        });
    </script>
@endpush
