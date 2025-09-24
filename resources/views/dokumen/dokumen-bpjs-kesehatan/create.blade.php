@extends('layouts.app')

@section('title', 'Tambah Dokumen BPJS Kesehatan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-medical me-2"></i>Tambah Dokumen BPJS Kesehatan</span>
                        <a href="{{ route('dokumen-bpjs-kesehatan.index') }}" class="btn btn-light btn-sm">
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

                        <!-- Alert for validation errors -->
                        <div class="alert alert-danger" id="validationAlert" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Mohon periksa kembali form. Beberapa field wajib belum diisi dengan benar.</span>
                            <ul id="validationMessages" class="mt-2 mb-0"></ul>
                        </div>

                        <form action="{{ route('dokumen-bpjs-kesehatan.store') }}" method="POST"
                            id="dokumenBpjsKesehatanForm" enctype="multipart/form-data">
                            @csrf
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $newId) }}" hidden readonly>

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
                                                <label for="IdKodeA04" class="form-label fw-bold">Karyawan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>

                                                    <!-- Custom searchable dropdown for Karyawan -->
                                                    <div class="custom-select-container @error('IdKodeA04') is-invalid @enderror"
                                                        style="flex: 1 1 auto; width: 1%;" id="karyawanContainer">
                                                        <input type="text" id="karyawanSearch"
                                                            class="form-control custom-select-search"
                                                            placeholder="Cari karyawan..." autocomplete="off">

                                                        <div class="custom-select-dropdown" id="karyawanDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="karyawanFilterInput"
                                                                    class="form-control"
                                                                    placeholder="Ketik untuk mencari">
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
                                                            placeholder="Cari kategori..." autocomplete="off">

                                                        <div class="custom-select-dropdown" id="kategoriDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="kategoriFilterInput"
                                                                    class="form-control"
                                                                    placeholder="Ketik untuk mencari">
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
                                                            placeholder="Cari jenis dokumen..." autocomplete="off">

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
                                                            value="{{ old('JenisDok') }}" required>
                                                    </div>
                                                    @error('JenisDok')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="NoRegDok" class="form-label fw-bold">Nomor Registrasi <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('NoRegDok') is-invalid @enderror"
                                                        id="NoRegDok" name="NoRegDok" value="{{ old('NoRegDok') }}"
                                                        placeholder="Masukkan nomor registrasi dokumen" required>
                                                    @error('NoRegDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informasi Upah
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="UpahKtrKry" class="form-label fw-bold">Upah Kotor
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('UpahKtrKry') is-invalid @enderror"
                                                                id="UpahKtrKry" name="UpahKtrKry"
                                                                value="{{ old('UpahKtrKry') }}" placeholder="0" required>
                                                            @error('UpahKtrKry')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="UpahBrshKry" class="form-label fw-bold">Upah Bersih
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('UpahBrshKry') is-invalid @enderror"
                                                                id="UpahBrshKry" name="UpahBrshKry"
                                                                value="{{ old('UpahBrshKry') }}" placeholder="0"
                                                                required>
                                                            @error('UpahBrshKry')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
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
                                                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Iuran
                                                        Perusahaan</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <label for="IuranPrshPersen" class="form-label fw-bold">Persentase
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number"
                                                                class="form-control @error('IuranPrshPersen') is-invalid @enderror"
                                                                id="IuranPrshPersen" name="IuranPrshPersen"
                                                                value="{{ old('IuranPrshPersen') }}" min="0"
                                                                max="100" step="0.01" placeholder="0" required>
                                                            <span class="input-group-text">%</span>
                                                            @error('IuranPrshPersen')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="IuranPrshRp" class="form-label fw-bold">Nominal <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('IuranPrshRp') is-invalid @enderror"
                                                                id="IuranPrshRp" name="IuranPrshRp"
                                                                value="{{ old('IuranPrshRp') }}" placeholder="0" readonly
                                                                required>
                                                            @error('IuranPrshRp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-warning mb-3">
                                                <div class="card-header bg-warning bg-opacity-25 text-dark">
                                                    <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Iuran Karyawan
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <label for="IuranKryPersen" class="form-label fw-bold">Persentase
                                                            <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number"
                                                                class="form-control @error('IuranKryPersen') is-invalid @enderror"
                                                                id="IuranKryPersen" name="IuranKryPersen"
                                                                value="{{ old('IuranKryPersen') }}" min="0"
                                                                max="100" step="0.01" placeholder="0" required>
                                                            <span class="input-group-text">%</span>
                                                            @error('IuranKryPersen')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="IuranKryRp" class="form-label fw-bold">Nominal <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('IuranKryRp') is-invalid @enderror"
                                                                id="IuranKryRp" name="IuranKryRp"
                                                                value="{{ old('IuranKryRp') }}" placeholder="0" readonly
                                                                required>
                                                            @error('IuranKryRp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Iuran Tambahan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="IuranKry1Rp" class="form-label fw-bold">Iuran Karyawan
                                                            1 (Opsional)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('IuranKry1Rp') is-invalid @enderror"
                                                                id="IuranKry1Rp" name="IuranKry1Rp"
                                                                value="{{ old('IuranKry1Rp') }}" placeholder="0">
                                                            @error('IuranKry1Rp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="IuranKry2Rp" class="form-label fw-bold">Iuran Karyawan
                                                            2 (Opsional)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('IuranKry2Rp') is-invalid @enderror"
                                                                id="IuranKry2Rp" name="IuranKry2Rp"
                                                                value="{{ old('IuranKry2Rp') }}" placeholder="0">
                                                            @error('IuranKry2Rp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="IuranKry3Rp" class="form-label fw-bold">Iuran Karyawan
                                                            3 (Opsional)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('IuranKry3Rp') is-invalid @enderror"
                                                                id="IuranKry3Rp" name="IuranKry3Rp"
                                                                value="{{ old('IuranKry3Rp') }}" placeholder="0">
                                                            @error('IuranKry3Rp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
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
                                                        <label for="JmlPrshRp" class="form-label fw-bold">Jumlah Iuran
                                                            Perusahaan <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('JmlPrshRp') is-invalid @enderror"
                                                                id="JmlPrshRp" name="JmlPrshRp"
                                                                value="{{ old('JmlPrshRp') }}" placeholder="0" readonly
                                                                required>
                                                            @error('JmlPrshRp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="JmlKryRp" class="form-label fw-bold">Jumlah Iuran
                                                            Karyawan <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('JmlKryRp') is-invalid @enderror"
                                                                id="JmlKryRp" name="JmlKryRp"
                                                                value="{{ old('JmlKryRp') }}" placeholder="0" readonly
                                                                required>
                                                            @error('JmlKryRp')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="TotIuran" class="form-label fw-bold">Total Iuran <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text"
                                                                class="form-control currency-input @error('TotIuran') is-invalid @enderror"
                                                                id="TotIuran" name="TotIuran"
                                                                value="{{ old('TotIuran') }}" placeholder="0" readonly
                                                                required>
                                                            @error('TotIuran')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
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
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku &
                                                Tanggal</h5>
                                        </div>
                                        <div class="card-body">
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
                                                                value="{{ old('TglTerbitDok') }}" required>
                                                            @error('TglTerbitDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TglBerakhirDok" class="form-label fw-bold">Tanggal
                                                            Berakhir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-times"></i></span>
                                                            <input type="date"
                                                                class="form-control tanggal-input @error('TglBerakhirDok') is-invalid @enderror"
                                                                id="TglBerakhirDok" name="TglBerakhirDok"
                                                                value="{{ old('TglBerakhirDok') }}">
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
                                                    name="MasaBerlaku" value="{{ old('MasaBerlaku', 'Tetap') }}"
                                                    readonly>
                                                <div class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Masa berlaku akan dihitung
                                                    otomatis
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>File Dokumen</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="FileDok" class="form-label fw-bold">File Dokumen <span
                                                        class="text-danger">*</span></label>
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
                                            <div class="form-group mb-3">
                                                <label for="KetDok" class="form-label fw-bold">Keterangan
                                                    Dokumen</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-align-left"></i></span>
                                                    <textarea class="form-control @error('KetDok') is-invalid @enderror" id="KetDok" name="KetDok" rows="3"
                                                        placeholder="Masukkan keterangan dokumen">{{ old('KetDok') }}</textarea>
                                                    @error('KetDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Status Dokumen</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="StatusDok" class="form-label fw-bold">Status Dokumen</label>
                                                <div class="bg-light p-3 rounded">
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input @error('StatusDok') is-invalid @enderror"
                                                            type="radio" name="StatusDok" id="berlaku"
                                                            value="Berlaku"
                                                            {{ old('StatusDok', 'Berlaku') == 'Berlaku' ? 'checked' : '' }}
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
                                                            {{ old('StatusDok') == 'Tidak Berlaku' ? 'checked' : '' }}>
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

                            <!-- Tombol navigasi tab -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" id="prevTabBtn" class="btn btn-secondary" style="display: none;">
                                    <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                </button>
                                <div class="ms-auto">
                                    <button type="button" id="nextTabBtn" class="btn btn-primary me-2">
                                        Selanjutnya <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-success"
                                        style="display: none;">
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Simpan Data</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyimpan data dokumen BPJS Kesehatan ini?</p>
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

    // File input preview and validation
    const fileInput = document.getElementById('FileDok');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileTypeIcon = document.getElementById('fileTypeIcon');
    const removeFileBtn = document.getElementById('removeFile');
    const fileValidationMessage = document.getElementById('fileValidationMessage');

    // Format currency inputs
    const formatCurrency = function(value) {
        if (!value) return '';
        // Remove non-numeric characters
        let number = value.replace(/[^\d]/g, '');
        // Format with thousand separator
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    // Parse currency value to number
    const parseCurrency = function(value) {
        if (!value) return 0;
        // Remove non-numeric characters
        return parseInt(value.replace(/[^\d]/g, '')) || 0;
    };

    // Create hidden fields for storing raw values
    function createHiddenFields() {
        document.querySelectorAll('.currency-input').forEach(function(input) {
            // Check if hidden field already exists
            const hiddenId = input.id + '_raw';
            if (!document.getElementById(hiddenId)) {
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.id = hiddenId;
                hiddenField.name = input.name;
                // Set initial value from the visible input
                hiddenField.value = parseCurrency(input.value);
                // Insert hidden field after the input
                input.parentNode.insertBefore(hiddenField, input.nextSibling);

                // Change the name of the visible input to prevent it from being submitted
                input.name = input.name + '_display';
            }
        });
    }

    // Apply currency formatter to all currency inputs
    document.querySelectorAll('.currency-input').forEach(function(input) {
        input.addEventListener('input', function(e) {
            const cursorPosition = this.selectionStart;
            const originalLength = this.value.length;

            // Parse the raw value
            const rawValue = parseCurrency(this.value);

            // Format the value for display
            const formattedValue = formatCurrency(this.value);
            this.value = formattedValue;

            // Update the hidden field with the raw value
            const hiddenField = document.getElementById(this.id + '_raw');
            if (hiddenField) {
                hiddenField.value = rawValue;
            }

            // Restore cursor position
            const newLength = formattedValue.length;
            const newPosition = cursorPosition + (newLength - originalLength);
            this.setSelectionRange(newPosition, newPosition);

            // Trigger calculation
            calculateAll();
        });
    });

    // Calculate all values
    function calculateAll() {
        // Get raw values
        const upahBersih = parseCurrency(document.getElementById('UpahBrshKry').value);
        const iuranPrshPersen = parseFloat(document.getElementById('IuranPrshPersen').value) || 0;
        const iuranKryPersen = parseFloat(document.getElementById('IuranKryPersen').value) || 0;

        // Calculate iuran perusahaan in rupiah (whole number, no decimals)
        const iuranPrshRp = Math.round(upahBersih * iuranPrshPersen / 100);
        document.getElementById('IuranPrshRp').value = formatCurrency(iuranPrshRp.toString());
        document.getElementById('IuranPrshRp_raw').value = iuranPrshRp;

        // Calculate iuran karyawan in rupiah (whole number, no decimals)
        const iuranKryRp = Math.round(upahBersih * iuranKryPersen / 100);
        document.getElementById('IuranKryRp').value = formatCurrency(iuranKryRp.toString());
        document.getElementById('IuranKryRp_raw').value = iuranKryRp;

        // Get optional iuran values
        const iuranKry1Rp = parseCurrency(document.getElementById('IuranKry1Rp').value);
        const iuranKry2Rp = parseCurrency(document.getElementById('IuranKry2Rp').value);
        const iuranKry3Rp = parseCurrency(document.getElementById('IuranKry3Rp').value);

        // Calculate totals
        const jmlPrshRp = iuranPrshRp; // Jumlah iuran perusahaan = iuran perusahaan
        const jmlKryRp = iuranKryRp + iuranKry1Rp + iuranKry2Rp + iuranKry3Rp; // Jumlah iuran karyawan
        const totIuran = jmlPrshRp + jmlKryRp; // Total iuran

        // Update display fields
        document.getElementById('JmlPrshRp').value = formatCurrency(jmlPrshRp.toString());
        document.getElementById('JmlKryRp').value = formatCurrency(jmlKryRp.toString());
        document.getElementById('TotIuran').value = formatCurrency(totIuran.toString());

        // Update hidden fields
        document.getElementById('JmlPrshRp_raw').value = jmlPrshRp;
        document.getElementById('JmlKryRp_raw').value = jmlKryRp;
        document.getElementById('TotIuran_raw').value = totIuran;
    }

    // Add event listeners for calculation fields
    document.getElementById('UpahBrshKry').addEventListener('input', calculateAll);
    document.getElementById('IuranPrshPersen').addEventListener('input', calculateAll);
    document.getElementById('IuranKryPersen').addEventListener('input', calculateAll);
    document.getElementById('IuranKry1Rp').addEventListener('input', calculateAll);
    document.getElementById('IuranKry2Rp').addEventListener('input', calculateAll);
    document.getElementById('IuranKry3Rp').addEventListener('input', calculateAll);

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

        // Reset jenis value
        jenisHiddenInput.value = '';
        jenisSearch.value = '';

        if (!kategoriId) return;

        // Get dokumen list for selected kategori
        const dokumenList = jenisDokumenByKategori[kategoriId] || [];

        // Add options
        dokumenList.forEach(dokumen => {
            const option = document.createElement('div');
            option.className = 'custom-select-option';
            option.dataset.value = dokumen.JenisDok;
            option.dataset.display = dokumen.JenisDok;
            option.textContent = dokumen.JenisDok;

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

        // Check if there's an old value to restore
        const oldJenisDok = "{{ old('JenisDok') }}";
        if (oldJenisDok) {
            dokumenList.forEach(dokumen => {
                if (dokumen.JenisDok === oldJenisDok) {
                    jenisHiddenInput.value = oldJenisDok;
                    jenisSearch.value = oldJenisDok;

                    const option = jenisOptions.querySelector(
                        `.custom-select-option[data-value="${oldJenisDok}"]`);
                    if (option) {
                        option.classList.add('selected');
                    }
                }
            });
        }
    }

    // Function to calculate and display validity period
    function hitungMasaBerlaku() {
        const tglTerbit = document.getElementById('TglTerbitDok').value;
        const tglBerakhir = document.getElementById('TglBerakhirDok').value;
        const masaBerlakuField = document.getElementById('MasaBerlaku');

        if (!tglBerakhir) {
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
            masaBerlakuField.value = 'Tetap';
        }
    }

    // Function to validate file input
    function validateFileInput() {
        if (!fileInput.files || fileInput.files.length === 0) {
            fileInput.classList.add('is-invalid');
            fileValidationMessage.textContent = 'Silakan pilih file dokumen';
            fileValidationMessage.classList.remove('d-none');

            // Animate the file input
            fileInput.classList.add('animate__animated', 'animate__shakeX');
            setTimeout(() => {
                fileInput.classList.remove('animate__shakeX');
            }, 1000);

            return false;
        } else {
            fileInput.classList.remove('is-invalid');
            fileValidationMessage.classList.add('d-none');
            return true;
        }
    }

    // Function to highlight missing fields
    function highlightMissingFields() {
        const invalidFieldsList = [];

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

                // Get field label for error message
                let fieldName = "";
                const label = input.closest('.form-group').querySelector('label');
                if (label) {
                    fieldName = label.textContent.replace('*', '').trim();
                }

                invalidFieldsList.push(fieldName);

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

        return invalidFieldsList;
    }

    // Add event listeners for dates
    document.querySelectorAll('.tanggal-input').forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.id === 'TglTerbitDok' || this.id === 'TglBerakhirDok') {
                hitungMasaBerlaku();
            }
        });
    });

    // Tab navigation variables
    const tabs = ['info-karyawan', 'iuran-bpjs', 'info-tambahan'];
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
                if (field.id === 'IdKodeA04' || field.id === 'KategoriDok' || field.id ===
                    'JenisDok') {
                    const containerId = field.id.replace('IdKode', '').replace('Dok', '') +
                        'Container';
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.classList.add('is-invalid');
                    }
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
                if (field.id === 'IdKodeA04' || field.id === 'KategoriDok' || field.id ===
                    'JenisDok') {
                    const containerId = field.id.replace('IdKode', '').replace('Dok', '') +
                        'Container';
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.classList.remove('is-invalid');
                    }
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

    // Confirmation Modal
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const confirmSubmitBtn = document.getElementById('confirmSubmit');

    // Form validation and submission
    const form = document.getElementById('dokumenBpjsKesehatanForm');
    const validationAlert = document.getElementById('validationAlert');
    const validationMessages = document.getElementById('validationMessages');

    submitBtn.addEventListener('click', function(event) {
        // Validate all required fields
        const invalidFieldsList = highlightMissingFields();

        // Validate file input
        const fileIsValid = validateFileInput();

        if (invalidFieldsList.length > 0 || !fileIsValid) {
            // Show validation error alert
            validationMessages.innerHTML = '';

            if (invalidFieldsList.length > 0) {
                invalidFieldsList.forEach(function(field) {
                    const li = document.createElement('li');
                    li.textContent = field;
                    validationMessages.appendChild(li);
                });
            }

            if (!fileIsValid) {
                const li = document.createElement('li');
                li.textContent = 'File Dokumen';
                validationMessages.appendChild(li);
            }

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
        // Ensure all raw values are properly set before submission
        document.querySelectorAll('.currency-input').forEach(function(input) {
            const hiddenField = document.getElementById(input.id + '_raw');
            if (hiddenField) {
                hiddenField.value = parseCurrency(input.value);
            }
        });

        form.submit();
    });

    // Remove invalid class when input changes
    document.querySelectorAll('input, select, textarea').forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.hasAttribute('required') && this.value) {
                this.classList.remove('is-invalid');

                // Handle custom select
                if (this.id === 'IdKodeA04' || this.id === 'KategoriDok' || this.id ===
                    'JenisDok') {
                    const containerId = this.id.replace('IdKode', '').replace('Dok', '') +
                        'Container';
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.classList.remove('is-invalid');
                    }
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

                // Handle custom select
                if (this.id === 'IdKodeA04' || this.id === 'KategoriDok' || this.id ===
                    'JenisDok') {
                    const containerId = this.id.replace('IdKode', '').replace('Dok', '') +
                        'Container';
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.classList.remove('is-invalid');
                    }
                }
            }

            // Check if all invalid fields are now valid
            const invalidFields = document.querySelectorAll('.is-invalid');
            if (invalidFields.length === 0) {
                validationAlert.style.display = 'none';
            }
        });
    });

    // Initialize
    createPortals();
    createHiddenFields();

    // Initialize each select
    initCustomSelect('karyawan', 'IdKodeA04');
    initCustomSelect('kategori', 'KategoriDok');
    initCustomSelect('jenis', 'JenisDok');

    // Initialize form state
    hitungMasaBerlaku();

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

    // Initialize currency formatting for all values
    document.querySelectorAll('.currency-input').forEach(function(input) {
        const rawValue = parseCurrency(input.value);
        input.value = formatCurrency(input.value);

        // Set raw value in hidden field
        const hiddenField = document.getElementById(input.id + '_raw');
        if (hiddenField) {
            hiddenField.value = rawValue;
        }
    });

    // Perform initial calculation
    calculateAll();
});
    </script>
@endpush
