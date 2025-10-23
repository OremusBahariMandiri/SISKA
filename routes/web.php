<?php

use App\Http\Controllers\A01UserController;
use App\Http\Controllers\A03PerusahaanController;
use App\Http\Controllers\A04KaryawanController;
use App\Http\Controllers\A05KeluargaKaryawanController;
use App\Http\Controllers\A06KategoriDokumenController;
use App\Http\Controllers\A07JenisDokumenController;
use App\Http\Controllers\A08JabatanController;
use App\Http\Controllers\A09DepartemenController;
use App\Http\Controllers\A10WilayahKerjaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DokumenBpjsKesehatanController;
use App\Http\Controllers\DokumenBpjsTenagaKerjaController;
use App\Http\Controllers\DokumenKarirController;
use App\Http\Controllers\DokumenKaryawanController;
use App\Http\Controllers\DokumenKontrakController;
use App\Http\Controllers\DokumenLegalitasController;
use App\Http\Controllers\FormulirDokumenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserAccessController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - HR Document Management System
|--------------------------------------------------------------------------
| Dengan Multi-Layer Security & Rate Limiting
*/

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================
// AUTHENTICATION ROUTES - STRICT RATE LIMITING
// ============================================
Route::middleware('guest')->group(function () {
    // Login dengan proteksi brute force (10 attempts/min)
    Route::middleware('throttle:login')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });
});

// Logout dengan rate limit sedang
Route::middleware(['auth', 'throttle:20,1'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

// ============================================
// AUTHENTICATED ROUTES - MULTI-LAYER SECURITY
// ============================================
Route::middleware(['auth', 'throttle:general'])->group(function () {

    // ============================================
    // DASHBOARD - Rate limit tinggi
    // ============================================
    Route::middleware('throttle:dashboard')->group(function () {
        Route::get('/home', function () {
            return view('home');
        })->name('home');
    });

    // ============================================
    // USER MANAGEMENT - Strict Security
    // ============================================
    Route::prefix('users')->name('users.')->group(function () {

        // Read operations
        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [A01UserController::class, 'index'])->name('index');
            Route::get('/create', [A01UserController::class, 'create'])->name('create');
            Route::get('/{user}', [A01UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [A01UserController::class, 'edit'])->name('edit');
        });

        // Write operations - ketat
        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [A01UserController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{user}', [A01UserController::class, 'update'])->name('update');
            Route::patch('/{user}', [A01UserController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{user}', [A01UserController::class, 'destroy'])->name('destroy');
        });
    });

    // User Access Management - Admin only dengan rate limit ketat
    Route::prefix('user-access')->name('user-access.')->middleware('throttle:admin')->group(function () {
        Route::get('/{user}', [UserAccessController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserAccessController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserAccessController::class, 'update'])->name('update');
    });

    // ============================================
    // PERUSAHAAN MANAGEMENT
    // ============================================
    Route::prefix('perusahaan')->name('perusahaan.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [A03PerusahaanController::class, 'index'])->name('index');
            Route::get('/create', [A03PerusahaanController::class, 'create'])->name('create');
            Route::get('/{perusahaan}', [A03PerusahaanController::class, 'show'])->name('show');
            Route::get('/{perusahaan}/edit', [A03PerusahaanController::class, 'edit'])->name('edit');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [A03PerusahaanController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{perusahaan}', [A03PerusahaanController::class, 'update'])->name('update');
            Route::patch('/{perusahaan}', [A03PerusahaanController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{perusahaan}', [A03PerusahaanController::class, 'destroy'])->name('destroy');
        });
    });

    // ============================================
    // KARYAWAN MANAGEMENT - Sensitive Data
    // ============================================
    Route::prefix('karyawan')->name('karyawan.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [A04KaryawanController::class, 'index'])->name('index');
            Route::get('/create', [A04KaryawanController::class, 'create'])->name('create');
            Route::get('/{karyawan}', [A04KaryawanController::class, 'show'])->name('show');
            Route::get('/{karyawan}/edit', [A04KaryawanController::class, 'edit'])->name('edit');
            Route::get('/{id}/view-document', [A04KaryawanController::class, 'viewDocument'])->name('view-document');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [A04KaryawanController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{karyawan}', [A04KaryawanController::class, 'update'])->name('update');
            Route::patch('/{karyawan}', [A04KaryawanController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{karyawan}', [A04KaryawanController::class, 'destroy'])->name('destroy');
        });

        // Export - strict limit karena resource intensive
        Route::middleware('throttle:export')->group(function () {
            Route::get('/export-excel', [A04KaryawanController::class, 'exportExcel'])->name('exportexcel');
        });
    });

    // Alias route untuk backward compatibility
    Route::get('exportexcelkaryawan', [A04KaryawanController::class, 'exportExcel'])
        ->middleware('throttle:export')
        ->name('exportexcelkaryawan');

    // ============================================
    // KELUARGA KARYAWAN - Personal Data Protection
    // ============================================
    Route::prefix('keluarga-karyawan')->name('keluarga-karyawan.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [A05KeluargaKaryawanController::class, 'index'])->name('index');
            Route::get('/create', [A05KeluargaKaryawanController::class, 'create'])->name('create');
            Route::get('/{keluargaKaryawan}', [A05KeluargaKaryawanController::class, 'show'])->name('show');
            Route::get('/{keluargaKaryawan}/edit', [A05KeluargaKaryawanController::class, 'edit'])->name('edit');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [A05KeluargaKaryawanController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{keluargaKaryawan}', [A05KeluargaKaryawanController::class, 'update'])->name('update');
            Route::patch('/{keluargaKaryawan}', [A05KeluargaKaryawanController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{keluargaKaryawan}', [A05KeluargaKaryawanController::class, 'destroy'])->name('destroy');
        });

        // API endpoints - rate limit sedang
        Route::middleware('throttle:api')->group(function () {
            Route::get('/get-karyawan-detail/{id}', [A05KeluargaKaryawanController::class, 'getKaryawanDetail'])
                ->name('get-karyawan-detail');
        });

        // Export
        Route::middleware('throttle:export')->group(function () {
            Route::get('/export-excel', [A05KeluargaKaryawanController::class, 'exportExcel'])
                ->name('exportexcel');
        });
    });

    // Alias route
    Route::get('/export-excel-keluarga-karyawan', [A05KeluargaKaryawanController::class, 'exportExcel'])
        ->middleware('throttle:export')
        ->name('exportexcelkeluargakaryawan');

    // ============================================
    // MASTER DATA ROUTES - Moderate Security
    // ============================================

    // Kategori Dokumen
    Route::resource('kategori-dokumen', A06KategoriDokumenController::class)
        ->middleware('throttle:read')
        ->except(['store', 'update', 'destroy']);

    Route::middleware('throttle:create')->group(function () {
        Route::post('kategori-dokumen', [A06KategoriDokumenController::class, 'store'])
            ->name('kategori-dokumen.store');
    });

    Route::middleware('throttle:update')->group(function () {
        Route::put('kategori-dokumen/{kategoriDokumen}', [A06KategoriDokumenController::class, 'update'])
            ->name('kategori-dokumen.update');
        Route::patch('kategori-dokumen/{kategoriDokumen}', [A06KategoriDokumenController::class, 'update']);
    });

    Route::middleware('throttle:delete')->group(function () {
        Route::delete('kategori-dokumen/{kategoriDokumen}', [A06KategoriDokumenController::class, 'destroy'])
            ->name('kategori-dokumen.destroy');
    });

    // Jenis Dokumen
    Route::resource('jenis-dokumen', A07JenisDokumenController::class)
        ->middleware('throttle:read')
        ->except(['store', 'update', 'destroy']);

    Route::middleware('throttle:create')->group(function () {
        Route::post('jenis-dokumen', [A07JenisDokumenController::class, 'store'])
            ->name('jenis-dokumen.store');
    });

    Route::middleware('throttle:update')->group(function () {
        Route::put('jenis-dokumen/{jenisDokumen}', [A07JenisDokumenController::class, 'update'])
            ->name('jenis-dokumen.update');
        Route::patch('jenis-dokumen/{jenisDokumen}', [A07JenisDokumenController::class, 'update']);
    });

    Route::middleware('throttle:delete')->group(function () {
        Route::delete('jenis-dokumen/{jenisDokumen}', [A07JenisDokumenController::class, 'destroy'])
            ->name('jenis-dokumen.destroy');
    });

    // Jabatan
    Route::resource('jabatan', A08JabatanController::class)
        ->middleware('throttle:read')
        ->except(['store', 'update', 'destroy']);

    Route::middleware('throttle:create')->group(function () {
        Route::post('jabatan', [A08JabatanController::class, 'store'])->name('jabatan.store');
    });

    Route::middleware('throttle:update')->group(function () {
        Route::put('jabatan/{jabatan}', [A08JabatanController::class, 'update'])->name('jabatan.update');
        Route::patch('jabatan/{jabatan}', [A08JabatanController::class, 'update']);
    });

    Route::middleware('throttle:delete')->group(function () {
        Route::delete('jabatan/{jabatan}', [A08JabatanController::class, 'destroy'])->name('jabatan.destroy');
    });

    // Departemen
    Route::resource('departemen', A09DepartemenController::class)
        ->middleware('throttle:read')
        ->except(['store', 'update', 'destroy']);

    Route::middleware('throttle:create')->group(function () {
        Route::post('departemen', [A09DepartemenController::class, 'store'])->name('departemen.store');
    });

    Route::middleware('throttle:update')->group(function () {
        Route::put('departemen/{departemen}', [A09DepartemenController::class, 'update'])->name('departemen.update');
        Route::patch('departemen/{departemen}', [A09DepartemenController::class, 'update']);
    });

    Route::middleware('throttle:delete')->group(function () {
        Route::delete('departemen/{departemen}', [A09DepartemenController::class, 'destroy'])->name('departemen.destroy');
    });

    // Wilayah Kerja
    Route::resource('wilayah-kerja', A10WilayahKerjaController::class)
        ->middleware('throttle:read')
        ->except(['store', 'update', 'destroy']);

    Route::middleware('throttle:create')->group(function () {
        Route::post('wilayah-kerja', [A10WilayahKerjaController::class, 'store'])->name('wilayah-kerja.store');
    });

    Route::middleware('throttle:update')->group(function () {
        Route::put('wilayah-kerja/{wilayahKerja}', [A10WilayahKerjaController::class, 'update'])->name('wilayah-kerja.update');
        Route::patch('wilayah-kerja/{wilayahKerja}', [A10WilayahKerjaController::class, 'update']);
    });

    Route::middleware('throttle:delete')->group(function () {
        Route::delete('wilayah-kerja/{wilayahKerja}', [A10WilayahKerjaController::class, 'destroy'])->name('wilayah-kerja.destroy');
    });

    // ============================================
    // DOCUMENT MANAGEMENT - HIGH SECURITY
    // ============================================

    // Formulir Dokumen
    Route::prefix('formulir-dokumen')->name('formulir-dokumen.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [FormulirDokumenController::class, 'index'])->name('index');
            Route::get('/create', [FormulirDokumenController::class, 'create'])->name('create');
            Route::get('/{formulirDokumen}', [FormulirDokumenController::class, 'show'])->name('show');
            Route::get('/{formulirDokumen}/edit', [FormulirDokumenController::class, 'edit'])->name('edit');
            Route::get('/{id}/view-document', [FormulirDokumenController::class, 'viewDocument'])->name('viewDocument');
        });

        Route::middleware('throttle:download')->group(function () {
            Route::get('/{id}/download', [FormulirDokumenController::class, 'download'])->name('download');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [FormulirDokumenController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{formulirDokumen}', [FormulirDokumenController::class, 'update'])->name('update');
            Route::patch('/{formulirDokumen}', [FormulirDokumenController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{formulirDokumen}', [FormulirDokumenController::class, 'destroy'])->name('destroy');
        });
    });

    // API untuk jenis dokumen
    Route::middleware('throttle:api')->group(function () {
        Route::get('api/jenis-dokumen/{kategoriId}', [FormulirDokumenController::class, 'getJenisByKategori']);
    });

    // Dokumen Karyawan - Confidential
    Route::prefix('dokumen-karyawan')->name('dokumen-karyawan.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [DokumenKaryawanController::class, 'index'])->name('index');
            Route::get('/create', [DokumenKaryawanController::class, 'create'])->name('create');
            Route::get('/{dokumenKaryawan}', [DokumenKaryawanController::class, 'show'])->name('show');
            Route::get('/{dokumenKaryawan}/edit', [DokumenKaryawanController::class, 'edit'])->name('edit');
            Route::get('/{dokumenKaryawan}/view', [DokumenKaryawanController::class, 'viewDocument'])->name('view');
        });

        Route::middleware('throttle:api')->group(function () {
            Route::get('/get-jenis-by-kategori/{kategoriId}', [DokumenKaryawanController::class, 'getJenisByKategori'])
                ->name('get-jenis-by-kategori');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [DokumenKaryawanController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{dokumenKaryawan}', [DokumenKaryawanController::class, 'update'])->name('update');
            Route::patch('/{dokumenKaryawan}', [DokumenKaryawanController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{dokumenKaryawan}', [DokumenKaryawanController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokumenKaryawanController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // Alias route
    Route::get('dokumen-karyawan/{dokumenKaryawan}/viewdocumentkaryawan', [DokumenKaryawanController::class, 'viewDocument'])
        ->middleware('throttle:read')
        ->name('viewdocumentkaryawan');

    // Dokumen Kontrak - Legal Documents
    Route::prefix('dokumen-kontrak')->name('dokumen-kontrak.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [DokumenKontrakController::class, 'index'])->name('index');
            Route::get('/create', [DokumenKontrakController::class, 'create'])->name('create');
            Route::get('/{dokumenKontrak}', [DokumenKontrakController::class, 'show'])->name('show');
            Route::get('/{dokumenKontrak}/edit', [DokumenKontrakController::class, 'edit'])->name('edit');
            Route::get('/{dokumenKontrak}/view-document', [DokumenKontrakController::class, 'viewDocument'])->name('viewDocument');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [DokumenKontrakController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{dokumenKontrak}', [DokumenKontrakController::class, 'update'])->name('update');
            Route::patch('/{dokumenKontrak}', [DokumenKontrakController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{dokumenKontrak}', [DokumenKontrakController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokumenKontrakController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // Dokumen Karir
    Route::prefix('dokumen-karir')->name('dokumen-karir.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [DokumenKarirController::class, 'index'])->name('index');
            Route::get('/create', [DokumenKarirController::class, 'create'])->name('create');
            Route::get('/{dokumenKarir}', [DokumenKarirController::class, 'show'])->name('show');
            Route::get('/{dokumenKarir}/edit', [DokumenKarirController::class, 'edit'])->name('edit');
            Route::get('/{dokumenKarir}/view-document', [DokumenKarirController::class, 'viewDocument'])->name('viewDocument');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [DokumenKarirController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{dokumenKarir}', [DokumenKarirController::class, 'update'])->name('update');
            Route::patch('/{dokumenKarir}', [DokumenKarirController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{dokumenKarir}', [DokumenKarirController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokumenKarirController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // Dokumen Legalitas
    Route::prefix('dokumen-legalitas')->name('dokumen-legalitas.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [DokumenLegalitasController::class, 'index'])->name('index');
            Route::get('/create', [DokumenLegalitasController::class, 'create'])->name('create');
            Route::get('/{dokumenLegalitas}', [DokumenLegalitasController::class, 'show'])->name('show');
            Route::get('/{dokumenLegalitas}/edit', [DokumenLegalitasController::class, 'edit'])->name('edit');
            Route::get('/{dokumenLegalitas}/view-document', [DokumenLegalitasController::class, 'viewDocument'])->name('viewDocument');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [DokumenLegalitasController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{dokumenLegalitas}', [DokumenLegalitasController::class, 'update'])->name('update');
            Route::patch('/{dokumenLegalitas}', [DokumenLegalitasController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{dokumenLegalitas}', [DokumenLegalitasController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokumenLegalitasController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // Dokumen BPJS Kesehatan - Sensitive Health Data
    Route::prefix('dokumen-bpjs-kesehatan')->name('dokumen-bpjs-kesehatan.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [DokumenBpjsKesehatanController::class, 'index'])->name('index');
            Route::get('/create', [DokumenBpjsKesehatanController::class, 'create'])->name('create');
            Route::get('/{dokumenBpjsKesehatan}', [DokumenBpjsKesehatanController::class, 'show'])->name('show');
            Route::get('/{dokumenBpjsKesehatan}/edit', [DokumenBpjsKesehatanController::class, 'edit'])->name('edit');
            Route::get('/{dokumenBpjsKesehatan}/view-document', [DokumenBpjsKesehatanController::class, 'viewDocument'])->name('viewDocument');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [DokumenBpjsKesehatanController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{dokumenBpjsKesehatan}', [DokumenBpjsKesehatanController::class, 'update'])->name('update');
            Route::patch('/{dokumenBpjsKesehatan}', [DokumenBpjsKesehatanController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{dokumenBpjsKesehatan}', [DokumenBpjsKesehatanController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokumenBpjsKesehatanController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // Dokumen BPJS Tenaga Kerja
    Route::prefix('dokumen-bpjs-tenaga-kerja')->name('dokumen-bpjs-tenaga-kerja.')->group(function () {

        Route::middleware('throttle:read')->group(function () {
            Route::get('/', [DokumenBpjsTenagaKerjaController::class, 'index'])->name('index');
            Route::get('/create', [DokumenBpjsTenagaKerjaController::class, 'create'])->name('create');
            Route::get('/{dokumenBpjsTenagaKerja}', [DokumenBpjsTenagaKerjaController::class, 'show'])->name('show');
            Route::get('/{dokumenBpjsTenagaKerja}/edit', [DokumenBpjsTenagaKerjaController::class, 'edit'])->name('edit');
            Route::get('/{dokumenBpjsTenagaKerja}/view-document', [DokumenBpjsTenagaKerjaController::class, 'viewDocument'])->name('viewDocument');
        });

        Route::middleware('throttle:create')->group(function () {
            Route::post('/', [DokumenBpjsTenagaKerjaController::class, 'store'])->name('store');
        });

        Route::middleware('throttle:update')->group(function () {
            Route::put('/{dokumenBpjsTenagaKerja}', [DokumenBpjsTenagaKerjaController::class, 'update'])->name('update');
            Route::patch('/{dokumenBpjsTenagaKerja}', [DokumenBpjsTenagaKerjaController::class, 'update']);
        });

        Route::middleware('throttle:delete')->group(function () {
            Route::delete('/{dokumenBpjsTenagaKerja}', [DokumenBpjsTenagaKerjaController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('throttle:export')->group(function () {
            Route::post('/export-excel', [DokumenBpjsTenagaKerjaController::class, 'exportExcel'])->name('export-excel');
        });
    });

    // ============================================
    // PROFILE & SETTINGS - Personal Data
    // ============================================
    Route::middleware('throttle:profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/update-password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.update-password');
    });
});