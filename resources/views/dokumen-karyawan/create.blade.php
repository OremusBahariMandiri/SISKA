@extends('layouts.app')

@section('title', 'Tambah Dokumen Karyawan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-plus me-2"></i>Tambah Dokumen Karyawan</span>
                        <a href="{{ route('dokumen-karyawan.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('dokumen-karyawan.store') }}" method="POST" id="dokumenKaryawanForm"
                            enctype="multipart/form-data">
                            @csrf

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
                                                        <input type="text" id="karyawanSearch" class="form-control custom-select-search @error('IdKodeA04') is-invalid @enderror"
                                                            placeholder="Cari karyawan..." autocomplete="off">

                                                        <div class="custom-select-dropdown" id="karyawanDropdown">
                                                            <div class="custom-select-search-wrapper">
                                                                <input type="text" id="karyawanFilterInput" class="form-control" placeholder="Ketik untuk mencari">
                                                            </div>
                                                            <div class="custom-select-options">
                                                                <div class="custom-select-option empty-option" data-value="" data-display="-- Pilih Karyawan --">-- Pilih Karyawan --</div>
                                                                @foreach ($karyawan as $employee)
                                                                    <div class="custom-select-option" data-value="{{ $employee->IdKode }}"
                                                                        data-display="{{ $employee->NamaKry }} - {{ $employee->NrkKry ?? '' }}">
                                                                        {{ $employee->NamaKry }} - {{ $employee->NrkKry ?? '' }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="IdKodeA04" id="IdKodeA04" value="{{ old('IdKodeA04') }}" required>
                                                    </div>
                                                    @error('IdKodeA04')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="KategoriDok" class="form-label fw-bold">Kategori Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                                    <select class="form-select @error('KategoriDok') is-invalid @enderror"
                                                        id="KategoriDok" name="KategoriDok" required>
                                                        <option value="">-- Pilih Kategori --</option>
                                                        @foreach ($kategoriDokumen as $kategori)
                                                            <option value="{{ $kategori->IdKode }}"
                                                                {{ old('KategoriDok') == $kategori->IdKode ? 'selected' : '' }}>
                                                                {{ $kategori->KategoriDok }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                                                    <select class="form-select @error('JenisDok') is-invalid @enderror"
                                                        id="JenisDok" name="JenisDok" required>
                                                        <option value="">-- Pilih Jenis Dokumen --</option>
                                                    </select>
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
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
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
                                <div class="card-header bg-secondary bg-opacity-25 text-white">
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
            // Custom Select Implementation for Karyawan
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
                    const event = new Event('change', { bubbles: true });
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

            // Form validation with visual feedback
            const form = document.getElementById('dokumenKaryawanForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Highlight missing required fields
                    document.querySelectorAll('[required]').forEach(function(input) {
                        if (!input.value) {
                            input.classList.add('is-invalid');

                            // Handle custom select validation
                            if (input.id === 'IdKodeA04') {
                                customSelectContainer.classList.add('is-invalid');
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
                            if (input.id === 'IdKodeA04') {
                                customSelectContainer.classList.remove('is-invalid');
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

            // Remove invalid class when input changes
            document.querySelectorAll('[required]').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');

                        // Handle custom select validation
                        if (this.id === 'IdKodeA04') {
                            customSelectContainer.classList.remove('is-invalid');
                        }
                    }
                });
            });

            // Elemen select kategori dan jenis dokumen
            const kategoriDokSelect = document.getElementById('KategoriDok');
            const jenisDokSelect = document.getElementById('JenisDok');

            // Simpan jenis dokumen yang sudah terpilih (untuk halaman edit)
            const selectedJenisDok = jenisDokSelect.value;

            // Data jenisDokumenByKategori dari controller
            const jenisDokumenByKategori = @json($jenisDokumenByKategori);

            // Fungsi untuk memfilter jenis dokumen berdasarkan kategori
            function filterJenisDokumen(kategoriId) {
                // Reset jenis dokumen select
                jenisDokSelect.innerHTML = '<option value="">-- Pilih Jenis Dokumen --</option>';

                if (!kategoriId) return;

                // Dapatkan jenis dokumen untuk kategori yang dipilih
                const dokumenList = jenisDokumenByKategori[kategoriId] || [];

                // Tambahkan opsi yang difilter ke elemen select
                dokumenList.forEach(dokumen => {
                    const option = document.createElement('option');
                    option.value = dokumen.JenisDok;
                    option.textContent = dokumen.JenisDok;

                    // Jika ini sebelumnya terpilih, pilih lagi
                    if (dokumen.JenisDok === selectedJenisDok) {
                        option.selected = true;
                    }

                    jenisDokSelect.appendChild(option);
                });
            }

            // Event listener untuk perubahan kategori
            kategoriDokSelect.addEventListener('change', function() {
                filterJenisDokumen(this.value);
            });

            // Inisialisasi pada saat memuat halaman jika kategori sudah terpilih
            if (kategoriDokSelect.value) {
                filterJenisDokumen(kategoriDokSelect.value);
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

            // Initialize form state
            toggleFieldsVisibility();

            // Calculate initial reminder period if fields already have values
            if (document.getElementById('TglPengingat').value && document.getElementById('TglBerakhirDok').value) {
                hitungMasaPengingat();
            }
        });
    </script>
@endpush