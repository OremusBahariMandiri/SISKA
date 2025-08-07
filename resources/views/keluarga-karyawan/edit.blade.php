@extends('layouts.app')

@section('title', 'Edit Keluarga Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Data Keluarga Karyawan</span>
                        <a href="{{ route('keluarga-karyawan.index') }}" class="btn btn-light btn-sm">
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

                        <!-- Alert for validation errors -->
                        <div class="alert alert-danger" id="validationAlert" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Mohon periksa kembali form. Beberapa field wajib belum diisi dengan benar.</span>
                            <ul id="validationMessages" class="mt-2 mb-0"></ul>
                        </div>

                        <form action="{{ route('keluarga-karyawan.update', $keluargaKaryawan->id) }}" method="POST"
                            id="keluargaForm">
                            @csrf
                            @method('PUT')
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $keluargaKaryawan->IdKode) }}" hidden readonly>

                            <!-- Nav tabs for form sections -->
                            <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="info-utama-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-utama" type="button" role="tab"
                                        aria-controls="info-utama" aria-selected="true">
                                        <i class="fas fa-user me-1"></i> Informasi Karyawan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-pribadi-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-pribadi" type="button" role="tab"
                                        aria-controls="info-pribadi" aria-selected="false">
                                        <i class="fas fa-id-card me-1"></i> Informasi Keluarga
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-pendidikan-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-pendidikan" type="button" role="tab"
                                        aria-controls="info-pendidikan" aria-selected="false">
                                        <i class="fas fa-graduation-cap me-1"></i> Pendidikan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-kontak-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-kontak" type="button" role="tab"
                                        aria-controls="info-kontak" aria-selected="false">
                                        <i class="fas fa-phone me-1"></i> Kontak
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Informasi Utama -->
                                <div class="tab-pane fade show active" id="info-utama" role="tabpanel"
                                    aria-labelledby="info-utama-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Karyawan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Add photo container at the top of the form -->

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="IdKodeA04" class="form-label fw-bold">Karyawan <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user-tie"></i></span>

                                                            <!-- Custom searchable dropdown replacement for Select2 -->
                                                            <div class="custom-select-container"
                                                                style="flex: 1 1 auto; width: 1%;">
                                                                <input type="text" id="karyawanSearch"
                                                                    class="form-control custom-select-search @error('IdKodeA04') is-invalid @enderror"
                                                                    placeholder="Cari karyawan..." autocomplete="off">

                                                                <div class="custom-select-dropdown" id="karyawanDropdown">
                                                                    <div class="custom-select-search-wrapper">
                                                                        <input type="text" id="karyawanFilterInput"
                                                                            class="form-control"
                                                                            placeholder="Ketik untuk mencari">
                                                                    </div>
                                                                    <div class="custom-select-options">
                                                                        <div class="custom-select-option empty-option"
                                                                            data-value="" data-display="Pilih Karyawan">
                                                                            Pilih Karyawan</div>
                                                                        @foreach ($karyawans as $karyawan)
                                                                            <div class="custom-select-option"
                                                                                data-value="{{ $karyawan->IdKode }}"
                                                                                data-display="{{ $karyawan->NamaKry }} - {{ $karyawan->NrkKry }}">
                                                                                {{ $karyawan->NamaKry }} -
                                                                                {{ $karyawan->NrkKry }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" name="IdKodeA04" id="IdKodeA04"
                                                                    value="{{ old('IdKodeA04', $keluargaKaryawan->IdKodeA04) }}"
                                                                    required>
                                                            </div>

                                                            @error('IdKodeA04')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-4 text-center">
                                                    <div class="form-group">
                                                        <label class="form-label fw-bold mb-2">Foto Karyawan</label>
                                                        <div class="mx-auto" style="width: 150px; height: 180px;">
                                                            <img id="fotoKaryawan"
                                                                src="{{ asset('images/default-user.png') }}"
                                                                class="img-thumbnail shadow-sm" alt="Foto Karyawan"
                                                                style="width: 100%; height: 100%; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="NikKtpUtama" class="form-label fw-bold">NIK
                                                            KTP</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-id-card"></i></span>
                                                            <input type="text" class="form-control" id="NikKtpUtama"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="NrkKry" class="form-label fw-bold">NRK
                                                            Karyawan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-id-badge"></i></span>
                                                            <input type="text" class="form-control" id="NrkKry"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TglMsk" class="form-label fw-bold">Tanggal
                                                            Masuk</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-plus"></i></span>
                                                            <input type="text" class="form-control" id="TglMsk"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="MasaKerja" class="form-label fw-bold">Masa
                                                            Kerja</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-business-time"></i></span>
                                                            <input type="text" class="form-control" id="MasaKerja"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKaryawan" class="form-label fw-bold">Status
                                                            Karyawan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user-check"></i></span>
                                                            <input type="text" class="form-control" id="StsKaryawan"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKawinKry" class="form-label fw-bold">Status
                                                            Perkawinan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-ring"></i></span>
                                                            <input type="text" class="form-control" id="StsKawinKry"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKeluargaKryInfo" class="form-label fw-bold">Status
                                                            Keluarga</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-users"></i></span>
                                                            <input type="text" class="form-control"
                                                                id="StsKeluargaKryInfo" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="JumlahAnakKry" class="form-label fw-bold">Jumlah
                                                            Anak</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-child"></i></span>
                                                            <input type="text" class="form-control" id="JumlahAnakKry"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="UmurKry" class="form-label fw-bold">Umur</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-birthday-cake"></i></span>
                                                            <input type="text" class="form-control" id="UmurKry"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="KotaKry" class="form-label fw-bold">Kota</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-city"></i></span>
                                                            <input type="text" class="form-control" id="KotaKry"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="ProvinsiKry"
                                                            class="form-label fw-bold">Provinsi</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map"></i></span>
                                                            <input type="text" class="form-control" id="ProvinsiKry"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="AlamatKry" class="form-label fw-bold">Alamat</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-home"></i></span>
                                                            <textarea class="form-control" id="AlamatKry" rows="3" disabled></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Pribadi -->
                                <div class="tab-pane fade" id="info-pribadi" role="tabpanel"
                                    aria-labelledby="info-pribadi-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Informasi Keluarga</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="NikKlg" class="form-label fw-bold">NIK</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-id-card"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('NikKlg') is-invalid @enderror"
                                                                id="NikKlg" name="NikKlg"
                                                                value="{{ old('NikKlg', $keluargaKaryawan->NikKlg) }}">
                                                            @error('NikKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="NamaKlg" class="form-label fw-bold">Nama <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('NamaKlg') is-invalid @enderror"
                                                                id="NamaKlg" name="NamaKlg"
                                                                value="{{ old('NamaKlg', $keluargaKaryawan->NamaKlg) }}"
                                                                required>
                                                            @error('NamaKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TempatLhrKlg" class="form-label fw-bold">Tempat
                                                            Lahir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map-marker-alt"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('TempatLhrKlg') is-invalid @enderror"
                                                                id="TempatLhrKlg" name="TempatLhrKlg"
                                                                value="{{ old('TempatLhrKlg', $keluargaKaryawan->TempatLhrKlg) }}">
                                                            @error('TempatLhrKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TanggalLhrKlg" class="form-label fw-bold">Tanggal
                                                            Lahir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-alt"></i></span>
                                                            <input type="date"
                                                                class="form-control @error('TanggalLhrKlg') is-invalid @enderror"
                                                                id="TanggalLhrKlg" name="TanggalLhrKlg"
                                                                value="{{ old('TanggalLhrKlg', $keluargaKaryawan->TanggalLhrKlg ? $keluargaKaryawan->TanggalLhrKlg->format('Y-m-d') : '') }}">
                                                            @error('TanggalLhrKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div id="usiaInfo" class="form-text text-muted mt-1"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="SexKlg" class="form-label fw-bold">Jenis Kelamin
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-venus-mars"></i></span>
                                                            <select
                                                                class="form-select @error('SexKlg') is-invalid @enderror"
                                                                id="SexKlg" name="SexKlg" required>
                                                                <option value="" disabled>Pilih Jenis Kelamin
                                                                </option>
                                                                @foreach ($jenisKelaminOptions as $value)
                                                                    <option value="{{ $value }}"
                                                                        {{ old('SexKlg', $keluargaKaryawan->SexKlg) == $value ? 'selected' : '' }}>
                                                                        {{ $value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('SexKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="AgamaKlg" class="form-label fw-bold">Agama</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-pray"></i></span>
                                                            <select
                                                                class="form-select @error('AgamaKlg') is-invalid @enderror"
                                                                id="AgamaKlg" name="AgamaKlg">
                                                                <option value="" disabled>Pilih Agama</option>
                                                                @foreach ($agamaOptions as $agama)
                                                                    <option value="{{ $agama }}"
                                                                        {{ old('AgamaKlg', $keluargaKaryawan->AgamaKlg) == $agama ? 'selected' : '' }}>
                                                                        {{ $agama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('AgamaKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKeluargaKry" class="form-label fw-bold">Status
                                                            Keluarga <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-users"></i></span>
                                                            <select
                                                                class="form-select @error('StsKeluargaKry') is-invalid @enderror"
                                                                id="StsKeluargaKry" name="StsKeluargaKry" required>
                                                                <option value="" disabled>Pilih Status</option>
                                                                @foreach ($statusKeluargaOptions as $status)
                                                                    <option value="{{ $status }}"
                                                                        {{ old('StsKeluargaKry', $keluargaKaryawan->StsKeluargaKry) == $status ? 'selected' : '' }}>
                                                                        {{ $status }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('StsKeluargaKry')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKawinKlg" class="form-label fw-bold">Status
                                                            Kawin</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-ring"></i></span>
                                                            <select
                                                                class="form-select @error('StsKawinKlg') is-invalid @enderror"
                                                                id="StsKawinKlg" name="StsKawinKlg">
                                                                <option value="" disabled>Pilih Status</option>
                                                                @foreach ($statusKawinOptions as $status)
                                                                    <option value="{{ $status }}"
                                                                        {{ old('StsKawinKlg', $keluargaKaryawan->StsKawinKlg) == $status ? 'selected' : '' }}>
                                                                        {{ $status }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('StsKawinKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="KetKeluargaKry"
                                                            class="form-label fw-bold">Keterangan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-sticky-note"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('KetKeluargaKry') is-invalid @enderror"
                                                                id="KetKeluargaKry" name="KetKeluargaKry"
                                                                value="{{ old('KetKeluargaKry', $keluargaKaryawan->KetKeluargaKry) }}">
                                                            @error('KetKeluargaKry')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle me-1"></i>Misalnya: Anak ke-1,
                                                            dll.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="AlamatKtpKlg" class="form-label fw-bold">Alamat
                                                            KTP</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-home"></i></span>
                                                            <textarea class="form-control @error('AlamatKtpKlg') is-invalid @enderror" id="AlamatKtpKlg" name="AlamatKtpKlg"
                                                                rows="3">{{ old('AlamatKtpKlg', $keluargaKaryawan->AlamatKtpKlg) }}</textarea>
                                                            @error('AlamatKtpKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="DomisiliKlg" class="form-label fw-bold">Alamat
                                                            Domisili</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map-marked-alt"></i></span>
                                                            <textarea class="form-control @error('DomisiliKlg') is-invalid @enderror" id="DomisiliKlg" name="DomisiliKlg"
                                                                rows="3">{{ old('DomisiliKlg', $keluargaKaryawan->DomisiliKlg) }}</textarea>
                                                            @error('DomisiliKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="PekerjaanKlg"
                                                            class="form-label fw-bold">Pekerjaan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-briefcase"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('PekerjaanKlg') is-invalid @enderror"
                                                                id="PekerjaanKlg" name="PekerjaanKlg"
                                                                value="{{ old('PekerjaanKlg', $keluargaKaryawan->PekerjaanKlg) }}">
                                                            @error('PekerjaanKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="WargaNegaraKlg" class="form-label fw-bold">Warga
                                                            Negara</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-flag"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('WargaNegaraKlg') is-invalid @enderror"
                                                                id="WargaNegaraKlg" name="WargaNegaraKlg"
                                                                value="{{ old('WargaNegaraKlg', $keluargaKaryawan->WargaNegaraKlg) }}">
                                                            @error('WargaNegaraKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontak -->
                                <div class="tab-pane fade" id="info-kontak" role="tabpanel"
                                    aria-labelledby="info-kontak-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Informasi Kontak</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="Telpon1Klg" class="form-label fw-bold">Telepon
                                                            Utama</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-phone"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('Telpon1Klg') is-invalid @enderror"
                                                                id="Telpon1Klg" name="Telpon1Klg"
                                                                value="{{ old('Telpon1Klg', $keluargaKaryawan->Telpon1Klg) }}">
                                                            @error('Telpon1Klg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="Telpon2Klg" class="form-label fw-bold">Telepon
                                                            Alternatif</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-phone-alt"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('Telpon2Klg') is-invalid @enderror"
                                                                id="Telpon2Klg" name="Telpon2Klg"
                                                                value="{{ old('Telpon2Klg', $keluargaKaryawan->Telpon2Klg) }}">
                                                            @error('Telpon2Klg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="EmailKlg" class="form-label fw-bold">Email</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-envelope"></i></span>
                                                            <input type="email"
                                                                class="form-control @error('EmailKlg') is-invalid @enderror"
                                                                id="EmailKlg" name="EmailKlg"
                                                                value="{{ old('EmailKlg', $keluargaKaryawan->EmailKlg) }}">
                                                            @error('EmailKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-text text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>Format Email:
                                                            testing@gmail.com
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="InstagramKlg"
                                                            class="form-label fw-bold">Instagram</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fab fa-instagram"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('InstagramKlg') is-invalid @enderror"
                                                                id="InstagramKlg" name="InstagramKlg"
                                                                value="{{ old('InstagramKlg', $keluargaKaryawan->InstagramKlg) }}">
                                                            @error('InstagramKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle me-1"></i>Contoh: @username
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pendidikan -->
                                <div class="tab-pane fade" id="info-pendidikan" role="tabpanel"
                                    aria-labelledby="info-pendidikan-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Informasi
                                                Pendidikan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="PendidikanTrhKlg"
                                                            class="form-label fw-bold">Pendidikan
                                                            Terakhir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-graduation-cap"></i></span>
                                                            <select
                                                                class="form-select @error('PendidikanTrhKlg') is-invalid @enderror"
                                                                id="PendidikanTrhKlg" name="PendidikanTrhKlg">
                                                                <option value="" disabled>Pilih Pendidikan
                                                                </option>
                                                                @foreach ($pendidikanOptions as $value => $label)
                                                                    <option value="{{ $value }}"
                                                                        {{ old('PendidikanTrhKlg', $keluargaKaryawan->PendidikanTrhKlg) == $value ? 'selected' : '' }}>
                                                                        {{ $label }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('PendidikanTrhKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="JurusanPdkKlg"
                                                            class="form-label fw-bold">Jurusan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-book"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('JurusanPdkKlg') is-invalid @enderror"
                                                                id="JurusanPdkKlg" name="JurusanPdkKlg"
                                                                value="{{ old('JurusanPdkKlg', $keluargaKaryawan->JurusanPdkKlg) }}">
                                                            @error('JurusanPdkKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="InstitusiPdkKlg"
                                                            class="form-label fw-bold">Institusi</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-university"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('InstitusiPdkKlg') is-invalid @enderror"
                                                                id="InstitusiPdkKlg" name="InstitusiPdkKlg"
                                                                value="{{ old('InstitusiPdkKlg', $keluargaKaryawan->InstitusiPdkKlg) }}">
                                                            @error('InstitusiPdkKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="TahunLlsKlg" class="form-label fw-bold">Tahun
                                                            Lulus</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-check"></i></span>
                                                            <input type="number"
                                                                class="form-control @error('TahunLlsKlg') is-invalid @enderror"
                                                                id="TahunLlsKlg" name="TahunLlsKlg"
                                                                value="{{ old('TahunLlsKlg', $keluargaKaryawan->TahunLlsKlg) }}"
                                                                min="1900" max="{{ date('Y') }}">
                                                            @error('TahunLlsKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="GelarPdkKlg" class="form-label fw-bold">Gelar</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-award"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('GelarPdkKlg') is-invalid @enderror"
                                                        id="GelarPdkKlg" name="GelarPdkKlg"
                                                        value="{{ old('GelarPdkKlg', $keluargaKaryawan->GelarPdkKlg) }}">
                                                    @error('GelarPdkKlg')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>Contoh: S.Kom, S.E., dll.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol aksi di bagian bawah form -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" id="prevTabBtn" class="btn btn-secondary" style="display: none;">
                                    <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                </button>
                                <div class="ms-auto">
                                    <button type="button" id="nextTabBtn" class="btn btn-primary me-2">
                                        Selanjutnya <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                    <button type="button" id="submitBtn" class="btn btn-success"
                                        style="display: none;">
                                        <i class="fas fa-save me-1"></i> Update Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #02786e">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Update Data</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin memperbarui data keluarga karyawan ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Update</button>
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

        .form-text {
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #6c757d;
        }

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

        /* Custom styling for date inputs */
        input[type="date"] {
            padding: 0.375rem 0.75rem;
            font-family: inherit;
        }

        /* Validation styles */
        .was-validated .form-control:invalid,
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .was-validated .form-select:invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            padding-right: 4.125rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-position: right 0.75rem center, center right 2.25rem;
            background-size: 16px 12px, calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        /* Age info styles */
        #usiaInfo {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        #usiaInfo.text-danger {
            color: #dc3545 !important;
        }

        #usiaInfo.text-success {
            color: #198754 !important;
        }

        #usiaInfo.text-primary {
            color: #0d6efd !important;
        }

        #usiaInfo.text-warning {
            color: #ffc107 !important;
        }

        /* Styling for employee photo */
        #fotoKaryawan {
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
            object-fit: cover;
        }

        #fotoKaryawan:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Add a subtle loading animation */
        .img-loading {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.6;
            }
        }

        /* Styling for validation alert */
        #validationAlert {
            display: none;
            margin-bottom: 20px;
        }

        #validationMessages {
            margin-top: 10px;
        }

        /* Custom Select Styles - Replacement for Select2 */
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
            // Custom Select Implementation
            const searchInput = document.getElementById('karyawanSearch');
            const filterInput = document.getElementById('karyawanFilterInput');
            const dropdown = document.getElementById('karyawanDropdown');
            const hiddenInput = document.getElementById('IdKodeA04');
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

            // Validate custom select
            function validateCustomSelect() {
                if (hiddenInput.hasAttribute('required') && !hiddenInput.value) {
                    customSelectContainer.classList.add('is-invalid');
                    return false;
                } else {
                    customSelectContainer.classList.remove('is-invalid');
                    return true;
                }
            }

            // Form validation variables
            const form = document.getElementById('keluargaForm');
            const validationAlert = document.getElementById('validationAlert');
            const validationMessages = document.getElementById('validationMessages');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            const confirmSubmitBtn = document.getElementById('confirmSubmit');

            // Age calculation function
            const tanggalLahirInput = document.getElementById('TanggalLhrKlg');
            const usiaInfo = document.getElementById('usiaInfo');

            function calculateAge(birthDate) {
                const today = new Date();
                const birthDateObj = new Date(birthDate);

                let age = today.getFullYear() - birthDateObj.getFullYear();
                const monthDiff = today.getMonth() - birthDateObj.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
                    age--;
                }

                return age;
            }

            function updateAge() {
                if (tanggalLahirInput.value) {
                    const age = calculateAge(tanggalLahirInput.value);
                    usiaInfo.innerHTML =
                        `<i class="fas fa-info-circle me-1"></i>Usia: <strong>${age} tahun</strong>`;

                    // Add color coding for age
                    if (age < 17) {
                        usiaInfo.classList.add('text-danger');
                        usiaInfo.classList.remove('text-muted', 'text-success', 'text-warning', 'text-primary');
                    } else if (age >= 17 && age < 25) {
                        usiaInfo.classList.add('text-success');
                        usiaInfo.classList.remove('text-muted', 'text-danger', 'text-warning', 'text-primary');
                    } else if (age >= 25 && age < 55) {
                        usiaInfo.classList.add('text-primary');
                        usiaInfo.classList.remove('text-muted', 'text-danger', 'text-success', 'text-warning');
                    } else {
                        usiaInfo.classList.add('text-warning');
                        usiaInfo.classList.remove('text-muted', 'text-danger', 'text-success', 'text-primary');
                    }
                } else {
                    usiaInfo.innerHTML = '';
                    usiaInfo.className = 'form-text text-muted mt-1';
                }
            }

            if (tanggalLahirInput) {
                tanggalLahirInput.addEventListener('change', updateAge);
                tanggalLahirInput.addEventListener('input', updateAge);

                // Run once on page load if a date is already set
                if (tanggalLahirInput.value) {
                    updateAge();
                }
            }

            // Fetch and display employee data when selected
            hiddenInput.addEventListener('change', function() {
                const karyawanId = this.value;
                if (!karyawanId) {
                    // Clear all fields if no employee selected
                    clearEmployeeData();
                    return;
                }

                // Show loading indicators in all employee info fields
                document.getElementById('NikKtpUtama').value = 'Loading...';
                document.getElementById('NrkKry').value = 'Loading...';
                document.getElementById('TglMsk').value = 'Loading...';
                document.getElementById('MasaKerja').value = 'Loading...';
                document.getElementById('StsKaryawan').value = 'Loading...';
                document.getElementById('StsKawinKry').value = 'Loading...';
                document.getElementById('StsKeluargaKryInfo').value = 'Loading...';
                document.getElementById('JumlahAnakKry').value = 'Loading...';
                document.getElementById('UmurKry').value = 'Loading...';
                document.getElementById('KotaKry').value = 'Loading...';
                document.getElementById('ProvinsiKry').value = 'Loading...';
                document.getElementById('AlamatKry').value = 'Loading...';

                // Set default photo while loading and add loading animation
                const photoElement = document.getElementById('fotoKaryawan');
                photoElement.src = "{{ asset('images/default-user.png') }}";
                photoElement.classList.add('img-loading');

                // Use the correct URL format with the karyawanId
                fetch(`/keluarga-karyawan/get-karyawan-detail/${karyawanId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Employee data received:', data);

                        // Fill in employee info fields
                        document.getElementById('NikKtpUtama').value = data.NikKtp || '';
                        document.getElementById('NrkKry').value = data.NrkKry || '';
                        document.getElementById('TglMsk').value = data.formatted_tgl_msk || '';
                        document.getElementById('MasaKerja').value = data.masa_kerja || '';
                        document.getElementById('StsKaryawan').value = data.StsKaryawan || '';
                        document.getElementById('StsKawinKry').value = data.StsKawinKry || '';
                        document.getElementById('StsKeluargaKryInfo').value = data.StsKeluargaKry || '';
                        document.getElementById('JumlahAnakKry').value = data.JumlahAnakKry || '0';
                        document.getElementById('UmurKry').value = data.umur || '';
                        document.getElementById('KotaKry').value = data.KotaKry || '';
                        document.getElementById('ProvinsiKry').value = data.ProvinsiKry || '';
                        document.getElementById('AlamatKry').value = data.AlamatKry || '';

                        // Set employee photo and remove loading animation
                        photoElement.classList.remove('img-loading');
                        if (data.FotoKry) {
                            photoElement.src = data.FotoKry;
                        } else {
                            photoElement.src = "{{ asset('images/default-user.png') }}";
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching employee data:', error);

                        // Clear all fields on error
                        clearEmployeeData();

                        // Remove loading animation from photo
                        document.getElementById('fotoKaryawan').classList.remove('img-loading');

                        // Show error alert
                        alert('Gagal memuat data karyawan. Silakan coba lagi.');
                    });
            });

            // Load employee data on page load if ID is present (especially for edit form)
            const initialKaryawanId = hiddenInput.value;
            if (initialKaryawanId) {
                // Trigger the change event to load data
                const event = new Event('change');
                hiddenInput.dispatchEvent(event);
            }

            // Function to clear all employee data fields
            function clearEmployeeData() {
                document.getElementById('NikKtpUtama').value = '';
                document.getElementById('NrkKry').value = '';
                document.getElementById('TglMsk').value = '';
                document.getElementById('MasaKerja').value = '';
                document.getElementById('StsKaryawan').value = '';
                document.getElementById('StsKawinKry').value = '';
                document.getElementById('StsKeluargaKryInfo').value = '';
                document.getElementById('JumlahAnakKry').value = '';
                document.getElementById('UmurKry').value = '';
                document.getElementById('KotaKry').value = '';
                document.getElementById('ProvinsiKry').value = '';
                document.getElementById('AlamatKry').value = '';
                document.getElementById('fotoKaryawan').src = "{{ asset('images/default-user.png') }}";
                document.getElementById('fotoKaryawan').classList.remove('img-loading');
            }

            // Tab navigation variables
            const tabs = ['info-utama', 'info-pribadi', 'info-pendidikan', 'info-kontak'];
            let currentTabIndex = 0;

            const prevTabBtn = document.getElementById('prevTabBtn');
            const nextTabBtn = document.getElementById('nextTabBtn');
            const submitBtn = document.getElementById('submitBtn');

            // Bootstrap Tab objects
            const tabElements = [];
            tabs.forEach(tabId => {
                const tabElement = document.getElementById(`${tabId}-tab`);
                if (tabElement) {
                    tabElements.push(new bootstrap.Tab(tabElement));
                }
            });

            // Function to show specific tab
            function showTab(tabIndex) {
                // Activate the tab using Bootstrap's API
                if (tabElements[tabIndex]) {
                    tabElements[tabIndex].show();
                }

                // Update buttons state
                prevTabBtn.style.display = tabIndex > 0 ? 'block' : 'none';

                if (tabIndex === tabs.length - 1) {
                    nextTabBtn.style.display = 'none';
                    submitBtn.style.display = 'block';
                } else {
                    nextTabBtn.style.display = 'block';
                    submitBtn.style.display = 'none';
                }

                currentTabIndex = tabIndex;
            }

            // Initialize with first tab
            showTab(0);

            // Previous button click
            prevTabBtn.addEventListener('click', function() {
                if (currentTabIndex > 0) {
                    showTab(currentTabIndex - 1);
                }
            });

            // Next button click
            nextTabBtn.addEventListener('click', function() {
                // Validate current tab fields
                let isValid = true;

                // Check validation for the current tab
                const currentTabElement = document.getElementById(tabs[currentTabIndex]);
                const requiredFields = currentTabElement.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value) {
                        isValid = false;
                        field.classList.add('is-invalid');

                        // Handle custom select validation
                        if (field.id === 'IdKodeA04') {
                            customSelectContainer.classList.add('is-invalid');
                        }

                        // Create error message if it doesn't exist
                        if (!field.nextElementSibling || !field.nextElementSibling.classList
                            .contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Field ini wajib diisi';
                            field.parentNode.insertBefore(feedback, field.nextElementSibling);
                        }
                    } else {
                        field.classList.remove('is-invalid');

                        // Handle custom select validation
                        if (field.id === 'IdKodeA04') {
                            customSelectContainer.classList.remove('is-invalid');
                        }
                    }
                });

                if (isValid && currentTabIndex < tabs.length - 1) {
                    showTab(currentTabIndex + 1);
                } else if (!isValid) {
                    // Focus on first invalid field
                    const firstInvalid = currentTabElement.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }
            });

            // Listen to Bootstrap's tab events to keep track of current tab
            document.querySelectorAll('button[data-bs-toggle="tab"]').forEach((tab, index) => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    currentTabIndex = index;

                    // Update navigation buttons state
                    prevTabBtn.style.display = currentTabIndex > 0 ? 'block' : 'none';

                    if (currentTabIndex === tabs.length - 1) {
                        nextTabBtn.style.display = 'none';
                        submitBtn.style.display = 'block';
                    } else {
                        nextTabBtn.style.display = 'block';
                        submitBtn.style.display = 'none';
                    }
                });
            });

            // Submit button click - Show confirmation modal
            submitBtn.addEventListener('click', function(event) {
                // Validate all required fields before showing confirmation
                let isValid = true;
                const invalidFieldsList = [];

                // Check all required fields across all tabs
                document.querySelectorAll('[required]').forEach(function(input) {
                    if (!input.value) {
                        isValid = false;
                        input.classList.add('is-invalid');

                        // Handle custom select validation
                        if (input.id === 'IdKodeA04') {
                            customSelectContainer.classList.add('is-invalid');
                        }

                        // Get field label for error message
                        let fieldName = "";
                        const label = input.closest('.form-group').querySelector('label');
                        if (label) {
                            fieldName = label.textContent.replace('*', '').trim();
                        }

                        invalidFieldsList.push(fieldName);

                        // Create error message if it doesn't exist
                        if (!input.nextElementSibling || !input.nextElementSibling.classList
                            .contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Field ini wajib diisi';
                            input.parentNode.insertBefore(feedback, input.nextElementSibling);
                        }
                    } else {
                        input.classList.remove('is-invalid');

                        // Handle custom select validation
                        if (input.id === 'IdKodeA04') {
                            customSelectContainer.classList.remove('is-invalid');
                        }
                    }
                });

                if (!isValid) {
                    // Show validation error alert
                    validationMessages.innerHTML = '';
                    invalidFieldsList.forEach(function(field) {
                        const li = document.createElement('li');
                        li.textContent = field;
                        validationMessages.appendChild(li);
                    });

                    validationAlert.style.display = 'block';

                    // Find tab with first error and show it
                    for (let i = 0; i < tabs.length; i++) {
                        const tabElement = document.getElementById(tabs[i]);
                        const invalidField = tabElement.querySelector('.is-invalid');

                        if (invalidField) {
                            showTab(i);
                            invalidField.focus();
                            break;
                        }
                    }

                    // Scroll to the validation alert
                    validationAlert.scrollIntoView({
                        behavior: 'smooth'
                    });
                } else {
                    // Hide validation alert if shown previously
                    validationAlert.style.display = 'none';

                    // Show confirmation modal
                    confirmationModal.show();
                }
            });

            // Confirm submit button click - Submit the form
            confirmSubmitBtn.addEventListener('click', function() {
                form.submit();
            });

            // Remove invalid class when input changes
            document.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (this.hasAttribute('required') && this.value) {
                        this.classList.remove('is-invalid');

                        // Handle custom select validation
                        if (this.id === 'IdKodeA04') {
                            customSelectContainer.classList.remove('is-invalid');
                        }
                    }

                    // Check if all invalid fields are now valid
                    const invalidFields = document.querySelectorAll('.is-invalid');
                    if (invalidFields.length === 0) {
                        validationAlert.style.display = 'none';
                    }
                });

                input.addEventListener('change', function() {
                    if (this.hasAttribute('required') && this.value) {
                        this.classList.remove('is-invalid');

                        // Handle custom select validation
                        if (this.id === 'IdKodeA04') {
                            customSelectContainer.classList.remove('is-invalid');
                        }
                    }

                    // Check if all invalid fields are now valid
                    const invalidFields = document.querySelectorAll('.is-invalid');
                    if (invalidFields.length === 0) {
                        validationAlert.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endpush
