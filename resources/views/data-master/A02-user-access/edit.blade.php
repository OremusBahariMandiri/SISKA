@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-key me-2"></i>Kelola Hak Akses: {{ $user->nama_kry }}</span>
                    <a href="{{ route('users.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('user-access.update', $user->id) }}" method="POST" id="userAccessForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin"
                                    {{ $user->is_admin ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_admin">
                                    <span class="fw-bold text-danger">Administrator (Akses Penuh)</span>
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Administrator memiliki akses penuh ke semua fitur aplikasi.
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Detail</th>
                                        <th class="text-center">Tambah</th>
                                        <th class="text-center">Ubah</th>
                                        <th class="text-center">Hapus</th>
                                        <th class="text-center">Download</th>
                                        <th class="text-center">Monitoring</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($menuList as $key => $value)
                                    <tr>
                                        <td>
                                            <strong>{{ $value }}</strong>
                                            <div class="text-muted small">{{ $key }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="detail_{{ $key }}" name="menu_access[{{ $key }}][detail]"
                                                    {{ isset($userAccess[$key]['detail']) && $userAccess[$key]['detail'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="tambah_{{ $key }}" name="menu_access[{{ $key }}][tambah]"
                                                    {{ isset($userAccess[$key]['tambah']) && $userAccess[$key]['tambah'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="ubah_{{ $key }}" name="menu_access[{{ $key }}][ubah]"
                                                    {{ isset($userAccess[$key]['ubah']) && $userAccess[$key]['ubah'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="hapus_{{ $key }}" name="menu_access[{{ $key }}][hapus]"
                                                    {{ isset($userAccess[$key]['hapus']) && $userAccess[$key]['hapus'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="download_{{ $key }}" name="menu_access[{{ $key }}][download]"
                                                    {{ isset($userAccess[$key]['download']) && $userAccess[$key]['download'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="monitoring_{{ $key }}" name="menu_access[{{ $key }}][monitoring]"
                                                    {{ isset($userAccess[$key]['monitoring']) && $userAccess[$key]['monitoring'] ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
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
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
    }
    .form-check-input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $(".alert-dismissible").fadeOut("slow");
        }, 5000);
    });
</script>
@endpush