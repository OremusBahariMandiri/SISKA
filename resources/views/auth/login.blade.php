{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SISKA') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #6f6e6e;
            --primary-dark: #0a5bca;
            --primary-light: #e8f1ff;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fc;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .login-container {
            height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .login-image-container {
            flex: 1;
            display: none;
            position: relative;
            overflow: hidden;
        }

        .login-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }

        .login-image:hover {
            transform: scale(1.02);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(18, 112, 252, 0.4));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .company-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: white;
        }

        .image-overlay h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .image-overlay p {
            font-size: 1.1rem;
            max-width: 80%;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .login-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            background-color: white;
            min-height: 100vh;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;

        }

        .login-header h2 {
            color: #02786e;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #02786e;
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-control {
            padding: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(18, 112, 252, 0.25);
        }

        .form-check-label {
            color: #6c757d;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(18, 112, 252, 0.3);
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .mobile-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .mobile-logo i {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .mobile-logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 0.5rem;
        }

        .mobile-logo p {
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Password Toggle Styles */
        .password-input-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-toggle:focus {
            outline: none;
        }

        .password-input-container .form-control {
            padding-right: 2.5rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 991.98px) {
            .login-form-container {
                padding: 1.5rem;
            }
        }

        @media (min-width: 992px) {
            .login-image-container {
                display: block;
            }

            .mobile-logo {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .login-form-container {
                padding: 1rem;
            }

            .mobile-logo h1 {
                font-size: 1.3rem;
            }

            .image-overlay h1 {
                font-size: 2rem;
            }

            .image-overlay p {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Image -->
        <div class="login-image-container">
            <img src="{{ asset('images/hrd3.png') }}"
                alt="Ship Navigation Bridge" class="login-image">
            <div class="image-overlay">
                <h1>SISKA</h1>
                <p>Sistem Informasi Karyawan</p>
                <p>&copy; {{ date('Y') }} Rev.01 </p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <!-- Mobile Logo (visible on small screens) -->
            <div class="mobile-logo mt-5">
                <i class="fas fa-id-badge"></i>
                <h1>SISKA</h1>
                <p>Sistem Informasi Karyawan</p>
            </div>

            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Masukkan NRK dan Password Anda untuk mengakses sistem</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="login-form">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- NIK -->
                    <div class="form-group">
                        <label for="NikKry" class="form-label">NRK Karyawan</label>
                        <input id="NikKry" class="form-control @error('NikKry') is-invalid @enderror" type="text"
                            name="NikKry" value="{{ old('NikKry') }}" required autofocus autocomplete="username"
                            placeholder="Masukkan NRK Karyawan">
                        @error('NikKry')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-container">
                            <input id="password" class="form-control @error('password') is-invalid @enderror"
                                type="password" name="password" required autocomplete="current-password"
                                placeholder="Masukkan Password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Masuk
                        </button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="forgot-password" href="{{ route('password.request') }}">
                                {{ __('Lupa Password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="login-footer">
                <p>SISKA </p>
                <p>Sistem Informasi Karyawan.</p>
                <p>&copy; {{ date('Y') }} Rev.01 </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordEye = document.getElementById('password-eye');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordEye.classList.remove('fa-eye');
                passwordEye.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordEye.classList.remove('fa-eye-slash');
                passwordEye.classList.add('fa-eye');
            }
        }

        // Auto dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>

</html>