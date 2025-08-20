@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-cog me-2"></i>Pengaturan</span>
                        <a href="{{ route('home') }}" class="btn btn-warning">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary bg-opacity-25 text-white">
                                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informasi Akun</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">NIK</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <div class="form-control">{{ $user->NikKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Nama</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <div class="form-control">{{ $user->NamaKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Departemen</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <div class="form-control">{{ $user->DepartemenKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Jabatan</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                            <div class="form-control">{{ $user->JabatanKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Wilker</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <div class="form-control">{{ $user->WilkerKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="changePasswordModalLabel"><i class="fas fa-key me-2"></i>Ubah Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('settings.update-password') }}" method="POST" id="changePasswordForm">
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">Password Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password" placeholder="Masukan password saat ini" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="fas fa-eye" id="eyeIconCurrent"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Masukan password baru" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Password minimal 6 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Masukan konfirmasi password baru" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="fas fa-eye" id="eyeIconConfirmation"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Ulangi password baru yang sama persis.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
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

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }

        .text-danger {
            font-weight: bold;
        }

        /* Password toggle button styling */
        .password-toggle {
            border: none;
            background: none;
            cursor: pointer;
            padding: 0.375rem 0.75rem;
            transition: color 0.15s ease-in-out;
        }

        .password-toggle:hover {
            color: #0056b3;
        }

        .password-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show modal with validation errors if any
            @if ($errors->any())
                var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                changePasswordModal.show();
            @endif

            // Password toggle functionality
            const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
            const currentPasswordInput = document.getElementById('current_password');
            const eyeIconCurrent = document.getElementById('eyeIconCurrent');

            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const eyeIconConfirmation = document.getElementById('eyeIconConfirmation');

            // Toggle current password visibility
            toggleCurrentPassword.addEventListener('click', function() {
                const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                currentPasswordInput.setAttribute('type', type);

                // Toggle eye icon
                if (type === 'text') {
                    eyeIconCurrent.classList.remove('fa-eye');
                    eyeIconCurrent.classList.add('fa-eye-slash');
                } else {
                    eyeIconCurrent.classList.remove('fa-eye-slash');
                    eyeIconCurrent.classList.add('fa-eye');
                }
            });

            // Toggle new password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle eye icon
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });

            // Toggle password confirmation visibility
            togglePasswordConfirmation.addEventListener('click', function() {
                const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmationInput.setAttribute('type', type);

                // Toggle eye icon
                if (type === 'text') {
                    eyeIconConfirmation.classList.remove('fa-eye');
                    eyeIconConfirmation.classList.add('fa-eye-slash');
                } else {
                    eyeIconConfirmation.classList.remove('fa-eye-slash');
                    eyeIconConfirmation.classList.add('fa-eye');
                }
            });

            // Form validation with visual feedback
            const form = document.getElementById('changePasswordForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Highlight missing required fields
                    document.querySelectorAll('[required]').forEach(function(input) {
                        if (!input.value) {
                            input.classList.add('is-invalid');
                            // Create error message if it doesn't exist
                            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
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
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
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

            // Password confirmation validation
            function validatePassword() {
                if (passwordInput.value && passwordInput.value !== passwordConfirmationInput.value) {
                    passwordConfirmationInput.setCustomValidity('Password tidak sama');
                    passwordConfirmationInput.classList.add('is-invalid');
                } else {
                    passwordConfirmationInput.setCustomValidity('');
                    passwordConfirmationInput.classList.remove('is-invalid');
                }
            }

            passwordInput.addEventListener('input', validatePassword);
            passwordConfirmationInput.addEventListener('input', validatePassword);

            // Clear form when modal is hidden
            document.getElementById('changePasswordModal').addEventListener('hidden.bs.modal', function() {
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(function(element) {
                    element.classList.remove('is-invalid');
                });
            });
        });
    </script>
@endpush