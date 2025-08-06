@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')
    <div class="container-fluid karyawanPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-users me-2"></i>Manajemen Karyawan</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            <a href="{{ route('karyawan.create') }}" class="btn btn-light">
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
                            <table id="karyawanTable" class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="7%">NRK</th>
                                        <th>Nama</th>
                                        <th>Tempat Lahir</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Umur</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Masa Kerja</th>
                                        <th width="8%" class="text-center">Status</th>
                                        <th class="text-center" width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawans as $karyawan)
                                        @php
                                            // Calculate age from date of birth
                                            $birthDate = $karyawan->TanggalLhrKry
                                                ? \Carbon\Carbon::parse($karyawan->TanggalLhrKry)
                                                : null;
                                            $age = $birthDate ? $birthDate->age : '-';

                                            // Calculate work duration from join date
                                            $joinDate = $karyawan->TglMsk
                                                ? \Carbon\Carbon::parse($karyawan->TglMsk)
                                                : null;
                                            $workDuration = '-';
                                            $workYears = 0;
                                            $workMonths = 0;

                                            if ($joinDate) {
                                                $now = \Carbon\Carbon::now();
                                                $diffInDays = $joinDate->diffInDays($now);
                                                $years = floor($diffInDays / 365);
                                                $months = floor(($diffInDays % 365) / 30);
                                                $days = $diffInDays - $years * 365 - $months * 30;

                                                // Save for data attributes
                                                $workYears = $years;
                                                $workMonths = $months;

                                                $workDuration = '';
                                                if ($years > 0) {
                                                    $workDuration .= $years . ' thn ';
                                                }
                                                if ($months > 0) {
                                                    $workDuration .= $months . ' bln ';
                                                }
                                                if ($days > 0) {
                                                    $workDuration .= $days . ' hri';
                                                }
                                                $workDuration = trim($workDuration);
                                            }
                                        @endphp
                                        <tr data-status="{{ $karyawan->StsKaryawan }}" data-nama="{{ $karyawan->NamaKry }}"
                                            data-tempat-lahir="{{ $karyawan->TempatLhrKry }}"
                                            data-umur="{{ $age }}" data-masa-kerja-tahun="{{ $workYears }}"
                                            data-masa-kerja-bulan="{{ $workMonths }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $karyawan->NrkKry }}</td>
                                            <td>{{ $karyawan->NamaKry }}</td>
                                            <td>{{ $karyawan->TempatLhrKry }}</td>
                                            <td>{{ $karyawan->TanggalLhrKry ? date('d-m-Y', strtotime($karyawan->TanggalLhrKry)) : '-' }}
                                            </td>
                                            <td>{{ $age }} {{ is_numeric($age) ? 'thn' : '' }}</td>
                                            <td>{{ $karyawan->TglMsk ? date('d-m-Y', strtotime($karyawan->TglMsk)) : '-' }}
                                            </td>
                                            <td>{{ $workDuration }}</td>
                                            <td class="text-center">
                                                @if ($karyawan->StsKaryawan == 'AKTIF')
                                                    <span class="badge bg-success">AKTIF</span>
                                                @elseif ($karyawan->StsKaryawan == 'PENSIUN')
                                                    <span class="badge bg-info">PENSIUN</span>
                                                @elseif ($karyawan->StsKaryawan == 'MENGUNDURKAN DIRI')
                                                    <span class="badge bg-warning">MENGUNDURKAN DIRI</span>
                                                @elseif ($karyawan->StsKaryawan == 'DIKELUARKAN')
                                                    <span class="badge bg-danger">DIKELUARKAN</span>
                                                @elseif ($karyawan->StsKaryawan == 'MENINGGAL')
                                                    <span class="badge bg-secondary">MENINGGAL</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $karyawan->StsKaryawan }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('karyawan.show', $karyawan->id) }}"
                                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('karyawan.edit', $karyawan->id) }}"
                                                        class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                                        data-bs-toggle="tooltip" title="Hapus"
                                                        data-id="{{ $karyawan->id }}"
                                                        data-name="{{ $karyawan->NamaKry }}">
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #02786e">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filter Data Karyawan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Status Filter - Positioned First -->
                                <div class="mb-3">
                                    <label for="filterStatus" class="form-label">Status</label>
                                    <select class="form-select select2" id="filterStatus">
                                        <option value="">Semua Status</option>
                                        <option value="AKTIF">AKTIF</option>
                                        <option value="PENSIUN">PENSIUN</option>
                                        <option value="MENGUNDURKAN DIRI">MENGUNDURKAN DIRI</option>
                                        <option value="DIKELUARKAN">DIKELUARKAN</option>
                                        <option value="MENINGGAL">MENINGGAL</option>
                                    </select>
                                </div>

                                <!-- Nama Filter -->
                                <div class="mb-3">
                                    <label for="filterNama" class="form-label">Nama</label>
                                    <select class="form-select select2" id="filterNama">
                                        <option value="">Semua Nama</option>
                                        @foreach ($karyawans->pluck('NamaKry')->unique()->sort() as $nama)
                                            <option value="{{ $nama }}">{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tempat Lahir Filter -->
                                <div class="mb-3">
                                    <label for="filterTempatLahir" class="form-label">Tempat Lahir</label>
                                    <select class="form-select select2" id="filterTempatLahir">
                                        <option value="">Semua Tempat</option>
                                        @foreach ($karyawans->pluck('TempatLhrKry')->unique()->sort() as $tempatLahir)
                                            <option value="{{ $tempatLahir }}">{{ $tempatLahir }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Umur Filter -->
                                <div class="mb-3">
                                    <label for="filterUmur" class="form-label">Rentang Umur</label>
                                    <select class="form-select select2" id="filterUmur">
                                        <option value="">Semua Umur</option>
                                        <option value="18-25">18-25 tahun</option>
                                        <option value="26-35">26-35 tahun</option>
                                        <option value="36-45">36-45 tahun</option>
                                        <option value="46-55">46-55 tahun</option>
                                        <option value="56-65">56-65 tahun</option>
                                        <option value="66-100">> 65 tahun</option>
                                    </select>
                                </div>

                                <!-- Masa Kerja Filter -->
                                <div class="mb-3">
                                    <label for="filterMasaKerja" class="form-label">Masa Kerja</label>
                                    <select class="form-select select2" id="filterMasaKerja">
                                        <option value="">Semua Masa Kerja</option>
                                        <option value="0-0.5">
                                            < 6 bulan</option>
                                        <option value="0.5-1">6 bulan - 1 tahun</option>
                                        <option value="1-1.5">1 - 1,5 tahun</option>
                                        <option value="1.5-2">1,5 - 2 tahun</option>
                                        <option value="2-3">2 - 3 tahun</option>
                                        <option value="3-5">3 - 5 tahun</option>
                                        <option value="5-10">5 - 10 tahun</option>
                                        <option value="10-100">> 10 tahun</option>
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
                        <i class="fas fa-download me-2"></i>Ekspor Data Karyawan
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
        .karyawanPage .dataTables_wrapper .dataTables_length,
        .karyawanPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .karyawanPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .karyawanPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .karyawanPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .karyawanPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .karyawanPage table.dataTable thead th.sorting:after,
        .karyawanPage table.dataTable thead th.sorting_asc:after,
        .karyawanPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .karyawanPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .karyawanPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .karyawanPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* Add hover effect to action buttons */
        .karyawanPage .btn-sm {
            transition: transform 0.2s;
        }

        .karyawanPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .karyawanPage #karyawanTable tbody tr {
            transition: all 0.2s ease;
        }

        .karyawanPage #karyawanTable tbody tr:hover {
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

        .karyawanPage #karyawanTable tbody tr.row-hover-active {
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
            // Initialize modal
            var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));

            // Event listener for filter button
            $('#filterButton').on('click', function() {
                filterModal.show();
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

            // Initialize DataTables - FIXED: removed parentheses after selector
            var karyawanTable = $('#karyawanTable').DataTable(
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

            // Apply filter function - Improved implementation for more reliable filtering
            $('#applyFilter').click(function() {
                // Get filter values
                var statusFilter = $('#filterStatus').val();
                var namaFilter = $('#filterNama').val();
                var tempatLahirFilter = $('#filterTempatLahir').val();
                var umurFilter = $('#filterUmur').val();
                var masaKerjaFilter = $('#filterMasaKerja').val();

                // Clear any existing filter functions first
                $.fn.dataTable.ext.search = [];

                // Apply custom filtering with a new function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var row = $(karyawanTable.row(dataIndex).node());
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

                    // Tempat Lahir filtering
                    if (show && tempatLahirFilter && tempatLahirFilter !== '') {
                        if (row.data('tempat-lahir') !== tempatLahirFilter) {
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

                    // Masa Kerja filtering
                    if (show && masaKerjaFilter && masaKerjaFilter !== '') {
                        var yearService = parseFloat(row.data('masa-kerja-tahun') || 0);
                        var monthService = parseFloat(row.data('masa-kerja-bulan') || 0);
                        var totalServiceYears = yearService + (monthService / 12);

                        var serviceRange = masaKerjaFilter.split('-');
                        var minService = parseFloat(serviceRange[0]);
                        var maxService = parseFloat(serviceRange[1]);

                        if (totalServiceYears < minService || totalServiceYears > maxService) {
                            show = false;
                        }
                    }

                    // Add filtered-row class for styling
                    if (show && (statusFilter || namaFilter || tempatLahirFilter || umurFilter ||
                            masaKerjaFilter)) {
                        row.addClass('filtered-row');
                    } else {
                        row.removeClass('filtered-row');
                    }

                    return show;
                });

                // Clear any existing classes first, then apply new ones after filtering
                $('#karyawanTable tbody tr').removeClass('filtered-row');

                // Draw table after filtering
                karyawanTable.draw();

                // Apply filter highlighting and close modal
                highlightFilterButton();
                filterModal.hide();
            });

            // Reset filter function - Improved implementation
            $('#resetFilter').click(function() {
                // Reset all select2 filters
                $('#filterStatus, #filterNama, #filterTempatLahir, #filterUmur, #filterMasaKerja').val(null)
                    .trigger('change.select2');

                // Clear ALL custom filtering functions (not just the last one)
                $.fn.dataTable.ext.search = [];

                // Clear DataTable filtering and show all rows
                karyawanTable.search('').columns().search('').draw();

                // Remove highlight from rows
                $('#karyawanTable tbody tr').removeClass('filtered-row');

                // Remove filter button highlight
                $('#filterButton').removeClass('filter-active');
            });

            $('#exportButton').on('click', function() {
                $('#exportModal').modal('show');
            });

            // Export buttons
            $('#exportExcel').on('click', function() {
                // Ambil nilai filter saat ini
                var statusFilter = $('#filterStatus').val();
                var namaFilter = $('#filterNama').val();
                var tempatLahirFilter = $('#filterTempatLahir').val();
                var umurFilter = $('#filterUmur').val();
                var masaKerjaFilter = $('#filterMasaKerja').val();

                // Buat URL dengan parameter filter
                var exportUrl = "{{ route('exportexcelkaryawan') }}";
                var params = [];

                if (statusFilter) params.push('status=' + encodeURIComponent(statusFilter));
                if (namaFilter) params.push('nama=' + encodeURIComponent(namaFilter));
                if (tempatLahirFilter) params.push('tempatLahir=' + encodeURIComponent(tempatLahirFilter));
                if (umurFilter) params.push('umurRange=' + encodeURIComponent(umurFilter));
                if (masaKerjaFilter) params.push('masaKerjaRange=' + encodeURIComponent(masaKerjaFilter));

                // Tambahkan parameter ke URL jika ada
                if (params.length > 0) {
                    exportUrl += '?' + params.join('&');
                }

                // Redirect ke URL export dengan parameter filter
                window.location.href = exportUrl;
                $('#exportModal').modal('hide');
            });

            // Function to highlight filter button when filters are active
            function highlightFilterButton() {
                if ($('#filterStatus').val() ||
                    $('#filterNama').val() ||
                    $('#filterTempatLahir').val() ||
                    $('#filterUmur').val() ||
                    $('#filterMasaKerja').val()) {
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
            $('#karyawanTable tbody').on('click', 'tr', function(e) {
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

                if (confirm('Apakah Anda yakin ingin menghapus karyawan: ' + name + '?')) {
                    // Create and submit form dynamically
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': Laravel.baseUrl + '/karyawan/' + id
                    });

                    form.append($('<input>', {
                        'name': '_method',
                        'type': 'hidden',
                        'value': 'DELETE'
                    }));

                    form.append($('<input>', {
                        'name': '_token',
                        'type': 'hidden',
                        'value': Laravel.csrfToken
                    }));

                    $('body').append(form);
                    form.submit();
                }
            });

            // Add hover effects for rows
            $('#karyawanTable tbody').on('mouseenter', 'tr', function() {
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
