@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Card 1: Image -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-0">
                        <div class="dashboard-image-container position-relative">
                            <img src="{{asset('images/hrdlandscape.png')}}" class="dashboard-image">
                            <div class="overlay"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Text and Clock -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="dashboard-welcome-container text-center">
                            <div class="welcome-time mb-3">
                                <div class="current-date fs-5" style="color: #02786e"></div>
                                <div class="current-time display-4 fw-bold" style="color: #02786e;"></div>
                            </div>
                            <h2 class="fs-1 fw-light mb-0" id="siska" style="color: #02786e;">SISKA</h2>
                            <p class="lead mt-2 text-strong" style="color: #02786e;">
                                Sistem Informasi Karyawan
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .dashboard-image-container {
            height: 100%;
            overflow: hidden;
            border-radius: 8px;
        }

        .dashboard-welcome-container {
            background-color: #fff;
        }

        .dashboard-image {
            width: 100%;
            height: 350px;
            object-fit: fill;
            object-position: center;
            transition: transform 0.3s ease;
        }

        .dashboard-image:hover {
            transform: scale(1.05);
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.3));
        }

        .welcome-heading {
            margin-bottom: 0;
        }

        .text-gradient {
            background: linear-gradient(90deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        .quick-action-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .quick-action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1);
        }

        @media (max-width: 991.98px) {
            .welcome-heading {
                font-size: 2.5rem;
            }

            h2.fs-1 {
                font-size: 1.8rem !important;
            }

            .current-time {
                font-size: 2.5rem !important;
            }

            .dashboard-image-container {
                height: 250px;
            }
        }

        @media (max-width: 575.98px) {
            .dashboard-image-container {
                height: 200px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update jam dan tanggal
            function updateClock() {
                const now = new Date();

                // Format waktu
                let hours = now.getHours();
                let minutes = now.getMinutes();
                let seconds = now.getSeconds();

                // Format tanggal dalam Bahasa Indonesia
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];

                const day = days[now.getDay()];
                const date = now.getDate();
                const month = months[now.getMonth()];
                const year = now.getFullYear();

                // Tampilkan waktu dengan format jam:menit:detik
                hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                $('.current-time').text(`${hours}:${minutes}:${seconds}`);
                $('.current-date').text(`${day}, ${date} ${month} ${year}`);

                // Update setiap detik
                setTimeout(updateClock, 1000);
            }

            // Atur salam berdasarkan waktu
            function setGreeting() {
                const hour = new Date().getHours();
                let greeting;

                if (hour >= 5 && hour < 12) {
                    greeting = "Selamat Pagi,";
                } else if (hour >= 12 && hour < 15) {
                    greeting = "Selamat Siang,";
                } else if (hour >= 15 && hour < 19) {
                    greeting = "Selamat Sore,";
                } else {
                    greeting = "Selamat Malam,";
                }

                $('.welcome-heading').text(greeting);
            }

            // Jalankan fungsi
            updateClock();
            setGreeting();

            // Inisialisasi tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush