@extends('layouts.app')

@section('title', 'Detail Jenis Dokumen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-alt me-2"></i>Detail Jenis Dokumen</span>
                        <div>
                            <a href="{{ route('jenis-dokumen.edit', $jenisDokumen->id   ) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('jenis-dokumen.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card border-secondary mb-4">
                            <div class="card-header bg-secondary bg-opacity-25 text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jenis Dokumen</h5>
                            </div>
                            <div class="card-body">


                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Kategori Dokumen</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                            <div class="form-control">{{ $jenisDokumen->kategoriDokumen->KategoriDok ?? 'Tidak tersedia' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Golongan Dokumen</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                            <div class="form-control">{{ $jenisDokumen->kategoriDokumen->GolDok ?? 'Tidak tersedia' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Nama Jenis Dokumen</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                            <div class="form-control">{{ $jenisDokumen->JenisDok }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Action Buttons -->

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                        <h5>Apakah Anda yakin ingin menghapus jenis dokumen ini?</h5>
                        <p class="text-muted">
                            <strong>{{ $jenisDokumen->JenisDok }}</strong><br>
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <form action="{{ route('jenis-dokumen.destroy', $jenisDokumen->IdKode) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Ya, Hapus
                        </button>
                    </form>
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

        .info-label {
            margin-bottom: 0.3rem;
            display: block;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-value .form-control {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            min-height: 38px;
        }

        .card {
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }

        .modal-content {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Confirmation before delete
            const deleteForm = document.querySelector('#deleteModal form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
                    submitBtn.disabled = true;
                });
            }
        });
    </script>
@endpush