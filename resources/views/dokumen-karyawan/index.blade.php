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
                                        <th>No Registrasi</th>
                                        <th>Nama Karyawan</th>
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
                                            data-goldok="{{ (int)($dokumen->GolDok ?? 999) }}"
                                            data-employee-id="{{ $dokumen->IdKodeA04 }}"
                                            data-employee-name="{{ $dokumen->karyawan->NamaKry ?? '-' }}"
                                            data-jenis-dok="{{ $dokumen->JenisDok }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $dokumen->NoRegDok }}</td>
                                            <td>{{ $dokumen->karyawan->NamaKry ?? '-' }}</td>
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
                <div class="modal-header bg-primary text-white">
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

                                <div class="mb-3">
                                    <label for="filter_kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="filter_kategori">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($dokumenKaryawan->pluck('KategoriDok')->unique() as $kategori)
                                            @if ($kategori)
                                                <option value="{{ $kategori }}">{{ $kategori }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

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
       // Perbaikan fungsi untuk sorting dan menangani pembaruan data
$(document).ready(function() {
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

    // Fungsi untuk mendapatkan statistik dokumen
    function updateDocumentStats() {
        // Reset highlight classes first
        $('#dokumenKaryawanTable tbody tr').removeClass(
            'highlight-red highlight-yellow highlight-orange highlight-gray');

        // Initialize counters
        let expiredCount = 0;
        let warningCount = 0;

        // Check each table row to determine status
        $('#dokumenKaryawanTable tbody tr').each(function() {
            const row = $(this);
            let isExpired = false;
            let isWarning = false;

            // Check document status first
            const statusText = row.find('td:eq(10)').text().trim();

            // If status is "Tidak Berlaku", skip for expired/warning counting
            if (statusText.includes("Tidak Berlaku")) {
                row.addClass('highlight-gray');
                return; // Continue to next row
            }

            // Logic for TglBerakhir (second priority)
            const tglBerakhir = row.find('td:eq(5)').text().trim();
            if (tglBerakhir !== '-') {
                const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                const today = moment();

                if (berakhirDate.isBefore(today)) {
                    row.addClass('highlight-red');
                    isExpired = true;
                    expiredCount++;
                    return; // Continue to next row
                }
            }

            // Logic for TglPengingat
            const tglPengingatStr = row.data('tgl-pengingat');
            if (tglPengingatStr) {
                const tglPengingat = moment(tglPengingatStr);
                const today = moment();
                const diffDays = tglPengingat.diff(today, 'days');

                if (diffDays < 0 || diffDays === 0) {
                    // Reminder date has passed or is today
                    row.addClass('highlight-red');
                    if (!isExpired) {
                        expiredCount++;
                        isExpired = true;
                    }
                    return; // Continue to next row
                } else if (diffDays <= 7) {
                    row.addClass('highlight-yellow');
                    if (!isExpired) {
                        warningCount++;
                        isWarning = true;
                    }
                    return; // Continue to next row
                } else if (diffDays <= 30) {
                    row.addClass('highlight-orange');
                    if (!isExpired && !isWarning) {
                        warningCount++;
                    }
                    return; // Continue to next row
                }
            }

            // Logic for TglBerakhir within 30 days (lowest priority)
            if (tglBerakhir !== '-') {
                const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                const today = moment();

                if (berakhirDate.isAfter(today) && berakhirDate.isBefore(moment().add(30, 'days'))) {
                    row.addClass('highlight-yellow');
                    if (!isExpired && !isWarning) {
                        warningCount++;
                    }
                }
            }
        });

        // Update counter badges with calculated data
        $('#expiredDocsCount').text(expiredCount);
        $('#warningDocsCount').text(warningCount);

        // Always show badges regardless of count
        $('#expiredDocsBadge').show();
        $('#warningDocsBadge').show();
    }

    // Function to update reminder period text
    function updateMasaPengingatText() {
        const today = moment();

        $('#dokumenKaryawanTable tbody tr').each(function() {
            const tglPengingatStr = $(this).data('tgl-pengingat');
            const $masaPengingatCol = $(this).find('.sisa-peringatan-col');

            // If no reminder date, skip this row
            if (!tglPengingatStr) {
                $masaPengingatCol.text('-');
                return;
            }

            // Parse reminder date
            const tglPengingat = moment(tglPengingatStr);

            // Calculate difference in days
            const diffDays = tglPengingat.diff(today, 'days');

            // Determine text to display in reminder period column
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

            // Update reminder period text
            $masaPengingatCol.text(masaPengingatText);
        });
    }

    // IMPROVED: Manual sorting function that works reliably
    function manualSortTable() {
        console.log("Performing manual employee+GolDok sort");

        // Collection to hold our sorted data
        const employeeGroups = {};

        // First pass: group rows by employee
        $('#dokumenKaryawanTable tbody tr').each(function() {
            const row = $(this);
            // Get employee data - use fallbacks for safety
            const employeeId = row.data('employee-id') || '';
            const employeeName = row.data('employee-name') || '';

            // Create employee group if it doesn't exist
            if (!employeeGroups[employeeId]) {
                employeeGroups[employeeId] = {
                    id: employeeId,
                    name: employeeName,
                    rows: []
                };
            }

            // Store the original row
            employeeGroups[employeeId].rows.push(row);
        });

        // Sort employees by name
        const sortedEmployeeIds = Object.keys(employeeGroups).sort((a, b) => {
            return employeeGroups[a].name.localeCompare(employeeGroups[b].name);
        });

        // For each employee group, sort rows by GolDok
        for (const employeeId of sortedEmployeeIds) {
            const group = employeeGroups[employeeId];

            // Sort rows by GolDok - use Number for consistent comparison
            group.rows.sort((rowA, rowB) => {
                // Always use explicit Number conversion for reliable comparison
                const golDokA = Number($(rowA).data('goldok')) || 999;
                const golDokB = Number($(rowB).data('goldok')) || 999;
                return golDokA - golDokB;
            });
        }

        // Get the table body
        const tbody = $('#dokumenKaryawanTable tbody');

        // Store all rows in a document fragment for better performance
        const fragment = document.createDocumentFragment();
        let rowIndex = 1;

        // Add rows back in sorted order
        for (const employeeId of sortedEmployeeIds) {
            const group = employeeGroups[employeeId];

            for (const row of group.rows) {
                // Update row number
                row.find('td:first').text(rowIndex);

                // Append to fragment
                fragment.appendChild(row[0]);
                rowIndex++;
            }
        }

        // Clear tbody and append all rows at once for better performance
        tbody.empty().append(fragment);

        console.log("Manual sorting completed with " + (rowIndex-1) + " rows");
    }

    // Initialize DataTable with appropriate options
    var table = $('#dokumenKaryawanTable').DataTable({
        responsive: true,
        language: indonesianLanguage,
        ordering: false, // Disable DataTables ordering - we'll do it ourselves
        processing: true,
        stateSave: false, // Don't save state to avoid filtering issues
        dom: 'Bfrtip', // Include buttons in the DOM
        columnDefs: [{
                // Highest priority for most important columns
                responsivePriority: 1,
                targets: [0, 1, 2, 10] // No, No.Reg, Nama Karyawan, Status
            },
            {
                // Second priority for other important columns
                responsivePriority: 2,
                targets: [3, 4] // Jenis, Tgl Terbit
            },
            {
                // Third priority for date columns
                responsivePriority: 3,
                targets: [5, 6] // Tgl Berakhir, Tgl Pengingat
            },
            {
                // Fourth priority for less important columns
                responsivePriority: 4,
                targets: [7, 8, 9, 11] // Peringatan, Catatan, File, Aksi
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
        // Modified drawCallback to ensure consistent rendering
        drawCallback: function(settings) {
            console.log("DataTable drawCallback triggered");

            // Apply highlighting for visible rows
            $('#dokumenKaryawanTable tbody tr').each(function() {
                const row = $(this);

                // Check document status first
                const statusText = row.find('td:eq(10)').text().trim();
                if (statusText.includes("Tidak Berlaku")) {
                    row.addClass('highlight-gray');
                    return;
                }

                // Check expiry date
                const tglBerakhir = row.find('td:eq(5)').text().trim();
                if (tglBerakhir !== '-') {
                    const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                    const today = moment();

                    if (berakhirDate.isBefore(today)) {
                        row.addClass('highlight-red');
                        return;
                    }
                }

                // Check reminder date
                const tglPengingatStr = row.data('tgl-pengingat');
                if (tglPengingatStr) {
                    const tglPengingat = moment(tglPengingatStr);
                    const today = moment();
                    const diffDays = tglPengingat.diff(today, 'days');

                    if (diffDays <= 0) {
                        row.addClass('highlight-red');
                    } else if (diffDays <= 7) {
                        row.addClass('highlight-yellow');
                    } else if (diffDays <= 30) {
                        row.addClass('highlight-orange');
                    }
                }
            });

            // Update text for warning period
            updateMasaPengingatText();

            // Re-apply manual sorting after each draw
            setTimeout(manualSortTable, 10);
        },
        initComplete: function() {
            console.log("DataTable initComplete triggered");

            // Force style search input box
            $('.dataTables_filter input').addClass('form-control');

            // Apply initial manual sort with a small delay to ensure DOM is ready
            setTimeout(function() {
                try {
                    // Manual sort independent of DataTables
                    manualSortTable();

                    // Calculate stats from ALL data
                    updateDocumentStats();

                    // Update reminder text
                    updateMasaPengingatText();
                } catch (e) {
                    console.error("Error in initial setup:", e);
                }
            }, 200);
        }
    });

    // Event for filter and export buttons
    $('#filterButton').on('click', function() {
        $('#filterModal').modal('show');
    });

    $('#exportButton').on('click', function() {
        $('#exportModal').modal('show');
    });

    // Modified event handler for Apply Filter button
    $('#applyFilter').on('click', function() {
        // Apply filter to columns
        table.column(1).search($('#filter_noreg').val()); // No Reg
        table.column(2).search($('#filter_karyawan').val()); // Karyawan
        table.column(3).search($('#filter_jenis').val()); // Jenis Dokumen

        // Refresh table to apply all filters
        table.draw();
        $('#filterModal').modal('hide');

        // Highlight filter button if any filter is active
        highlightFilterButton();

        // Refresh statistics after filter is applied
        updateDocumentStats();
    });

    // Modified Reset Filter event handler
    $('#resetFilter').on('click', function() {
        // Reset the form fields
        $('#filterForm')[0].reset();

        // Remove active class from filter button
        $('#filterButton').removeClass('filter-active');

        // Reset table filters
        table.search('').columns().search('').draw();

        // Refresh statistics after filter is reset
        updateDocumentStats();
    });

    // Event listener for table draw event
    table.on('draw.dt', function() {
        console.log("DataTable draw.dt event triggered");

        // Update highlighting and reminder text
        updateMasaPengingatText();

        // Update document statistics
        updateDocumentStats();
    });

    // Highlight filter button if any filter is active
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
        $('#exportModal').modal('hide');
    });

    $('#exportPdf').on('click', function() {
        $('.pdf-export-btn').trigger('click');
        $('#exportModal').modal('hide');
    });

    $('#exportPrint').on('click', function() {
        $('.print-export-btn').trigger('click');
        $('#exportModal').modal('hide');
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
        $('#deleteConfirmationModal').modal('show');
    });

    // Add click effect on table rows to go to detail page
    $('#dokumenKaryawanTable tbody').on('click', 'tr', function(e) {
        // Don't follow link if what was clicked is a button or link in the row
        if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
            $(e.target).closest('button').length || $(e.target).closest('a').length) {
            return;
        }

        // Get detail URL
        var detailLink = $(this).find('a[title="Detail"]').attr('href');
        if (detailLink) {
            window.location.href = detailLink;
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 5000);

    // Add reload button for debugging
    // $('<button>')
    //     .attr('id', 'reloadDataButton')
    //     .addClass('btn btn-sm btn-outline-primary')
    //     .html('<i class="fas fa-sync-alt me-1"></i> Reload Data')
    //     .css({
    //         'position': 'fixed',
    //         'bottom': '60px',
    //         'right': '20px',
    //         'z-index': '1000',
    //         'opacity': '0.8'
    //     })
    //     .on('click', function() {
    //         // Reload the page to get fresh data
    //         window.location.reload();
    //     })
    //     .appendTo('body');

    // Add sort button for debugging/testing
    // $('<button>')
    //     .attr('id', 'manualSortButton')
    //     .addClass('btn btn-sm btn-outline-primary')
    //     .html('<i class="fas fa-sort me-1"></i> Manual Sort')
    //     .css({
    //         'position': 'fixed',
    //         'bottom': '20px',
    //         'right': '20px',
    //         'z-index': '1000',
    //         'opacity': '0.8'
    //     })
    //     .on('click', function() {
    //         manualSortTable();
    //         updateDocumentStats();
    //     })
    //     .appendTo('body');
});
    </script>
@endpush