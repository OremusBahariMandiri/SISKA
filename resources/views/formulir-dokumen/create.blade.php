{{-- resources/views/formulir-dokumen/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Formulir Dokumen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-plus me-2"></i>Tambah Formulir Dokumen</span>
                        <a href="{{ route('formulir-dokumen.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('formulir-dokumen.store') }}" method="POST" id="formulirDokumenForm"
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
                                                    <input type="text"
                                                        class="form-control @error('NoRegDok') is-invalid @enderror"
                                                        id="NoRegDok" name="NoRegDok" value="{{ old('NoRegDok') }}"
                                                        placeholder="Masukkan nomor registrasi dokumen" required>
                                                    @error('NoRegDok')
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
                                </div>

                                <!-- Additional Information -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Tanggal & Status</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="TglTerbitDok" class="form-label fw-bold">Tanggal
                                                    Terbit <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-calendar-plus"></i></span>
                                                    <input type="date"
                                                        class="form-control @error('TglTerbitDok') is-invalid @enderror"
                                                        id="TglTerbitDok" name="TglTerbitDok"
                                                        value="{{ old('TglTerbitDok') }}" required>
                                                    @error('TglTerbitDok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="FileDok" class="form-label fw-bold">File Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-file-upload"></i></span>
                                                    <input type="file"
                                                        class="form-control @error('FileDok') is-invalid @enderror"
                                                        id="FileDok" name="FileDok" accept=".pdf,.jpg,.jpeg,.png"
                                                        required>
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
                                                <label for="StatusDok" class="form-label fw-bold">Status Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <div class="bg-light p-2 rounded">
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

                // Add filtered options
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

                    // Show alert for file missing
                    const fileAlert = document.createElement('div');
                    fileAlert.className = 'alert alert-warning alert-dismissible fade show mt-3';
                    fileAlert.innerHTML = `
                        <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-1"></i> File Dokumen Kosong!</h5>
                        <p>Anda belum memilih file dokumen. Silakan pilih file dengan format PDF, JPG, JPEG, atau PNG.</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;

                    // Insert alert below file input
                    const fileContainer = fileInput.closest('.form-group');
                    if (filePreviewContainer) {
                        fileContainer.insertBefore(fileAlert, filePreviewContainer.nextSibling);
                    } else {
                        fileContainer.appendChild(fileAlert);
                    }

                    return false;
                } else {
                    fileInput.classList.remove('is-invalid');
                    fileValidationMessage.classList.add('d-none');
                    return true;
                }
            }

            // Function to highlight missing fields
            function highlightMissingFields() {
                document.querySelectorAll('[required]').forEach(function(input) {
                    if (!input.value) {
                        input.classList.add('is-invalid');

                        // Handle custom select validation
                        if (input.type === 'hidden' && input.id.includes('Dok')) {
                            const containerId = input.id.replace('Dok', '') + 'Container';
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

            // Form validation and submission
            const form = document.getElementById('formulirDokumenForm');
            form.addEventListener('submit', function(event) {
                // Validate file input first
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
            initCustomSelect('kategori', 'KategoriDok');
            initCustomSelect('jenis', 'JenisDok');

            // Kategori change handler untuk update jenis dokumen
            document.getElementById('KategoriDok').addEventListener('change', function() {
                updateJenisDokumen(this.value);
            });

            // Initialize jenis dokumen if kategori is selected
            const oldKategori = document.getElementById('KategoriDok').value;
            if (oldKategori) {
                updateJenisDokumen(oldKategori);
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
        });
    </script>
@endpush