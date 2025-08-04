@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')
    <div class="container-fluid karyawanPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-users me-2"></i>Manajemen Karyawan</span>
                        <a href="{{ route('karyawan.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Tambah
                        </a>
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
                                        <th width="12%">NRK</th>
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

                                            if ($joinDate) {
                                                $now = \Carbon\Carbon::now();
                                                $diffInDays = $joinDate->diffInDays($now);
                                                $years = floor($diffInDays / 365);
                                                $months = floor(($diffInDays % 365) / 30);
                                                $days = $diffInDays - $years * 365 - $months * 30;

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
                                        <tr>
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
                                                @if ($karyawan->StsKaryawan == 'Aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @elseif ($karyawan->StsKaryawan == 'Pensiun')
                                                    <span class="badge bg-info">Pensiun</span>
                                                @elseif ($karyawan->StsKaryawan == 'Mengundurkan Diri')
                                                    <span class="badge bg-warning">Mengundurkan Diri</span>
                                                @elseif ($karyawan->StsKaryawan == 'Dikeluarkan')
                                                    <span class="badge bg-danger">Dikeluarkan</span>
                                                @elseif ($karyawan->StsKaryawan == 'Meninggal')
                                                    <span class="badge bg-secondary">Meninggal</span>
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
                    <p>Apakah Anda yakin ingin menghapus data karyawan <strong id="karyawanNameToDelete"></strong>?</p>
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
    </style>
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables jika belum ada
            if (!$.fn.DataTable.isDataTable('#karyawanTable')) {
                $('#karyawanTable').DataTable({
                    responsive: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                    }
                });
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Handle delete confirmation
            $('.delete-confirm').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                // Set user name in modal
                $('#karyawanNameToDelete').text(name);

                // Set form action URL with explicit URL construction
                $('#deleteForm').attr('action', "{{ url('karyawan') }}/" + id);

                // Show modal
                $('#deleteConfirmationModal').modal('show');
            });

            // Tambahkan efek klik pada baris tabel untuk menuju halaman detail
            $('#karyawanTable tbody').on('click', 'tr', function(e) {
                // Don't follow link if clicking on buttons or links
                if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                    $(e.target).closest('button').length || $(e.target).closest('a').length) {
                    return;
                }

                // Get detail link URL
                var detailLink = $(this).find('a[title="Detail"]').attr('href');
                if (detailLink) {
                    window.location.href = detailLink;
                }
            });

            // Add flash effect when hovering over rows
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
