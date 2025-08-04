@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-user me-2"></i>Detail Karyawan</span>
                        <div>
                            <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('karyawan.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs mb-4" id="detailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="biodata-tab" data-bs-toggle="tab"
                                    data-bs-target="#biodata" type="button" role="tab" aria-controls="biodata"
                                    aria-selected="true">
                                    <i class="fas fa-id-card me-1"></i> Biodata
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="alamat-tab" data-bs-toggle="tab"
                                    data-bs-target="#alamat" type="button" role="tab"
                                    aria-controls="alamat" aria-selected="false">
                                    <i class="fas fa-home me-1"></i> Alamat
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tambahan-tab" data-bs-toggle="tab"
                                    data-bs-target="#tambahan" type="button" role="tab"
                                    aria-controls="tambahan" aria-selected="false">
                                    <i class="fas fa-info-circle me-1"></i> Informasi Tambahan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab"
                                    data-bs-target="#pendidikan" type="button" role="tab"
                                    aria-controls="pendidikan" aria-selected="false">
                                    <i class="fas fa-graduation-cap me-1"></i> Pendidikan
                                </button>
                            </li>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content">
                            <!-- Biodata Tab -->
                            <div class="tab-pane fade show active" id="biodata" role="tabpanel" aria-labelledby="biodata-tab">
                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Biodata Karyawan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Nama</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <div class="form-control">{{ $karyawan->NamaKry }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">NRK</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                            <div class="form-control">{{ $karyawan->NrkKry }}</div>
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
                                                            <div class="form-control">{{ $karyawan->NikKtp }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tempat Lahir</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                            <div class="form-control">{{ $karyawan->TempatLhrKry }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tanggal Lahir</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                            <div class="form-control">
                                                                {{ $karyawan->TanggalLhrKry ? date('d-m-Y', strtotime($karyawan->TanggalLhrKry)) : '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jenis Kelamin</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                                            <div class="form-control">{{ $karyawan->SexKry }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Umur</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                            <div class="form-control">{{ $karyawan->umur ?? '-' }} Tahun</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Agama</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-pray"></i></span>
                                                            <div class="form-control">{{ $karyawan->AgamaKry }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Kewarganegaraan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                                            <div class="form-control">{{ $karyawan->WargaNegaraKry ?? 'Indonesia' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat Tab -->
                            <div class="tab-pane fade" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-home me-2"></i>Alamat Karyawan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Alamat Lengkap</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                                    <div class="form-control">{{ $karyawan->AlamatKry }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">RT/RW</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                                                            <div class="form-control">{{ $karyawan->RtRwKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Kelurahan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                                                            <div class="form-control">{{ $karyawan->KelurahanKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Kecamatan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                                                            <div class="form-control">{{ $karyawan->KecamatanKry ?? '-' }}</div>
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
                                                            <div class="form-control">{{ $karyawan->KotaKry }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-group mb-3">
                                            <label class="info-label fw-bold">Provinsi</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-map"></i></span>
                                                    <div class="form-control">{{ $karyawan->ProvinsiKry }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Tambahan Tab -->
                            <div class="tab-pane fade" id="tambahan" role="tabpanel" aria-labelledby="tambahan-tab">
                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Karyawan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                            <div class="form-control">
                                                                @if ($karyawan->StsKaryawan == 'Aktif')
                                                                    <span class="badge bg-success">Aktif</span>
                                                                @elseif ($karyawan->StsKaryawan == 'Pensiun')
                                                                    <span class="badge bg-info">Pensiun</span>
                                                                @elseif ($karyawan->StsKaryawan == 'Mengundurkan Diri')
                                                                    <span class="badge bg-warning">Mengundurkan Diri</span>
                                                                @elseif ($karyawan->StsKaryawan == 'Dikeluarkan')
                                                                    <span class="badge bg-danger">Dikeluarkan</span>
                                                                @elseif ($karyawan->StsKaryawan == 'Meninggal')
                                                                    <span class="badge bg-secondary">Meninggal</span>
                                                                @else
                                                                    <span class="badge bg-dark">{{ $karyawan->StsKaryawan }}</span>
                                                                @endif
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
                                                            <div class="form-control">
                                                                {{ $karyawan->TglMsk ? date('d-m-Y', strtotime($karyawan->TglMsk)) : '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($karyawan->StsKaryawan != 'Aktif')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Tanggal Non-Aktif</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fas fa-calendar-minus"></i></span>
                                                                <div class="form-control">
                                                                    {{ $karyawan->TglOffKry ? date('d-m-Y', strtotime($karyawan->TglOffKry)) : '-' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Keterangan</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                                                <div class="form-control">{{ $karyawan->KetOffKry ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Status Perkawinan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-ring"></i></span>
                                                            <div class="form-control">{{ $karyawan->StsKawinKry }}</div>
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
                                                            <div class="form-control">{{ $karyawan->StsKeluargaKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jumlah Anak</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-child"></i></span>
                                                            <div class="form-control">{{ $karyawan->JumlahAnakKry ?? '0' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Pekerjaan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                            <div class="form-control">{{ $karyawan->PekerjaanKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Telepon 1</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <div class="form-control">{{ $karyawan->Telpon1Kry }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Telepon 2</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                                            <div class="form-control">{{ $karyawan->Telpon2Kry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Email</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                            <div class="form-control">{{ $karyawan->EmailKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Instagram</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                            <div class="form-control">{{ $karyawan->InstagramKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pendidikan Tab -->
                            <div class="tab-pane fade" id="pendidikan" role="tabpanel" aria-labelledby="pendidikan-tab">
                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Pendidikan Terakhir</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                                                            <div class="form-control">{{ $karyawan->PendidikanTrhKry }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Institusi</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-university"></i></span>
                                                            <div class="form-control">{{ $karyawan->InstitusiPdkKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jurusan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                                                            <div class="form-control">{{ $karyawan->JurusanPdkKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Tahun Lulus</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                                            <div class="form-control">{{ $karyawan->TahunLlsKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Gelar</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-award"></i></span>
                                                            <div class="form-control">{{ $karyawan->GelarPdkKry ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Dokumen</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-file-pdf"></i></span>
                                                            <div class="form-control">
                                                                @if ($karyawan->FileDokKry)
                                                                    @php
                                                                        $fileExtension = pathinfo(
                                                                            storage_path(
                                                                                'app/public/' . $karyawan->FileDokKry,
                                                                            ),
                                                                            PATHINFO_EXTENSION,
                                                                        );
                                                                        $isPdf = strtolower($fileExtension) === 'pdf';
                                                                        $isImage = in_array(
                                                                            strtolower($fileExtension),
                                                                            ['jpg', 'jpeg', 'png', 'gif'],
                                                                        );
                                                                    @endphp

                                                                    @if ($isPdf || $isImage)
                                                                        <a href="{{ asset('storage/' . $karyawan->FileDokKry) }}"
                                                                            target="_blank" class="text-primary"
                                                                            rel="noopener">
                                                                            <i
                                                                                class="fas fa-external-link-alt me-1"></i>Lihat
                                                                            Dokumen
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('karyawan.view-document', $karyawan->id) }}"
                                                                            target="_blank" class="text-primary"
                                                                            rel="noopener">
                                                                            <i
                                                                                class="fas fa-external-link-alt me-1"></i>Lihat
                                                                            Dokumen
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">Tidak ada dokumen</span>
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

        .badge {
            font-size: 0.75em;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activate Bootstrap tabs
            const triggerTabList = [].slice.call(document.querySelectorAll('#detailTabs button'));
            triggerTabList.forEach(function(triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);

                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });
        });
    </script>
@endpush