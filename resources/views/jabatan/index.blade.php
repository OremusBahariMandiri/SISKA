@extends('layouts.app')

@section('title', 'Manajemen Jabatan')

@section('content')
    <div class="container-fluid jabatanPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-folder me-2"></i>Manajemen Jabatan</span>
                        @if (auth()->user()->is_admin || ($userPermissions['tambah'] ?? false))
                            <a href="{{ route('jabatan.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Tambah
                            </a>
                        @endif
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
                            <table id="jabatanTable" class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Golongan Jabatan</th>
                                        <th>Jabatan</th>
                                        <th>Singkatan Jabatan</th>
                                        <th width="20%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jabatan as $jbt)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $jbt->GolonganJbt }}</td>
                                            <td>{{ $jbt->Jabatan }}</td>
                                            <td>{{ $jbt->SingkatanJbtn }}</td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->user()->is_admin || ($userPermissions['detail'] ?? false))
                                                        <a href="{{ route('jabatan.show', $jbt->id) }}"
                                                            class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['ubah'] ?? false))
                                                        <a href="{{ route('jabatan.edit', $jbt->id) }}"
                                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->is_admin || ($userPermissions['hapus'] ?? false))
                                                        <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                                            data-bs-toggle="tooltip" title="Hapus"
                                                            data-id="{{ $jbt->id }}"
                                                            data-name="{{ $jbt->GolonganJbt }}"
                                                            data-bs-toggle="tooltip" title="Hapus">
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
                    <p>Apakah Anda yakin ingin menghapus jabatan <strong id="jabatanNameToDelete"></strong>?</p>
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
        .jabatanPage .dataTables_wrapper .dataTables_length,
        .jabatanPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .jabatanPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .jabatanPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .jabatanPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .jabatanPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .jabatanPage table.dataTable thead th.sorting:after,
        .jabatanPage table.dataTable thead th.sorting_asc:after,
        .jabatanPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .jabatanPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .jabatanPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .jabatanPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* Add hover effect to action buttons */
        .jabatanPage .btn-sm {
            transition: transform 0.2s;
        }

        .jabatanPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .jabatanPage #jabatanTable tbody tr {
            transition: all 0.2s ease;
        }

        .jabatanPage #jabatanTable tbody tr:hover {
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

        .jabatanPage #jabatanTable tbody tr.row-hover-active {
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
            if (!$.fn.DataTable.isDataTable('#jabatanTable')) {
                $('#jabatanTable').DataTable({
                    responsive: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                    }
                });
            }

            // Initialize tooltips dengan event delegation
            $(document).on('mouseenter', '[data-bs-toggle="tooltip"]', function() {
                if (!$(this).attr('data-bs-original-title')) {
                    new bootstrap.Tooltip(this);
                }
            });

            // MODIFIED: Use event delegation for delete confirmation
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var id = $(this).data('id');
                var name = $(this).data('name');

                console.log("Delete clicked for:", name, "ID:", id);

                // Set jabatan name in modal
                $('#jabatanNameToDelete').text(name);

                // Set form action URL with explicit URL construction
                $('#deleteForm').attr('action', "{{ url('jabatan') }}/" + id);

                // Show modal
                $('#deleteConfirmationModal').modal('show');
            });

            // MODIFIED: Use event delegation for row click
            $(document).on('click', '#jabatanTable tbody tr', function(e) {
                // Don't follow link if clicking on buttons or links
                if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                    $(e.target).closest('button').length || $(e.target).closest('a').length) {
                    return;
                }

                // Check if user has detail access before redirecting
                @if (auth()->user()->is_admin || ($userPermissions['detail'] ?? false))
                    // Get detail link URL
                    var detailLink = $(this).find('a[title="Detail"]').attr('href');
                    if (detailLink) {
                        window.location.href = detailLink;
                    }
                @endif
            });

            // MODIFIED: Use event delegation for hover effects
            $(document).on('mouseenter', '#jabatanTable tbody tr', function() {
                $(this).addClass('row-hover-active');
            }).on('mouseleave', '#jabatanTable tbody tr', function() {
                $(this).removeClass('row-hover-active');
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush