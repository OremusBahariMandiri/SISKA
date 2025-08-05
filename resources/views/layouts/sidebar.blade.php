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
                    $dataMasterActive = request()->is('users*') ||
                                       request()->is('perusahaan*') ||
                                       request()->is('karyawan*') ||
                                       request()->is('keluarga-karyawan*') ||
                                       request()->is('kategori-dokumen*') ||
                                       request()->is('jenis-dokumen*');
                @endphp
                <li class="nav-item has-submenu">
                    <a class="nav-link sidebar-menu-item {{ $dataMasterActive ? 'active' : '' }}"
                       href="#" data-menu="dataMaster">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="menu-icon-text">
                                <i class="fas fa-database"></i>
                                <span class="nav-text">Data Master</span>
                            </div>
                            <i class="fas fa-chevron-down submenu-indicator {{ $dataMasterActive ? 'rotated' : '' }}"></i>
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
                    </ul>
                </li>

                {{-- Manajemen Dokumen Dropdown --}}
                @php
                    $dokumenActive = request()->is('dokumen-karyawan*') ||
                                    request()->is('dokumen-kontrak*') ||
                                    request()->is('dokumen-karir*') ||
                                    request()->is('dokumen-legalitas*');
                @endphp
                <li class="nav-item has-submenu">
                    <a class="nav-link sidebar-menu-item {{ $dokumenActive ? 'active' : '' }}"
                       href="#" data-menu="manajemenDokumen">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="menu-icon-text">
                                <i class="fas fa-file-alt"></i>
                                <span class="nav-text">Manajemen Dokumen</span>
                            </div>
                            <i class="fas fa-chevron-down submenu-indicator {{ $dokumenActive ? 'rotated' : '' }}"></i>
                        </div>
                    </a>
                    <ul class="sidebar-submenu {{ $dokumenActive ? 'show' : '' }}" id="manajemenDokumen">
                        {{-- Dokumen Karyawan --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-karyawan'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-karyawan*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-karyawan.index') }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>Dokumen Karyawan</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Kontrak --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-kontrak'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-kontrak*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-kontrak.index') }}">
                                    <i class="fas fa-handshake"></i>
                                    <span>Dokumen Kontrak</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Karir --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-karir'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-karir*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-karir.index') }}">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Dokumen Karir</span>
                                </a>
                            </li>
                        @endif

                        {{-- Dokumen Legalitas --}}
                        @if (Auth::user()->is_admin || Auth::user()->hasAccess('dokumen-legalitas'))
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokumen-legalitas*') ? 'active' : '' }}"
                                    href="{{ route('dokumen-legalitas.index') }}">
                                    <i class="fas fa-gavel"></i>
                                    <span>Dokumen Legalitas</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>