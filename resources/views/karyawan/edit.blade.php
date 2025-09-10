@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Karyawan</span>
                        <a href="{{ route('karyawan.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" id="karyawanForm"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $karyawan->IdKode) }}" hidden readonly>

                            <!-- Nav tabs for form sections -->
                            <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="biodata-tab" data-bs-toggle="tab"
                                        data-bs-target="#biodata" type="button" role="tab" aria-controls="biodata"
                                        aria-selected="true">
                                        <i class="fas fa-id-card me-1"></i> Biodata
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="alamat-tab" data-bs-toggle="tab" data-bs-target="#alamat"
                                        type="button" role="tab" aria-controls="alamat" aria-selected="false">
                                        <i class="fas fa-home me-1"></i> Alamat
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab"
                                        data-bs-target="#pendidikan" type="button" role="tab"
                                        aria-controls="pendidikan" aria-selected="false">
                                        <i class="fas fa-graduation-cap me-1"></i> Pendidikan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tambahan-tab" data-bs-toggle="tab"
                                        data-bs-target="#tambahan" type="button" role="tab" aria-controls="tambahan"
                                        aria-selected="false">
                                        <i class="fas fa-info-circle me-1"></i> Informasi Tambahan
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Biodata Karyawan -->
                                <div class="tab-pane fade show active" id="biodata" role="tabpanel"
                                    aria-labelledby="biodata-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Biodata Karyawan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="NrkKry" class="form-label fw-bold">NRK <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-id-card"></i></span>
                                                            <input type="text" class="form-control" id="NrkKry"
                                                                name="NrkKry"
                                                                value="{{ old('NrkKry', $karyawan->NrkKry) }}" required>
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
                                                            <input type="date" class="form-control" id="TglMsk"
                                                                name="TglMsk"
                                                                value="{{ old('TglMsk', $karyawan->TglMsk ? $karyawan->TglMsk->format('Y-m-d') : '') }}">
                                                        </div>
                                                        <div id="masaKerjaInfo" class="form-text text-muted mt-1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="NikKtp" class="form-label fw-bold">NIK KTP <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-id-card"></i></span>
                                                            <input type="text" class="form-control" id="NikKtp"
                                                                name="NikKtp"
                                                                value="{{ old('NikKtp', $karyawan->NikKtp) }}"
                                                                minlength="16" maxlength="16" required>
                                                        </div>
                                                        <div class="form-text text-muted"><i
                                                                class="fas fa-info-circle me-1"></i>16 digit angka NIK KTP
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="NamaKry" class="form-label fw-bold">Nama <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user"></i></span>
                                                            <input type="text" class="form-control" id="NamaKry"
                                                                name="NamaKry"
                                                                value="{{ old('NamaKry', $karyawan->NamaKry) }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TempatLhrKry" class="form-label fw-bold">Tempat Lahir
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map-marker-alt"></i></span>
                                                            <input type="text" class="form-control" id="TempatLhrKry"
                                                                name="TempatLhrKry"
                                                                value="{{ old('TempatLhrKry', $karyawan->TempatLhrKry) }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TanggalLhrKry" class="form-label fw-bold">Tanggal
                                                            Lahir <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar"></i></span>
                                                            <input type="date" class="form-control" id="TanggalLhrKry"
                                                                name="TanggalLhrKry"
                                                                value="{{ old('TanggalLhrKry', $karyawan->TanggalLhrKry ? $karyawan->TanggalLhrKry->format('Y-m-d') : '') }}"
                                                                required>
                                                        </div>
                                                        <div id="usiaInfo" class="form-text text-muted mt-1"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="SexKry" class="form-label fw-bold">Jenis Kelamin
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-venus-mars"></i></span>
                                                            <select class="form-select" id="SexKry" name="SexKry"
                                                                required>
                                                                <option value="" disabled>Pilih Jenis Kelamin
                                                                </option>
                                                                <option value="LAKI-LAKI"
                                                                    {{ old('SexKry', $karyawan->SexKry) == 'LAKI-LAKI' ? 'selected' : '' }}>
                                                                    LAKI-LAKI</option>
                                                                <option value="PEREMPUAN"
                                                                    {{ old('SexKry', $karyawan->SexKry) == 'PEREMPUAN' ? 'selected' : '' }}>
                                                                    PEREMPUAN</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="AgamaKry" class="form-label fw-bold">Agama <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-pray"></i></span>
                                                            <select class="form-select" id="AgamaKry" name="AgamaKry"
                                                                required>
                                                                <option value="" disabled>Pilih Agama</option>
                                                                <option value="ISLAM"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'ISLAM' ? 'selected' : '' }}>
                                                                    ISLAM</option>
                                                                <option value="KRISTEN"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'KRISTEN' ? 'selected' : '' }}>
                                                                    KRISTEN</option>
                                                                <option value="KATOLIK"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'KATOLIK' ? 'selected' : '' }}>
                                                                    KATOLIK</option>
                                                                <option value="HINDU"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'HINDU' ? 'selected' : '' }}>
                                                                    HINDU</option>
                                                                <option value="BUDDHA"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'BUDDHA' ? 'selected' : '' }}>
                                                                    BUDDHA</option>
                                                                <option value="KONGHUCU"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'KONGHUCU' ? 'selected' : '' }}>
                                                                    KONGHUCU</option>
                                                                <option value="LAINYA"
                                                                    {{ old('AgamaKry', $karyawan->AgamaKry) == 'LAINYA' ? 'selected' : '' }}>
                                                                    LAINYA</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKawinKry" class="form-label fw-bold">Status
                                                            Perkawinan <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-ring"></i></span>
                                                            <select class="form-select" id="StsKawinKry"
                                                                name="StsKawinKry" required>
                                                                <option value="" disabled>Pilih Status</option>
                                                                <option value="BELUM KAWIN"
                                                                    {{ old('StsKawinKry', $karyawan->StsKawinKry) == 'BELUM KAWIN' ? 'selected' : '' }}>
                                                                    BELUM KAWIN</option>
                                                                <option value="KAWIN"
                                                                    {{ old('StsKawinKry', $karyawan->StsKawinKry) == 'KAWIN' ? 'selected' : '' }}>
                                                                    KAWIN</option>
                                                                <option value="CERAI HIDUP"
                                                                    {{ old('StsKawinKry', $karyawan->StsKawinKry) == 'CERAI HIDUP' ? 'selected' : '' }}>
                                                                    CERAI HIDUP</option>
                                                                <option value="CERAI MATI"
                                                                    {{ old('StsKawinKry', $karyawan->StsKawinKry) == 'CERAI MATI' ? 'selected' : '' }}>
                                                                    CERAI MATI</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKeluargaKry" class="form-label fw-bold">Status
                                                            Keluarga</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-users"></i></span>
                                                            <select class="form-select" id="StsKeluargaKry"
                                                                name="StsKeluargaKry">
                                                                <option value="" disabled>Pilih Status</option>
                                                                <option value="SUAMI"
                                                                    {{ old('StsKeluargaKry', $karyawan->StsKeluargaKry) == 'SUAMI' ? 'selected' : '' }}>
                                                                    SUAMI</option>
                                                                <option value="ISTRI"
                                                                    {{ old('StsKeluargaKry', $karyawan->StsKeluargaKry) == 'ISTRI' ? 'selected' : '' }}>
                                                                    ISTRI</option>
                                                                <option value="BAPAK"
                                                                    {{ old('StsKeluargaKry', $karyawan->StsKeluargaKry) == 'BAPAK' ? 'selected' : '' }}>
                                                                    BAPAK</option>
                                                                <option value="IBU"
                                                                    {{ old('StsKeluargaKry', $karyawan->StsKeluargaKry) == 'IBU' ? 'selected' : '' }}>
                                                                    IBU</option>
                                                                <option value="ANAK"
                                                                    {{ old('StsKeluargaKry', $karyawan->StsKeluargaKry) == 'ANAK' ? 'selected' : '' }}>
                                                                    ANAK</option>
                                                            </select>
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
                                                            <input type="number" class="form-control" id="JumlahAnakKry"
                                                                name="JumlahAnakKry"
                                                                value="{{ old('JumlahAnakKry', $karyawan->JumlahAnakKry) }}"
                                                                min="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="PekerjaanKry"
                                                            class="form-label fw-bold">Pekerjaan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-briefcase"></i></span>
                                                            <input type="text" class="form-control" id="PekerjaanKry"
                                                                name="PekerjaanKry"
                                                                value="{{ old('PekerjaanKry', $karyawan->PekerjaanKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="WargaNegaraKry"
                                                            class="form-label fw-bold">Kewarganegaraan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-flag"></i></span>
                                                            <input type="text" class="form-control"
                                                                id="WargaNegaraKry" name="WargaNegaraKry"
                                                                value="{{ old('WargaNegaraKry', $karyawan->WargaNegaraKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="Telpon1Kry" class="form-label fw-bold">Telepon 1 <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-phone"></i></span>
                                                            <input type="text" class="form-control" id="Telpon1Kry"
                                                                name="Telpon1Kry"
                                                                value="{{ old('Telpon1Kry', $karyawan->Telpon1Kry) }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="Telpon2Kry" class="form-label fw-bold">Telepon
                                                            2</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-phone-alt"></i></span>
                                                            <input type="text" class="form-control" id="Telpon2Kry"
                                                                name="Telpon2Kry"
                                                                value="{{ old('Telpon2Kry', $karyawan->Telpon2Kry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="EmailKry" class="form-label fw-bold">Email</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-envelope"></i></span>
                                                            <input type="email" class="form-control" id="EmailKry"
                                                                name="EmailKry"
                                                                value="{{ old('EmailKry', $karyawan->EmailKry) }}">
                                                        </div>
                                                        <div class="form-text text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>Format Email:
                                                            testing@gmail.com

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="InstagramKry"
                                                            class="form-label fw-bold">Instagram</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fab fa-instagram"></i></span>
                                                            <input type="text" class="form-control" id="InstagramKry"
                                                                name="InstagramKry"
                                                                value="{{ old('InstagramKry', $karyawan->InstagramKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat Karyawan -->
                                <div class="tab-pane fade" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-home me-2"></i>Alamat Karyawan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="AlamatKry" class="form-label fw-bold">Alamat <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                                    <input class="form-control" id="AlamatKry" name="AlamatKry"
                                                        value="{{ old('AlamatKry', $karyawan->AlamatKry) }}" required />
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="RtRwKry" class="form-label fw-bold">RT/RW</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map-signs"></i></span>
                                                            <input type="text" class="form-control" id="RtRwKry"
                                                                name="RtRwKry"
                                                                value="{{ old('RtRwKry', $karyawan->RtRwKry) }}"
                                                                placeholder="000/000">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="KelurahanKry"
                                                            class="form-label fw-bold">Kelurahan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map"></i></span>
                                                            <input type="text" class="form-control" id="KelurahanKry"
                                                                name="KelurahanKry"
                                                                value="{{ old('KelurahanKry', $karyawan->KelurahanKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="KecamatanKry"
                                                            class="form-label fw-bold">Kecamatan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map"></i></span>
                                                            <input type="text" class="form-control" id="KecamatanKry"
                                                                name="KecamatanKry"
                                                                value="{{ old('KecamatanKry', $karyawan->KecamatanKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="KotaKry" class="form-label fw-bold">Kota <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-city"></i></span>
                                                            <input type="text" class="form-control" id="KotaKry"
                                                                name="KotaKry"
                                                                value="{{ old('KotaKry', $karyawan->KotaKry) }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="ProvinsiKry" class="form-label fw-bold">Provinsi <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map"></i></span>
                                                            <input type="text" class="form-control" id="ProvinsiKry"
                                                                name="ProvinsiKry"
                                                                value="{{ old('ProvinsiKry', $karyawan->ProvinsiKry) }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="DomisiliKry"
                                                            class="form-label fw-bold">Domisili</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-map-marked-alt"></i></span>
                                                            <textarea class="form-control" id="DomisiliKry" name="DomisiliKry" rows="3" required>{{ old('DomisiliKry', $karyawan->DomisiliKry) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pendidikan -->
                                <div class="tab-pane fade" id="pendidikan" role="tabpanel"
                                    aria-labelledby="pendidikan-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Informasi
                                                Pendidikan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="PendidikanTrhKry"
                                                            class="form-label fw-bold">Pendidikan Terakhir <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user-graduate"></i></span>
                                                            <select class="form-select" id="PendidikanTrhKry"
                                                                name="PendidikanTrhKry" required>
                                                                <option value="" disabled>Pilih Pendidikan</option>
                                                                <option value="-"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == '-' ? 'selected' : '' }}>
                                                                    -</option>
                                                                <option value="SD"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'SD' ? 'selected' : '' }}>
                                                                    SD</option>
                                                                <option value="SMP"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'SMP' ? 'selected' : '' }}>
                                                                    SMP</option>
                                                                <option value="SMA"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'SMA' ? 'selected' : '' }}>
                                                                    SMA</option>
                                                                <option value="SMK"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'SMK' ? 'selected' : '' }}>
                                                                    SMK</option>
                                                                <option value="D1"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'D1' ? 'selected' : '' }}>
                                                                    D1</option>
                                                                <option value="D2"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'D2' ? 'selected' : '' }}>
                                                                    D2</option>
                                                                <option value="D3"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'D3' ? 'selected' : '' }}>
                                                                    D3</option>
                                                                <option value="D4"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'D4' ? 'selected' : '' }}>
                                                                    D4</option>
                                                                <option value="S1"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'S1' ? 'selected' : '' }}>
                                                                    S1</option>
                                                                <option value="S2"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'S2' ? 'selected' : '' }}>
                                                                    S2</option>
                                                                <option value="S3"
                                                                    {{ old('PendidikanTrhKry', $karyawan->PendidikanTrhKry) == 'S3' ? 'selected' : '' }}>
                                                                    S3</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="InstitusiPdkKry"
                                                            class="form-label fw-bold">Institusi</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-university"></i></span>
                                                            <input type="text" class="form-control"
                                                                id="InstitusiPdkKry" name="InstitusiPdkKry"
                                                                value="{{ old('InstitusiPdkKry', $karyawan->InstitusiPdkKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="JurusanPdkKry"
                                                            class="form-label fw-bold">Jurusan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-book"></i></span>
                                                            <input type="text" class="form-control" id="JurusanPdkKry"
                                                                name="JurusanPdkKry"
                                                                value="{{ old('JurusanPdkKry', $karyawan->JurusanPdkKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="TahunLlsKry" class="form-label fw-bold">Tahun
                                                            Lulus</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-check"></i></span>
                                                            <input type="number" class="form-control" id="TahunLlsKry"
                                                                name="TahunLlsKry"
                                                                value="{{ old('TahunLlsKry', $karyawan->TahunLlsKry) }}"
                                                                min="1950" max="{{ date('Y') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="GelarPdkKry" class="form-label fw-bold">Gelar</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-award"></i></span>
                                                            <input type="text" class="form-control" id="GelarPdkKry"
                                                                name="GelarPdkKry"
                                                                value="{{ old('GelarPdkKry', $karyawan->GelarPdkKry) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Tambahan -->
                                <div class="tab-pane fade" id="tambahan" role="tabpanel"
                                    aria-labelledby="tambahan-tab">
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="FileDokKry" class="form-label fw-bold">Unggah
                                                            Dokumen</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-file-pdf"></i></span>
                                                            <input type="file" class="form-control" id="FileDokKry"
                                                                name="FileDokKry">
                                                        </div>
                                                        <div class="form-text text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>Format file: PDF, JPG,
                                                            PNG.
                                                            @if ($karyawan->FileDokKry)
                                                                <br>
                                                                <a href="{{ asset('storage/' . $karyawan->FileDokKry) }}"
                                                                    target="_blank" class="text-primary">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Lihat
                                                                    Dokumen Saat Ini
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="StsKaryawan" class="form-label fw-bold">Status
                                                            Karyawan <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user-check"></i></span>
                                                            <select class="form-select" id="StsKaryawan"
                                                                name="StsKaryawan" required>
                                                                <option value="" disabled>Pilih Status</option>
                                                                <option value="AKTIF"
                                                                    {{ old('StsKaryawan', $karyawan->StsKaryawan) == 'AKTIF' ? 'selected' : '' }}>
                                                                    AKTIF</option>
                                                                <option value="PENSIUN"
                                                                    {{ old('StsKaryawan', $karyawan->StsKaryawan) == 'PENSIUN' ? 'selected' : '' }}>
                                                                    PENSIUN</option>
                                                                <option value="MENGUNDURKAN DIRI"
                                                                    {{ old('StsKaryawan', $karyawan->StsKaryawan) == 'MENGUNDURKAN DIRI' ? 'selected' : '' }}>
                                                                    MENGUNDURKAN DIRI</option>
                                                                <option value="DIKELUARKAN"
                                                                    {{ old('StsKaryawan', $karyawan->StsKaryawan) == 'DIKELUARKAN' ? 'selected' : '' }}>
                                                                    DIKELUARKAN</option>
                                                                <option value="MENINGGAL"
                                                                    {{ old('StsKaryawan', $karyawan->StsKaryawan) == 'MENINGGAL' ? 'selected' : '' }}>
                                                                    MENINGGAL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="nonActiveFields"
                                                class="{{ $karyawan->StsKaryawan != 'AKTIF' ? '' : 'd-none' }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="TglOffKry" class="form-label fw-bold">Tanggal
                                                                Non-Aktif</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-calendar-minus"></i></span>
                                                                <input type="date" class="form-control" id="TglOffKry"
                                                                    name="TglOffKry"
                                                                    value="{{ old('TglOffKry', $karyawan->TglOffKry ? $karyawan->TglOffKry->format('Y-m-d') : '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="KetOffKry"
                                                                class="form-label fw-bold">Keterangan</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-comment"></i></span>
                                                                <input type="text" class="form-control" id="KetOffKry"
                                                                    name="KetOffKry"
                                                                    value="{{ old('KetOffKry', $karyawan->KetOffKry) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($karyawan->FileDokKry)
                                                @php
                                                    $fileExtension = pathinfo(
                                                        storage_path('app/public/' . $karyawan->FileDokKry),
                                                        PATHINFO_EXTENSION,
                                                    );
                                                    $isImage = in_array(strtolower($fileExtension), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'gif',
                                                    ]);
                                                @endphp

                                                @if ($isImage)
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-4">
                                                            <label class="form-label fw-bold">Foto Karyawan Saat
                                                                Ini</label>
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <img src="{{ asset('storage/' . $karyawan->FileDokKry) }}"
                                                                        alt="Foto {{ $karyawan->NamaKry }}"
                                                                        class="img-fluid img-thumbnail"
                                                                        style="max-height: 250px;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="Catatan" class="form-label fw-bold">Catatan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-sticky-note"></i></span>
                                                        <textarea class="form-control" id="Catatan" name="Catatan" rows="3">{{ old('Catatan', $karyawan->Catatan) }}</textarea>
                                                    </div>
                                                    <div class="form-text text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>Catatan tambahan untuk
                                                        karyawan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol navigasi tab dan simpan -->
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
                <div class="modal-header text-white" style="background-color:#02786e">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Update Data</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyimpan perubahan data karyawan ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Simpan</button>
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

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
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

        /* Age info and work duration styles */
        #usiaInfo,
        #masaKerjaInfo {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Styling for validation alert */
        #validationAlert {
            display: none;
            margin-bottom: 20px;
        }

        #validationMessages {
            margin-top: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation with visual feedback
            const form = document.getElementById('karyawanForm');
            const validationAlert = document.getElementById('validationAlert');
            const validationMessages = document.getElementById('validationMessages');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            const confirmSubmitBtn = document.getElementById('confirmSubmit');

            // Tab navigation variables
            const tabs = ['biodata', 'alamat', 'pendidikan', 'tambahan'];
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
                        if (!field.nextElementSibling || !field.nextElementSibling.classList
                            .contains('invalid-feedback')) {
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
                    }
                });

                // Additional validation for NIK KTP (must be 16 digits)
                const nikKtp = document.getElementById('NikKtp');
                if (nikKtp.value && nikKtp.value.length !== 16) {
                    isValid = false;
                    nikKtp.classList.add('is-invalid');
                    invalidFieldsList.push('NIK KTP (harus 16 digit)');
                }

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
                    }

                    // Check if all invalid fields are now valid
                    const invalidFields = document.querySelectorAll('.is-invalid');
                    if (invalidFields.length === 0) {
                        validationAlert.style.display = 'none';
                    }
                });
            });

            // Show/hide non-active fields based on status
            const statusSelect = document.getElementById('StsKaryawan');
            const nonActiveFields = document.getElementById('nonActiveFields');

            function toggleNonActiveFields() {
                if (statusSelect.value !== 'AKTIF' && statusSelect.value !== '') {
                    nonActiveFields.classList.remove('d-none');
                } else {
                    nonActiveFields.classList.add('d-none');
                }
            }

            statusSelect.addEventListener('change', toggleNonActiveFields);
            toggleNonActiveFields(); // Initial state

            // Age calculation function
            const tanggalLahirInput = document.getElementById('TanggalLhrKry');
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
                        usiaInfo.classList.remove('text-muted', 'text-success', 'text-warning');
                    } else if (age >= 17 && age < 25) {
                        usiaInfo.classList.add('text-success');
                        usiaInfo.classList.remove('text-muted', 'text-danger', 'text-warning');
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

            tanggalLahirInput.addEventListener('change', updateAge);
            tanggalLahirInput.addEventListener('input', updateAge);

            // Run once on page load if a date is already set
            if (tanggalLahirInput.value) {
                updateAge();
            }

            // Work duration calculation
            const tanggalMasukInput = document.getElementById('TglMsk');
            const masaKerjaInfo = document.getElementById('masaKerjaInfo');

            function calculateWorkDuration(startDate) {
                const today = new Date();
                const startDateObj = new Date(startDate);

                let years = today.getFullYear() - startDateObj.getFullYear();
                let months = today.getMonth() - startDateObj.getMonth();

                if (months < 0) {
                    years--;
                    months += 12;
                }

                return {
                    years,
                    months
                };
            }

            function updateWorkDuration() {
                if (tanggalMasukInput.value) {
                    const duration = calculateWorkDuration(tanggalMasukInput.value);

                    let durationText = '';
                    if (duration.years > 0) {
                        durationText += `${duration.years} tahun`;
                    }

                    if (duration.months > 0) {
                        if (durationText) {
                            durationText += ` ${duration.months} bulan`;
                        } else {
                            durationText += `${duration.months} bulan`;
                        }
                    }

                    if (!durationText) {
                        durationText = 'Kurang dari 1 bulan';
                    }

                    masaKerjaInfo.innerHTML =
                        `<i class="fas fa-business-time me-1"></i>Masa Kerja: <strong>${durationText}</strong>`;

                    // Add color coding for work duration
                    if (duration.years < 1) {
                        masaKerjaInfo.classList.add('text-info');
                        masaKerjaInfo.classList.remove('text-muted', 'text-success', 'text-warning',
                        'text-primary');
                    } else if (duration.years >= 1 && duration.years < 3) {
                        masaKerjaInfo.classList.add('text-primary');
                        masaKerjaInfo.classList.remove('text-muted', 'text-info', 'text-success', 'text-warning');
                    } else if (duration.years >= 3 && duration.years < 10) {
                        masaKerjaInfo.classList.add('text-success');
                        masaKerjaInfo.classList.remove('text-muted', 'text-info', 'text-primary', 'text-warning');
                    } else {
                        masaKerjaInfo.classList.add('text-warning');
                        masaKerjaInfo.classList.remove('text-muted', 'text-info', 'text-primary', 'text-success');
                    }
                } else {
                    masaKerjaInfo.innerHTML = '';
                    masaKerjaInfo.className = 'form-text text-muted mt-1';
                }
            }

            tanggalMasukInput.addEventListener('change', updateWorkDuration);
            tanggalMasukInput.addEventListener('input', updateWorkDuration);

            // Run once on page load if a date is already set
            if (tanggalMasukInput.value) {
                updateWorkDuration();
            }

            // Preview uploaded image file before submission
            document.getElementById('FileDokKry').addEventListener('change', function(e) {
                const fileInput = e.target;
                if (fileInput.files && fileInput.files[0]) {
                    const fileType = fileInput.files[0].type;

                    // Check if the file is an image
                    if (fileType.match('image.*')) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            // Find existing preview or create new one
                            let previewContainer = document.querySelector('.preview-container');

                            if (!previewContainer) {
                                previewContainer = document.createElement('div');
                                previewContainer.className = 'preview-container form-group mb-4 mt-3';

                                const label = document.createElement('label');
                                label.className = 'form-label fw-bold';
                                label.textContent = 'Preview Foto Baru:';

                                const card = document.createElement('div');
                                card.className = 'card';

                                const cardBody = document.createElement('div');
                                cardBody.className = 'card-body text-center';

                                const img = document.createElement('img');
                                img.className = 'img-fluid img-thumbnail preview-image';
                                img.style.maxHeight = '250px';
                                img.alt = 'Preview';

                                cardBody.appendChild(img);
                                card.appendChild(cardBody);
                                previewContainer.appendChild(label);
                                previewContainer.appendChild(card);

                                // Insert after the file input container
                                const fileInputContainer = fileInput.closest('.form-group');
                                fileInputContainer.parentNode.insertBefore(previewContainer,
                                    fileInputContainer.nextSibling);
                            }

                            // Update the image
                            const previewImage = previewContainer.querySelector('.preview-image');
                            previewImage.src = e.target.result;
                        }

                        reader.readAsDataURL(fileInput.files[0]);
                    } else {
                        // Remove preview if exists when a non-image file is selected
                        const previewContainer = document.querySelector('.preview-container');
                        if (previewContainer) {
                            previewContainer.remove();
                        }
                    }
                }
            });
        });
    </script>
    @endpushƒ
