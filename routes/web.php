<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DokumenKarirController;
use App\Http\Controllers\DokumenKaryawanController;
use App\Http\Controllers\DokumenKontrakController;
use App\Http\Controllers\DokumenLegalitasController;
use App\Http\Controllers\JenisDokumenController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriDokumenController;
use App\Http\Controllers\KeluargaKaryawanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\UserController;
use App\Models\DokumenKarir;
use App\Models\DokumenKaryawan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // User Route
    Route::resource('users', UserController::class);
    Route::get('user-access/{user}', [UserAccessController::class, 'show'])->name('user-access.show');
    Route::get('user-access/{user}/edit', [UserAccessController::class, 'edit'])->name('user-access.edit');
    Route::put('user-access/{user}', [UserAccessController::class, 'update'])->name('user-access.update');

    // Perusahaan Route
    Route::resource('perusahaan', PerusahaanController::class);

    //Karyawan Route
    Route::resource('karyawan', KaryawanController::class);
    Route::get('karyawan/{id}/view-document', [KaryawanController::class, 'viewDocument'])->name('karyawan.view-document');

    //Keluarga Karyawan Route
    Route::resource('keluarga-karyawan', KeluargaKaryawanController::class);
    Route::get('keluarga-by-karyawan/{karyawanId}', [KeluargaKaryawanController::class, 'getByKaryawan'])->name('keluarga.by.karyawan');
    Route::get('karyawan-detail/{id}', [KeluargaKaryawanController::class, 'getKaryawanDetail'])->name('karyawan.detail');

    //Kategori Dokumen Route
    Route::resource('kategori-dokumen', KategoriDokumenController::class);

    //Jenis Dokumen Route
    Route::resource('jenis-dokumen', JenisDokumenController::class);

    //Dokumen Karyawan Route
    Route::resource('dokumen-karyawan', DokumenKaryawanController::class);
    Route::get('/dokumen-karyawan/get-jenis-by-kategori/{kategoriId}', [DokumenKaryawanController::class, 'getJenisByKategori'])->name('dokumen-karyawan.get-jenis-by-kategori');
    Route::get('dokumen-karyawan/{dokumenKaryawan}/view-document', [DokumenKaryawanController::class, 'viewDocument'])->name('dokumen-karyawan.viewDocument');
    Route::post('/dokumen-karyawan/export-excel', [DokumenKaryawanController::class, 'exportExcel'])->name('dokumen-karyawan.export-excel');

    //Dokumen Kontrak Route
    Route::resource('dokumen-kontrak', DokumenKontrakController::class);
    Route::post('/dokumen-kontrak/export-excel', [DokumenKontrakController::class, 'exportExcel'])->name('dokumen-kontrak.export-excel');
    Route::get('dokumen-kontrak/{dokumenKontrak}/view-document', [DokumenKontrakController::class, 'viewDocument'])->name('dokumen-kontrak.viewDocument');

    //Dokumen Karir Route
    Route::resource('dokumen-karir', DokumenKarirController::class);
    Route::post('/dokumen-karir/export-excel', [DokumenKarirController::class, 'exportExcel'])->name('dokumen-karir.export-excel');
    Route::get('dokumen-karir/{dokummenKarir}/view-document', [DokumenKarirController::class, 'viewDocument'])->name('dokumen-karir.viewDocument');


    //Dokumen Legalitas Route
    Route::resource('dokumen-legalitas', DokumenLegalitasController::class);
    Route::get('dokumen-legalitas/{dokumenLegalitas}/view-document', [DokumenLegalitasController::class, 'viewDocument'])->name('dokumen-legalitas.viewDocument');
    Route::post('/dokumen-legalitas/export-excel', [DokumenLegalitasController::class, 'exportExcel'])->name('dokumen-legalitas.export-excel');
});
