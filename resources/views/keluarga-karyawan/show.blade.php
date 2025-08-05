@extends('layouts.app')

@section('title', 'Detail Keluarga Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-user me-2"></i>Detail Keluarga Karyawan</span>
                        <div>
                            <a href="{{ route('keluarga-karyawan.edit', $keluargaKaryawan->id) }}"
                                class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('keluarga-karyawan.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Nav tabs for viewing sections -->
                        <ul class="nav nav-tabs mb-4" id="viewTabs" role="tablist">
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
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Utama</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Nama</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user"></i></span>
                                                            <div class="form-control">{{ $keluargaKaryawan->NamaKlg }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Karyawan</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-user-tie"></i></span>
                                                            <div class="form-control">
                                                                @if ($keluargaKaryawan->karyawan)
                                                                    {{ $keluargaKaryawan->karyawan->NamaKry }}
                                                                    @if ($keluargaKaryawan->karyawan->NikKry)
                                                                        ({{ $keluargaKaryawan->karyawan->NikKry }})
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted fst-italic">Tidak ada
                                                                        data</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Status Keluarga</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-users"></i></span>
                                                                <div class="form-control">
                                                                    @php
                                                                        $badgeClass = 'bg-primary';
                                                                        if (
                                                                            in_array(
                                                                                $keluargaKaryawan->StsKeluargaKry,
                                                                                ['Suami', 'Istri'],
                                                                            )
                                                                        ) {
                                                                            $badgeClass = 'bg-success';
                                                                        } elseif (
                                                                            $keluargaKaryawan->StsKeluargaKry === 'Anak'
                                                                        ) {
                                                                            $badgeClass = 'bg-info';
                                                                        } elseif (
                                                                            in_array(
                                                                                $keluargaKaryawan->StsKeluargaKry,
                                                                                ['Bapak', 'Ibu'],
                                                                            )
                                                                        ) {
                                                                            $badgeClass = 'bg-warning text-dark';
                                                                        }
                                                                    @endphp
                                                                    <span
                                                                        class="badge {{ $badgeClass }}">{{ $keluargaKaryawan->StsKeluargaKry }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if ($keluargaKaryawan->KetKeluargaKry)
                                                        <div class="info-group mb-3">
                                                            <label class="info-label fw-bold">Keterangan</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-sticky-note"></i></span>
                                                                    <div class="form-control">
                                                                        {{ $keluargaKaryawan->KetKeluargaKry }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>


                                                @if ($keluargaKaryawan->TanggalLhrKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Umur</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-birthday-cake"></i></span>
                                                                <div class="form-control">{{ $keluargaKaryawan->umur }}
                                                                    tahun</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
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
                                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Informasi Pribadi</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if ($keluargaKaryawan->NikKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">NIK</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-id-card"></i></span>
                                                                <div class="form-control">{{ $keluargaKaryawan->NikKlg }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="info-group mb-3">
                                                    <label class="info-label fw-bold">Jenis Kelamin</label>
                                                    <div class="info-value">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-venus-mars"></i></span>
                                                            <div class="form-control">
                                                                @if ($keluargaKaryawan->SexKlg === 'L')
                                                                    <i class="fas fa-mars text-primary me-1"></i> Laki-laki
                                                                @else
                                                                    <i class="fas fa-venus text-danger me-1"></i> Perempuan
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($keluargaKaryawan->TempatLhrKlg || $keluargaKaryawan->TanggalLhrKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Tempat, Tanggal Lahir</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-map-marker-alt"></i></span>
                                                                <div class="form-control">
                                                                    {{ $keluargaKaryawan->TempatLhrKlg ?? '-' }}{{ $keluargaKaryawan->TempatLhrKlg && $keluargaKaryawan->TanggalLhrKlg ? ', ' : '' }}
                                                                    {{ $keluargaKaryawan->TanggalLhrKlg ? $keluargaKaryawan->TanggalLhrKlg->format('d F Y') : '' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($keluargaKaryawan->AgamaKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Agama</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-pray"></i></span>
                                                                <div class="form-control">
                                                                    {{ $keluargaKaryawan->AgamaKlg }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                @if ($keluargaKaryawan->StsKawinKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Status Kawin</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-ring"></i></span>
                                                                <div class="form-control">
                                                                    {{ $keluargaKaryawan->StsKawinKlg }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($keluargaKaryawan->PekerjaanKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Pekerjaan</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-briefcase"></i></span>
                                                                <div class="form-control">
                                                                    {{ $keluargaKaryawan->PekerjaanKlg }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($keluargaKaryawan->WargaNegaraKlg)
                                                    <div class="info-group mb-3">
                                                        <label class="info-label fw-bold">Warga Negara</label>
                                                        <div class="info-value">
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-flag"></i></span>
                                                                <div class="form-control">
                                                                    {{ $keluargaKaryawan->WargaNegaraKlg }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($keluargaKaryawan->AlamatKtpKlg)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Alamat KTP</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-home"></i></span>
                                                        <div class="form-control">{{ $keluargaKaryawan->AlamatKtpKlg }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($keluargaKaryawan->DomisiliKlg)
                                            <div class="info-group mb-3">
                                                <label class="info-label fw-bold">Alamat Domisili</label>
                                                <div class="info-value">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-map-marked-alt"></i></span>
                                                        <div class="form-control">{{ $keluargaKaryawan->DomisiliKlg }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
                                        @if (
                                            !$keluargaKaryawan->Telpon1Klg &&
                                                !$keluargaKaryawan->Telpon2Klg &&
                                                !$keluargaKaryawan->EmailKlg &&
                                                !$keluargaKaryawan->InstagramKlg)
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>Tidak ada informasi kontak yang
                                                tersedia.
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if ($keluargaKaryawan->Telpon1Klg)
                                                        <div class="info-group mb-12">
                                                            <label class="info-label fw-bold">Telepon Utama</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-phone"></i></span>
                                                                    <div class="form-control">
                                                                        <a href="tel:{{ $keluargaKaryawan->Telpon1Klg }}"
                                                                            class="text-decoration-none">
                                                                            {{ $keluargaKaryawan->Telpon1Klg }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($keluargaKaryawan->Telpon2Klg)
                                                        <div class="info-group mb-12">
                                                            <label class="info-label fw-bold">Telepon Alternatif</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-phone-alt"></i></span>
                                                                    <div class="form-control">
                                                                        <a href="tel:{{ $keluargaKaryawan->Telpon2Klg }}"
                                                                            class="text-decoration-none">
                                                                            {{ $keluargaKaryawan->Telpon2Klg }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-12">
                                                    @if ($keluargaKaryawan->EmailKlg)
                                                        <div class="info-group mb-12">
                                                            <label class="info-label fw-bold">Email</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-envelope"></i></span>
                                                                    <div class="form-control">
                                                                        <a href="mailto:{{ $keluargaKaryawan->EmailKlg }}"
                                                                            class="text-decoration-none">
                                                                            {{ $keluargaKaryawan->EmailKlg }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($keluargaKaryawan->InstagramKlg)
                                                        <div class="info-group mb-12">
                                                            <label class="info-label fw-bold">Instagram</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fab fa-instagram"></i></span>
                                                                    <div class="form-control">
                                                                        @php
                                                                            $igUsername = ltrim(
                                                                                $keluargaKaryawan->InstagramKlg,
                                                                                '@',
                                                                            );
                                                                        @endphp
                                                                        <a href="https://instagram.com/{{ $igUsername }}"
                                                                            class="text-decoration-none" target="_blank">
                                                                            {{ $keluargaKaryawan->InstagramKlg }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Pendidikan -->
                            <div class="tab-pane fade" id="info-pendidikan" role="tabpanel"
                                aria-labelledby="info-pendidikan-tab">
                                <div class="card border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-25 text-white">
                                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if (
                                            !$keluargaKaryawan->PendidikanTrhKlg &&
                                                !$keluargaKaryawan->JurusanPdkKlg &&
                                                !$keluargaKaryawan->InstitusiPdkKlg &&
                                                !$keluargaKaryawan->TahunLlsKlg &&
                                                !$keluargaKaryawan->GelarPdkKlg)
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>Tidak ada informasi pendidikan yang
                                                tersedia.
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if ($keluargaKaryawan->PendidikanTrhKlg)
                                                        <div class="info-group mb-3">
                                                            <label class="info-label fw-bold">Pendidikan Terakhir</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-graduation-cap"></i></span>
                                                                    <div class="form-control">
                                                                        @php
                                                                            $pendidikanOptions = [
                                                                                'SD' => 'SD',
                                                                                'SMP' => 'SMP/Sederajat',
                                                                                'SMA' => 'SMA/Sederajat',
                                                                                'D1' => 'D1',
                                                                                'D2' => 'D2',
                                                                                'D3' => 'D3',
                                                                                'D4' => 'D4',
                                                                                'S1' => 'S1',
                                                                                'S2' => 'S2',
                                                                                'S3' => 'S3',
                                                                            ];
                                                                        @endphp
                                                                        {{ $pendidikanOptions[$keluargaKaryawan->PendidikanTrhKlg] ?? $keluargaKaryawan->PendidikanTrhKlg }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($keluargaKaryawan->JurusanPdkKlg)
                                                        <div class="info-group mb-3">
                                                            <label class="info-label fw-bold">Jurusan</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-book"></i></span>
                                                                    <div class="form-control">
                                                                        {{ $keluargaKaryawan->JurusanPdkKlg }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($keluargaKaryawan->GelarPdkKlg)
                                                        <div class="info-group mb-3">
                                                            <label class="info-label fw-bold">Gelar</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-award"></i></span>
                                                                    <div class="form-control">
                                                                        {{ $keluargaKaryawan->GelarPdkKlg }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-12">
                                                    @if ($keluargaKaryawan->InstitusiPdkKlg)
                                                        <div class="info-group mb-3">
                                                            <label class="info-label fw-bold">Institusi</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-university"></i></span>
                                                                    <div class="form-control">
                                                                        {{ $keluargaKaryawan->InstitusiPdkKlg }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($keluargaKaryawan->TahunLlsKlg)
                                                        <div class="info-group mb-3">
                                                            <label class="info-label fw-bold">Tahun Lulus</label>
                                                            <div class="info-value">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fas fa-calendar-check"></i></span>
                                                                    <div class="form-control">
                                                                        {{ $keluargaKaryawan->TahunLlsKlg }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
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
@endsection

@push('styles')
    <style>
        .card-header {
            font-weight: 600;
        }

        .info-label {
            margin-bottom: 0.3rem;
            display: block;
            font-weight: 600;
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
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
