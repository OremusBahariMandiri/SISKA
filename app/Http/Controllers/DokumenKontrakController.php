<?php

namespace App\Http\Controllers;

use App\Models\DokumenKontrak;
use App\Models\Karyawan;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Models\Perusahaan;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DokumenKontrakController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen-kontrak')->only('index', 'show');
        $this->middleware('check.access:dokumen-kontrak,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen-kontrak,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen-kontrak,hapus')->only('destroy');
        $this->middleware('check.access:dokumen-kontrak,download')->only('download');
        $this->middleware('check.access:dokumen-kontrak,monitoring')->only('monitoring');
    }

    public function index()
    {
        // Get user permissions for this menu
        $userPermissions = [];
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->is_admin) {
                // Admin has all permissions
                $userPermissions = [
                    'tambah' => true,
                    'ubah' => true,
                    'hapus' => true,
                    'download' => true,
                    'detail' => true,
                    'monitoring' => true,
                ];
            } else {
                // Get specific permissions from user access
                $access = $user->userAccess()->where('MenuAcs', 'dokumen-kontrak')->first();
                if ($access) {
                    $userPermissions = [
                        'tambah' => (bool)$access->TambahAcs,
                        'ubah' => (bool)$access->UbahAcs,
                        'hapus' => (bool)$access->HapusAcs,
                        'download' => (bool)$access->DownloadAcs,
                        'detail' => (bool)$access->DetailAcs,
                        'monitoring' => (bool)$access->MonitoringAcs,
                    ];
                }
            }
        }

        // Ambil dokumen kontrak dengan join ke Jenis Dokumen untuk mendapatkan GolDok
        $dokumenKontrak = DB::table('B02DokKontrak')
            ->select(
                'B02DokKontrak.*',
                'A04DmKaryawan.NamaKry',
                'A04DmKaryawan.NrkKry',
                'A04DmKaryawan.TglMsk',
                'A03DmPerusahaan.NamaPrsh',
                'A07DmJenisDok.GolDok as GolDokValue' // Ganti nama untuk menghindari konflik
            )
            ->leftJoin('A04DmKaryawan', 'B02DokKontrak.IdKodeA04', '=', 'A04DmKaryawan.IdKode')
            ->leftJoin('A03DmPerusahaan', 'B02DokKontrak.NamaPrsh', '=', 'A03DmPerusahaan.IdKode')
            ->leftJoin('A07DmJenisDok', function($join) {
                $join->on('B02DokKontrak.JenisDok', '=', 'A07DmJenisDok.JenisDok');
            })
            ->get();

        // Konversi stdClass objects ke collections agar lebih mudah dimanipulasi
        $dokumenKontrak = collect($dokumenKontrak)->map(function($item) {
            // Konversi tanggal menjadi objek Carbon
            if (!empty($item->TglTerbitDok)) {
                $item->TglTerbitDok = \Carbon\Carbon::parse($item->TglTerbitDok);
            }
            if (!empty($item->TglBerakhirDok)) {
                $item->TglBerakhirDok = \Carbon\Carbon::parse($item->TglBerakhirDok);
            }
            if (!empty($item->TglPengingat)) {
                $item->TglPengingat = \Carbon\Carbon::parse($item->TglPengingat);
            }

            // Pastikan nilai GolDok adalah integer untuk pengurutan numerik yang benar
            $item->GolDok = is_null($item->GolDokValue) ? 999 : (int)$item->GolDokValue;

            return $item;
        });

        // Kelompokkan dokumen berdasarkan karyawan
        $dokumenByKaryawan = $dokumenKontrak->groupBy('IdKodeA04');

        // Urutkan karyawan berdasarkan nama
        $karyawanNames = [];
        foreach ($dokumenByKaryawan as $karyawanId => $documents) {
            if (!empty($documents->first()->NamaKry)) {
                $karyawanNames[$karyawanId] = $documents->first()->NamaKry;
            } else {
                $karyawanNames[$karyawanId] = 'Unknown';
            }
        }
        asort($karyawanNames);

        // Gabungkan dokumen dengan urutan: karyawan, lalu GolDok
        $sortedDokumen = collect();
        foreach ($karyawanNames as $karyawanId => $name) {
            // Urutkan dokumen dengan numeric sorting berdasarkan GolDok
            $docs = $dokumenByKaryawan[$karyawanId]->sortBy(function($doc) {
                return (int)$doc->GolDok; // Cast ke integer untuk pengurutan numerik
            });
            $sortedDokumen = $sortedDokumen->concat($docs);
        }

        // Ubah kembali ke model object agar relationships dapat diakses
        $dokumenKontrak = $sortedDokumen->map(function($item) {
            // Tambahkan properti-properti yang diperlukan untuk view
            $dokumen = new DokumenKontrak();
            foreach ((array)$item as $key => $value) {
                $dokumen->$key = $value;
            }

            // Add karyawan and kategori objects
            $karyawan = new Karyawan();
            $karyawan->NamaKry = $item->NamaKry;
            $karyawan->NrkKry = $item->NrkKry;
            $karyawan->TglMsk = $item->TglMsk;
            $karyawan->IdKode = $item->IdKodeA04;
            $dokumen->karyawan = $karyawan;

            // Add perusahaan object
            $perusahaan = new Perusahaan();
            $perusahaan->NamaPrsh = $item->NamaPrsh;
            $perusahaan->IdKode = $item->NamaPrsh;
            $dokumen->perusahaan = $perusahaan;

            $kategori = new KategoriDokumen();
            $kategori->KategoriDok = $item->KategoriDok;
            $dokumen->kategoriDok = $kategori;

            // Add is_expired attribute
            $dokumen->is_expired = false;
            if ($dokumen->TglBerakhirDok && $dokumen->StatusDok === 'Berlaku') {
                $dokumen->is_expired = \Carbon\Carbon::parse($dokumen->TglBerakhirDok)->isPast();
            }

            return $dokumen;
        });

        return view('dokumen-kontrak.index', compact('dokumenKontrak', 'userPermissions'));
    }

    public function create()
    {
        $karyawan = Karyawan::all();
        $perusahaan = Perusahaan::all();

        // Ambil kategori dokumen dan urutkan berdasarkan GolDok terendah
        $kategoriDokumen = KategoriDokumen::select('A06DmKategoriDok.*')
            ->leftJoin('A07DmJenisDok', 'A06DmKategoriDok.IdKode', '=', 'A07DmJenisDok.IdKodeA06')
            ->orderBy('A07DmJenisDok.GolDok', 'asc')
            ->distinct()
            ->get();

        // Ambil jenis dokumen dan urutkan berdasarkan GolDok
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')
            ->orderBy('GolDok', 'asc')
            ->get();

        // Buat array yang dikelompokkan berdasarkan kategori untuk JavaScript
        $jenisDokumenByKategori = [];
        foreach ($kategoriDokumen as $kategori) {
            $kategoriId = $kategori->IdKode;

            // Ambil jenis dokumen untuk kategori ini dan urutkan berdasarkan GolDok
            $jenisForKategori = JenisDokumen::where('IdKodeA06', $kategoriId)
                ->orderBy('GolDok', 'asc')
                ->get();

            if (!isset($jenisDokumenByKategori[$kategoriId])) {
                $jenisDokumenByKategori[$kategoriId] = [];
            }

            foreach ($jenisForKategori as $jenis) {
                $jenisDokumenByKategori[$kategoriId][] = [
                    'id' => $jenis->id,
                    'IdKode' => $jenis->IdKode,
                    'JenisDok' => $jenis->JenisDok,
                    'GolDok' => $jenis->GolDok
                ];
            }
        }

        // Generate ID otomatis
        $newId = $this->generateId('B02', 'B02DokKontrak');

        return view('dokumen-kontrak.create', compact('karyawan', 'perusahaan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B02DokKontrak,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'NamaPrsh' => 'required|exists:A03DmPerusahaan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => $request->ValidasiDok == 'Perpanjangan' ? 'required|date|after:TglTerbitDok' : 'nullable|date',
            'FileDok' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA04.exists' => 'Karyawan yang dipilih tidak valid',
            'NamaPrsh.required' => 'Perusahaan harus dipilih',
            'NamaPrsh.exists' => 'Perusahaan yang dipilih tidak valid',
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'TglTerbitDok.date' => 'Format Tanggal Terbit tidak valid',
            'TglBerakhirDok.required' => 'Tanggal Berakhir harus diisi untuk dokumen dengan validasi Perpanjangan',
            'TglBerakhirDok.date' => 'Format Tanggal Berakhir tidak valid',
            'TglBerakhirDok.after' => 'Tanggal Berakhir harus setelah Tanggal Terbit',
            'FileDok.required' => 'File Dokumen harus diunggah',
            'FileDok.file' => 'File Dokumen tidak valid',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
            'FileDok.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('B02', 'B02DokKontrak');
        } else {
            $IdKode = $request->IdKode;
        }

        // Calculate validity period automatically
        $masaBerlaku = 'Tetap';
        if ($request->ValidasiDok == 'Perpanjangan' && $request->filled('TglTerbitDok') && $request->filled('TglBerakhirDok')) {
            $tglTerbit = Carbon::parse($request->TglTerbitDok);
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);

            // Calculate difference in years, months, and days
            $years = $tglBerakhir->diffInYears($tglTerbit);
            $months = $tglBerakhir->copy()->subYears($years)->diffInMonths($tglTerbit);
            $days = $tglBerakhir->copy()->subYears($years)->subMonths($months)->diffInDays($tglTerbit);

            $masaBerlaku = '';
            if ($years > 0) $masaBerlaku .= $years . ' thn ';
            if ($months > 0) $masaBerlaku .= $months . ' bln ';
            if ($days > 0) $masaBerlaku .= $days . ' hri';
            $masaBerlaku = trim($masaBerlaku) ?: '0 hri';
        }

        // Calculate reminder period automatically
        $masaPengingat = '-';
        if ($request->filled('TglPengingat') && $request->filled('TglBerakhirDok')) {
            $tglPengingat = Carbon::parse($request->TglPengingat);
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);

            // Validate reminder date is not after expiry date
            if ($tglPengingat->lte($tglBerakhir)) {
                $days = $tglBerakhir->diffInDays($tglPengingat);
                $masaPengingat = $days . ' hari';
            } else {
                return redirect()->back()->withInput()->with('error', 'Tanggal pengingat harus sebelum tanggal berakhir');
            }
        }

        $data = [
            'IdKode' => $IdKode,
            'IdKodeA04' => $request->IdKodeA04,
            'NamaPrsh' => $request->NamaPrsh,
            'NoRegDok' => $request->NoRegDok,
            'KategoriDok' => $request->KategoriDok,
            'JenisDok' => $request->JenisDok,
            'KetDok' => $request->KetDok,
            'ValidasiDok' => $request->ValidasiDok,
            'TglTerbitDok' => $request->TglTerbitDok,
            'TglBerakhirDok' => $request->ValidasiDok == 'Tetap' ? null : $request->TglBerakhirDok,
            'MasaBerlaku' => $masaBerlaku,
            'TglPengingat' => $request->ValidasiDok == 'Tetap' ? null : $request->TglPengingat,
            'MasaPengingat' => $masaPengingat,
            'StatusDok' => $request->StatusDok,
            'created_by' => auth()->user()->IdKode ?? null,
        ];

        if ($request->hasFile('FileDok')) {
            $file = $request->file('FileDok');

            // Get file extension
            $extension = $file->getClientOriginalExtension();

            // Clean string from invalid characters for filename
            $sanitizeFileName = function ($string) {
                // Replace all invalid characters with dash
                $cleaned = preg_replace('/[\/\\\:*?"<>|]/', '-', $string);
                // Remove excessive dashes
                $cleaned = preg_replace('/-+/', '-', $cleaned);
                // Trim dashes at beginning and end
                return trim($cleaned, '-');
            };

            // Get karyawan name
            $karyawan = Karyawan::where('IdKode', $request->IdKodeA04)->first();
            $namaKaryawan = $karyawan ? $sanitizeFileName($karyawan->NamaKry) : 'unknown';

            // Format tanggal berakhir
            $tglBerakhir = $request->ValidasiDok == 'Tetap' ? 'Permanent' : Carbon::parse($request->TglBerakhirDok)->format('Ymd');

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $namaKaryawan . '_' . $tglBerakhir . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/kontrak', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenKontrak::create($data);

        return redirect()->route('dokumen-kontrak.index')
            ->with('success', 'Dokumen Kontrak berhasil dibuat.');
    }

    public function show(DokumenKontrak $dokumenKontrak)
    {
        $dokumenKontrak->load(['karyawan', 'kategoriDok', 'perusahaan']);
        return view('dokumen-kontrak.show', compact('dokumenKontrak'));
    }

    public function edit(DokumenKontrak $dokumenKontrak)
    {
        $karyawan = Karyawan::all();
        $perusahaan = Perusahaan::all();

        // Ambil kategori dokumen dan urutkan berdasarkan GolDok terendah
        $kategoriDokumen = KategoriDokumen::select('A06DmKategoriDok.*')
            ->leftJoin('A07DmJenisDok', 'A06DmKategoriDok.IdKode', '=', 'A07DmJenisDok.IdKodeA06')
            ->orderBy('A07DmJenisDok.GolDok', 'asc')
            ->distinct()
            ->get();

        // Ambil jenis dokumen dan urutkan berdasarkan GolDok
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')
            ->orderBy('GolDok', 'asc')
            ->get();

        // Buat array yang dikelompokkan berdasarkan kategori untuk JavaScript
        $jenisDokumenByKategori = [];
        foreach ($kategoriDokumen as $kategori) {
            $kategoriId = $kategori->IdKode;

            // Ambil jenis dokumen untuk kategori ini dan urutkan berdasarkan GolDok
            $jenisForKategori = JenisDokumen::where('IdKodeA06', $kategoriId)
                ->orderBy('GolDok', 'asc')
                ->get();

            if (!isset($jenisDokumenByKategori[$kategoriId])) {
                $jenisDokumenByKategori[$kategoriId] = [];
            }

            foreach ($jenisForKategori as $jenis) {
                $jenisDokumenByKategori[$kategoriId][] = [
                    'id' => $jenis->id,
                    'IdKode' => $jenis->IdKode,
                    'JenisDok' => $jenis->JenisDok,
                    'GolDok' => $jenis->GolDok
                ];
            }
        }

        return view('dokumen-kontrak.edit', compact('dokumenKontrak', 'perusahaan', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori'));
    }

    public function update(Request $request, DokumenKontrak $dokumenKontrak)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B02DokKontrak,NoRegDok,' . $dokumenKontrak->Id . ',Id',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'NamaPrsh' => 'required|exists:A03DmPerusahaan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => $request->ValidasiDok == 'Perpanjangan' ? 'required|date|after:TglTerbitDok' : 'nullable|date',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'KetDok' => 'nullable|string', // Add explicit validation for KetDok
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA04.exists' => 'Karyawan yang dipilih tidak valid',
            'NamaPrsh.required' => 'Perusahaan harus dipilih',
            'NamaPrsh.exists' => 'Perusahaan yang dipilih tidak valid',
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'TglTerbitDok.date' => 'Format Tanggal Terbit tidak valid',
            'TglBerakhirDok.required' => 'Tanggal Berakhir harus diisi untuk dokumen dengan validasi Perpanjangan',
            'TglBerakhirDok.date' => 'Format Tanggal Berakhir tidak valid',
            'TglBerakhirDok.after' => 'Tanggal Berakhir harus setelah Tanggal Terbit',
            'FileDok.file' => 'File Dokumen tidak valid',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
            'FileDok.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        // Calculate validity period automatically
        $masaBerlaku = 'Tetap';
        if ($request->ValidasiDok == 'Perpanjangan' && $request->filled('TglTerbitDok') && $request->filled('TglBerakhirDok')) {
            $tglTerbit = Carbon::parse($request->TglTerbitDok);
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);

            // Calculate difference in years, months, and days
            $years = $tglBerakhir->diffInYears($tglTerbit);
            $months = $tglBerakhir->copy()->subYears($years)->diffInMonths($tglTerbit);
            $days = $tglBerakhir->copy()->subYears($years)->subMonths($months)->diffInDays($tglTerbit);

            $masaBerlaku = '';
            if ($years > 0) $masaBerlaku .= $years . ' thn ';
            if ($months > 0) $masaBerlaku .= $months . ' bln ';
            if ($days > 0) $masaBerlaku .= $days . ' hri';
            $masaBerlaku = trim($masaBerlaku) ?: '0 hri';
        }

        // Calculate reminder period automatically
        $masaPengingat = '-';
        if ($request->filled('TglPengingat') && $request->filled('TglBerakhirDok')) {
            $tglPengingat = Carbon::parse($request->TglPengingat);
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);

            // Validate reminder date is not after expiry date
            if ($tglPengingat->lte($tglBerakhir)) {
                $days = $tglBerakhir->diffInDays($tglPengingat);
                $masaPengingat = $days . ' hari';
            } else {
                return redirect()->back()->withInput()->with('error', 'Tanggal pengingat harus sebelum tanggal berakhir');
            }
        }

        // Try to get KetDok from various possible sources
        $ketDokValue = null;

        // First try the direct input
        if ($request->has('KetDok')) {
            $ketDokValue = $request->input('KetDok');
        }
        // Then try the backup field if it exists
        elseif ($request->has('KetDokBackup')) {
            $ketDokValue = $request->input('KetDokBackup');
        }
        // Finally, if no value is found, maintain the current value
        else {
            $ketDokValue = $dokumenKontrak->KetDok;
        }

        $data = [
            'IdKodeA04' => $request->IdKodeA04,
            'NoRegDok' => $request->NoRegDok,
            'NamaPrsh' => $request->NamaPrsh,
            'KategoriDok' => $request->KategoriDok,
            'JenisDok' => $request->JenisDok,
            'KetDok' => $ketDokValue, // Use the determined value
            'ValidasiDok' => $request->ValidasiDok,
            'TglTerbitDok' => $request->TglTerbitDok,
            'TglBerakhirDok' => $request->ValidasiDok == 'Tetap' ? null : $request->TglBerakhirDok,
            'MasaBerlaku' => $masaBerlaku,
            'TglPengingat' => $request->ValidasiDok == 'Tetap' ? null : $request->TglPengingat,
            'MasaPengingat' => $masaPengingat,
            'StatusDok' => $request->StatusDok,
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        if ($request->hasFile('FileDok')) {
            // Delete old file if exists
            if ($dokumenKontrak->FileDok) {
                Storage::disk('public')->delete('documents/kontrak/' . $dokumenKontrak->FileDok);
                // Check alternative location if file not found in main location
                if (!Storage::disk('public')->exists('documents/kontrak/' . $dokumenKontrak->FileDok)) {
                    Storage::disk('public')->delete('contracts/' . $dokumenKontrak->FileDok);
                }
            }

            $file = $request->file('FileDok');

            // Get file extension
            $extension = $file->getClientOriginalExtension();

            // Clean string from invalid characters for filename
            $sanitizeFileName = function ($string) {
                // Replace all invalid characters with dash
                $cleaned = preg_replace('/[\/\\\:*?"<>|]/', '-', $string);
                // Remove excessive dashes
                $cleaned = preg_replace('/-+/', '-', $cleaned);
                // Trim dashes at beginning and end
                return trim($cleaned, '-');
            };

            // Get karyawan name
            $karyawan = Karyawan::where('IdKode', $request->IdKodeA04)->first();
            $namaKaryawan = $karyawan ? $sanitizeFileName($karyawan->NamaKry) : 'unknown';

            // Format tanggal berakhir
            $tglBerakhir = $request->ValidasiDok == 'Tetap' ? 'Permanent' : Carbon::parse($request->TglBerakhirDok)->format('Ymd');

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $namaKaryawan . '_' . $tglBerakhir . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/kontrak', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        $dokumenKontrak->update($data);

        return redirect()->route('dokumen-kontrak.index')
            ->with('success', 'Dokumen Kontrak berhasil diperbarui.');
    }

    public function destroy(DokumenKontrak $dokumenKontrak)
    {
        // Delete file if exists
        if ($dokumenKontrak->FileDok) {
            Storage::disk('public')->delete('documents/kontrak/' . $dokumenKontrak->FileDok);
            // Check alternative location if file not found in main location
            if (!Storage::disk('public')->exists('documents/kontrak/' . $dokumenKontrak->FileDok)) {
                Storage::disk('public')->delete('contracts/' . $dokumenKontrak->FileDok);
            }
        }

        $dokumenKontrak->delete();

        return redirect()->route('dokumen-kontrak.index')
            ->with('success', 'Dokumen Kontrak berhasil dihapus.');
    }

    public function download(DokumenKontrak $dokumenKontrak)
    {
        if (!$dokumenKontrak->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Check in the new location first
        $path = storage_path('app/public/documents/kontrak/' . $dokumenKontrak->FileDok);

        // If not found, try the old location
        if (!file_exists($path)) {
            $path = storage_path('app/public/contracts/' . $dokumenKontrak->FileDok);

            if (!file_exists($path)) {
                return back()->with('error', 'File tidak ditemukan.');
            }
        }

        // File already has the formatted name in new system
        return response()->download($path, $dokumenKontrak->FileDok);
    }

    public function monitoring()
    {
        $dokumen = DokumenKontrak::with(['karyawan'])
            ->whereNotNull('TglBerakhirDok')
            ->get();

        // Calculate documents that will expire in 30, 60, 90 days
        $today = now();
        $expired30 = $dokumen->filter(function ($item) use ($today) {
            return $item->TglBerakhirDok && $today->diffInDays($item->TglBerakhirDok, false) <= 30 && $today->diffInDays($item->TglBerakhirDok, false) >= 0;
        });

        $expired60 = $dokumen->filter(function ($item) use ($today) {
            return $item->TglBerakhirDok && $today->diffInDays($item->TglBerakhirDok, false) <= 60 && $today->diffInDays($item->TglBerakhirDok, false) > 30;
        });

        $expired90 = $dokumen->filter(function ($item) use ($today) {
            return $item->TglBerakhirDok && $today->diffInDays($item->TglBerakhirDok, false) <= 90 && $today->diffInDays($item->TglBerakhirDok, false) > 60;
        });

        $expired = $dokumen->filter(function ($item) use ($today) {
            return $item->TglBerakhirDok && $today->diffInDays($item->TglBerakhirDok, false) < 0;
        });

        return view('dokumen-kontrak.monitoring', compact('expired30', 'expired60', 'expired90', 'expired'));
    }

    /**
     * Get document statistics for dashboard
     */
    public function getDocumentStats()
    {
        // Count expired documents based on TglPengingat or TglBerakhirDok
        $expiredCount = DokumenKontrak::where(function ($query) {
            // Documents with reminder date that has passed or is today
            $query->whereNotNull('TglPengingat')
                ->where('TglPengingat', '<=', now());
        })->orWhere(function ($query) {
            // Or documents with expiry date that has passed
            $query->whereNotNull('TglBerakhirDok')
                ->where('TglBerakhirDok', '<', now())
                // And don't have a reminder date already covered in the previous condition
                ->where(function ($q) {
                    $q->whereNull('TglPengingat')
                        ->orWhere('TglPengingat', '>', now());
                });
        })->count();

        // Count documents that will expire soon (warning)
        $warningCount = DokumenKontrak::where(function ($query) {
            // Documents with reminder date in the next 30 days
            $query->whereNotNull('TglPengingat')
                ->where('TglPengingat', '>', now())
                ->where('TglPengingat', '<=', now()->addDays(30));
        })->orWhere(function ($query) {
            // Or documents with expiry date in the next 30 days
            $query->whereNotNull('TglBerakhirDok')
                ->where('TglBerakhirDok', '>=', now())
                ->where('TglBerakhirDok', '<=', now()->addDays(30))
                // And don't have a reminder date already covered in the previous condition
                ->where(function ($q) {
                    $q->whereNull('TglPengingat')
                        ->orWhere('TglPengingat', '>', now()->addDays(30));
                });
        })->count();

        return response()->json([
            'expired' => $expiredCount,
            'warning' => $warningCount
        ]);
    }

    public function viewDocument(DokumenKontrak $dokumenKontrak)
    {
        // Ensure file exists
        if (!$dokumenKontrak->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path - check in new location first
        $filePath = storage_path('app/public/documents/kontrak/' . $dokumenKontrak->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (old location)
            $alternativePath = storage_path('app/public/contracts/' . $dokumenKontrak->FileDok);

            if (file_exists($alternativePath)) {
                $filePath = $alternativePath;
            } else {
                return back()->with('error', 'File tidak ditemukan di sistem penyimpanan.');
            }
        }

        // Get file information
        $fileInfo = pathinfo($filePath);
        $extension = strtolower($fileInfo['extension']);

        // Determine content type based on file extension
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';

        // For files that can be displayed in browser (PDF, images)
        if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'])) {
            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $dokumenKontrak->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return redirect()->route('dokumen-kontrak.download', $dokumenKontrak);
    }

    public function getJenisByKategori($kategoriId)
    {
        $jenisDokumen = JenisDokumen::where('IdKodeA06', $kategoriId)
            ->select('id', 'IdKode', 'JenisDok', 'GolDok')
            ->orderBy('GolDok', 'asc')
            ->get();

        return response()->json($jenisDokumen);
    }
}
