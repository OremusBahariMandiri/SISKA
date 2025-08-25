@extends('layouts.app')

@section('title', 'Edit Dokumen Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Dokumen Karyawan</span>
                        <a href="{{ route('dokumen-karyawan.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-1"></i> Ada kesalahan
                                    dalam pengisian form:</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-times-circle me-1"></i> {{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('dokumen-karyawan.update', $dokumenKaryawan->id) }}" method="POST"
                            id="dokumenKaryawanForm" enctype="multipart/form-data">
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
                                                value="{{ old('IdKode', $dokumenKaryawan->IdKode) }}" hidden readonly>

                                            <div class="form-group mb-3">
                                                <label for="NoRegDok" class="form-label fw-bold">Nomor Registrasi <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('NoRegDok') is-invalid @enderror"
                                                        id="NoRegDok" name="NoRegDok"
                                                        value="{{ old('NoRegDok', $dokumenKaryawan->NoRegDok) }}"
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
                                                            value="{{ old('karyawanDisplay', $dokumenKaryawan->karyawan ? $dokumenKaryawan->karyawan->NamaKry . ' - ' . ($dokumenKaryawan->karyawan->NrkKry ?? '') : '') }}">

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
                                                                    <div class="custom-select-option"
                                                                        data-value="{{ $employee->IdKode }}"
                                                                        data-display="{{ $employee->NamaKry }} - {{ $employee->NrkKry ?? '' }}"
                                                                        {{ old('IdKodeA04', $dokumenKaryawan->IdKodeA04) == $employee->IdKode ? 'selected' : '' }}>
                                                                        {{ $employee->NamaKry }} -
                                                                        {{ $employee->NrkKry ?? '' }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA04" id="IdKodeA04"
                                                            value="{{ old('IdKodeA04', $dokumenKaryawan->IdKodeA04) }}"
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
                                                            value="{{ old('kategoriDisplay', $dokumenKaryawan->KategoriDok) }}">

                                                        <div class="custom-select-dropdown" id="kategoriDropdown">

                                                                <input type="text" id="kategoriFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                    
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Kategori --">--
                                                                    Pilih Kategori --</div>
                                                                @foreach ($kategoriDokumen as $kategori)
                                                                    <div class="custom-select-option"
                                                                        data-value="{{ $kategori->IdKode }}"
                                                                        data-display="{{ $kategori->KategoriDok }}"
                                                                        {{ old('KategoriDok', $dokumenKaryawan->KategoriDok) == $kategori->KategoriDok ? 'selected' : '' }}>
                                                                        {{ $kategori->KategoriDok }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="KategoriDok" id="KategoriDok"
                                                            value="{{ old('KategoriDok', $dokumenKaryawan->KategoriDok) }}"
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
                                                            value="{{ old('jenisDisplay', $dokumenKaryawan->JenisDok) }}">

                                                        <div class="custom-select-dropdown" id="jenisDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="jenisFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options" id="jenisOptions">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Jenis Dokumen --">--
                                                                    Pilih Jenis Dokumen --</div>
                                                                <!-- Options will be populated dynamically based on selected category -->
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="JenisDok" id="JenisDok"
                                                            value="{{ old('JenisDok', $dokumenKaryawan->JenisDok) }}"
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
                                                        placeholder="Masukkan keterangan dokumen">{{ old('KetDok', $dokumenKaryawan->KetDok) }}</textarea>
                                                    @error('KetDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <input type="hidden" name="KetDokBackup" value="{{ old('KetDok', $dokumenKaryawan->KetDok) }}">
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
                                                            {{ old('ValidasiDok', $dokumenKaryawan->ValidasiDok) == 'Tetap' ? 'checked' : '' }}
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
                                                            {{ old('ValidasiDok', $dokumenKaryawan->ValidasiDok) == 'Perpanjangan' ? 'checked' : '' }}>
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
                                                                value="{{ old('TglTerbitDok', $dokumenKaryawan->TglTerbitDok ? $dokumenKaryawan->TglTerbitDok->format('Y-m-d') : '') }}"
                                                                required>
                                                            @error('TglTerbitDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3 tgl-berakhir-group"
                                                        style="{{ old('ValidasiDok', $dokumenKaryawan->ValidasiDok) == 'Tetap' ? 'display: none;' : '' }}">
                                                        <label for="TglBerakhirDok" class="form-label fw-bold">Tanggal
                                                            Berakhir
                                                            @if (old('ValidasiDok', $dokumenKaryawan->ValidasiDok) == 'Perpanjangan')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-times"></i></span>
                                                            <input type="date"
                                                                class="form-control tanggal-input @error('TglBerakhirDok') is-invalid @enderror"
                                                                id="TglBerakhirDok" name="TglBerakhirDok"
                                                                value="{{ old('TglBerakhirDok', $dokumenKaryawan->TglBerakhirDok ? $dokumenKaryawan->TglBerakhirDok->format('Y-m-d') : '') }}"
                                                                {{ old('ValidasiDok', $dokumenKaryawan->ValidasiDok) == 'Perpanjangan' ? 'required' : '' }}>
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
                                                    value="{{ old('MasaBerlaku', $dokumenKaryawan->MasaBerlaku) }}"
                                                    readonly>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Masa berlaku akan dihitung
                                                    otomatis
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 tgl-pengingat-group"
                                                style="{{ old('ValidasiDok', $dokumenKaryawan->ValidasiDok) == 'Tetap' ? 'display: none;' : '' }}">
                                                <label for="TglPengingat" class="form-label fw-bold">Tanggal
                                                    Pengingat</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-bell"></i></span>
                                                    <input type="date"
                                                        class="form-control tanggal-input @error('TglPengingat') is-invalid @enderror"
                                                        id="TglPengingat" name="TglPengingat"
                                                        value="{{ old('TglPengingat', $dokumenKaryawan->TglPengingat ? $dokumenKaryawan->TglPengingat->format('Y-m-d') : '') }}">
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
                                                    value="{{ old('MasaPengingat', $dokumenKaryawan->MasaPengingat) }}"
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
                                                        id="FileDok" name="FileDok" accept=".pdf,.jpg,.jpeg,.png">
                                                    @error('FileDok')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div id="fileHelp" class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Format: PDF, JPG, JPEG, PNG.
                                                    @if ($dokumenKaryawan->FileDok)
                                                        <br>
                                                        <span class="text-info">File saat ini:
                                                            {{ $dokumenKaryawan->FileDok }}</span>
                                                    @endif
                                                </div>
                                                <div id="fileValidationMessage" class="invalid-feedback d-none">
                                                    Silakan pilih file dengan format yang sesuai (PDF, JPG, JPEG, PNG)
                                                </div>
                                            </div>

                                            <!-- File preview container -->
                                            <div id="filePreviewContainer" class="mb-3 d-none">
                                                <div class="card border-secondary">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex align-items-center">
                                                            <div id="fileTypeIcon" class="me-2">
                                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 id="fileName" class="mb-0 text-truncate">document.pdf
                                                                </h6>
                                                                <small id="fileSize" class="text-muted">0 KB</small>
                                                            </div>
                                                            <button type="button" id="removeFile"
                                                                class="btn btn-sm btn-light border">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($dokumenKaryawan->FileDok)
                                                <div class="mb-3">
                                                    <div class="card border-primary">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-2">
                                                                    @php
                                                                        $extension = pathinfo($dokumenKaryawan->FileDok, PATHINFO_EXTENSION);
                                                                        $iconClass = 'fas fa-file fa-2x text-secondary';

                                                                        if (strtolower($extension) === 'pdf') {
                                                                            $iconClass = 'fas fa-file-pdf fa-2x text-danger';
                                                                        } elseif (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                                                            $iconClass = 'fas fa-file-image fa-2x text-primary';
                                                                        }
                                                                    @endphp
                                                                    <i class="{{ $iconClass }}"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-0 text-truncate">
                                                                        {{ $dokumenKaryawan->FileDok }}</h6>
                                                                    <small class="text-muted">File sudah ada</small>
                                                                </div>
                                                                <a href="{{ route('viewdocumentkaryawan', $dokumenKaryawan->id) }}"
                                                                    class="btn btn-sm btn-info text-white me-1"
                                                                    target="_blank">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                {{-- <a href="{{ route('dokumen-karyawan.download', $dokumenKaryawan->id) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download"></i>
                                                                </a> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
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
                                                            {{ old('StatusDok', $dokumenKaryawan->StatusDok) == 'Berlaku' ? 'checked' : '' }}
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
                                                            {{ old('StatusDok', $dokumenKaryawan->StatusDok) == 'Tidak Berlaku' ? 'checked' : '' }}>
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
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
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

        .invalid-feedback {
            display: block;
            font-weight: 500;
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
    // Previously selected jenis dokumen
    const selectedJenisDok = "{{ old('JenisDok', $dokumenKaryawan->JenisDok) }}";

    console.log("Selected Jenis Dok:", selectedJenisDok);
    console.log("JenisDokumenByKategori:", jenisDokumenByKategori);

    // File input preview and validation
    const fileInput = document.getElementById('FileDok');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileTypeIcon = document.getElementById('fileTypeIcon');
    const removeFileBtn = document.getElementById('removeFile');
    const fileValidationMessage = document.getElementById('fileValidationMessage');

    fileInput.addEventListener('change', function() {
        // Clear previous validation messages
        fileInput.classList.remove('is-invalid');
        fileValidationMessage.classList.add('d-none');

        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            const fileExt = file.name.split('.').pop().toLowerCase();

            // Validate file type
            const validTypes = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!validTypes.includes(fileExt)) {
                fileInput.classList.add('is-invalid');
                fileValidationMessage.textContent =
                    'Format file tidak valid. Gunakan PDF, JPG, JPEG, atau PNG.';
                fileValidationMessage.classList.remove('d-none');
                fileInput.value = '';
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                fileInput.classList.add('is-invalid');
                fileValidationMessage.textContent = 'Ukuran file maksimal 5MB.';
                fileValidationMessage.classList.remove('d-none');
                fileInput.value = '';
                return;
            }

            // Update preview
            fileName.textContent = file.name;

            // Format file size
            let formattedSize;
            if (file.size < 1024) {
                formattedSize = file.size + ' bytes';
            } else if (file.size < 1024 * 1024) {
                formattedSize = (file.size / 1024).toFixed(1) + ' KB';
            } else {
                formattedSize = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            }
            fileSize.textContent = formattedSize;

            // Update icon based on file type
            let iconClass = 'fas fa-file fa-2x text-secondary';
            if (fileExt === 'pdf') {
                iconClass = 'fas fa-file-pdf fa-2x text-danger';
            } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                iconClass = 'fas fa-file-image fa-2x text-primary';
            }
            fileTypeIcon.innerHTML = `<i class="${iconClass}"></i>`;

            // Show preview container
            filePreviewContainer.classList.remove('d-none');
        } else {
            // Hide preview container
            filePreviewContainer.classList.add('d-none');
        }
    });

    // Remove file button
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreviewContainer.classList.add('d-none');
        });
    }

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
            // For all fields, search by data-value attribute
            const selectedOption = Array.from(options).find(option => option.dataset.value === initialValue);

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
        console.log("updateJenisDokumen called with kategoriId:", kategoriId);

        const jenisPortal = document.getElementById('jenisPortal');
        if (!jenisPortal) {
            console.error("Jenis portal not found");
            return;
        }

        const jenisOptions = jenisPortal.querySelector('#jenisOptions');
        const jenisSearch = document.getElementById('jenisSearch');
        const jenisHiddenInput = document.getElementById('JenisDok');

        console.log("Current JenisDok value:", jenisHiddenInput.value);
        console.log("Selected JenisDok value:", selectedJenisDok);

        // Keep the empty option
        const emptyOption = jenisOptions.querySelector('.empty-option');
        jenisOptions.innerHTML = '';
        jenisOptions.appendChild(emptyOption);

        // Get the selected value before updating options
        let previouslySelected = jenisHiddenInput.value;

        // Don't reset value if it matches the previously selected jenis dokumen
        if (previouslySelected !== selectedJenisDok) {
            previouslySelected = selectedJenisDok;
        }

        // Reset if no category is selected
        if (!kategoriId) {
            console.log("No kategori selected");
            return;
        }

        // Get dokumen list for selected kategori
        const dokumenList = jenisDokumenByKategori[kategoriId] || [];
        console.log("Dokumen list for kategori:", dokumenList);

        // Add options - jenis dokumen already sorted by GolDok from controller
        dokumenList.forEach(dokumen => {
            const option = document.createElement('div');
            option.className = 'custom-select-option';
            option.dataset.value = dokumen.JenisDok;
            option.dataset.display = dokumen.JenisDok;
            option.textContent = dokumen.JenisDok;

            // Check if this option should be selected
            const shouldBeSelected = dokumen.JenisDok === selectedJenisDok;
            if (shouldBeSelected) {
                console.log("Found matching jenis:", dokumen.JenisDok);
                option.classList.add('selected');
                jenisSearch.value = dokumen.JenisDok;
                jenisHiddenInput.value = dokumen.JenisDok;
            }

            // Add click event handler
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

        // If we have a previously selected value and it's in the new options list,
        // make sure it's selected
        if (previouslySelected) {
            console.log("Looking for previously selected value:", previouslySelected);

            // Check all options for a match
            const allOptions = jenisOptions.querySelectorAll('.custom-select-option');
            let matchFound = false;

            allOptions.forEach(option => {
                if (option.dataset.value === previouslySelected) {
                    console.log("Found matching option for:", previouslySelected);
                    option.classList.add('selected');
                    jenisSearch.value = option.dataset.display;
                    jenisHiddenInput.value = option.dataset.value;
                    matchFound = true;
                }
            });

            if (!matchFound) {
                console.log("No matching option found for:", previouslySelected);
            }
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

    // Function to validate file input
    function validateFileInput() {
        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileExt = file.name.split('.').pop().toLowerCase();
            const validTypes = ['pdf', 'jpg', 'jpeg', 'png'];

            // Validate file type
            if (!validTypes.includes(fileExt)) {
                fileInput.classList.add('is-invalid');
                fileValidationMessage.textContent =
                    'Format file tidak valid. Gunakan PDF, JPG, JPEG, atau PNG.';
                fileValidationMessage.classList.remove('d-none');
                return false;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                fileInput.classList.add('is-invalid');
                fileValidationMessage.textContent = 'Ukuran file maksimal 5MB.';
                fileValidationMessage.classList.remove('d-none');
                return false;
            }

            fileInput.classList.remove('is-invalid');
            fileValidationMessage.classList.add('d-none');
            return true;
        }

        // No file selected is valid (we already have a file)
        return true;
    }

    // Function to highlight missing fields
    function highlightMissingFields() {
        document.querySelectorAll('[required]').forEach(function(input) {
            if (!input.value) {
                input.classList.add('is-invalid');

                // Handle custom select validation
                if (input.type === 'hidden' && (input.id.includes('IdKode') || input.id.includes(
                        'Dok'))) {
                    const containerId = input.id.replace('IdKode', '').replace('Dok', '') + 'Container';
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
    const form = document.getElementById('dokumenKaryawanForm');
    form.addEventListener('submit', function(event) {
        // Validate file input first if file is selected
        const fileIsValid = validateFileInput();

        if (!fileIsValid || !form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            // Close any open dropdowns
            document.querySelectorAll('.dropdown-portal').forEach(portal => {
                portal.classList.remove('active');
            });
            document.querySelectorAll('.custom-select-container').forEach(container => {
                container.classList.remove('open');
            });

            // Add alert for validation errors
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-3';
            errorAlert.innerHTML = `
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-1"></i> Formulir belum lengkap!</h5>
                <p>Silakan periksa kembali dan lengkapi semua field yang diperlukan.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            // Insert alert at top of form
            const firstElement = form.firstElementChild;
            form.insertBefore(errorAlert, firstElement);

            // Scroll to alert
            errorAlert.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
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

    // Initialize jenis dokumen based on current kategori
    const currentKategori = document.getElementById('KategoriDok').value;
    console.log("Initial current kategori:", currentKategori);

    if (currentKategori) {
        // Call initially
        updateJenisDokumen(currentKategori);

        // And also try with a slight delay to ensure DOM is ready
        setTimeout(() => {
            updateJenisDokumen(currentKategori);
        }, 100);
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

    // Also make sure kategori is properly initialized
    // This will help ensure the display shows the correct value
    (function initializeKategoriDisplay() {
        const kategoriValue = document.getElementById('KategoriDok').value;
        const kategoriSearch = document.getElementById('kategoriSearch');
        const kategoriPortal = document.getElementById('kategoriPortal');

        if (kategoriValue && kategoriPortal) {
            const options = kategoriPortal.querySelectorAll('.custom-select-option');
            const selectedOption = Array.from(options).find(option => option.dataset.value === kategoriValue);

            if (selectedOption && kategoriSearch) {
                console.log("Setting kategori display to:", selectedOption.dataset.display);
                kategoriSearch.value = selectedOption.dataset.display;
            }
        }
    })();
});
    </script>
@endpush