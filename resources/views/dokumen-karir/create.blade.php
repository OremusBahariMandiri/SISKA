@extends('layouts.app')

@section('title', 'Tambah Dokumen Karir')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-plus me-2"></i>Tambah Dokumen Karir</span>
                        <a href="{{ route('dokumen-karir.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('dokumen-karir.store') }}" method="POST" id="dokumenKarirForm"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Grouped form sections with cards -->
                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Dokumen</h5>
                                        </div>
                                        <div class="card-body">
                                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                                value="{{ old('IdKode', $newId) }}" hidden readonly>

                                            <div class="form-group mb-3">
                                                <label for="NoRegDok" class="form-label fw-bold">Nomor Registrasi <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    <input type="text" class="form-control @error('NoRegDok') is-invalid @enderror"
                                                        id="NoRegDok" name="NoRegDok" value="{{ old('NoRegDok') }}"
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
                                                    <div class="custom-select-container" style="flex: 1 1 auto; width: 1%;">
                                                        <input type="text" id="karyawanSearch"
                                                            class="form-control custom-select-search @error('IdKodeA04') is-invalid @enderror"
                                                            placeholder="Cari karyawan..." autocomplete="off">

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
                                                                        data-display="{{ $employee->NamaKry }} - {{ $employee->NrkKry ?? '' }}">
                                                                        {{ $employee->NamaKry }} -
                                                                        {{ $employee->NrkKry ?? '' }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA04" id="IdKodeA04"
                                                            value="{{ old('IdKodeA04') }}" required>
                                                    </div>
                                                    @error('IdKodeA04')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="IdKodeA08" class="form-label fw-bold">Jabatan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>

                                                    <!-- Custom searchable dropdown for Jabatan -->
                                                    <div class="custom-select-container" style="flex: 1 1 auto; width: 1%;">
                                                        <input type="text" id="jabatanSearch"
                                                            class="form-control custom-select-search @error('IdKodeA08') is-invalid @enderror"
                                                            placeholder="Cari jabatan..." autocomplete="off">

                                                        <div class="custom-select-dropdown" id="jabatanDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="jabatanFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Jabatan --">--
                                                                    Pilih Jabatan --</div>
                                                                @foreach ($jabatan as $jab)
                                                                    <div class="custom-select-option"
                                                                        data-value="{{ $jab->IdKode }}"
                                                                        data-display="{{ $jab->GolonganJbt }} - {{ $jab->Jabatan }}">
                                                                        {{ $jab->GolonganJbt }} - {{ $jab->Jabatan }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA08" id="IdKodeA08"
                                                            value="{{ old('IdKodeA08') }}" required>
                                                    </div>
                                                    @error('IdKodeA08')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="IdKodeA09" class="form-label fw-bold">Departemen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>

                                                    <!-- Custom searchable dropdown for Departemen -->
                                                    <div class="custom-select-container" style="flex: 1 1 auto; width: 1%;">
                                                        <input type="text" id="departemenSearch"
                                                            class="form-control custom-select-search @error('IdKodeA09') is-invalid @enderror"
                                                            placeholder="Cari departemen..." autocomplete="off">

                                                        <div class="custom-select-dropdown" id="departemenDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="departemenFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Departemen --">--
                                                                    Pilih Departemen --</div>
                                                                @foreach ($departemen as $dept)
                                                                    <div class="custom-select-option"
                                                                        data-value="{{ $dept->IdKode }}"
                                                                        data-display="{{ $dept->GolonganDep }} - {{ $dept->Departemen}}">
                                                                        {{ $dept->GolonganDep }} - {{ $dept->Departemen}}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA09" id="IdKodeA09"
                                                            value="{{ old('IdKodeA09') }}" required>
                                                    </div>
                                                    @error('IdKodeA09')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="IdKodeA10" class="form-label fw-bold">Wilayah Kerja <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>

                                                    <!-- Custom searchable dropdown for Wilayah Kerja -->
                                                    <div class="custom-select-container" style="flex: 1 1 auto; width: 1%;">
                                                        <input type="text" id="wilkerSearch"
                                                            class="form-control custom-select-search @error('IdKodeA10') is-invalid @enderror"
                                                            placeholder="Cari wilayah kerja..." autocomplete="off">

                                                        <div class="custom-select-dropdown" id="wilkerDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="wilkerFilterInput"
                                                                    class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option"
                                                                    data-value="" data-display="-- Pilih Wilayah Kerja --">--
                                                                    Pilih Wilayah Kerja --</div>
                                                                @foreach ($wilker as $wk)
                                                                    <div class="custom-select-option"
                                                                        data-value="{{ $wk->IdKode }}"
                                                                        data-display="{{ $wk->GolonganWilker }} - {{$wk->WilayahKerja}}">
                                                                        {{ $wk->GolonganWilker }} - {{$wk->WilayahKerja}}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA10" id="IdKodeA10"
                                                            value="{{ old('IdKodeA10') }}" required>
                                                    </div>
                                                    @error('IdKodeA10')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="KategoriDok" class="form-label fw-bold">Kategori Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-folder"></i></span>

                                                    <!-- Custom searchable dropdown for Kategori -->
                                                    <div class="custom-select-container" style="flex: 1 1 auto; width: 1%;">
                                                        <input type="text" id="kategoriSearch"
                                                            class="form-control custom-select-search @error('KategoriDok') is-invalid @enderror"
                                                            placeholder="Cari kategori..." autocomplete="off">

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
                                                                    <div class="custom-select-option"
                                                                        data-value="{{ $kategori->IdKode }}"
                                                                        data-display="{{ $kategori->KategoriDok }}">
                                                                        {{ $kategori->KategoriDok }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="KategoriDok" id="KategoriDok"
                                                            value="{{ old('KategoriDok') }}" required>
                                                    </div>
                                                    @error('KategoriDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="JenisDok" class="form-label fw-bold">Jenis Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-file-alt"></i></span>

                                                    <!-- Custom searchable dropdown for Jenis Dokumen -->
                                                    <div class="custom-select-container" style="flex: 1 1 auto; width: 1%;">
                                                        <input type="text" id="jenisSearch"
                                                            class="form-control custom-select-search @error('JenisDok') is-invalid @enderror"
                                                            placeholder="Cari jenis dokumen..." autocomplete="off">

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
                                                            value="{{ old('JenisDok') }}" required>
                                                    </div>
                                                    @error('JenisDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="KetDok" class="form-label fw-bold">Keterangan Dokumen</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                                    <textarea class="form-control @error('KetDok') is-invalid @enderror" id="KetDok" name="KetDok"
                                                        rows="3" placeholder="Masukkan keterangan dokumen">{{ old('KetDok') }}</textarea>
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
                                        <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku & Tanggal</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Validasi Dokumen <span class="text-danger">*</span></label>
                                                <div class="bg-light p-2 rounded">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input validasi-dokumen @error('ValidasiDok') is-invalid @enderror"
                                                            type="radio" name="ValidasiDok" id="tetap" value="Tetap"
                                                            {{ old('ValidasiDok', 'Tetap') == 'Tetap' ? 'checked' : '' }} required>
                                                        <label class="form-check-label" for="tetap">
                                                            <i class="fas fa-infinity text-primary me-1"></i>Tetap
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input validasi-dokumen @error('ValidasiDok') is-invalid @enderror"
                                                            type="radio" name="ValidasiDok" id="perpanjangan" value="Perpanjangan"
                                                            {{ old('ValidasiDok') == 'Perpanjangan' ? 'checked' : '' }}>
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
                                                        <label for="TglTerbitDok" class="form-label fw-bold">Tanggal Terbit <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                                                            <input type="date" class="form-control tanggal-input @error('TglTerbitDok') is-invalid @enderror"
                                                                id="TglTerbitDok" name="TglTerbitDok" value="{{ old('TglTerbitDok') }}" required>
                                                            @error('TglTerbitDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3 tgl-berakhir-group">
                                                        <label for="TglBerakhirDok" class="form-label fw-bold">Tanggal Berakhir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                                                            <input type="date" class="form-control tanggal-input @error('TglBerakhirDok') is-invalid @enderror"
                                                                id="TglBerakhirDok" name="TglBerakhirDok" value="{{ old('TglBerakhirDok') }}">
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
                                                    name="MasaBerlaku" value="{{ old('MasaBerlaku', 'Tetap') }}" readonly>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Masa berlaku akan dihitung otomatis
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 tgl-pengingat-group">
                                                <label for="TglPengingat" class="form-label fw-bold">Tanggal Pengingat</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-bell"></i></span>
                                                    <input type="date" class="form-control tanggal-input @error('TglPengingat') is-invalid @enderror"
                                                        id="TglPengingat" name="TglPengingat" value="{{ old('TglPengingat') }}">
                                                    @error('TglPengingat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="MasaPengingat" class="form-label fw-bold">Masa Pengingat</label>
                                                <input type="text" class="form-control bg-light" id="MasaPengingat"
                                                    name="MasaPengingat" value="{{ old('MasaPengingat', '-') }}" readonly>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Masa pengingat akan dihitung otomatis
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="card border-secondary mt-4">
                                <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="FileDok" class="form-label fw-bold">File Dokumen</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-file-upload"></i></span>
                                                    <input type="file" class="form-control @error('FileDok') is-invalid @enderror"
                                                        id="FileDok" name="FileDok">
                                                    @error('FileDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Format: PDF, JPG, JPEG, PNG.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="StatusDok" class="form-label fw-bold">Status Dokumen</label>
                                                <div class="bg-light p-2 rounded">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input @error('StatusDok') is-invalid @enderror"
                                                            type="radio" name="StatusDok" id="berlaku" value="Berlaku"
                                                            {{ old('StatusDok', 'Berlaku') == 'Berlaku' ? 'checked' : '' }} required>
                                                        <label class="form-check-label" for="berlaku">
                                                            <i class="fas fa-check-circle text-success me-1"></i>Berlaku
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input @error('StatusDok') is-invalid @enderror"
                                                            type="radio" name="StatusDok" id="tidak_berlaku" value="Tidak Berlaku"
                                                            {{ old('StatusDok') == 'Tidak Berlaku' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tidak_berlaku">
                                                            <i class="fas fa-times-circle text-danger me-1"></i>Tidak Berlaku
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
                                    <i class="fas fa-save me-2"></i>Simpan
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

        /* Custom Select Styles */
        .custom-select-container {
            position: relative;
        }

        .custom-select-search {
            cursor: pointer;
            background-color: #fff;
        }

        .custom-select-dropdown {
            position: absolute;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            z-index: 1000;
            display: none;
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

        /* When dropdown is open */
        .custom-select-container.open .custom-select-dropdown {
            display: block;
        }

        .custom-select-container.open .custom-select-search {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Invalid state for custom select */
        .custom-select-container.is-invalid .custom-select-search {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Custom Select Dropdowns for all select fields
            initCustomSelect('karyawan', 'IdKodeA04');
            initCustomSelect('jabatan', 'IdKodeA08');
            initCustomSelect('departemen', 'IdKodeA09');
            initCustomSelect('wilker', 'IdKodeA10');
            initCustomSelect('kategori', 'KategoriDok');
            initCustomSelect('jenis', 'JenisDok');

            // Function to initialize custom select
            function initCustomSelect(prefix, hiddenInputId) {
                const searchInput = document.getElementById(prefix + 'Search');
                const filterInput = document.getElementById(prefix + 'FilterInput');
                const dropdown = document.getElementById(prefix + 'Dropdown');
                const hiddenInput = document.getElementById(hiddenInputId);
                const customSelectContainer = searchInput.closest('.custom-select-container');
                const options = dropdown.querySelectorAll('.custom-select-option');

                // Initialize selected value if exists
                const initialValue = hiddenInput.value;
                if (initialValue) {
                    const selectedOption = Array.from(options).find(option => option.dataset.value === initialValue);
                    if (selectedOption) {
                        searchInput.value = selectedOption.dataset.display;
                        selectedOption.classList.add('selected');
                    }
                }

                // Show dropdown when clicking on search input
                searchInput.addEventListener('click', function() {
                    customSelectContainer.classList.add('open');
                    dropdown.style.display = 'block';
                    filterInput.value = '';
                    filterInput.focus();

                    // Reset options visibility
                    options.forEach(option => {
                        option.classList.remove('hidden');
                    });
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!customSelectContainer.contains(e.target)) {
                        customSelectContainer.classList.remove('open');
                        dropdown.style.display = 'none';
                    }
                });

                // Filter options as user types
                filterInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    options.forEach(option => {
                        const text = option.textContent.toLowerCase();
                        if (text.includes(filter) || option.classList.contains('empty-option')) {
                            option.classList.remove('hidden');
                        } else {
                            option.classList.add('hidden');
                        }
                    });
                });

                // Handle option selection
                options.forEach(option => {
                    option.addEventListener('click', function() {
                        const value = this.dataset.value;
                        const display = this.dataset.display;

                        // Update hidden input and display
                        hiddenInput.value = value;
                        searchInput.value = display;

                        // Mark as selected
                        options.forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');

                        // Close dropdown
                        customSelectContainer.classList.remove('open');
                        dropdown.style.display = 'none';

                        // Remove invalid state if set
                        customSelectContainer.classList.remove('is-invalid');

                        // Trigger change event on hidden input
                        const event = new Event('change', {
                            bubbles: true
                        });
                        hiddenInput.dispatchEvent(event);

                        // If kategori is changed, update jenis dokumen
                        if (prefix === 'kategori') {
                            updateJenisDokumen(value);
                        }

                        // If karyawan is changed, try to populate departemen, jabatan, and wilker
                        if (prefix === 'karyawan') {
                            fetchEmployeeDetails(value);
                        }
                    });
                });

                // Make Enter key on search input open the dropdown
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.click();
                    }
                });

                // Keep focus in dropdown when clicking filter input
                filterInput.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                // Handle input changes for validation
                searchInput.addEventListener('input', function() {
                    if (this.value) {
                        customSelectContainer.classList.remove('is-invalid');
                    }
                });
            }

            // Function to fetch employee details and populate related fields
            function fetchEmployeeDetails(employeeId) {
                // Make AJAX request to get employee details
                fetch(`/api/karyawan/${employeeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Populate jabatan
                            if (data.jabatan) {
                                document.getElementById('IdKodeA08').value = data.jabatan;
                                const jabatanOption = document.querySelector(`.custom-select-option[data-value="${data.jabatan}"]`);
                                if (jabatanOption) {
                                    document.getElementById('jabatanSearch').value = jabatanOption.dataset.display;
                                }
                            }

                            // Populate departemen
                            if (data.departemen) {
                                document.getElementById('IdKodeA09').value = data.departemen;
                                const departemenOption = document.querySelector(`.custom-select-option[data-value="${data.departemen}"]`);
                                if (departemenOption) {
                                    document.getElementById('departemenSearch').value = departemenOption.dataset.display;
                                }
                            }

                            // Populate wilker
                            if (data.wilker) {
                                document.getElementById('IdKodeA10').value = data.wilker;
                                const wilkerOption = document.querySelector(`.custom-select-option[data-value="${data.wilker}"]`);
                                if (wilkerOption) {
                                    document.getElementById('wilkerSearch').value = wilkerOption.dataset.display;
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching employee details:', error);
                    });
            }

            // Data jenisDokumenByKategori dari controller
            const jenisDokumenByKategori = @json($jenisDokumenByKategori);

            // Function to update jenis dokumen options based on selected kategori
            function updateJenisDokumen(kategoriId) {
                // Get jenis dokumen container and clear previous options
                const jenisOptions = document.getElementById('jenisOptions');
                // Keep the empty option and remove the rest
                const emptyOption = jenisOptions.querySelector('.empty-option');
                jenisOptions.innerHTML = '';
                jenisOptions.appendChild(emptyOption);

                // Reset jenis dokumen value
                document.getElementById('JenisDok').value = '';
                document.getElementById('jenisSearch').value = '';

                if (!kategoriId) return;

                // Get dokumen list for selected kategori
                const dokumenList = jenisDokumenByKategori[kategoriId] || [];

                // Add filtered options to select element
                dokumenList.forEach(dokumen => {
                    const option = document.createElement('div');
                    option.className = 'custom-select-option';
                    option.dataset.value = dokumen.JenisDok;
                    option.dataset.display = dokumen.JenisDok;
                    option.textContent = dokumen.JenisDok;
                    jenisOptions.appendChild(option);
                });

                // Reinitialize event listeners for new options
                const jenisSearchInput = document.getElementById('jenisSearch');
                const jenisFilterInput = document.getElementById('jenisFilterInput');
                const jenisDropdown = document.getElementById('jenisDropdown');
                const jenisHiddenInput = document.getElementById('JenisDok');
                const jenisSelectContainer = jenisSearchInput.closest('.custom-select-container');
                const newOptions = jenisOptions.querySelectorAll('.custom-select-option');

                newOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        const value = this.dataset.value;
                        const display = this.dataset.display;

                        // Update hidden input and display
                        jenisHiddenInput.value = value;
                        jenisSearchInput.value = display;

                        // Mark as selected
                        newOptions.forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');

                        // Close dropdown
                        jenisSelectContainer.classList.remove('open');
                        jenisDropdown.style.display = 'none';

                        // Remove invalid state if set
                        jenisSelectContainer.classList.remove('is-invalid');

                        // Trigger change event on hidden input
                        const event = new Event('change', {
                            bubbles: true
                        });
                        jenisHiddenInput.dispatchEvent(event);
                    });
                });
            }

            // Listen for changes on kategori
            document.getElementById('KategoriDok').addEventListener('change', function() {
                updateJenisDokumen(this.value);
            });

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

            // Form validation with visual feedback
            const form = document.getElementById('dokumenKarirForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Highlight missing required fields
                    document.querySelectorAll('[required]').forEach(function(input) {
                        if (!input.value) {
                            input.classList.add('is-invalid');

                            // Handle custom select validation
                            if (input.id === 'IdKodeA04' || input.id === 'IdKodeA08' ||
                                input.id === 'IdKodeA09' || input.id === 'IdKodeA10' ||
                                input.id === 'KategoriDok' || input.id === 'JenisDok') {
                                const container = document.getElementById(input.id).closest('.custom-select-container');
                                if (container) {
                                    container.classList.add('is-invalid');
                                }
                            }

                            // Create error message if it doesn't exist
                            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = 'Field ini wajib diisi';
                                input.parentNode.insertBefore(feedback, input.nextElementSibling);
                            }
                        } else {
                            input.classList.remove('is-invalid');

                            // Handle custom select validation
                            if (input.id === 'IdKodeA04' || input.id === 'IdKodeA08' ||
                                input.id === 'IdKodeA09' || input.id === 'IdKodeA10' ||
                                input.id === 'KategoriDok' || input.id === 'JenisDok') {
                                const container = document.getElementById(input.id).closest('.custom-select-container');
                                if (container) {
                                    container.classList.remove('is-invalid');
                                }
                            }
                        }
                    });

                    // Scroll to first error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                }
            });

            // Initialize form state
            toggleFieldsVisibility();

            // Calculate initial reminder period if fields already have values
            if (document.getElementById('TglPengingat').value && document.getElementById('TglBerakhirDok').value) {
                hitungMasaPengingat();
            }

            // Initialize jenis dokumen if kategori already has a value
            if (document.getElementById('KategoriDok').value) {
                updateJenisDokumen(document.getElementById('KategoriDok').value);
            }

            // Initialize employee details if already selected
            const initialEmployeeId = document.getElementById('IdKodeA04').value;
            if (initialEmployeeId) {
                fetchEmployeeDetails(initialEmployeeId);
            }
        });
    </script>
@endpush