@extends('layouts.app')

@section('title', 'Manajemen Dokumen Karyawan')

@section('content')
    <div class="container-fluid dokumenKaryawanPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-alt me-2"></i>Manajemen Dokumen Karyawan</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->user()->is_admin || ($userPermissions['tambah'] ?? false))
                                <a href="{{ route('dokumen-karyawan.create') }}" class="btn btn-light">
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
                            <table id="dokumenKaryawanTable" class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NRK</th>
                                        <th>Nama Karyawan</th>
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
                                    @foreach ($dokumenKaryawan as $index => $dokumen)
                                        <tr data-tgl-pengingat="{{ $dokumen->TglPengingat ? $dokumen->TglPengingat->format('Y-m-d') : '' }}"
                                            data-tgl-berakhir="{{ $dokumen->TglBerakhirDok ? $dokumen->TglBerakhirDok->format('Y-m-d') : '' }}"
                                            data-goldok="{{ (int) ($dokumen->GolDok ?? 999) }}"
                                            data-employee-id="{{ $dokumen->IdKodeA04 }}"
                                            data-employee-name="{{ $dokumen->karyawan->NamaKry ?? '-' }}"
                                            data-jenis-dok="{{ $dokumen->JenisDok }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $dokumen->karyawan->NrkKry ?? '-' }}</td>
                                            <td>{{ $dokumen->karyawan->NamaKry ?? '-' }}</td>
                                            <td>{{ $dokumen->NoRegDok }}</td>
                                            <td>{{ $dokumen->JenisDok }}</td>
                                            <td>
                                                @if ($dokumen->TglTerbitDok)
                                                    {{ $dokumen->TglTerbitDok->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dokumen->TglBerakhirDok)
                                                    <span class="{{ $dokumen->is_expired ? 'text-danger fw-bold' : '' }}">
                                                        {{ $dokumen->TglBerakhirDok->format('d/m/Y') }}
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
                                                    {{ $dokumen->TglPengingat->format('d/m/Y') }}
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
                                            <td>
                                                <a href="{{ route('viewdocumentkaryawan', $dokumen->id) }}"
                                                    class="btn btn-sm text-white" data-bs-toggle="tooltip" title="View"
                                                    style="background-color: #2980b9;" target="_blank">
                                                    <i class="fas fa-eye"></i>Lihat
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if ($dokumen->StatusDok == 'Berlaku')
                                                    <span class="badge" style="background-color: blue">Berlaku</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Berlaku</span>
                                                @endif
                                            </td>

                                            <!-- Bagian tombol aksi sesuai dengan hak akses -->
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->user()->is_admin || ($userPermissions['detail'] ?? false))
                                                        <a href="{{ route('dokumen-karyawan.show', $dokumen->id) }}"
                                                            class="btn btn-sm text-white" data-bs-toggle="tooltip"
                                                            title="Detail" style="background-color: #4a90e2;">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['ubah'] ?? false))
                                                        <a href="{{ route('dokumen-karyawan.edit', $dokumen->id) }}"
                                                            class="btn btn-sm text-white" data-bs-toggle="tooltip"
                                                            title="Edit" style="background-color: #8e44ad;">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['hapus'] ?? false))
                                                        <button type="button" class="btn btn-sm text-white delete-confirm"
                                                            data-bs-toggle="tooltip" title="Hapus"
                                                            style="background-color: #f700ff;"
                                                            data-id="{{ $dokumen->id }}"
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
                <div class="modal-header text-white" style="background-color: #04786e">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filter Dokumen Karyawan
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
                                    <select class="form-select" id="filter_karyawan">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($dokumenKaryawan->pluck('karyawan.NamaKry')->unique() as $namaKaryawan)
                                            @if ($namaKaryawan)
                                                <option value="{{ $namaKaryawan }}">{{ $namaKaryawan }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="filter_kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="filter_kategori">
                                        <option value="">Semua Kategori</option>
                                        @php
                                            $kategoris = App\Models\KategoriDokumen::all();
                                        @endphp
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->IdKode }}">{{ $kategori->KategoriDok }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class="mb-3">
                                    <label for="filter_jenis" class="form-label">Jenis Dokumen</label>
                                    <select class="form-select" id="filter_jenis">
                                        <option value="">Semua Jenis</option>
                                        @foreach ($dokumenKaryawan->pluck('JenisDok')->unique() as $jenis)
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
                                    <select class="form-select" id="filter_status">
                                        <option value="">Semua Status</option>
                                        <option value="Berlaku">Berlaku</option>
                                        <option value="Tidak Berlaku">Tidak Berlaku</option>
                                        <option value="Warning">Akan Expired</option>
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

    <!-- Hidden Export Form -->
    <form id="exportForm" action="{{ route('dokumen-karyawan.export-excel') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="filter_noreg" id="export_filter_noreg">
        <input type="hidden" name="filter_karyawan" id="export_filter_karyawan">
        <input type="hidden" name="filter_jenis" id="export_filter_jenis">
        <input type="hidden" name="filter_tgl_terbit_from" id="export_filter_tgl_terbit_from">
        <input type="hidden" name="filter_tgl_terbit_to" id="export_filter_tgl_terbit_to">
        <input type="hidden" name="filter_tgl_berakhir_from" id="export_filter_tgl_berakhir_from">
        <input type="hidden" name="filter_tgl_berakhir_to" id="export_filter_tgl_berakhir_to">
        <input type="hidden" name="filter_status" id="export_filter_status">
    </form>

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
                    <p>Apakah Anda yakin ingin menghapus dokumen karyawan dengan nomor registrasi <strong
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
    <style>
        /* CSS dengan spesifisitas tinggi untuk DataTables */
        .dokumenKaryawanPage .dataTables_wrapper .dataTables_length,
        .dokumenKaryawanPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .dokumenKaryawanPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .dokumenKaryawanPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .dokumenKaryawanPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .dokumenKaryawanPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .dokumenKaryawanPage table.dataTable thead th.sorting:after,
        .dokumenKaryawanPage table.dataTable thead th.sorting_asc:after,
        .dokumenKaryawanPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .dokumenKaryawanPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .dokumenKaryawanPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .dokumenKaryawanPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* ===== HIGHLIGHT ROWS STYLING ===== */
        /* Custom CSS untuk highlight rows */
        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-red {
            background-color: #fc0000 !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-yellow {
            background-color: #ffff00 !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-orange {
            background-color: #00e013 !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-gray {
            background-color: #cccccc !important;
            color: rgb(0, 0, 0) !important;
            --bs-table-accent-bg: none !important;
            --bs-table-striped-bg: none !important;
        }

        /* Ensure hover states don't override highlight colors */
        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-red:hover {
            background-color: #ff3333 !important;
        }

        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-yellow:hover {
            background-color: #ffff66 !important;
        }

        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-orange:hover {
            background-color: #00e013 !important;
        }

        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-gray:hover {
            background-color: #dddddd !important;
        }

        /* Override Bootstrap's striped table styles */
        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(odd).highlight-red,
        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(even).highlight-red {
            background-color: #fc0000 !important;
        }

        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(odd).highlight-yellow,
        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(even).highlight-yellow {
            background-color: #ffff00 !important;
        }

        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(odd).highlight-orange,
        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(even).highlight-orange {
            background-color: #00e013 !important;
        }

        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(odd).highlight-gray,
        .dokumenKaryawanPage .table-striped>tbody>tr:nth-of-type(even).highlight-gray {
            background-color: #cccccc !important;
        }

        /* Memastikan kolom tabel tetap terlihat meskipun dalam baris yang di-highlight */
        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-red>td,
        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-yellow>td,
        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-orange>td,
        .dokumenKaryawanPage table#dokumenKaryawanTable tbody tr.highlight-gray>td {
            background-color: inherit !important;
        }

        /* Add hover effect to action buttons */
        .dokumenKaryawanPage .btn-sm {
            transition: transform 0.2s;
        }

        .dokumenKaryawanPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .dokumenKaryawanPage #dokumenKaryawanTable tbody tr {
            transition: all 0.2s ease;
        }

        .dokumenKaryawanPage #dokumenKaryawanTable tbody tr:hover {
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

        .dokumenKaryawanPage #dokumenKaryawanTable tbody tr.row-hover-active {
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

            // Prevent DataTables reinit error
            if ($.fn.dataTable.isDataTable('#dokumenKaryawanTable')) {
                // Destroy existing instance if it exists
                $('#dokumenKaryawanTable').DataTable().destroy();
            }

            // Store all rows with their data in a global array
            let allRows = [];
            let isDataSorted = false;

            // Function to sort all data once at initialization
            function sortAllData() {
                if (isDataSorted) return;

                console.log("Sorting all data...");

                // Group all rows by employee
                const employeeGroups = {};

                // First collect all rows
                $('#dokumenKaryawanTable tbody tr').each(function() {
                    const $row = $(this);
                    const employeeId = $row.data('employee-id') || '';
                    const employeeName = $row.data('employee-name') || '';
                    const goldok = Number($row.data('goldok')) || 999;
                    const rowHTML = $row[0].outerHTML;

                    if (!employeeGroups[employeeId]) {
                        employeeGroups[employeeId] = {
                            id: employeeId,
                            name: employeeName,
                            rows: []
                        };
                    }

                    employeeGroups[employeeId].rows.push({
                        goldok: goldok,
                        html: rowHTML
                    });
                });

                // Sort each employee's rows by goldok
                for (const employeeId in employeeGroups) {
                    employeeGroups[employeeId].rows.sort((a, b) => a.goldok - b.goldok);
                }

                // Sort employees by name
                const sortedEmployeeIds = Object.keys(employeeGroups).sort((a, b) => {
                    return employeeGroups[a].name.localeCompare(employeeGroups[b].name);
                });

                // Create the final sorted array
                allRows = [];
                for (const employeeId of sortedEmployeeIds) {
                    for (const row of employeeGroups[employeeId].rows) {
                        allRows.push(row.html);
                    }
                }

                // Apply the sorted data to the table
                const tbody = $('#dokumenKaryawanTable tbody');
                tbody.empty();

                for (let i = 0; i < allRows.length; i++) {
                    const $row = $(allRows[i]);
                    $row.find('td:first').text(i + 1); // Update row number
                    tbody.append($row);
                }

                isDataSorted = true;
                console.log(`Sorted ${allRows.length} rows`);
            }

            // Call this function before initializing DataTables
            sortAllData();

            /**
             * UNIFIED DOCUMENT STATUS FUNCTIONS
             * These functions use consistent logic for document status calculation
             */

            // Calculate document statistics (expired and warning counts)
            function updateDocumentStats() {
                // Reset counts
                let expiredCount = 0;
                let warningCount = 0;

                // Get all rows, not just visible ones
                const allRows = table.rows().nodes();
                const today = moment().startOf('day');
                const warningThreshold = 30; // Days before expiry to show warning

                $(allRows).each(function() {
                    const row = $(this);

                    // 1. Check document status first - skip inactive documents
                    const statusText = row.find('td:eq(11)').text().trim();
                    if (statusText.includes("Tidak Berlaku")) {
                        return true; // continue to next iteration
                    }

                    // 2. Check if expired based on TglBerakhir
                    const tglBerakhirCell = row.find('td:eq(6)').text().trim();
                    let isExpired = false;

                    if (tglBerakhirCell !== '-' && !tglBerakhirCell.includes('span class="text-muted"')) {
                        // Extract date from any HTML content
                        const berakhirText = tglBerakhirCell.replace(/<[^>]*>/g, '').trim();
                        const berakhirDate = moment(berakhirText, 'DD/MM/YYYY');

                        if (berakhirDate.isValid() && berakhirDate.isBefore(today)) {
                            expiredCount++;
                            isExpired = true;
                            return true; // Skip to next row as we've counted this document
                        }
                    }

                    // 3. Check TglPengingat for warning/expired status
                    const tglPengingatStr = row.data('tgl-pengingat');
                    if (tglPengingatStr) {
                        const tglPengingat = moment(tglPengingatStr);

                        if (tglPengingat.isValid()) {
                            const diffDays = tglPengingat.diff(today, 'days');

                            if (diffDays <= 0) {
                                // Reminder date has passed
                                if (!isExpired) {
                                    expiredCount++;
                                    isExpired = true;
                                }
                                return true; // Skip to next row
                            } else if (diffDays <= warningThreshold) {
                                // Within warning threshold
                                if (!isExpired) {
                                    warningCount++;
                                }
                                return true; // Skip to next row
                            }
                        }
                    }

                    // 4. Check if approaching expiry based on TglBerakhir
                    if (tglBerakhirCell !== '-' && !tglBerakhirCell.includes('span class="text-muted"')) {
                        const berakhirText = tglBerakhirCell.replace(/<[^>]*>/g, '').trim();
                        const berakhirDate = moment(berakhirText, 'DD/MM/YYYY');

                        if (berakhirDate.isValid()) {
                            const diffDays = berakhirDate.diff(today, 'days');

                            if (diffDays > 0 && diffDays <= warningThreshold) {
                                if (!isExpired) {
                                    warningCount++;
                                }
                            }
                        }
                    }
                });

                // Update counter badges
                $('#expiredDocsCount').text(expiredCount);
                $('#warningDocsCount').text(warningCount);

                // Always show badges regardless of count
                $('#expiredDocsBadge').show();
                $('#warningDocsBadge').show();

                // Log for debugging
                console.log(`Document stats: ${expiredCount} expired, ${warningCount} warning`);
            }

            // Apply row highlighting based on document status
            function applyRowHighlighting() {
                // Reset all highlights
                $('#dokumenKaryawanTable tbody tr').removeClass(
                    'highlight-red highlight-yellow highlight-orange highlight-gray');

                const today = moment().startOf('day');
                const warningThreshold = 30; // Days before expiry for warning
                const urgentThreshold = 7; // Days before expiry for urgent warning
                const criticalThreshold = 3; // Days before expiry for critical warning

                // Apply highlighting to each row
                $('#dokumenKaryawanTable tbody tr').each(function() {
                    const row = $(this);

                    // 1. Check document status first (highest priority)
                    const statusText = row.find('td:eq(11)').text().trim();
                    if (statusText.includes("Tidak Berlaku")) {
                        row.addClass('highlight-gray');
                        row.attr('data-status', 'inactive');
                        return true; // continue to next iteration
                    }

                    // 2. Check if expired based on TglBerakhir
                    const tglBerakhirCell = row.find('td:eq(6)').text().trim();

                    if (tglBerakhirCell !== '-' && !tglBerakhirCell.includes('span class="text-muted"')) {
                        // Extract date from any HTML content
                        const berakhirText = tglBerakhirCell.replace(/<[^>]*>/g, '').trim();
                        const berakhirDate = moment(berakhirText, 'DD/MM/YYYY');

                        if (berakhirDate.isValid() && berakhirDate.isBefore(today)) {
                            row.addClass('highlight-red');
                            row.attr('data-status', 'expired');
                            row.attr('data-masa-peringatan', 'Expired');
                            return true; // Stop processing this row
                        }
                    }

                    // 3. Check TglPengingat for warning/expired status
                    const tglPengingatStr = row.data('tgl-pengingat');
                    if (tglPengingatStr) {
                        const tglPengingat = moment(tglPengingatStr);

                        if (tglPengingat.isValid()) {
                            const diffDays = tglPengingat.diff(today, 'days');

                            if (diffDays <= 0) {
                                // Reminder date has passed
                                row.addClass('highlight-red');
                                row.attr('data-status', 'reminder-expired');
                                row.attr('data-masa-peringatan', 'Waktu pengingat telah tiba');
                                return true; // Stop processing this row
                            } else if (diffDays <= criticalThreshold) {
                                row.addClass('highlight-red');
                                row.attr('data-status', 'critical');
                                row.attr('data-masa-peringatan', 'Kurang dari ' + criticalThreshold +
                                    ' hari');
                                return true; // Stop processing this row
                            } else if (diffDays <= urgentThreshold) {
                                row.addClass('highlight-yellow');
                                row.attr('data-status', 'urgent');
                                row.attr('data-masa-peringatan', 'Kurang dari ' + urgentThreshold +
                                    ' hari');
                                return true; // Stop processing this row
                            } else if (diffDays <= warningThreshold) {
                                row.addClass('highlight-orange');
                                row.attr('data-status', 'warning');
                                row.attr('data-masa-peringatan', 'Kurang dari ' + warningThreshold +
                                    ' hari');
                                return true; // Stop processing this row
                            }
                        }
                    }

                    // 4. Check if approaching expiry based on TglBerakhir
                    if (tglBerakhirCell !== '-' && !tglBerakhirCell.includes('span class="text-muted"')) {
                        const berakhirText = tglBerakhirCell.replace(/<[^>]*>/g, '').trim();
                        const berakhirDate = moment(berakhirText, 'DD/MM/YYYY');

                        if (berakhirDate.isValid()) {
                            const diffDays = berakhirDate.diff(today, 'days');

                            if (diffDays > 0) {
                                if (diffDays <= criticalThreshold) {
                                    row.addClass('highlight-red');
                                    row.attr('data-status', 'critical');
                                    row.attr('data-masa-peringatan', 'Kurang dari ' + criticalThreshold +
                                        ' hari');
                                } else if (diffDays <= urgentThreshold) {
                                    row.addClass('highlight-yellow');
                                    row.attr('data-status', 'urgent');
                                    row.attr('data-masa-peringatan', 'Kurang dari ' + urgentThreshold +
                                        ' hari');
                                } else if (diffDays <= warningThreshold) {
                                    row.addClass('highlight-orange');
                                    row.attr('data-status', 'warning');
                                    row.attr('data-masa-peringatan', 'Kurang dari ' + warningThreshold +
                                        ' hari');
                                } else {
                                    // Valid document with no warnings
                                    row.attr('data-status', 'valid');
                                    row.attr('data-masa-peringatan', 'Lebih dari ' + warningThreshold +
                                        ' hari');
                                }
                            }
                        }
                    }
                });
            }

            // Update masa peringatan text for each row
            function updateMasaPengingatText() {
                const today = moment().startOf('day');

                $('#dokumenKaryawanTable tbody tr').each(function() {
                    const tglPengingatStr = $(this).data('tgl-pengingat');
                    const $masaPengingatCol = $(this).find('.sisa-peringatan-col');

                    // Skip if no reminder date
                    if (!tglPengingatStr) {
                        $masaPengingatCol.text('-');
                        return true;
                    }

                    // Parse reminder date
                    const tglPengingat = moment(tglPengingatStr);

                    // Skip if invalid date
                    if (!tglPengingat.isValid()) {
                        $masaPengingatCol.text('-');
                        return true;
                    }

                    // Calculate days difference
                    const diffDays = tglPengingat.diff(today, 'days');
                    let masaPengingatText = '';

                    if (diffDays < 0) {
                        // Reminder date has passed
                        masaPengingatText = 'Terlambat ' + Math.abs(diffDays) + ' hari';
                    } else if (diffDays === 0) {
                        // Reminder date is today
                        masaPengingatText = 'Hari ini';
                    } else {
                        // Reminder date is in the future
                        masaPengingatText = diffDays + ' hari lagi';
                    }

                    // Update the text
                    $masaPengingatCol.text(masaPengingatText);
                });
            }

            // Initialize DataTable
            var table = $('#dokumenKaryawanTable').DataTable({
                responsive: true,
                language: indonesianLanguage,
                // FIX: Set custom display and sequence for page numbers
                displayStart: 0, // Start from the first record
                pagingType: 'full_numbers', // Use full_numbers pagination
                ordering: false, // Disable DataTables ordering since we do custom sorting
                pageLength: 10, // Default page length
                lengthMenu: [10, 25, 50, 100], // Page length options
                columnDefs: [{
                        responsivePriority: 1,
                        targets: [0, 1, 2, 11] // No, NRK, Nama Karyawan, Status
                    },
                    {
                        responsivePriority: 2,
                        targets: [3, 4] // No Registrasi, Jenis
                    },
                    {
                        responsivePriority: 3,
                        targets: [5, 6, 7] // Tgl Terbit, Tgl Berakhir, Tgl Peringatan
                    },
                    {
                        responsivePriority: 4,
                        targets: [8, 9, 10, 12] // Peringatan, Catatan, File, Aksi
                    }
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
                // FIX: Update row numbering to be continuous across pages
                drawCallback: function(settings) {
                    // Get pagination info
                    var api = this.api();
                    var pageInfo = api.page.info();

                    // Update row numbers based on current page and page length
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        // Calculate the correct row number across pages
                        cell.innerHTML = (pageInfo.page * pageInfo.length) + i + 1;
                    });

                    // Apply highlighting for visible rows
                    applyRowHighlighting();

                    // Update masa peringatan text
                    updateMasaPengingatText();

                    // Customize info text
                    customizeInfoText();
                },
                initComplete: function() {
                    console.log("DataTable initComplete triggered");

                    try {
                        // Force style search input box
                        $('.dataTables_filter input').addClass('form-control');

                        // Initial highlighting and stats
                        applyRowHighlighting();
                        updateMasaPengingatText();

                        // Delay document stats calculation to ensure all data is processed
                        setTimeout(function() {
                            updateDocumentStats();
                        }, 500);

                        // Customize info text
                        customizeInfoText();
                    } catch (e) {
                        console.error("Error in initComplete:", e);
                    }
                }
            });

            // Function to customize the info text
            function customizeInfoText() {
                $('.dataTables_info').each(function() {
                    let text = $(this).text();
                    // Replace the text to include "Baris" (Rows)
                    text = text.replace('Menampilkan', 'Baris');
                    $(this).text(text);
                });
            }

            // FIX: Improved filter handling with new approach
            // Apply Filter button handler
            $('#applyFilter').on('click', function() {
                // Clear existing search filters
                table.search('').columns().search('');

                // Apply each filter individually
                if ($('#filter_noreg').val()) {
                    table.column(3).search($('#filter_noreg').val()); // No Registrasi (column 3)
                }

                if ($('#filter_karyawan').val()) {
                    table.column(2).search($('#filter_karyawan').val()); // Nama Karyawan (column 2)
                }

                if ($('#filter_jenis').val()) {
                    table.column(4).search($('#filter_jenis').val()); // Jenis (column 4)
                }

                // Date filters from and to for Tanggal Terbit
                const tglTerbitFrom = $('#filter_tgl_terbit_from').val();
                const tglTerbitTo = $('#filter_tgl_terbit_to').val();

                // Date filters from and to for Tanggal Berakhir
                const tglBerakhirFrom = $('#filter_tgl_berakhir_from').val();
                const tglBerakhirTo = $('#filter_tgl_berakhir_to').val();

                // Status filter
                const statusFilter = $('#filter_status').val();

                // Apply custom filtering for Status, Tgl Terbit, and Tgl Berakhir
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        // Only apply to our table
                        if (settings.nTable.id !== 'dokumenKaryawanTable') return true;

                        let row = table.row(dataIndex).node();

                        // Check Tanggal Terbit filter
                        if (tglTerbitFrom !== '' || tglTerbitTo !== '') {
                            // Get the date from column 5 (Tgl Terbit)
                            let terbitText = data[5].trim().replace(/<[^>]*>/g, '').trim();

                            // Skip rows with no date
                            if (terbitText === '-') {
                                return false;
                            }

                            // Parse the date
                            let terbitDate = moment(terbitText, 'DD/MM/YYYY');

                            // Apply "from" filter
                            if (tglTerbitFrom !== '' && terbitDate.isBefore(moment(tglTerbitFrom))) {
                                return false;
                            }

                            // Apply "to" filter
                            if (tglTerbitTo !== '' && terbitDate.isAfter(moment(tglTerbitTo))) {
                                return false;
                            }
                        }

                        // Check Tanggal Berakhir filter
                        if (tglBerakhirFrom !== '' || tglBerakhirTo !== '') {
                            // Get the date from column 6 (Tgl Berakhir)
                            let berakhirText = data[6].trim().replace(/<[^>]*>/g, '').trim();

                            // Skip rows with no date
                            if (berakhirText === '-') {
                                return false;
                            }

                            // Parse the date
                            let berakhirDate = moment(berakhirText, 'DD/MM/YYYY');

                            // Apply "from" filter
                            if (tglBerakhirFrom !== '' && berakhirDate.isBefore(moment(
                                    tglBerakhirFrom))) {
                                return false;
                            }

                            // Apply "to" filter
                            if (tglBerakhirTo !== '' && berakhirDate.isAfter(moment(tglBerakhirTo))) {
                                return false;
                            }
                        }

                        // Check Status filter - the critical fix
                        if (statusFilter !== '') {
                            let statusText = data[11].trim();

                            if (statusFilter === 'Berlaku') {
                                // For "Berlaku" filter, match only if text is exactly "Berlaku"
                                if (!statusText.includes('Berlaku') || statusText.includes(
                                        'Tidak Berlaku')) {
                                    return false;
                                }
                            } else if (statusFilter === 'Tidak Berlaku') {
                                // For "Tidak Berlaku" filter, match only if text contains "Tidak Berlaku"
                                if (!statusText.includes('Tidak Berlaku')) {
                                    return false;
                                }
                            } else if (statusFilter === 'Warning') {
                                // For "Warning" (Akan Expired) filter, check for yellow/orange highlighting
                                if (!$(row).hasClass('highlight-yellow') && !$(row).hasClass(
                                        'highlight-orange')) {
                                    return false;
                                }
                            } else if (statusFilter === 'Expired') {
                                // For "Expired" filter, check for red highlighting
                                if (!$(row).hasClass('highlight-red')) {
                                    return false;
                                }
                            }
                        }

                        return true;
                    }
                );

                // Refresh table to apply all filters
                table.draw();

                // Remove our custom filter after it's been applied
                $.fn.dataTable.ext.search.pop();

                // Hide the filter modal
                filterModal.hide();

                // Highlight filter button if any filter is active
                highlightFilterButton();
            });

            // Reset Filter button handler
            $('#resetFilter').on('click', function() {
                // Reset form fields
                $('#filterForm')[0].reset();

                // Reset Select2 fields if available
                if ($.fn.select2) {
                    $('#filter_karyawan, #filter_jenis, #filter_status').val(null).trigger('change');
                }

                // Remove active class from filter button
                $('#filterButton').removeClass('filter-active');

                // Reset table filters
                table.search('').columns().search('').draw();
            });

            // Table draw event - update highlighting and text
            table.on('draw.dt', function() {
                updateMasaPengingatText();
                applyRowHighlighting();
            });

            // Highlight filter button if any filter is active
            function highlightFilterButton() {
                if ($('#filter_noreg').val() ||
                    $('#filter_karyawan').val() ||
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

            // Event untuk filter dan export button
            $('#filterButton').on('click', function() {
                filterModal.show();
            });

            $('#exportButton').on('click', function() {
                exportModal.show();
            });

            // Export buttons
            // Export buttons
            $('#exportExcel').on('click', function() {
                // Copy current filter values to export form
                $('#export_filter_noreg').val($('#filter_noreg').val());
                $('#export_filter_karyawan').val($('#filter_karyawan').val());
                $('#export_filter_jenis').val($('#filter_jenis').val());
                $('#export_filter_tgl_terbit_from').val($('#filter_tgl_terbit_from').val());
                $('#export_filter_tgl_terbit_to').val($('#filter_tgl_terbit_to').val());
                $('#export_filter_tgl_berakhir_from').val($('#filter_tgl_berakhir_from').val());
                $('#export_filter_tgl_berakhir_to').val($('#filter_tgl_berakhir_to').val());
                $('#export_filter_status').val($('#filter_status').val());

                // Submit export form
                $('#exportForm').submit();
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
                $('#deleteForm').attr('action', "/dokumen-karyawan/" + id);

                // Show the delete confirmation modal
                deleteConfirmationModal.show();
            });

            // Row click handler for navigation to detail page
            $('#dokumenKaryawanTable tbody').on('click', 'tr', function(e) {
                // Don't follow link if clicking on button or link inside row
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

            // Row hover effects
            $('#dokumenKaryawanTable tbody').on('mouseenter', 'tr', function() {
                $(this).addClass('row-hover-active');
            }).on('mouseleave', 'tr', function() {
                $(this).removeClass('row-hover-active');
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
