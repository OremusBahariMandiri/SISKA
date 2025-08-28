{{-- resources/views/layouts/sidebar.blade.php --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <i class="fas fa-id-badge"></i>
            <span class="brand-text">SISKA</span>
        </a>
        <button type="button" class="sidebar-close d-md-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-menu">
            <div class="sidebar-heading">Menu Utama</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                {{-- Data Master Dropdown --}}
                @php
                    $dataMasterActive =
                        request()->is('users*') ||
                        request()->is('perusahaan*') ||
                        request()->is('karyawan*') ||
                        request()->is('keluarga-karyawan*') ||
                        request()->is('kategori-dokumen*') ||
                        request()->is('jenis-dokumen*') ||
                        request()->is('jabatan') ||
                        request()->is('departemen') ||
                        request()->is('wilayah-kerja');

                    // Check if user has access to any Data Master submenu
                    $hasDataMasterAccess = Auth::user()->is_admin ||
                        Auth::user()->hasAccess('pengguna') ||
                        Auth::user()->hasAccess('hak_akses') ||
                        Auth::user()->hasAccess('perusahaan') ||
                        Auth::user()->hasAccess('karyawan') ||
                        Auth::user()->hasAccess('keluarga-karyawan') ||
                        Auth::user()->hasAccess('kategori_dokumen') ||
                        Auth::user()->hasAccess('jenis-dokumen') ||
                        Auth::user()->hasAccess('jabatan') ||
                        Auth::user()->hasAccess('departemen') ||
                        Auth::user()->hasAccess('wilayah-kerja');
                @endphp

                @if($hasDataMasterAccess)
                <li class="nav-item has-submenu">
                    <a class="nav-link sidebar-menu-item {{ $dataMasterActive ? 'active' : '' }}" href="#"
                        data-menu="dataMaster">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="menu-icon-text">
                                <i class="fas fa-database"></i>
                                <span class="nav-text">Data Master</span>
                            </div>
                            <i
                                class="fas fa-chevron-down submenu-indicator {{ $dataMasterActive ? 'rotated' : '' }}"></i>
                        </div>
                    </a>
                    <ul class="sidebar-submenu {{ $dataMasterActive ? 'show' : '' }}" id="dataMaster">
                        {{-- Users --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('pengguna'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('users*') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>Pengguna</span>
                                </a>
                            </li>
                        @endif

                        {{-- Perusahaan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('perusahaan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('perusahaan*') ? 'active' : '' }}"
                                    href="{{ route('perusahaan.index') }}">
                                    <i class="fas fa-building"></i>
                                    <span>Perusahaan</span>
                                </a>
                            </li>
                        @endif

                        {{-- Karyawan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('karyawan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('karyawan*') ? 'active' : '' }}"
                                    href="{{ route('karyawan.index') }}">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Karyawan</span>
                                </a>
                            </li>
                        @endif

                        {{-- Keluarga Karyawan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('keluarga-karyawan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('keluarga-karyawan*') ? 'active' : '' }}"
                                    href="{{ route('keluarga-karyawan.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>Keluarga Karyawan</span>
                                </a>
                            </li>
                        @endif

                        {{-- Kategori Dokumen --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('kategori-dokumen'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('kategori-dokumen*') ? 'active' : '' }}"
                                    href="{{ route('kategori-dokumen.index') }}">
                                    <i class="fas fa-folder"></i>
                                    <span>Kategori Dokumen</span>
                                </a>
                            </li>
                        @endif

                        {{-- Jenis Dokumen --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('jenis-dokumen'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('jenis-dokumen*') ? 'active' : '' }}"
                                    href="{{ route('jenis-dokumen.index') }}">
                                    <i class="fas fa-clone"></i>
                                    <span>Jenis Dokumen</span>
                                </a>
                            </li>
                        @endif
                        {{-- Jabatan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('jabatan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('jabatan*') ? 'active' : '' }}"
                                    href="{{ route('jabatan.index') }}">
                                    <i class="fas fa-clipboard-user"></i>
                                    <span>Jabatan</span>
                                </a>
                            </li>
                        @endif
                        {{-- Departemen --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('departemen'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('departemen*') ? 'active' : '' }}"
                                    href="{{ route('departemen.index') }}">
                                    <i class="fas fa-building"></i>
                                    <span>Departemen</span>
                                </a>
                            </li>
                        @endif
                        {{-- Wilayah Kerja --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('wilayah-kerja'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('wilayah-kerja*') ? 'active' : '' }}"
                                    href="{{ route('wilayah-kerja.index') }}">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Wilayah Kerja</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- Manajemen Dokumen Dropdown --}}
                @php
                    $dokumenActive =
                        request()->is('dokumen-karyawan*') ||
                        request()->is('dokumen-kontrak*') ||
                        request()->is('dokumen-karir*') ||
                        request()->is('dokumen-legalitas*');

                    // Check if user has access to any Dokumen submenu
                    $hasDokumenAccess = Auth::user()->is_admin ||
                        Auth::user()->hasAccess('formulir-dokumen') ||
                        Auth::user()->hasAccess('dokumen-karyawan') ||
                        Auth::user()->hasAccess('dokumen-kontrak') ||
                        Auth::user()->hasAccess('dokumen-karir') ||
                        Auth::user()->hasAccess('dokumen-legalitas');
                @endphp

                @if($hasDokumenAccess)
                <li class="nav-item has-submenu">
                    <a class="nav-link sidebar-menu-item {{ $dokumenActive ? 'active' : '' }}" href="#"
                        data-menu="manajemenDokumen">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="menu-icon-text">
                                <i class="fas fa-file-alt"></i>
                                <span class="nav-text">Dokumen</span>
                            </div>
                            <i class="fas fa-chevron-down submenu-indicator {{ $dokumenActive ? 'rotated' : '' }}"></i>
                        </div>
                    </a>
                    <ul class="sidebar-submenu {{ $dokumenActive ? 'show' : '' }}" id="manajemenDokumen">
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('formulir-dokumen'))
                        <li class="nav-item">
                            <a class="submenu-link {{ request()->is('formulir-dokumen*') ? 'active' : '' }}"
                                href="{{ route('formulir-dokumen.index') }}">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Formulir</span>
                            </a>
                        </li>
                    @endif
                        {{-- Dokumen Karyawan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-karyawan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-karyawan*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-karyawan.index') }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>Karyawan</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Kontrak --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-kontrak'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-kontrak*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-kontrak.index') }}">
                                    <i class="fas fa-handshake"></i>
                                    <span>Kontrak Kerja</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Karir --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-karir'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-karir*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-karir.index') }}">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Jenjang Karir</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Legalitas --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-legalitas'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-legalitas*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-legalitas.index') }}">
                                    <i class="fas fa-gavel"></i>
                                    <span>Legalitas & Kebijakan</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Bpjs Kesehatan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-bpjs-kesehatan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-bpjs-kesehatan*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-bpjs-kesehatan.index') }}">
                                    <i class="fas fa-file-medical"></i>
                                    <span>BPJS Kesehatan</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('settings.index') ? 'active' : '' }}"
                        href="{{ route('settings.index') }}">
                        <i class="fas fa-gear"></i>
                        <span class="nav-text">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>