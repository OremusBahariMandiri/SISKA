@extends('layouts.app')

@section('title', 'Tambah Jenis Dokumen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-plus me-2"></i>Tambah Jenis Dokumen</span>
                        <a href="{{ route('jenis-dokumen.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('jenis-dokumen.store') }}" method="POST" id="jenisForm">
                            @csrf
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $newId) }}" hidden readonly>

                            <div class="card border-secondary mb-4">
                                <div class="card-header bg-secondary bg-opacity-25 text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jenis Dokumen</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label for="IdKodeA06" class="form-label fw-bold">Kategori Dokumen <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                            <select class="form-select @error('IdKodeA06') is-invalid @enderror" id="IdKodeA06" name="IdKodeA06" required>
                                                <option value="" selected disabled>Pilih Kategori Dokumen</option>
                                                @foreach($kategoriDokumens as $kategori)
                                                    <option value="{{ $kategori->IdKode }}" {{ old('IdKodeA06') == $kategori->IdKode ? 'selected' : '' }}>
                                                        {{ $kategori->KategoriDok }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('IdKodeA06')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>Pilih kategori dokumen yang sesuai
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="GolDok" class="form-label fw-bold">Golongan Dokumen <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                            <input type="text" class="form-control @error('GolDok') is-invalid @enderror" id="GolDok" name="GolDok"
                                                value="{{ old('GolDok') }}" required placeholder="Masukkan golongan dokumen">
                                            @error('GolDok')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="JenisDok" class="form-label fw-bold">Nama Jenis Dokumen <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                            <input type="text" class="form-control @error('JenisDok') is-invalid @enderror" id="JenisDok" name="JenisDok"
                                                value="{{ old('JenisDok') }}" required>
                                            @error('JenisDok')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>Contoh: SOP, Instruksi Kerja, Pedoman, dll.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 col-md-4 mx-auto">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Simpan
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

        /* Custom select styling */
        .form-select {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            background-position: right 0.75rem center;
        }

        .form-select:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation with visual feedback
            const form = document.getElementById('jenisForm');
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
                                input.parentNode.insertBefore(feedback, input.nextElementSibling);
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

                input.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endpush