@extends('layouts.app')

@section('title', 'Detail Keluarga Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-user-friends me-2"></i>Detail Keluarga Karyawan</span>
                        <div>
                            <a href="{{ route('keluarga-karyawan.edit', $keluargaKaryawan->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('keluarga-karyawan.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Nav tabs for form sections -->
                        <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-utama-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-utama" type="button" role="tab" aria-controls="info-utama"
                                    aria-selected="true">
                                    <i class="fas fa-user me-1"></i> Informasi Karyawan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="info-pribadi-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-pribadi" type="button" role="tab" aria-controls="info-pribadi"
                                    aria-selected="false">
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
                                    data-bs-target="#info-kontak" type="button" role="tab" aria-controls="info-kontak"
                                    aria-selected="false">
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
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Karyawan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Add photo container at the top of the form -->
                                            <div class="col-md-12 mb-4 text-center">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold mb-2">Foto Karyawan</label>
                                                    <div class="mx-auto" style="width: 150px; height: 180px;">
                                                        <img id="fotoKaryawan" src="{{ asset('images/default-user.png') }}"
                                                            class="img-thumbnail shadow-sm" alt="Foto Karyawan"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Karyawan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->karyawan->NamaKry ?? '-' }} - {{ $keluargaKaryawan->karyawan->NrkKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">NIK KTP</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                            <div class="form-control" id="NikKtpUtama">
                                                                {{ $keluargaKaryawan->karyawan->NikKtp ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">NRK Karyawan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                                            <div class="form-control" id="NrkKry">
                                                                {{ $keluargaKaryawan->karyawan->NrkKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tanggal Masuk</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                                                            <div class="form-control" id="TglMsk">
                                                                {{ $keluargaKaryawan->karyawan->TglMsk ? date('d/m/Y', strtotime($keluargaKaryawan->karyawan->TglMsk)) : '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Masa Kerja</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-business-time"></i></span>
                                                            <div class="form-control" id="MasaKerja">
                                                                {{ $keluargaKaryawan->karyawan->masa_kerja ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Karyawan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                            <div class="form-control" id="StsKaryawan">
                                                                {{ $keluargaKaryawan->karyawan->StsKaryawan ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Perkawinan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-ring"></i></span>
                                                            <div class="form-control" id="StsKawinKry">
                                                                {{ $keluargaKaryawan->karyawan->StsKawinKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Keluarga</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                            <div class="form-control" id="StsKeluargaKryInfo">
                                                                {{ $keluargaKaryawan->karyawan->StsKeluargaKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jumlah Anak</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-child"></i></span>
                                                            <div class="form-control" id="JumlahAnakKry">
                                                                {{ $keluargaKaryawan->karyawan->JumlahAnakKry ?? '0' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Umur</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                                            <div class="form-control" id="UmurKry">
                                                                {{ $keluargaKaryawan->karyawan->umur ? $keluargaKaryawan->karyawan->umur . ' tahun' : '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Kota</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                            <div class="form-control" id="KotaKry">
                                                                {{ $keluargaKaryawan->karyawan->KotaKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Provinsi</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                                                            <div class="form-control" id="ProvinsiKry">
                                                                {{ $keluargaKaryawan->karyawan->ProvinsiKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Alamat</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                                                            <div class="form-control" id="AlamatKry">
                                                                {{ $keluargaKaryawan->karyawan->alamat_lengkap ?? '-' }}
                                                            </div>
                                                        </div>
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
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">NIK</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->NikKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Nama</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->NamaKlg }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tempat Lahir</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->TempatLhrKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tanggal Lahir</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->TanggalLhrKlg ? date('d/m/Y', strtotime($keluargaKaryawan->TanggalLhrKlg)) : '-' }}
                                                            </div>
                                                        </div>
                                                        <div class="age-info mt-2">
                                                            @if($keluargaKaryawan->umur)
                                                                <i class="fas fa-info-circle me-1"></i>Usia:
                                                                <strong class="{{ $keluargaKaryawan->umur < 17 ? 'text-danger' : ($keluargaKaryawan->umur < 25 ? 'text-success' : ($keluargaKaryawan->umur < 55 ? 'text-primary' : 'text-warning')) }}">
                                                                    {{ $keluargaKaryawan->umur }} tahun
                                                                </strong>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jenis Kelamin</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                                            <div class="form-control">
                                                                @if($keluargaKaryawan->SexKlg == 'L')
                                                                    LAKI-LAKI
                                                                @elseif($keluargaKaryawan->SexKlg == 'P')
                                                                    PEREMPUAN
                                                                @else
                                                                    {{ $keluargaKaryawan->SexKlg ?? '-' }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Agama</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-pray"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->AgamaKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Keluarga</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->StsKeluargaKry }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Kawin</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-ring"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->StsKawinKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Keterangan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->KetKeluargaKry ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Alamat KTP</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->AlamatKtpKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Alamat Domisili</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->DomisiliKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Pekerjaan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->PekerjaanKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Warga Negara</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->WargaNegaraKlg ?? 'INDONESIA' }}
                                                            </div>
                                                        </div>
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
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Telepon Utama</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->Telpon1Klg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Telepon Alternatif</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->Telpon2Klg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Email</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->EmailKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Instagram</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->InstagramKlg ?? '-' }}
                                                            </div>
                                                        </div>
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
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Pendidikan Terakhir</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->PendidikanTrhKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jurusan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->JurusanPdkKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Institusi</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-university"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->InstitusiPdkKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tahun Lulus</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->TahunLlsKlg ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Gelar</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-award"></i></span>
                                                            <div class="form-control">
                                                                {{ $keluargaKaryawan->GelarPdkKlg ?? '-' }}
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
            min-height: 38px;
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

        /* Age info styles */
        .age-info {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-success {
            color: #198754 !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .text-warning {
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
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab navigation
            const triggerTabList = [].slice.call(document.querySelectorAll('#formTabs button'));
            triggerTabList.forEach(function(triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);

                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });

            // Load employee photo
            const karyawanId = "{{ $keluargaKaryawan->IdKodeA04 }}";

            if (karyawanId) {
                // Set default photo while loading and add loading animation
                const photoElement = document.getElementById('fotoKaryawan');
                photoElement.classList.add('img-loading');

                // Fetch employee details to get the photo
                fetch(`/keluarga-karyawan/get-karyawan-detail/${karyawanId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
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
                        photoElement.classList.remove('img-loading');
                    });
            }
        });
    </script>
@endpush