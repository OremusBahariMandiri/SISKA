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
        $dokumenKarir = DokumenKarir::with(['karyawan', 'jabatan', 'departemen', 'wilker','kategori'])->get();

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
        $kategoriDokumen = KategoriDokumen::all();
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();
        $jabatan = Jabatan::all();
        $departemen = Departemen::all();
        $wilker = WilayahKerja::all();

        // Buat array yang dikelompokkan berdasarkan kategori untuk JavaScript
        $jenisDokumenByKategori = [];
        foreach ($jenisDokumen as $jenis) {
            $kategoriId = $jenis->IdKodeA06;
            if (!isset($jenisDokumenByKategori[$kategoriId])) {
                $jenisDokumenByKategori[$kategoriId] = [];
            }
            $jenisDokumenByKategori[$kategoriId][] = [
                'id' => $jenis->id,
                'IdKode' => $jenis->IdKode,
                'JenisDok' => $jenis->JenisDok
            ];
        }

        // Generate ID otomatis
        $newId = $this->generateId('B03', 'B03DokKarir');

        return view('dokumen-karir.create', compact('karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId', 'jabatan', 'departemen', 'wilker'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NoRegDok' => 'required|unique:B03DokKarir,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'IdKodeA08' => 'required|exists:A08DmJabatan,IdKode',
            'IdKodeA09' => 'required|exists:A09DmDepartemen,IdKode',
            'IdKodeA10' => 'required|exists:A10DmWilayahKrj,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

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
        $dokumenKarir->load(['karyawan', 'jabatan', 'departemen', 'wilker','kategori']);
        return view('dokumen-karir.show', compact('dokumenKarir'));
    }

    public function edit(DokumenKarir $dokumenKarir)
    {
        $karyawan = Karyawan::all();
        $kategoriDokumen = KategoriDokumen::all();
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();
        $jabatan = Jabatan::all();
        $departemen = Departemen::all();
        $wilker = WilayahKerja::all();

        // Buat array yang dikelompokkan berdasarkan kategori untuk JavaScript
        $jenisDokumenByKategori = [];
        foreach ($jenisDokumen as $jenis) {
            $kategoriId = $jenis->IdKodeA06;
            if (!isset($jenisDokumenByKategori[$kategoriId])) {
                $jenisDokumenByKategori[$kategoriId] = [];
            }
            $jenisDokumenByKategori[$kategoriId][] = [
                'id' => $jenis->id,
                'IdKode' => $jenis->IdKode,
                'JenisDok' => $jenis->JenisDok
            ];
        }

        return view('dokumen-karir.edit', compact('dokumenKarir', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'jabatan', 'departemen', 'wilker'));
    }

    public function update(Request $request, DokumenKarir $dokumenKarir)
    {
        $request->validate([
            'NoRegDok' => 'required|unique:B03DokKarir,NoRegDok,' . $dokumenKarir->Id . ',Id',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'IdKodeA08' => 'required|exists:A08DmJabatan,IdKode',
            'IdKodeA09' => 'required|exists:A09DmDepartemen,IdKode',
            'IdKodeA10' => 'required|exists:A10DmWilayahKrj,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

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
        return redirect()->route('dokumen-karir.download', $dokumenKarir);
    }

    public function getJenisByKategori($kategoriId)
    {
        $jenisDokumen = JenisDokumen::where('IdKodeA06', $kategoriId)
            ->select('id', 'IdKode', 'JenisDok')
            ->orderBy('JenisDok', 'asc')
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