@extends('layouts.app')

@section('title', 'Edit Kategori Dokumen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Kategori Dokumen</span>
                        <a href="{{ route('kategori-dokumen.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('kategori-dokumen.update', $kategoriDokumen->id) }}" method="POST" id="kategoriForm">
                            @csrf
                            @method('PUT')

                            <div class="card border-secondary mb-4">
                                <div class="card-header bg-secondary bg-opacity-25 text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Kategori</h5>
                                </div>
                                <div class="card-body">
                                    <input type="text" class="form-control" id="IdKode" value="{{ $kategoriDokumen->IdKode }}" hidden>

                                    <div class="form-group mb-3">
                                        <label for="GolDok" class="form-label fw-bold">Golongan Dokumen <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                            <input type="text" class="form-control" id="GolDok" name="GolDok"
                                                value="{{ old('GolDok', $kategoriDokumen->GolDok) }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="KategoriDok" class="form-label fw-bold">Nama Kategori <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                            <input type="text" class="form-control" id="KategoriDok" name="KategoriDok"
                                                value="{{ old('KategoriDok', $kategoriDokumen->KategoriDok) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 col-md-4 mx-auto">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Update
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

        .form-text {
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation with visual feedback
            const form = document.getElementById('kategoriForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Highlight missing required fields
                    document.querySelectorAll('[required]').forEach(function(input) {
                        if (!input.value) {
                            input.classList.add('is-invalid');
                            // Create error message if it doesn't exist
                            if (!input.nextElementSibling || !input.nextElementSibling.classList
                                .contains('invalid-feedback')) {
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = 'Field ini wajib diisi';

                                // For select elements in input-group, insert after the input-group
                                if (input.parentNode.classList.contains('input-group')) {
                                    input.parentNode.parentNode.insertBefore(feedback, input.parentNode.nextElementSibling);
                                } else {
                                    input.parentNode.insertBefore(feedback, input.nextElementSibling);
                                }
                            }
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    // Scroll to first error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                }
            });

            // Remove invalid class when input changes
            document.querySelectorAll('[required]').forEach(function(input) {
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endpush