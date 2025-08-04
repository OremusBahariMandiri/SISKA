<?php

namespace App\Http\Controllers;

use App\Models\DokumenKaryawan;
use App\Models\Karyawan;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class DokumenKaryawanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen_karyawan')->only('index', 'show');
        $this->middleware('check.access:dokumen_karyawan,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen_karyawan,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen_karyawan,hapus')->only('destroy');
        $this->middleware('check.access:dokumen_karyawan,download')->only('download');
        $this->middleware('check.access:dokumen_karyawan,monitoring')->only('monitoring');
    }

    public function index()
    {
        $dokumenKaryawan = DokumenKaryawan::with(['karyawan'])->get();
        return view('dokumen-karyawan.index', compact('dokumenKaryawan'));
    }

    public function create()
    {
        $karyawan = Karyawan::all();
        $kategoriDokumen = KategoriDokumen::all();
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();

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
        $newId = $this->generateId('B01', 'B01DmDokKaryawan');

        return view('dokumen-karyawan.create', compact('karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NoRegDok' => 'required|unique:B01DmDokKaryawan,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('B01', 'B01DmDokKaryawan');
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
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('documents', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenKaryawan::create($data);

        return redirect()->route('dokumen-karyawan.index')
            ->with('success', 'Dokumen Karyawan berhasil dibuat.');
    }

    public function show(DokumenKaryawan $dokumenKaryawan)
    {
        $dokumenKaryawan->load(['karyawan']);
        return view('dokumen-karyawan.show', compact('dokumenKaryawan'));
    }

    public function edit(DokumenKaryawan $dokumenKaryawan)
    {
        $karyawan = Karyawan::all();
        $kategoriDokumen = KategoriDokumen::all();
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();

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

        return view('dokumen-karyawan.edit', compact('dokumenKaryawan', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori'));
    }


    public function update(Request $request, DokumenKaryawan $dokumenKaryawan)
    {
        $request->validate([
            'NoRegDok' => 'required|unique:B01DmDokKaryawan,NoRegDok,' . $dokumenKaryawan->id,
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
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
            if ($dokumenKaryawan->FileDok) {
                Storage::disk('public')->delete('documents/' . $dokumenKaryawan->FileDok);
            }

            $file = $request->file('FileDok');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('documents', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        $dokumenKaryawan->update($data);

        return redirect()->route('dokumen-karyawan.index')
            ->with('success', 'Dokumen Karyawan berhasil diperbarui.');
    }

    public function destroy(DokumenKaryawan $dokumenKaryawan)
    {
        // Delete file if exists
        if ($dokumenKaryawan->FileDok) {
            Storage::disk('public')->delete('documents/' . $dokumenKaryawan->FileDok);
        }

        $dokumenKaryawan->delete();

        return redirect()->route('dokumen-karyawan.index')
            ->with('success', 'Dokumen Karyawan berhasil dihapus.');
    }

    public function download(DokumenKaryawan $dokumenKaryawan)
    {
        if (!$dokumenKaryawan->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = storage_path('app/public/documents/' . $dokumenKaryawan->FileDok);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Get extension from original file
        $originalExtension = pathinfo($dokumenKaryawan->FileDok, PATHINFO_EXTENSION);

        // Format date for filename
        $tanggalTerbit = $dokumenKaryawan->TglTerbitDok ? Carbon::parse($dokumenKaryawan->TglTerbitDok)->format('Ymd') : date('Ymd');

        // Clean string from invalid characters for filename
        $sanitizeFileName = function ($string) {
            // Replace all invalid characters with dash
            $cleaned = preg_replace('/[\/\\\:*?"<>|]/', '-', $string);
            // Remove excessive dashes
            $cleaned = preg_replace('/-+/', '-', $cleaned);
            // Trim dashes at beginning and end
            return trim($cleaned, '-');
        };

        // Clean filename components
        $cleanNoReg = $sanitizeFileName($dokumenKaryawan->NoRegDok);
        $cleanJenisDok = $sanitizeFileName($dokumenKaryawan->JenisDok);
        $cleanNamaKaryawan = $dokumenKaryawan->karyawan ? $sanitizeFileName($dokumenKaryawan->karyawan->NamaKaryawan) : 'karyawan';

        // Format filename: NoReg_JenisDok_NamaKaryawan_TanggalTerbit
        $newFileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $cleanNamaKaryawan . '_' . $tanggalTerbit . '.' . $originalExtension;

        return response()->download($path, $newFileName);
    }

    public function monitoring()
    {
        $dokumen = DokumenKaryawan::with(['karyawan'])
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

        return view('dokumen-karyawan.monitoring', compact('expired30', 'expired60', 'expired90', 'expired'));
    }

    /**
     * Get document statistics for dashboard
     */
    public function getDocumentStats()
    {
        // Count expired documents based on TglPengingat or TglBerakhirDok
        $expiredCount = DokumenKaryawan::where(function ($query) {
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
        $warningCount = DokumenKaryawan::where(function ($query) {
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

    public function viewDocument(DokumenKaryawan $dokumenKaryawan)
    {
        // Ensure file exists
        if (!$dokumenKaryawan->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path
        $filePath = storage_path('app/public/documents/' . $dokumenKaryawan->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (in case path in database is different)
            $alternativePath = storage_path('app/public/documents/' . $dokumenKaryawan->FileDok);

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
                'Content-Disposition' => 'inline; filename="' . $dokumenKaryawan->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return redirect()->route('dokumen-karyawan.download', $dokumenKaryawan);
    }
    public function getJenisByKategori($kategoriId)
    {
        $jenisDokumen = JenisDokumen::where('IdKodeA06', $kategoriId)
            ->select('id', 'IdKode', 'JenisDok')
            ->orderBy('JenisDok', 'asc')
            ->get();

        return response()->json($jenisDokumen);
    }
}
