@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-building-circle-plus me-2"></i>Tambah Perusahaan</span>
                        <a href="{{ route('perusahaan.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('perusahaan.store') }}" method="POST" id="perusahaanForm">
                            @csrf
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $newId) }}" hidden readonly>

                            <!-- Grouped form sections with cards -->
                            <div class="row g-4">
                                <!-- Informasi Perusahaan -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-building me-2"></i>Informasi Perusahaan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="NamaPrsh" class="form-label fw-bold">Nama Perusahaan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                    <input type="text" class="form-control" id="NamaPrsh" name="NamaPrsh"
                                                        value="{{ old('NamaPrsh') }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="SingkatanPrsh" class="form-label fw-bold">Singkatan Perusahaan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-house"></i></span>
                                                    <input type="text" class="form-control" id="SingkatanPrsh" name="SingkatanPrsh"
                                                        value="{{ old('SingkatanPrsh') }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="AlamatPrsh" class="form-label fw-bold">Alamat <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                    <textarea class="form-control" id="AlamatPrsh" name="AlamatPrsh" rows="3" required>{{ old('AlamatPrsh') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="TelpPrsh" class="form-label fw-bold">Telepon <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    <input type="text" class="form-control" id="TelpPrsh" name="TelpPrsh"
                                                        value="{{ old('TelpPrsh') }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="TelpPrsh2" class="form-label fw-bold">Telepon 2</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                                    <input type="text" class="form-control" id="TelpPrsh2" name="TelpPrsh2"
                                                        value="{{ old('TelpPrsh2') }}">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="EmailPrsh" class="form-label fw-bold">Email <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <input type="email" class="form-control" id="EmailPrsh" name="EmailPrsh"
                                                        value="{{ old('EmailPrsh') }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="EmailPrsh2" class="form-label fw-bold">Email 2</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <input type="email" class="form-control" id="EmailPrsh2" name="EmailPrsh2"
                                                        value="{{ old('EmailPrsh2') }}">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="WebPrsh" class="form-label fw-bold">Website</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                    <input type="text" class="form-control" id="WebPrsh" name="WebPrsh"
                                                        value="{{ old('WebPrsh') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Bisnis dan Manajemen -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary bg-opacity-25 text-white">
                                            <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Informasi Bisnis & Manajemen</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="TglBerdiri" class="form-label fw-bold">Tanggal Berdiri</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                    <input type="date" class="form-control" id="TglBerdiri" name="TglBerdiri"
                                                        value="{{ old('TglBerdiri') }}">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="BidangUsh" class="form-label fw-bold">Bidang Usaha <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                                    <input type="text" class="form-control" id="BidangUsh" name="BidangUsh"
                                                        value="{{ old('BidangUsh') }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="IzinUsh" class="form-label fw-bold">Izin Usaha</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                                    <input type="text" class="form-control" id="IzinUsh" name="IzinUsh"
                                                        value="{{ old('IzinUsh') }}">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="GolonganUsh" class="form-label fw-bold">Golongan Usaha</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                                    <select class="form-select" id="GolonganUsh" name="GolonganUsh">
                                                        <option value="" selected disabled>Pilih Golongan Usaha</option>
                                                        <option value="MIKRO" {{ old('GolonganUsh') == 'MIKRO' ? 'selected' : '' }}>
                                                            MIKRO</option>
                                                        <option value="KECIL" {{ old('GolonganUsh') == 'KECIL' ? 'selected' : '' }}>
                                                            KECIL</option>
                                                        <option value="MENENGAH"
                                                            {{ old('GolonganUsh') == 'MENENGAH' ? 'selected' : '' }}>MENENGAH</option>
                                                        <option value="BESAR" {{ old('GolonganUsh') == 'BESAR' ? 'selected' : '' }}>
                                                            BESAR</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="DirekturUtm" class="form-label fw-bold">Direktur Utama <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                    <input type="text" class="form-control" id="DirekturUtm" name="DirekturUtm"
                                                        value="{{ old('DirekturUtm') }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="Direktur" class="form-label fw-bold">Direktur</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                    <input type="text" class="form-control" id="Direktur" name="Direktur"
                                                        value="{{ old('Direktur') }}">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="KomisarisUtm" class="form-label fw-bold">Komisaris Utama</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                    <input type="text" class="form-control" id="KomisarisUtm"
                                                        name="KomisarisUtm" value="{{ old('KomisarisUtm') }}">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="Komisaris" class="form-label fw-bold">Komisaris</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                    <input type="text" class="form-control" id="Komisaris" name="Komisaris"
                                                        value="{{ old('Komisaris') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 col-md-4 mx-auto mt-4">
                                <button type="submit" class="btn btn-secondary btn-lg">
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

        .bg-light {
            background-color: #f8f9fa;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation with visual feedback
            const form = document.getElementById('perusahaanForm');
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
            });

            // Validate email format
            const emailInputs = document.querySelectorAll('input[type="email"]');
            emailInputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    if (this.value && !isValidEmail(this.value)) {
                        this.classList.add('is-invalid');
                        // Create error message if it doesn't exist
                        if (!this.nextElementSibling || !this.nextElementSibling.classList
                            .contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Format email tidak valid';
                            this.parentNode.insertBefore(feedback, this.nextElementSibling);
                        }
                    }
                });
            });
        });

        // Email validation function
        function isValidEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
    </script>
@endpush