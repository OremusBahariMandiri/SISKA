@extends('layouts.app')

@section('title', 'Tambah Keluarga Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Data Keluarga Karyawan</span>
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

                        <form action="{{ route('keluarga-karyawan.store') }}" method="POST" id="keluargaForm">
                            @csrf
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $newId) }}" hidden readonly>

                            <!-- Nav tabs for form sections -->
                            <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="info-utama-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-utama" type="button" role="tab" aria-controls="info-utama"
                                        aria-selected="true">
                                        <i class="fas fa-user me-1"></i> Informasi Utama
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-pribadi-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-pribadi" type="button" role="tab"
                                        aria-controls="info-pribadi" aria-selected="false">
                                        <i class="fas fa-id-card me-1"></i> Informasi Pribadi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-kontak-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-kontak" type="button" role="tab"
                                        aria-controls="info-kontak" aria-selected="false">
                                        <i class="fas fa-phone me-1"></i> Kontak
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-pendidikan-tab" data-bs-toggle="tab"
                                        data-bs-target="#info-pendidikan" type="button" role="tab"
                                        aria-controls="info-pendidikan" aria-selected="false">
                                        <i class="fas fa-graduation-cap me-1"></i> Pendidikan
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
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Utama</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="IdKodeA04" class="form-label fw-bold">Karyawan <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                            <select class="form-select @error('IdKodeA04') is-invalid @enderror"
                                                                id="IdKodeA04" name="IdKodeA04" required>
                                                                <option value="" selected disabled>Pilih Karyawan</option>
                                                                @foreach ($karyawans as $karyawan)
                                                                    <option value="{{ $karyawan->IdKode }}"
                                                                        {{ old('IdKodeA04') == $karyawan->IdKode ? 'selected' : '' }}>
                                                                        {{ $karyawan->NamaKry }} - {{ $karyawan->NikKry }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('IdKodeA04')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="StsKeluargaKry" class="form-label fw-bold">Status Keluarga <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                            <select class="form-select @error('StsKeluargaKry') is-invalid @enderror"
                                                                id="StsKeluargaKry" name="StsKeluargaKry" required>
                                                                <option value="" selected disabled>Pilih Status</option>
                                                                @foreach ($statusKeluargaOptions as $status)
                                                                    <option value="{{ $status }}"
                                                                        {{ old('StsKeluargaKry') == $status ? 'selected' : '' }}>
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
                                                        <label for="NamaKlg" class="form-label fw-bold">Nama <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('NamaKlg') is-invalid @enderror"
                                                                id="NamaKlg" name="NamaKlg" value="{{ old('NamaKlg') }}"
                                                                required>
                                                            @error('NamaKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="KetKeluargaKry" class="form-label fw-bold">Keterangan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('KetKeluargaKry') is-invalid @enderror"
                                                                id="KetKeluargaKry" name="KetKeluargaKry"
                                                                value="{{ old('KetKeluargaKry') }}">
                                                            @error('KetKeluargaKry')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle me-1"></i>Misalnya: Anak ke-1, dll.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Pribadi -->
                                <div class="tab-pane fade" id="info-pribadi" role="tabpanel" aria-labelledby="info-pribadi-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Informasi Pribadi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="NikKlg" class="form-label fw-bold">NIK</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('NikKlg') is-invalid @enderror"
                                                                id="NikKlg" name="NikKlg" value="{{ old('NikKlg') }}">
                                                            @error('NikKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="SexKlg" class="form-label fw-bold">Jenis Kelamin <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                                            <select class="form-select @error('SexKlg') is-invalid @enderror"
                                                                id="SexKlg" name="SexKlg" required>
                                                                <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                                                @foreach ($jenisKelaminOptions as $value => $label)
                                                                    <option value="{{ $value }}"
                                                                        {{ old('SexKlg') == $value ? 'selected' : '' }}>
                                                                        {{ $label }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('SexKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="TempatLhrKlg" class="form-label fw-bold">Tempat Lahir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('TempatLhrKlg') is-invalid @enderror"
                                                                id="TempatLhrKlg" name="TempatLhrKlg"
                                                                value="{{ old('TempatLhrKlg') }}">
                                                            @error('TempatLhrKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="TanggalLhrKlg" class="form-label fw-bold">Tanggal Lahir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                            <input type="date"
                                                                class="form-control @error('TanggalLhrKlg') is-invalid @enderror"
                                                                id="TanggalLhrKlg" name="TanggalLhrKlg"
                                                                value="{{ old('TanggalLhrKlg') }}">
                                                            @error('TanggalLhrKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="AgamaKlg" class="form-label fw-bold">Agama</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-pray"></i></span>
                                                            <select class="form-select @error('AgamaKlg') is-invalid @enderror"
                                                                id="AgamaKlg" name="AgamaKlg">
                                                                <option value="" selected disabled>Pilih Agama</option>
                                                                @foreach ($agamaOptions as $agama)
                                                                    <option value="{{ $agama }}"
                                                                        {{ old('AgamaKlg') == $agama ? 'selected' : '' }}>
                                                                        {{ $agama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('AgamaKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="StsKawinKlg" class="form-label fw-bold">Status Kawin</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-ring"></i></span>
                                                            <select class="form-select @error('StsKawinKlg') is-invalid @enderror"
                                                                id="StsKawinKlg" name="StsKawinKlg">
                                                                <option value="" selected disabled>Pilih Status</option>
                                                                @foreach ($statusKawinOptions as $status)
                                                                    <option value="{{ $status }}"
                                                                        {{ old('StsKawinKlg') == $status ? 'selected' : '' }}>
                                                                        {{ $status }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('StsKawinKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="PekerjaanKlg" class="form-label fw-bold">Pekerjaan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('PekerjaanKlg') is-invalid @enderror"
                                                                id="PekerjaanKlg" name="PekerjaanKlg"
                                                                value="{{ old('PekerjaanKlg') }}">
                                                            @error('PekerjaanKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="WargaNegaraKlg" class="form-label fw-bold">Warga Negara</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('WargaNegaraKlg') is-invalid @enderror"
                                                                id="WargaNegaraKlg" name="WargaNegaraKlg"
                                                                value="{{ old('WargaNegaraKlg', 'Indonesia') }}">
                                                            @error('WargaNegaraKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="AlamatKtpKlg" class="form-label fw-bold">Alamat KTP</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                                    <textarea
                                                        class="form-control @error('AlamatKtpKlg') is-invalid @enderror"
                                                        id="AlamatKtpKlg" name="AlamatKtpKlg"
                                                        rows="3">{{ old('AlamatKtpKlg') }}</textarea>
                                                    @error('AlamatKtpKlg')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="DomisiliKlg" class="form-label fw-bold">Alamat Domisili</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                                    <textarea
                                                        class="form-control @error('DomisiliKlg') is-invalid @enderror"
                                                        id="DomisiliKlg" name="DomisiliKlg"
                                                        rows="3">{{ old('DomisiliKlg') }}</textarea>
                                                    @error('DomisiliKlg')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontak -->
                                <div class="tab-pane fade" id="info-kontak" role="tabpanel" aria-labelledby="info-kontak-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Informasi Kontak</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="Telpon1Klg" class="form-label fw-bold">Telepon Utama</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('Telpon1Klg') is-invalid @enderror"
                                                                id="Telpon1Klg" name="Telpon1Klg"
                                                                value="{{ old('Telpon1Klg') }}">
                                                            @error('Telpon1Klg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="Telpon2Klg" class="form-label fw-bold">Telepon Alternatif</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('Telpon2Klg') is-invalid @enderror"
                                                                id="Telpon2Klg" name="Telpon2Klg"
                                                                value="{{ old('Telpon2Klg') }}">
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
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                            <input type="email"
                                                                class="form-control @error('EmailKlg') is-invalid @enderror"
                                                                id="EmailKlg" name="EmailKlg" value="{{ old('EmailKlg') }}">
                                                            @error('EmailKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="InstagramKlg" class="form-label fw-bold">Instagram</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('InstagramKlg') is-invalid @enderror"
                                                                id="InstagramKlg" name="InstagramKlg"
                                                                value="{{ old('InstagramKlg') }}">
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
                                            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="PendidikanTrhKlg" class="form-label fw-bold">Pendidikan
                                                            Terakhir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-graduation-cap"></i></span>
                                                            <select
                                                                class="form-select @error('PendidikanTrhKlg') is-invalid @enderror"
                                                                id="PendidikanTrhKlg" name="PendidikanTrhKlg">
                                                                <option value="" selected disabled>Pilih Pendidikan</option>
                                                                @foreach ($pendidikanOptions as $value => $label)
                                                                    <option value="{{ $value }}"
                                                                        {{ old('PendidikanTrhKlg') == $value ? 'selected' : '' }}>
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
                                                        <label for="JurusanPdkKlg" class="form-label fw-bold">Jurusan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('JurusanPdkKlg') is-invalid @enderror"
                                                                id="JurusanPdkKlg" name="JurusanPdkKlg"
                                                                value="{{ old('JurusanPdkKlg') }}">
                                                            @error('JurusanPdkKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="InstitusiPdkKlg" class="form-label fw-bold">Institusi</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-university"></i></span>
                                                            <input type="text"
                                                                class="form-control @error('InstitusiPdkKlg') is-invalid @enderror"
                                                                id="InstitusiPdkKlg" name="InstitusiPdkKlg"
                                                                value="{{ old('InstitusiPdkKlg') }}">
                                                            @error('InstitusiPdkKlg')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="TahunLlsKlg" class="form-label fw-bold">Tahun Lulus</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                                            <input type="number"
                                                                class="form-control @error('TahunLlsKlg') is-invalid @enderror"
                                                                id="TahunLlsKlg" name="TahunLlsKlg"
                                                                value="{{ old('TahunLlsKlg') }}" min="1900"
                                                                max="{{ date('Y') }}">
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
                                                        value="{{ old('GelarPdkKlg') }}">
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
                                    <button type="submit" id="submitBtn" class="btn btn-success" style="display: none;">
                                        <i class="fas fa-save me-1"></i> Simpan Data
                                    </button>
                                </div>
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Form validation with visual feedback
    const form = document.getElementById('keluargaForm');

    // Tab navigation variables
    const tabs = ['info-utama', 'info-pribadi', 'info-kontak', 'info-pendidikan'];
    let currentTabIndex = 0;

    const prevTabBtn = document.getElementById('prevTabBtn');
    const nextTabBtn = document.getElementById('nextTabBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Bootstrap Tab objects
    const tabElements = [];
    tabs.forEach(tabId => {
        tabElements.push(new bootstrap.Tab(document.getElementById(`${tabId}-tab`)));
    });

    // Function to show specific tab
    function showTab(tabIndex) {
        // Activate the tab using Bootstrap's API
        tabElements[tabIndex].show();

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

                // Create error message if it doesn't exist
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Field ini wajib diisi';
                    field.parentNode.insertBefore(feedback, field.nextElementSibling);
                }
            } else {
                field.classList.remove('is-invalid');
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
        tab.addEventListener('shown.bs.tab', function (event) {
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

    // Form submission
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            // Highlight missing required fields across all tabs
            document.querySelectorAll('[required]').forEach(function(input) {
                if (!input.value) {
                    input.classList.add('is-invalid');

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
                }
            });

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
        }
    });

    // Remove invalid class when input changes
    document.querySelectorAll('input, select, textarea').forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.hasAttribute('required') && this.value) {
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('change', function() {
            if (this.hasAttribute('required') && this.value) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
    </script>
@endpush