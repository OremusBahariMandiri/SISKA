@extends('layouts.app')

@section('title', 'Manajemen Keluarga Karyawan')

@section('content')
    <div class="container-fluid keluargaKryPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-users me-2"></i>Manajemen Keluarga Karyawan</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            <a href="{{ route('keluarga-karyawan.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Tambah
                            </a>
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
                            <table id="keluargaKryTable" class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Keluarga</th>
                                        <th>Status</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Karyawan</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($keluargaKaryawans as $keluarga)
                                        <tr data-status="{{ $keluarga->StsKeluargaKry }}"
                                            data-nama="{{ $keluarga->NamaKlg }}"
                                            data-jenis-kelamin="{{ $keluarga->SexKlg }}"
                                            data-karyawan="{{ $keluarga->karyawan ? $keluarga->karyawan->IdKode : '' }}"
                                            data-karyawan-nama="{{ $keluarga->karyawan ? $keluarga->karyawan->NamaKry : '' }}"
                                            data-umur="{{ $keluarga->umur }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $keluarga->NamaKlg }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = 'bg-primary';
                                                    if (in_array($keluarga->StsKeluargaKry, ['SUAMI', 'ISTRI'])) {
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($keluarga->StsKeluargaKry === 'ANAK') {
                                                        $badgeClass = 'bg-info';
                                                    } elseif (in_array($keluarga->StsKeluargaKry, ['BAPAK', 'IBU'])) {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $keluarga->StsKeluargaKry }}</span>
                                            </td>
                                            <td>
                                                @if ($keluarga->SexKlg === 'LAKI-LAKI')
                                                    <i class="fas fa-mars text-primary me-1"></i> LAKI-LAKI
                                                @else
                                                    <i class="fas fa-venus text-danger me-1"></i> PEREMPUAN
                                                @endif
                                            </td>
                                            <td>
                                                @if($keluarga->karyawan)
                                                    {{ $keluarga->karyawan->NamaKry }}
                                                @else
                                                    <span class="text-muted fst-italic">Tidak ada data</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('keluarga-karyawan.show', $keluarga->id) }}"
                                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('keluarga-karyawan.edit', $keluarga->id) }}"
                                                        class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                                        data-bs-toggle="tooltip" title="Hapus"
                                                        data-id="{{ $keluarga->id }}"
                                                        data-name="{{ $keluarga->NamaKlg }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
                    <p>Apakah Anda yakin ingin menghapus data keluarga <strong id="keluargaNameToDelete"></strong>?</p>
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #02786e">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filter Data Keluarga Karyawan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Status Filter -->
                                <div class="mb-3">
                                    <label for="filterStatus" class="form-label">Status</label>
                                    <select class="form-select select2" id="filterStatus">
                                        <option value="">Semua Status</option>
                                        <option value="SUAMI">SUAMI</option>
                                        <option value="ISTRI">ISTRI</option>
                                        <option value="ANAK">ANAK</option>
                                        <option value="BAPAK">BAPAK</option>
                                        <option value="IBU">IBU</option>
                                    </select>
                                </div>

                                <!-- Nama Filter -->
                                <div class="mb-3">
                                    <label for="filterNama" class="form-label">Nama</label>
                                    <select class="form-select select2" id="filterNama">
                                        <option value="">Semua Nama</option>
                                        @foreach ($keluargaKaryawans->pluck('NamaKlg')->unique()->sort() as $nama)
                                            <option value="{{ $nama }}">{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jenis Kelamin Filter -->
                                <div class="mb-3">
                                    <label for="filterJenisKelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select select2" id="filterJenisKelamin">
                                        <option value="">Semua</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <!-- Karyawan Filter -->
                                <div class="mb-3">
                                    <label for="filterKaryawan" class="form-label">Karyawan</label>
                                    <select class="form-select select2" id="filterKaryawan">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($keluargaKaryawans->pluck('karyawan.NamaKry', 'karyawan.IdKode')->filter()->unique() as $id => $nama)
                                            <option value="{{ $id }}">{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Umur Filter -->
                                <div class="mb-3">
                                    <label for="filterUmur" class="form-label">Rentang Umur</label>
                                    <select class="form-select select2" id="filterUmur">
                                        <option value="">Semua Umur</option>
                                        <option value="0-5">0-5 tahun</option>
                                        <option value="6-12">6-12 tahun</option>
                                        <option value="13-17">13-17 tahun</option>
                                        <option value="18-25">18-25 tahun</option>
                                        <option value="26-35">26-35 tahun</option>
                                        <option value="36-45">36-45 tahun</option>
                                        <option value="46-55">46-55 tahun</option>
                                        <option value="56-65">56-65 tahun</option>
                                        <option value="66-100">> 65 tahun</option>
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
                        <i class="fas fa-download me-2"></i>Ekspor Data Keluarga Karyawan
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
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        /* CSS dengan spesifisitas tinggi untuk DataTables */
        .keluargaKryPage .dataTables_wrapper .dataTables_length,
        .keluargaKryPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .keluargaKryPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .keluargaKryPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .keluargaKryPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .keluargaKryPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .keluargaKryPage table.dataTable thead th.sorting:after,
        .keluargaKryPage table.dataTable thead th.sorting_asc:after,
        .keluargaKryPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .keluargaKryPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .keluargaKryPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .keluargaKryPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* Add hover effect to action buttons */
        .keluargaKryPage .btn-sm {
            transition: transform 0.2s;
        }

        .keluargaKryPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .keluargaKryPage #keluargaKryTable tbody tr {
            transition: all 0.2s ease;
        }

        .keluargaKryPage #keluargaKryTable tbody tr:hover {
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

        .keluargaKryPage #keluargaKryTable tbody tr.row-hover-active {
            animation: flashBorder 1s ease infinite;
        }

        /* Highlight filtered rows */
        tr.filtered-row {
            background-color: rgba(0, 123, 255, 0.05) !important;
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

        /* Filter button active state */
        .filter-active {
            background-color: #e8f4ff !important;
            border-left: 3px solid #0d6efd !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize modals
            var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
            var exportModal = new bootstrap.Modal(document.getElementById('exportModal'));

            // Event listener for filter button
            $('#filterButton').on('click', function() {
                filterModal.show();
            });

            // Event listener for export button
            $('#exportButton').on('click', function() {
                exportModal.show();
            });

            // Initialize Select2 properly - with correct timing and parent
            setTimeout(function() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#filterModal .modal-body'),
                    placeholder: 'Pilih filter...',
                    allowClear: true
                });
            }, 500); // Short delay to ensure modal is ready

            // Initialize DataTables
            var keluargaKryTable = $('#keluargaKryTable').DataTable(
            //     {
            //     responsive: true,
            //     language: {
            //         url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            //     },
            //     searching: true,
            //     ordering: true,
            //     lengthMenu: [
            //         [10, 25, 50, -1],
            //         [10, 25, 50, "Semua"]
            //     ],
            //     dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            //         "<'row'<'col-sm-12'tr>>" +
            //         "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            //     autoWidth: false
            // }
        );

            // Apply filter function
            $('#applyFilter').click(function() {
                // Get filter values
                var statusFilter = $('#filterStatus').val();
                var namaFilter = $('#filterNama').val();
                var jenisKelaminFilter = $('#filterJenisKelamin').val();
                var karyawanFilter = $('#filterKaryawan').val();
                var umurFilter = $('#filterUmur').val();

                // Clear any existing filter functions first
                $.fn.dataTable.ext.search = [];

                // Apply custom filtering with a new function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var row = $(keluargaKryTable.row(dataIndex).node());
                    var show = true;

                    // Status filtering
                    if (statusFilter && statusFilter !== '') {
                        if (row.data('status') !== statusFilter) {
                            show = false;
                        }
                    }

                    // Nama filtering
                    if (show && namaFilter && namaFilter !== '') {
                        if (row.data('nama') !== namaFilter) {
                            show = false;
                        }
                    }

                    // Jenis Kelamin filtering
                    if (show && jenisKelaminFilter && jenisKelaminFilter !== '') {
                        if (row.data('jenis-kelamin') !== jenisKelaminFilter) {
                            show = false;
                        }
                    }

                    // Karyawan filtering
                    if (show && karyawanFilter && karyawanFilter !== '') {
                        if (row.data('karyawan') !== karyawanFilter) {
                            show = false;
                        }
                    }

                    // Umur filtering
                    if (show && umurFilter && umurFilter !== '') {
                        var age = parseInt(row.data('umur'));
                        if (!isNaN(age)) {
                            var ageRange = umurFilter.split('-');
                            var minAge = parseInt(ageRange[0]);
                            var maxAge = parseInt(ageRange[1]);

                            if (age < minAge || age > maxAge) {
                                show = false;
                            }
                        } else {
                            show = false;
                        }
                    }

                    // Add filtered-row class for styling
                    if (show && (statusFilter || namaFilter || jenisKelaminFilter || karyawanFilter || umurFilter)) {
                        row.addClass('filtered-row');
                    } else {
                        row.removeClass('filtered-row');
                    }

                    return show;
                });

                // Clear any existing classes first, then apply new ones after filtering
                $('#keluargaKryTable tbody tr').removeClass('filtered-row');

                // Draw table after filtering
                keluargaKryTable.draw();

                // Apply filter highlighting and close modal
                highlightFilterButton();
                filterModal.hide();
            });

            // Reset filter function
            $('#resetFilter').click(function() {
                // Reset all select2 filters
                $('#filterStatus, #filterNama, #filterJenisKelamin, #filterKaryawan, #filterUmur').val(null)
                    .trigger('change.select2');

                // Clear ALL custom filtering functions
                $.fn.dataTable.ext.search = [];

                // Clear DataTable filtering and show all rows
                keluargaKryTable.search('').columns().search('').draw();

                // Remove highlight from rows
                $('#keluargaKryTable tbody tr').removeClass('filtered-row');

                // Remove filter button highlight
                $('#filterButton').removeClass('filter-active');
            });

            // Export to Excel button
            $('#exportExcel').on('click', function() {
                // Get current filter values
                var statusFilter = $('#filterStatus').val();
                var namaFilter = $('#filterNama').val();
                var jenisKelaminFilter = $('#filterJenisKelamin').val();
                var karyawanFilter = $('#filterKaryawan').val();
                var umurFilter = $('#filterUmur').val();

                // Build URL with filter parameters
                var exportUrl = "{{ route('exportexcelkeluargakaryawan') }}";
                var params = [];

                if (statusFilter) params.push('status=' + encodeURIComponent(statusFilter));
                if (namaFilter) params.push('nama=' + encodeURIComponent(namaFilter));
                if (jenisKelaminFilter) params.push('jenisKelamin=' + encodeURIComponent(jenisKelaminFilter));
                if (karyawanFilter) params.push('karyawan=' + encodeURIComponent(karyawanFilter));
                if (umurFilter) params.push('umurRange=' + encodeURIComponent(umurFilter));

                // Add parameters to URL if any
                if (params.length > 0) {
                    exportUrl += '?' + params.join('&');
                }

                // Redirect to export URL with filter parameters
                window.location.href = exportUrl;
                exportModal.hide();
            });

            // PDF and Print buttons
            $('#exportPdf').on('click', function() {
                alert('Fitur export PDF sedang dalam pengembangan');
                exportModal.hide();
            });

            $('#exportPrint').on('click', function() {
                window.print();
                exportModal.hide();
            });

            // Function to highlight filter button when filters are active
            function highlightFilterButton() {
                if ($('#filterStatus').val() ||
                    $('#filterNama').val() ||
                    $('#filterJenisKelamin').val() ||
                    $('#filterKaryawan').val() ||
                    $('#filterUmur').val()) {
                    $('#filterButton').addClass('filter-active');
                } else {
                    $('#filterButton').removeClass('filter-active');
                }
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle row click for details view
            $('#keluargaKryTable tbody').on('click', 'tr', function(e) {
                // Don't follow link if clicking on buttons or links
                if ($(e.target).is('button, a, i') || $(e.target).closest('button, a').length) {
                    return;
                }

                // Get detail link URL
                var detailLink = $(this).find('a[title="Detail"]').attr('href');
                if (detailLink) {
                    window.location.href = detailLink;
                }
            });

            // Handle delete confirmation
            $('.delete-confirm').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                // Set keluarga name in modal
                $('#keluargaNameToDelete').text(name);

                // Set form action URL with explicit URL construction
                $('#deleteForm').attr('action', "{{ url('keluarga-karyawan') }}/" + id);

                // Show modal
                $('#deleteConfirmationModal').modal('show');
            });

            // Add hover effects for rows
            $('#keluargaKryTable tbody').on('mouseenter', 'tr', function() {
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