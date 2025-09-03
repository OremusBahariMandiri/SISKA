@extends('layouts.app')

@section('title', 'Manajemen Dokumen Kontrak')

@section('content')
    <div class="container-fluid dokumenKontrakPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-contract me-2"></i>Manajemen Dokumen Kontrak</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->user()->is_admin || ($userPermissions['tambah'] ?? false))
                                <a href="{{ route('dokumen-kontrak.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Tambahkan document status summary -->
                        <div class="mb-3 document-status-summary">
                            <span id="expiredDocsBadge" class="badge bg-danger me-2" style="font-size: 0.9rem;">
                                <i class="fas fa-exclamation-circle me-1"></i> Dokumen Expired : <span
                                    id="expiredDocsCount">0</span>
                            </span>
                            <span id="warningDocsBadge" class="badge text-dark me-2"
                                style="font-size: 0.9rem; background-color:#ffff66">
                                <i class="fas fa-exclamation-triangle me-1"></i>Dokumen Akan Expired : <span
                                    id="warningDocsCount">0</span>
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table id="dokumenKontrakTable" class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NRK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Masa Kerja</th>
                                        <th>No Registrasi</th>
                                        <th>Jenis</th>
                                        <th>Tgl Terbit</th>
                                        <th>Tgl Berakhir</th>
                                        <th>Tgl Peringatan</th>
                                        <th>Peringatan</th>
                                        <th>Catatan</th>
                                        <th>File</th>
                                        <th width="8%" class="text-center">Status</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dokumenKontrak as $index => $dokumen)
                                        <tr data-tgl-pengingat="{{ $dokumen->TglPengingat ? \Carbon\Carbon::parse($dokumen->TglPengingat)->format('Y-m-d') : '' }}"
                                            data-tgl-berakhir="{{ $dokumen->TglBerakhirDok ? \Carbon\Carbon::parse($dokumen->TglBerakhirDok)->format('Y-m-d') : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $dokumen->karyawan->NrkKry }}</td>
                                            <td>{{ $dokumen->karyawan->NamaKry ?? '-' }}</td>
                                            <td>
                                                @if ($dokumen->karyawan->TglMsk)
                                                    {{ \Carbon\Carbon::parse($dokumen->karyawan->TglMsk)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dokumen->karyawan->TglMsk)
                                                    @php
                                                        $tglMasuk = \Carbon\Carbon::parse($dokumen->karyawan->TglMsk);
                                                        $now = \Carbon\Carbon::now();
                                                        $years = $now->diffInYears($tglMasuk);
                                                        $months = $now
                                                            ->copy()
                                                            ->subYears($years)
                                                            ->diffInMonths($tglMasuk);
                                                    @endphp
                                                    {{ $years }} thn {{ $months }} bln
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $dokumen->NoRegDok }}</td>
                                            <td>{{ $dokumen->JenisDok }}</td>
                                            <td>
                                                @if ($dokumen->TglTerbitDok)
                                                    {{ \Carbon\Carbon::parse($dokumen->TglTerbitDok)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dokumen->TglBerakhirDok)
                                                    <span class="{{ $dokumen->is_expired ? 'text-danger fw-bold' : '' }}">
                                                        {{ \Carbon\Carbon::parse($dokumen->TglBerakhirDok)->format('d/m/Y') }}
                                                        @if ($dokumen->is_expired)
                                                            <i class="fas fa-exclamation-circle text-danger ms-1"
                                                                data-bs-toggle="tooltip" title="Sudah kedaluwarsa"></i>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dokumen->TglPengingat)
                                                    {{ \Carbon\Carbon::parse($dokumen->TglPengingat)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="sisa-peringatan-col">
                                                @if ($dokumen->MasaPengingat)
                                                    {{ $dokumen->MasaPengingat }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $dokumen->KetDok }}</td>
                                            <td class="text-center">
                                                @if ($dokumen->FileDok)
                                                    <div class="btn-group">
                                                        <a href="{{ route('dokumen-kontrak.viewDocument', $dokumen->Id) }}"
                                                            target="_blank" class="btn btn-sm btn-info"
                                                            data-bs-toggle="tooltip" title="Lihat Dokumen">
                                                            <i class="fas fa-eye"></i> Lihat
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($dokumen->StatusDok == 'Berlaku')
                                                    <span class="badge" style="background-color: blue">Berlaku</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Berlaku</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->user()->is_admin || ($userPermissions['detail'] ?? false))
                                                        <a href="{{ route('dokumen-kontrak.show', $dokumen->Id) }}"
                                                            class="btn btn-sm text-white" data-bs-toggle="tooltip"
                                                            title="Detail" style="background-color: #4a90e2;">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['ubah'] ?? false))
                                                        <a href="{{ route('dokumen-kontrak.edit', $dokumen->Id) }}"
                                                            class="btn btn-sm text-white" data-bs-toggle="tooltip"
                                                            title="Edit" style="background-color: #8e44ad;">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['hapus'] ?? false))
                                                        <button type="button" class="btn btn-sm text-white delete-confirm"
                                                            data-bs-toggle="tooltip" title="Hapus"
                                                            style="background-color: #f700ff;"
                                                            data-id="{{ $dokumen->Id }}"
                                                            data-name="{{ $dokumen->NoRegDok }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filter Dokumen Kontrak
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="filter_noreg" class="form-label">Nomor Registrasi</label>
                                    <input type="text" class="form-control" id="filter_noreg"
                                        placeholder="Masukan No Registrasi">
                                </div>

                                <div class="mb-3">
                                    <label for="filter_karyawan" class="form-label">Nama Karyawan</label>
                                    <select class="form-select select2" id="filter_karyawan">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($dokumenKontrak->pluck('karyawan.NamaKry')->unique() as $namaKaryawan)
                                            @if ($namaKaryawan)
                                                <option value="{{ $namaKaryawan }}">{{ $namaKaryawan }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_kategori" class="form-label">Kategori</label>
                                    <select class="form-select select2" id="filter_kategori">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($dokumenKontrak->pluck('KategoriDok')->unique() as $kategori)
                                            @if ($kategori)
                                                <option value="{{ $kategori }}">{{ $kategori }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_jenis" class="form-label">Jenis Dokumen</label>
                                    <select class="form-select select2" id="filter_jenis">
                                        <option value="">Semua Jenis</option>
                                        @foreach ($dokumenKontrak->pluck('JenisDok')->unique() as $jenis)
                                            @if ($jenis)
                                                <option value="{{ $jenis }}">{{ $jenis }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_tgl_terbit" class="form-label">Tanggal Terbit</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter_tgl_terbit_from"
                                            placeholder="Dari">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" id="filter_tgl_terbit_to"
                                            placeholder="Sampai">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_tgl_berakhir" class="form-label">Tanggal Berakhir</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter_tgl_berakhir_from"
                                            placeholder="Dari">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" id="filter_tgl_berakhir_to"
                                            placeholder="Sampai">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_status" class="form-label">Status Dokumen</label>
                                    <select class="form-select select2" id="filter_status">
                                        <option value="">Semua Status</option>
                                        <option value="Valid">Berlaku</option>
                                        <option value="Warning">Segera Habis</option>
                                        <option value="Expired">Expired</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="resetFilter">
                        <i class="fas fa-undo me-1"></i>Reset Filter
                    </button>
                    <button type="button" class="btn btn-primary" id="applyFilter">
                        <i class="fas fa-check me-1"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exportModalLabel">
                        <i class="fas fa-download me-2"></i>Ekspor Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" id="exportExcel">
                            <i class="fas fa-file-excel me-2"></i> Ekspor ke Excel
                        </button>
                        <button type="button" class="btn btn-danger" id="exportPdf">
                            <i class="fas fa-file-pdf me-2"></i> Ekspor ke PDF
                        </button>
                        <button type="button" class="btn btn-secondary" id="exportPrint">
                            <i class="fas fa-print me-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus dokumen kontrak dengan nomor registrasi <strong
                            id="dokumenNameToDelete"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        /* CSS dengan spesifisitas tinggi untuk DataTables */
        .dokumenKontrakPage .dataTables_wrapper .dataTables_length,
        .dokumenKontrakPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .dokumenKontrakPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .dokumenKontrakPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .dokumenKontrakPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .dokumenKontrakPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .dokumenKontrakPage table.dataTable thead th.sorting:after,
        .dokumenKontrakPage table.dataTable thead th.sorting_asc:after,
        .dokumenKontrakPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .dokumenKontrakPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .dokumenKontrakPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .dokumenKontrakPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* ===== HIGHLIGHT ROWS STYLING ===== */
        /* Custom CSS untuk highlight rows */
        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-red {
            background-color: #fc0000 !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-yellow {
            background-color: #ffff00 !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-orange {
            background-color: #00e013 !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-gray {
            background-color: #cccccc !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        /* Ensure hover states don't override highlight colors */
        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-red:hover {
            background-color: #ff3333 !important;
        }

        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-yellow:hover {
            background-color: #ffff66 !important;
        }

        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-orange:hover {
            background-color: #00e013 !important;
        }

        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-gray:hover {
            background-color: #dddddd !important;
        }

        /* Override Bootstrap's striped table styles */
        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(odd).highlight-red,
        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(even).highlight-red {
            background-color: #fc0000 !important;
        }

        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(odd).highlight-yellow,
        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(even).highlight-yellow {
            background-color: #ffff00 !important;
        }

        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(odd).highlight-orange,
        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(even).highlight-orange {
            background-color: #00e013 !important;
        }

        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(odd).highlight-gray,
        .dokumenKontrakPage .table-striped>tbody>tr:nth-of-type(even).highlight-gray {
            background-color: #cccccc !important;
        }

        /* Memastikan kolom tabel tetap terlihat meskipun dalam baris yang di-highlight */
        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-red>td,
        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-yellow>td,
        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-orange>td,
        .dokumenKontrakPage table#dokumenKontrakTable tbody tr.highlight-gray>td {
            background-color: inherit !important;
        }

        /* Add hover effect to action buttons */
        .dokumenKontrakPage .btn-sm {
            transition: transform 0.2s;
        }

        .dokumenKontrakPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .dokumenKontrakPage #dokumenKontrakTable tbody tr {
            transition: all 0.2s ease;
        }

        .dokumenKontrakPage #dokumenKontrakTable tbody tr:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
            cursor: pointer;
            position: relative;
            z-index: 1;
        }

        /* Flash effect when hovering */
        @keyframes flashBorder {
            0% {
                box-shadow: 0 0 0 rgba(13, 110, 253, 0);
            }

            50% {
                box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
            }

            100% {
                box-shadow: 0 0 0 rgba(13, 110, 253, 0);
            }
        }

        .dokumenKontrakPage #dokumenKontrakTable tbody tr.row-hover-active {
            animation: flashBorder 1s ease infinite;
        }

        /* Highlight filter active state */
        .filter-active {
            background-color: #e8f4ff !important;
            border-left: 3px solid #0d6efd !important;
        }

        /* Hidden buttons untuk export */
        .dt-buttons {
            display: none !important;
        }

        /* Select2 custom styling */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            height: auto;
            min-height: 38px;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            padding-right: 0;
        }
    </style>
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize modals
            var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
            var exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
            var deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));

            // Indonesian language configuration for DataTables
            const indonesianLanguage = {
                "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "aria": {
                    "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                    "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
                }
            };

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize Select2 for filter dropdowns
            setTimeout(function() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#filterModal .modal-body'),
                    placeholder: 'Pilih filter...',
                    allowClear: true
                });
            }, 500);

            // Event listener for filter button
            $('#filterButton').on('click', function() {
                filterModal.show();
            });

            // Event listener for export button
            $('#exportButton').on('click', function() {
                exportModal.show();
            });

            // Prevent DataTables reinit error
            if ($.fn.dataTable.isDataTable('#dokumenKontrakTable')) {
                // Destroy existing instance if it exists
                $('#dokumenKontrakTable').DataTable().destroy();
            }

            

            // Fungsi untuk highlighting baris yang terlihat saja
            function applyVisibleRowHighlighting() {
                // Reset semua highlight di baris yang terlihat
                $('#dokumenKontrakTable tbody tr').removeClass(
                    'highlight-red highlight-yellow highlight-orange highlight-gray');

                // Apply highlighting untuk baris yang terlihat saja
                $('#dokumenKontrakTable tbody tr').each(function() {
                    const row = $(this);

                    // Cek status dokumen terlebih dahulu (prioritas tertinggi)
                    const statusText = row.find('td:eq(13)').text().trim();

                    if (statusText.includes("Tidak Berlaku")) {
                        row.addClass('highlight-gray');
                        return; // Stop di sini - abu-abu memiliki prioritas tertinggi
                    }

                    // Logic untuk TglBerakhir (prioritas kedua)
                    const tglBerakhir = row.find('td:eq(8)').text().trim();
                    if (tglBerakhir !== '-') {
                        const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                        const today = moment();

                        if (berakhirDate.isBefore(today)) {
                            row.addClass('highlight-red');

                            // Add a data attribute for masa peringatan
                            row.attr('data-masa-peringatan', 'Expired');
                            return; // Stop di sini - merah memiliki prioritas
                        }

                        // Check warning levels based on days remaining
                        const daysRemaining = berakhirDate.diff(today, 'days');

                        if (daysRemaining <= 3) {
                            row.addClass('highlight-red');
                            row.attr('data-masa-peringatan', 'Kurang dari 3 hari');
                            return;
                        } else if (daysRemaining <= 7) {
                            row.addClass('highlight-yellow');
                            row.attr('data-masa-peringatan', 'Kurang dari 7 hari');
                            return;
                        } else if (daysRemaining <= 30) {
                            row.addClass('highlight-orange');
                            row.attr('data-masa-peringatan', 'Kurang dari 30 hari');
                            return;
                        } else {
                            row.attr('data-masa-peringatan', 'Lebih dari 30 hari');
                        }
                    }

                    // Logic untuk TglPengingat (prioritas ketiga)
                    const tglPengingatStr = row.data('tgl-pengingat');
                    if (tglPengingatStr) {
                        const tglPengingat = moment(tglPengingatStr);
                        const today = moment();
                        const diffDays = tglPengingat.diff(today, 'days');

                        if (diffDays < 0 || diffDays === 0) {
                            // Tanggal pengingat sudah lewat atau hari ini
                            row.addClass('highlight-red');
                            row.attr('data-masa-peringatan', 'Waktu pengingat telah tiba');
                            return; // Stop di sini - merah memiliki prioritas
                        } else if (diffDays <= 3) {
                            row.addClass('highlight-red');
                            row.attr('data-masa-peringatan', 'Kurang dari 3 hari');
                            return;
                        } else if (diffDays <= 7) {
                            row.addClass('highlight-yellow');
                            row.attr('data-masa-peringatan', 'Kurang dari 7 hari');
                            return;
                        } else if (diffDays <= 30) {
                            row.addClass('highlight-orange');
                            row.attr('data-masa-peringatan', 'Kurang dari 30 hari');
                            return;
                        }
                    }
                });
            }

            // Fungsi untuk mendapatkan statistik dokumen
            function updateDocumentStats() {
                // Hapus kelas highlight terlebih dahulu untuk memastikan penghitungan yang bersih
                $('#dokumenKontrakTable tbody tr').removeClass(
                    'highlight-red highlight-yellow highlight-orange highlight-gray');

                // Inisialisasi counter
                let expiredCount = 0;
                let warningCount = 0;

                // Periksa setiap baris tabel untuk menentukan status
                $('#dokumenKontrakTable tbody tr').each(function() {
                    const row = $(this);
                    let isExpired = false;
                    let isWarning = false;

                    // Cek status dokumen terlebih dahulu
                    const statusText = row.find('td:eq(13)').text().trim();

                    // Jika status "Tidak Berlaku", baris dilewati untuk penghitungan expired/warning
                    if (statusText.includes("Tidak Berlaku")) {
                        row.addClass('highlight-gray');
                        return; // Lanjut ke baris berikutnya
                    }

                    // Logic untuk TglBerakhir (prioritas kedua)
                    const tglBerakhir = row.find('td:eq(8)').text().trim();
                    if (tglBerakhir !== '-') {
                        const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                        const today = moment();

                        if (berakhirDate.isBefore(today)) {
                            row.addClass('highlight-red');
                            isExpired = true;
                            expiredCount++;
                            return; // Lanjut ke baris berikutnya
                        }
                    }

                    // Logic untuk TglPengingat
                    const tglPengingatStr = row.data('tgl-pengingat');
                    if (tglPengingatStr) {
                        const tglPengingat = moment(tglPengingatStr);
                        const today = moment();
                        const diffDays = tglPengingat.diff(today, 'days');

                        if (diffDays < 0 || diffDays === 0) {
                            // Tanggal pengingat sudah lewat atau hari ini
                            row.addClass('highlight-red');
                            if (!isExpired) {
                                expiredCount++;
                                isExpired = true;
                            }
                            return; // Lanjut ke baris berikutnya
                        } else if (diffDays <= 7) {
                            row.addClass('highlight-yellow');
                            if (!isExpired) {
                                warningCount++;
                                isWarning = true;
                            }
                            return; // Lanjut ke baris berikutnya
                        } else if (diffDays <= 30) {
                            row.addClass('highlight-orange');
                            if (!isExpired && !isWarning) {
                                warningCount++;
                            }
                            return; // Lanjut ke baris berikutnya
                        }
                    }

                    // Logic untuk TglBerakhir dalam 30 hari (prioritas terakhir)
                    if (tglBerakhir !== '-') {
                        const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                        const today = moment();

                        if (berakhirDate.isAfter(today) && berakhirDate.isBefore(moment().add(30,
                                'days'))) {
                            row.addClass('highlight-yellow');
                            if (!isExpired && !isWarning) {
                                warningCount++;
                            }
                        }
                    }
                });

                // Update counter badges dengan data yang dihitung
                $('#expiredDocsCount').text(expiredCount);
                $('#warningDocsCount').text(warningCount);

                // Selalu tampilkan badges terlepas dari jumlahnya
                $('#expiredDocsBadge').show();
                $('#warningDocsBadge').show();
            }

            // Fungsi untuk memperbarui teks masa peringatan
            function updateMasaPengingatText() {
                const today = moment();

                $('#dokumenKontrakTable tbody tr').each(function() {
                    const tglPengingatStr = $(this).data('tgl-pengingat');
                    const $masaPengingatCol = $(this).find('.sisa-peringatan-col');

                    // Jika tidak ada tanggal pengingat, lewati baris ini
                    if (!tglPengingatStr) {
                        $masaPengingatCol.text('-');
                        return;
                    }

                    // Parse tanggal pengingat
                    const tglPengingat = moment(tglPengingatStr);

                    // Hitung selisih dalam hari
                    const diffDays = tglPengingat.diff(today, 'days');

                    // Menentukan teks yang akan ditampilkan di kolom masa peringatan
                    let masaPengingatText = '';

                    if (diffDays < 0) {
                        // Tanggal pengingat sudah lewat
                        masaPengingatText = 'Terlambat ' + Math.abs(diffDays) + ' hari';
                    } else if (diffDays === 0) {
                        // Tanggal pengingat hari ini
                        masaPengingatText = 'Hari ini';
                    } else {
                        // Tanggal pengingat di masa depan
                        masaPengingatText = diffDays + ' hari lagi';
                    }

                    // Update teks masa peringatan
                    $masaPengingatCol.text(masaPengingatText);
                });
            }

            // Format tanggal untuk filter
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Make sure we're only processing the correct table
                    if (settings.nTable.id !== 'dokumenKontrakTable') return true;

                    // Tanggal terbit filter
                    let terbitFrom = $('#filter_tgl_terbit_from').val();
                    let terbitTo = $('#filter_tgl_terbit_to').val();

                    // Get the date text from column 7 (Tgl Terbit)
                    let terbitText = data[7].trim();
                    // Skip processing if it's just a dash or empty
                    if (terbitText === '-' || terbitText === '<span class="text-muted">-</span>') {
                        if (terbitFrom === '' && terbitTo === '') {
                            // No date filter applied, include this row
                            return true;
                        } else {
                            // Date filter applied but this row has no date, exclude it
                            return false;
                        }
                    }

                    // Clean the text from any HTML tags
                    terbitText = terbitText.replace(/<[^>]*>/g, '').trim();

                    // Convert the date text to a moment object (format: DD/MM/YYYY)
                    let terbitDate = moment(terbitText, 'DD/MM/YYYY');

                    // Check if this date passes the filter
                    let passesTermitFilter =
                        (terbitFrom === '' && terbitTo === '') || // No filter
                        (terbitFrom !== '' && terbitTo === '' && terbitDate.isSameOrAfter(moment(
                            terbitFrom))) || // Only from filter
                        (terbitFrom === '' && terbitTo !== '' && terbitDate.isSameOrBefore(moment(terbitTo))) ||
                        // Only to filter
                        (terbitFrom !== '' && terbitTo !== '' && terbitDate.isBetween(moment(terbitFrom),
                            moment(terbitTo), 'day', '[]')); // Both filters

                    if (!passesTermitFilter) {
                        return false;
                    }

                    // Tanggal berakhir filter (column 8)
                    let berakhirFrom = $('#filter_tgl_berakhir_from').val();
                    let berakhirTo = $('#filter_tgl_berakhir_to').val();

                    let berakhirText = data[8].trim();
                    // Handle extra HTML in the cell (remove any HTML tags)
                    berakhirText = berakhirText.replace(/<[^>]*>/g, '').trim();

                    if (berakhirText === '-') {
                        if (berakhirFrom === '' && berakhirTo === '') {
                            // No filter for tanggal berakhir
                            return true;
                        } else {
                            // Filter applied but this row has no end date
                            return false;
                        }
                    }

                    let berakhirDate = moment(berakhirText, 'DD/MM/YYYY');

                    let passesBerakhirFilter =
                        (berakhirFrom === '' && berakhirTo === '') || // No filter
                        (berakhirFrom !== '' && berakhirTo === '' && berakhirDate.isSameOrAfter(moment(
                            berakhirFrom))) || // Only from filter
                        (berakhirFrom === '' && berakhirTo !== '' && berakhirDate.isSameOrBefore(moment(
                            berakhirTo))) || // Only to filter
                        (berakhirFrom !== '' && berakhirTo !== '' && berakhirDate.isBetween(moment(
                            berakhirFrom), moment(berakhirTo), 'day', '[]')); // Both filters

                    if (!passesBerakhirFilter) {
                        return false;
                    }

                    // Filter status dokumen
                    let status = $('#filter_status').val();
                    if (status === '') {
                        return true; // No status filter applied
                    } else if (status === 'Berlaku') {
                        // Directly check if StatusDok column (index 13) contains "Berlaku"
                        return data[13].includes("Berlaku");
                    } else if (status === 'Tidak Berlaku') {
                        // Directly check if StatusDok column (index 13) contains "Tidak Berlaku"
                        return data[13].includes("Tidak Berlaku");
                    } else if (status === 'Warning') {
                        // For "Warning" status - documents that will expire soon but are still valid
                        return data[13].includes("Berlaku") && berakhirDate &&
                            berakhirDate.isAfter(moment()) &&
                            berakhirDate.isBefore(moment().add(30, 'days'));
                    } else if (status === 'Expired') {
                        // For "Expired" status - documents past their expiration date but still marked as valid
                        return data[13].includes("Berlaku") && berakhirDate &&
                            berakhirDate.isBefore(moment());
                    }

                    return true;
                }
            );

            // Inisialisasi DataTable
            var table = $('#dokumenKontrakTable').DataTable({
                responsive: true,
                language: indonesianLanguage,
                columnDefs: [{
                        // Prioritas tertinggi untuk kolom yang paling penting
                        responsivePriority: 1,
                        targets: [0, 1, 2, 13] // No, NRK, Nama Karyawan, Status
                    },
                    {
                        // Prioritas kedua untuk kolom penting lainnya
                        responsivePriority: 2,
                        targets: [6, 3] // Jenis, Tanggal Masuk
                    },
                    {
                        // Prioritas ketiga untuk kolom tanggal
                        responsivePriority: 3,
                        targets: [7, 8, 9, 10] // Tgl Terbit, Tgl Berakhir, Tgl Peringatan, Peringatan
                    },
                    {
                        // Prioritas keempat untuk kolom yang tidak terlalu penting
                        responsivePriority: 4,
                        targets: [11, 12, 14] // Catatan, File, Aksi
                    },
                    {
                        // Kolom yang tidak bisa di-sort
                        orderable: false,
                        targets: [0, 14] // No dan Aksi
                    },
                    {
                        // Custom sorting untuk kolom Status (kolom 13)
                        // Memastikan dokumen "Tidak Berlaku" selalu di bawah
                        targets: 13,
                        type: 'string',
                        render: function(data, type, row, meta) {
                            // Untuk tipe display, tampilkan data asli
                            if (type === 'display') {
                                return data;
                            }
                            // Untuk sorting, taruh "Tidak Berlaku" di paling akhir
                            // dengan menambahkan prefix 'z' (akan selalu di akhir alfabet)
                            if (type === 'sort') {
                                return data.includes('Tidak Berlaku') ? 'z' + data : 'a' + data;
                            }
                            return data;
                        }
                    }
                ],
                order: [
                    [13,
                        'asc'
                    ], // Mengurutkan berdasarkan status terlebih dahulu (Tidak Berlaku di akhir)
                    [1, 'asc'] // Kemudian berdasarkan NRK
                ],
                buttons: [{
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-sm btn-success d-none excel-export-btn',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-sm btn-danger d-none pdf-export-btn',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-sm btn-secondary d-none print-export-btn',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                // Atur agar nomor selalu mulai dari 1 pada tiap halaman
                drawCallback: function() {
                    // Mengupdate nomor urut setiap kali tabel digambar ulang
                    this.api().column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });

                    // Apply highlighting untuk baris yang terlihat
                    applyVisibleRowHighlighting();

                    // Update text untuk masa peringatan
                    updateMasaPengingatText();
                },
                initComplete: function() {
                    // Force style search input box
                    $('.dataTables_filter input').addClass('form-control');

                    // Apply initial highlighting
                    applyVisibleRowHighlighting();

                    // Update text untuk masa peringatan
                    updateMasaPengingatText();

                    // Tunggu sampai tabel selesai diinisialisasi dan semua data dimuat
                    setTimeout(function() {
                        // Hitung stats dari SEMUA data, bukan hanya yang terlihat di halaman saat ini
                        updateDocumentStats();
                    }, 500);
                }
            });

            // Modifikasi event handler untuk tombol Apply Filter
            $('#applyFilter').on('click', function() {
                // Clear existing search filters
                table.search('').columns().search('');

                // Apply each filter individually
                if ($('#filter_noreg').val()) {
                    table.column(5).search($('#filter_noreg').val()); // No Registrasi
                }

                if ($('#filter_karyawan').val()) {
                    table.column(2).search($('#filter_karyawan').val()); // Nama Karyawan
                }

                if ($('#filter_kategori').val()) {
                    // If you have a category column, filter by it
                    // Column index might need adjustment based on your table
                    const kategoriIndex = table.column('KategoriDok:name').index();
                    if (kategoriIndex !== undefined) {
                        table.column(kategoriIndex).search($('#filter_kategori').val());
                    }
                }

                if ($('#filter_jenis').val()) {
                    table.column(6).search($('#filter_jenis').val()); // Jenis Dokumen
                }

                // Special handling for the new warning period filter
                const statusFilter = $('#filter_status').val();
                if (statusFilter) {
                    if (statusFilter === 'Berlaku' || statusFilter === 'Tidak Berlaku') {
                        // These are handled by the existing search.push function above
                    } else if (statusFilter === 'Warning30' || statusFilter === 'Warning7' ||
                        statusFilter === 'Warning3' || statusFilter === 'Expired') {
                        // Custom filtering for warning periods
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            const row = $(table.row(dataIndex).node());
                            const warningPeriod = row.attr('data-masa-peringatan');

                            if (statusFilter === 'Warning30' && warningPeriod ===
                                'Kurang dari 30 hari') return true;
                            if (statusFilter === 'Warning7' && warningPeriod ===
                                'Kurang dari 7 hari') return true;
                            if (statusFilter === 'Warning3' && warningPeriod ===
                                'Kurang dari 3 hari') return true;
                            if (statusFilter === 'Expired' && warningPeriod === 'Expired')
                                return true;

                            return false;
                        });
                    }
                }

                // Refresh table to apply all filters
                table.draw();

                // Hide the filter modal
                $('#filterModal').modal('hide');

                // Highlight filter button if any filter is active
                highlightFilterButton();

                // Update statistics after filtering
                updateDocumentStats();

                // Clear the custom filter functions after table is drawn
                if (statusFilter && ['Warning30', 'Warning7', 'Warning3', 'Expired'].includes(
                        statusFilter)) {
                    $.fn.dataTable.ext.search.pop();
                }
            });

            // Modify the Reset Filter event handler
            $('#resetFilter').on('click', function() {
                // Reset the form fields
                $('#filterForm')[0].reset();

                // Reset Select2 fields specifically
                $('#filter_karyawan, #filter_kategori, #filter_jenis, #filter_status').val(null).trigger(
                    'change');

                // Remove active class from filter button
                $('#filterButton').removeClass('filter-active');

                // Reset table filters
                table.search('').columns().search('').draw();

                // Refresh statistik setelah filter direset
                updateDocumentStats();
            });

            // Event listener untuk table draw event
            table.on('draw.dt', function() {
                // Update text untuk baris yang terlihat
                updateMasaPengingatText();

                // Update highlight untuk baris yang terlihat
                applyVisibleRowHighlighting();
            });

            // Highlight filter button jika ada filter aktif
            function highlightFilterButton() {
                if ($('#filter_noreg').val() ||
                    $('#filter_karyawan').val() ||
                    $('#filter_kategori').val() ||
                    $('#filter_jenis').val() ||
                    $('#filter_tgl_terbit_from').val() ||
                    $('#filter_tgl_terbit_to').val() ||
                    $('#filter_tgl_berakhir_from').val() ||
                    $('#filter_tgl_berakhir_to').val() ||
                    $('#filter_status').val()) {
                    $('#filterButton').addClass('filter-active');
                } else {
                    $('#filterButton').removeClass('filter-active');
                }
            }

            // Export buttons
            $('#exportExcel').on('click', function() {
                $('.excel-export-btn').trigger('click');
                exportModal.hide();
            });

            $('#exportPdf').on('click', function() {
                $('.pdf-export-btn').trigger('click');
                exportModal.hide();
            });

            $('#exportPrint').on('click', function() {
                $('.print-export-btn').trigger('click');
                exportModal.hide();
            });

            // Handle Delete Confirmation
            $(document).on('click', '.delete-confirm', function(e) {
                // Prevent any default action
                e.preventDefault();
                e.stopPropagation();

                // Get document ID and name
                var id = $(this).data('id');
                var name = $(this).data('name');

                // Set the document name in the modal
                $('#dokumenNameToDelete').text(name);

                // Set form action URL
                $('#deleteForm').attr('action', "/dokumen-kontrak/" + id);

                // Show the delete confirmation modal
                deleteConfirmationModal.show();
            });

            // Tambahkan efek klik pada baris tabel untuk menuju halaman detail
            $('#dokumenKontrakTable tbody').on('click', 'tr', function(e) {
                // Jangan ikuti link jika yang diklik adalah tombol atau link di dalam baris
                if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                    $(e.target).closest('button').length || $(e.target).closest('a').length) {
                    return;
                }

                // Check if user has detail access before redirecting
                var detailLink = $(this).find('a[title="Detail"]').attr('href');
                if (detailLink) {
                    window.location.href = detailLink;
                }
            });

            // Tambahkan efek flash saat baris di-hover
            $('#dokumenKontrakTable tbody').on('mouseenter', 'tr', function() {
                $(this).addClass('row-hover-active');
            }).on('mouseleave', 'tr', function() {
                $(this).removeClass('row-hover-active');
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);

            // Inisialisasi awal
            updateMasaPengingatText();
            updateDocumentStats();
        });
    </script>
@endpush
