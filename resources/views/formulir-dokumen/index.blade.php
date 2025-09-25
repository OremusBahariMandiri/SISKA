{{-- resources/views/formulir-dokumen/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Formulir Dokumen')

@section('content')
    <div class="container-fluid formulirDokumenPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-alt me-2"></i>Manajemen Formulir Dokumen</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            @if (auth()->user()->is_admin || ($userPermissions['download'] ?? false))
                                <button type="button" class="btn btn-light me-2" id="exportButton">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                            @endif
                            @if (auth()->user()->is_admin || ($userPermissions['tambah'] ?? false))
                                <a href="{{ route('formulir-dokumen.create') }}" class="btn btn-light">
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

                        <div class="table-responsive">
                            <table id="formulirDokumenTable" class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>No Registrasi</th>
                                        <th>Perusahaan</th>
                                        <th>Kategori</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Tgl Terbit</th>
                                        <th>Catatan</th>
                                        <th width="8%" class="text-center">Status</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($formulirDokumen as $index => $dokumen)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $dokumen->NoRegDok }}</td>
                                            <td>{{ $dokumen->perusahaan->NamaPrsh ?? '-'}}</td>
                                            <td>{{ $dokumen->kategori->KategoriDok ?? '-' }}</td>
                                            <td>{{ $dokumen->JenisDok }}</td>
                                            <td>
                                                @if ($dokumen->TglTerbitDok)
                                                    {{ $dokumen->TglTerbitDok->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $dokumen->KetDok }}</td>

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
                                                        <a href="{{ route('formulir-dokumen.show', $dokumen->id) }}"
                                                            class="btn btn-sm text-white" data-bs-toggle="tooltip"
                                                            title="Detail" style="background-color: #4a90e2;">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['ubah'] ?? false))
                                                        <a href="{{ route('formulir-dokumen.edit', $dokumen->id) }}"
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
                        <i class="fas fa-filter me-2"></i>Filter Formulir Dokumen
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
                                    <label for="filter_kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="filter_kategori">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($formulirDokumen->pluck('kategori.KategoriDok')->unique() as $kategori)
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
                                        @foreach ($formulirDokumen->pluck('JenisDok')->unique() as $jenis)
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
                                    <label for="filter_status" class="form-label">Status Dokumen</label>
                                    <select class="form-select" id="filter_status">
                                        <option value="">Semua Status</option>
                                        <option value="Berlaku">Berlaku</option>
                                        <option value="Tidak Berlaku">Tidak Berlaku</option>
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
                    <p>Apakah Anda yakin ingin menghapus formulir dokumen dengan nomor registrasi <strong
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
        .formulirDokumenPage .dataTables_wrapper .dataTables_length,
        .formulirDokumenPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .formulirDokumenPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .formulirDokumenPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .formulirDokumenPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .formulirDokumenPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .formulirDokumenPage table.dataTable thead th.sorting:after,
        .formulirDokumenPage table.dataTable thead th.sorting_asc:after,
        .formulirDokumenPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .formulirDokumenPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .formulirDokumenPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .formulirDokumenPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* Add hover effect to action buttons */
        .formulirDokumenPage .btn-sm {
            transition: transform 0.2s;
        }

        .formulirDokumenPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .formulirDokumenPage #formulirDokumenTable tbody tr {
            transition: all 0.2s ease;
        }

        .formulirDokumenPage #formulirDokumenTable tbody tr:hover {
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

        .formulirDokumenPage #formulirDokumenTable tbody tr.row-hover-active {
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
            if ($.fn.dataTable.isDataTable('#formulirDokumenTable')) {
                // Destroy existing instance if it exists
                $('#formulirDokumenTable').DataTable().destroy();
            }

            // Format tanggal untuk filter
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Tanggal terbit filter
                    let terbitFrom = $('#filter_tgl_terbit_from').val();
                    let terbitTo = $('#filter_tgl_terbit_to').val();
                    let terbitDate = data[4] !== '-' ? moment(data[4], 'DD/MM/YYYY') : null;

                    if (terbitDate === null) {
                        if (terbitFrom === '' && terbitTo === '') {
                            return true;
                        } else {
                            return false;
                        }
                    }

                    if ((terbitFrom === '' && terbitTo === '') ||
                        (terbitFrom === '' && terbitDate.isSameOrBefore(moment(terbitTo))) ||
                        (terbitTo === '' && terbitDate.isSameOrAfter(moment(terbitFrom))) ||
                        (terbitDate.isBetween(moment(terbitFrom), moment(terbitTo), null, '[]'))) {
                        return true;
                    }
                    return false;
                }
            );

            // Inisialisasi DataTable
            var table = $('#formulirDokumenTable').DataTable({
                responsive: true,
                language: indonesianLanguage,
                columnDefs: [{
                        // Prioritas tertinggi untuk kolom yang paling penting
                        responsivePriority: 1,
                        targets: [0, 1, 2, 7] // No, No.Reg, Kategori, Status
                    },
                    {
                        // Prioritas kedua untuk kolom penting lainnya
                        responsivePriority: 2,
                        targets: [3, 4] // Jenis, Tgl Terbit
                    },
                    {
                        // Kolom yang tidak bisa di-sort
                        orderable: false,
                        targets: [0, 8] // No dan Aksi
                    }
                ],
                order: [
                    [1, 'asc'] // Sort by No. Reg
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
                },
                initComplete: function() {
                    // Force style search input box
                    $('.dataTables_filter input').addClass('form-control');
                }
            });

            // Event untuk filter dan export button
            $('#filterButton').on('click', function() {
                $('#filterModal').modal('show');
            });

            @if (auth()->user()->is_admin || ($userPermissions['download'] ?? false))
                $('#exportButton').on('click', function() {
                    $('#exportModal').modal('show');
                });
            @endif

            // Modifikasi event handler untuk tombol Apply Filter
            $('#applyFilter').on('click', function() {
                // Terapkan filter untuk kolom-kolom
                table.column(1).search($('#filter_noreg').val()); // No Reg
                table.column(2).search($('#filter_kategori').val()); // Kategori
                table.column(3).search($('#filter_jenis').val()); // Jenis Dokumen
                table.column(7).search($('#filter_status').val()); // Status

                // Refresh table untuk menerapkan semua filter
                table.draw();
                $('#filterModal').modal('hide');

                // Highlight filter button jika ada filter aktif
                highlightFilterButton();
            });

            // Modify the Reset Filter event handler
            $('#resetFilter').on('click', function() {
                // Reset the form fields
                $('#filterForm')[0].reset();

                // Remove active class from filter button
                $('#filterButton').removeClass('filter-active');

                // Reset table filters
                table.search('').columns().search('').draw();
            });

            // Highlight filter button jika ada filter aktif
            function highlightFilterButton() {
                if ($('#filter_noreg').val() ||
                    $('#filter_kategori').val() ||
                    $('#filter_jenis').val() ||
                    $('#filter_tgl_terbit_from').val() ||
                    $('#filter_tgl_terbit_to').val() ||
                    $('#filter_status').val()) {
                    $('#filterButton').addClass('filter-active');
                } else {
                    $('#filterButton').removeClass('filter-active');
                }
            }

            @if (auth()->user()->is_admin || ($userPermissions['download'] ?? false))
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
            @endif

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
                $('#deleteForm').attr('action', "{{ url('formulir-dokumen') }}/" + id);

                // Show the delete confirmation modal
                $('#deleteConfirmationModal').modal('show');
            });

            // Tambahkan efek klik pada baris tabel untuk menuju halaman detail
            $('#formulirDokumenTable tbody').on('click', 'tr', function(e) {
                // Jangan ikuti link jika yang diklik adalah tombol atau link di dalam baris
                if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                    $(e.target).closest('button').length || $(e.target).closest('a').length) {
                    return;
                }

                @if (auth()->user()->is_admin || ($userPermissions['detail'] ?? false))
                    // Dapatkan URL detail
                    var detailLink = $(this).find('a[title="Detail"]').attr('href');
                    if (detailLink) {
                        window.location.href = detailLink;
                    }
                @endif
            });

            // Tambahkan efek flash saat baris di-hover
            $('#formulirDokumenTable tbody').on('mouseenter', 'tr', function() {
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