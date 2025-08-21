<?php

namespace App\Http\Controllers;

use App\Models\DokumenKarir;
use App\Models\Karyawan;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\WilayahKerja;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DokumenKarirController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen-karir')->only('index', 'show');
        $this->middleware('check.access:dokumen-karir,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen-karir,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen-karir,hapus')->only('destroy');
        $this->middleware('check.access:dokumen-karir,download')->only('download');
    }

    public function index()
    {
        // Join with JenisDokumen to get the GolDok value
        $dokumenKarir = DB::table('B03DokKarir')
            ->select(
                'B03DokKarir.*',
                'A04DmKaryawan.NamaKry',
                'A08DmJabatan.Jabatan',
                'A09DmDepartemen.Departemen',
                'A10DmWilayahKrj.WilayahKerja',
                'A07DmJenisDok.GolDok as GolDokValue' // Get GolDok for sorting
            )
            ->leftJoin('A04DmKaryawan', 'B03DokKarir.IdKodeA04', '=', 'A04DmKaryawan.IdKode')
            ->leftJoin('A08DmJabatan', 'B03DokKarir.IdKodeA08', '=', 'A08DmJabatan.IdKode')
            ->leftJoin('A09DmDepartemen', 'B03DokKarir.IdKodeA09', '=', 'A09DmDepartemen.IdKode')
            ->leftJoin('A10DmWilayahKrj', 'B03DokKarir.IdKodeA10', '=', 'A10DmWilayahKrj.IdKode')
            ->leftJoin('A07DmJenisDok', function($join) {
                $join->on('B03DokKarir.JenisDok', '=', 'A07DmJenisDok.JenisDok');
            })
            ->get();

        // Convert to collection for easier manipulation
        $dokumenKarir = collect($dokumenKarir)->map(function($item) {
            // Convert dates to Carbon objects
            if (!empty($item->TglTerbitDok)) {
                $item->TglTerbitDok = \Carbon\Carbon::parse($item->TglTerbitDok);
            }
            if (!empty($item->TglBerakhirDok)) {
                $item->TglBerakhirDok = \Carbon\Carbon::parse($item->TglBerakhirDok);
            }
            if (!empty($item->TglPengingat)) {
                $item->TglPengingat = \Carbon\Carbon::parse($item->TglPengingat);
            }

            // Ensure GolDok is numeric for sorting (default to 999 if null)
            $item->GolDok = is_null($item->GolDokValue) ? 999 : (int)$item->GolDokValue;

            return $item;
        });

        // Group by karyawan
        $dokumenByKaryawan = $dokumenKarir->groupBy('IdKodeA04');

        // Sort karyawan by name
        $karyawanNames = [];
        foreach ($dokumenByKaryawan as $karyawanId => $documents) {
            if (!empty($documents->first()->NamaKry)) {
                $karyawanNames[$karyawanId] = $documents->first()->NamaKry;
            } else {
                $karyawanNames[$karyawanId] = 'Unknown';
            }
        }
        asort($karyawanNames);

        // Combine documents with the order: karyawan, then GolDok
        $sortedDokumen = collect();
        foreach ($karyawanNames as $karyawanId => $name) {
            // Sort documents numerically by GolDok
            $docs = $dokumenByKaryawan[$karyawanId]->sortBy(function($doc) {
                return (int)$doc->GolDok; // Ensure numeric sorting
            });
            $sortedDokumen = $sortedDokumen->concat($docs);
        }

        // Convert back to model objects
        $dokumenKarir = $sortedDokumen->map(function($item) {
            $dokumen = new DokumenKarir();
            foreach ((array)$item as $key => $value) {
                $dokumen->$key = $value;
            }

            // Add related objects
            $karyawan = new Karyawan();
            $karyawan->NamaKry = $item->NamaKry;
            $karyawan->IdKode = $item->IdKodeA04;
            $dokumen->karyawan = $karyawan;

            $jabatan = new Jabatan();
            $jabatan->NamaJabatan = $item->Jabatan;
            $dokumen->jabatan = $jabatan;

            $departemen = new Departemen();
            $departemen->NamaDept = $item->Departemen;
            $dokumen->departemen = $departemen;

            $wilker = new WilayahKerja();
            $wilker->NamaWilker = $item->WilayahKerja;
            $dokumen->wilker = $wilker;

            $kategori = new KategoriDokumen();
            $kategori->KategoriDok = $item->KategoriDok;
            $dokumen->kategori = $kategori;

            // Add is_expired attribute
            $dokumen->is_expired = false;
            if ($dokumen->TglBerakhirDok && $dokumen->StatusDok === 'Berlaku') {
                $dokumen->is_expired = \Carbon\Carbon::parse($dokumen->TglBerakhirDok)->isPast();
            }

            return $dokumen;
        });

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
                $access = $user->userAccess()->where('MenuAcs', 'dokumen-karir')->first();
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

        return view('dokumen-karir.index', compact('dokumenKarir', 'userPermissions'));
    }

    public function create()
    {
        $karyawan = Karyawan::all();

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

        // Ambil jabatan dan urutkan berdasarkan GolDok terendah
        $jabatan = Jabatan::orderBy('GolonganJbt', 'asc')->get();

        // Ambil departemen dan urutkan berdasarkan GolDok terendah
        $departemen = Departemen::orderBy('GolonganDep', 'asc')->get();

        // Ambil wilayah kerja dan urutkan berdasarkan GolDok terendah
        $wilker = WilayahKerja::orderBy('GolonganWilker', 'asc')->get();

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
        $newId = $this->generateId('B03', 'B03DokKarir');

        return view('dokumen-karir.create', compact('karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId', 'jabatan', 'departemen', 'wilker'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B03DokKarir,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'IdKodeA08' => 'required|exists:A08DmJabatan,IdKode',
            'IdKodeA09' => 'required|exists:A09DmDepartemen,IdKode',
            'IdKodeA10' => 'required|exists:A10DmWilayahKrj,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => $request->ValidasiDok == 'Perpanjangan' ? 'required|date|after:TglTerbitDok' : 'nullable|date',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA08.required' => 'Jabatan harus dipilih',
            'IdKodeA09.required' => 'Departemen harus dipilih',
            'IdKodeA10.required' => 'Wilayah Kerja harus dipilih',
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'TglBerakhirDok.required' => 'Tanggal Berakhir harus diisi untuk dokumen dengan validasi Perpanjangan',
            'TglBerakhirDok.after' => 'Tanggal Berakhir harus setelah Tanggal Terbit',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('B03', 'B03DokKarir');
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
            'IdKodeA08' => $request->IdKodeA08,
            'IdKodeA09' => $request->IdKodeA09,
            'IdKodeA10' => $request->IdKodeA10,
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
            $file->storeAs('documents/karir', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenKarir::create($data);

        return redirect()->route('dokumen-karir.index')
            ->with('success', 'Dokumen Karir berhasil dibuat.');
    }

    public function show(DokumenKarir $dokumenKarir)
    {
        $dokumenKarir->load(['karyawan', 'jabatan', 'departemen', 'wilker', 'kategori']);
        return view('dokumen-karir.show', compact('dokumenKarir'));
    }

    public function edit(DokumenKarir $dokumenKarir)
    {
        $karyawan = Karyawan::all();

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

        // Ambil jabatan dan urutkan berdasarkan GolDok terendah
        $jabatan = Jabatan::orderBy('GolonganJbt', 'asc')->get();

        // Ambil departemen dan urutkan berdasarkan GolDok terendah
        $departemen = Departemen::orderBy('GolonganDep', 'asc')->get();

        // Ambil wilayah kerja dan urutkan berdasarkan GolDok terendah
        $wilker = WilayahKerja::orderBy('GolonganWilker', 'asc')->get();

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

        return view('dokumen-karir.edit', compact('dokumenKarir', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'jabatan', 'departemen', 'wilker'));
    }

    public function update(Request $request, DokumenKarir $dokumenKarir)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B03DokKarir,NoRegDok,' . $dokumenKarir->Id . ',Id',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'IdKodeA08' => 'required|exists:A08DmJabatan,IdKode',
            'IdKodeA09' => 'required|exists:A09DmDepartemen,IdKode',
            'IdKodeA10' => 'required|exists:A10DmWilayahKrj,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => $request->ValidasiDok == 'Perpanjangan' ? 'required|date|after:TglTerbitDok' : 'nullable|date',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA08.required' => 'Jabatan harus dipilih',
            'IdKodeA09.required' => 'Departemen harus dipilih',
            'IdKodeA10.required' => 'Wilayah Kerja harus dipilih',
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'TglBerakhirDok.required' => 'Tanggal Berakhir harus diisi untuk dokumen dengan validasi Perpanjangan',
            'TglBerakhirDok.after' => 'Tanggal Berakhir harus setelah Tanggal Terbit',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
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

        $data = [
            'IdKodeA04' => $request->IdKodeA04,
            'IdKodeA08' => $request->IdKodeA08,
            'IdKodeA09' => $request->IdKodeA09,
            'IdKodeA10' => $request->IdKodeA10,
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
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        if ($request->hasFile('FileDok')) {
            // Delete old file if exists
            if ($dokumenKarir->FileDok) {
                Storage::disk('public')->delete('documents/karir/' . $dokumenKarir->FileDok);
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
            $file->storeAs('documents/karir', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        $dokumenKarir->update($data);

        return redirect()->route('dokumen-karir.index')
            ->with('success', 'Dokumen Karir berhasil diperbarui.');
    }

    public function destroy(DokumenKarir $dokumenKarir)
    {
        // Delete file if exists
        if ($dokumenKarir->FileDok) {
            Storage::disk('public')->delete('documents/karir/' . $dokumenKarir->FileDok);
        }

        $dokumenKarir->delete();

        return redirect()->route('dokumen-karir.index')
            ->with('success', 'Dokumen Karir berhasil dihapus.');
    }

    public function download(DokumenKarir $dokumenKarir)
    {
        if (!$dokumenKarir->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = storage_path('app/public/documents/karir/' . $dokumenKarir->FileDok);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File already has the formatted name
        return response()->download($path, $dokumenKarir->FileDok);
    }

    public function viewDocument(DokumenKarir $dokumenKarir)
    {
        // Ensure file exists
        if (!$dokumenKarir->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path
        $filePath = storage_path('app/public/documents/karir/' . $dokumenKarir->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (in case path in database is different)
            $alternativePath = storage_path('app/public/documents/' . $dokumenKarir->FileDok);

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
                'Content-Disposition' => 'inline; filename="' . $dokumenKarir->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return response()->download($filePath, $dokumenKarir->FileDok);
    }

    public function getJenisByKategori($kategoriId)
    {
        $jenisDokumen = JenisDokumen::where('IdKodeA06', $kategoriId)
            ->select('id', 'IdKode', 'JenisDok', 'GolDok')
            ->orderBy('GolDok', 'asc') // Sort by GolDok
            ->get();

        return response()->json($jenisDokumen);
    }

    public function searchKaryawan(Request $request)
    {
        $term = $request->get('q');

        $karyawan = Karyawan::where('NamaKry', 'LIKE', '%' . $term . '%')
            ->orWhere('NIP', 'LIKE', '%' . $term . '%')
            ->select('IdKode', 'NamaKry', 'NIP')
            ->limit(10)
            ->get();

        return response()->json($karyawan);
    }

    public function getKaryawanDetails($id)
    {
        $karyawan = Karyawan::with(['jabatan', 'departemen', 'wilker'])
            ->where('IdKode', $id)
            ->first();

        if ($karyawan) {
            return response()->json([
                'success' => true,
                'data' => [
                    'jabatan' => $karyawan->jabatan ? $karyawan->jabatan->IdKode : null,
                    'departemen' => $karyawan->departemen ? $karyawan->departemen->IdKode : null,
                    'wilker' => $karyawan->wilker ? $karyawan->wilker->IdKode : null,
                ]
            ]);
        }

        return response()->json(['success' => false]);
    }
}