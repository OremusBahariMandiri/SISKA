<?php

namespace App\Http\Controllers;

use App\Models\DokumenLegalitas;
use App\Models\Karyawan;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class DokumenLegalitasController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen_legalitas')->only('index', 'show');
        $this->middleware('check.access:dokumen_legalitas,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen_legalitas,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen_legalitas,hapus')->only('destroy');
        $this->middleware('check.access:dokumen_legalitas,download')->only('download');
        $this->middleware('check.access:dokumen_legalitas,monitoring')->only('monitoring');
    }

    public function index()
    {
        $dokumenLegalitas = DokumenLegalitas::with(['karyawan'])->get();
        return view('dokumen-legalitas.index', compact('dokumenLegalitas'));
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
        $newId = $this->generateId('B04', 'B04DokLegalitas');

        return view('dokumen-legalitas.create', compact('karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NoRegDok' => 'required|unique:B04DokLegalitas,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('B04', 'B04DokLegalitas');
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

            // Format tanggal berakhir
            $tglBerakhir = $request->ValidasiDok == 'Tetap' ? 'Permanent' : Carbon::parse($request->TglBerakhirDok)->format('Ymd');

            // Get the KategoriDok name from database using the ID provided in the form
            $kategoriDokumen = KategoriDokumen::where('IdKode', $request->KategoriDok)->first();
            $namaKategori = $kategoriDokumen ? $kategoriDokumen->KategoriDok : $request->KategoriDok;

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanKategoriDok = $sanitizeFileName($namaKategori);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename - Format: NoRegDok_KategoriDok_JenisDok_TglBerakhir
            $fileName = $cleanNoReg . '_' . $cleanKategoriDok . '_' . $cleanJenisDok . '_' . $tglBerakhir . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/legalitas', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenLegalitas::create($data);

        return redirect()->route('dokumen-legalitas.index')
            ->with('success', 'Dokumen Legalitas berhasil dibuat.');
    }

    public function show(DokumenLegalitas $dokumenLegalita)
    {
        $dokumenLegalitas = $dokumenLegalita;
        $dokumenLegalitas->load(['karyawan']);

        // Initialize empty collections for any counts in the view
        $expired = collect();
        $expired30 = collect();
        $expired60 = collect();
        $expired90 = collect();

        return view('dokumen-legalitas.show', compact(
            'dokumenLegalitas',
            'expired',
            'expired30',
            'expired60',
            'expired90'
        ));
    }

    public function edit(DokumenLegalitas $dokumenLegalita)
    {
        $dokumenLegalitas = $dokumenLegalita;
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

        return view('dokumen-legalitas.edit', compact('dokumenLegalitas', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori'));
    }

    public function update(Request $request, DokumenLegalitas $dokumenLegalita)
    {
        $dokumenLegalitas = $dokumenLegalita;

        $request->validate([
            'NoRegDok' => 'required|unique:B04DokLegalitas,NoRegDok,' . $dokumenLegalitas->Id . ',Id',
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
            if ($dokumenLegalitas->FileDok) {
                Storage::disk('public')->delete('documents/legalitas/' . $dokumenLegalitas->FileDok);
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

            // Format tanggal berakhir
            $tglBerakhir = $request->ValidasiDok == 'Tetap' ? 'Permanent' : Carbon::parse($request->TglBerakhirDok)->format('Ymd');

            // Get the KategoriDok name from database using the ID provided in the form
            $kategoriDokumen = KategoriDokumen::where('IdKode', $request->KategoriDok)->first();
            $namaKategori = $kategoriDokumen ? $kategoriDokumen->KategoriDok : $request->KategoriDok;

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanKategoriDok = $sanitizeFileName($namaKategori);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename - Format: NoRegDok_KategoriDok_JenisDok_TglBerakhir
            $fileName = $cleanNoReg . '_' . $cleanKategoriDok . '_' . $cleanJenisDok . '_' . $tglBerakhir . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/legalitas', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        $dokumenLegalitas->update($data);

        return redirect()->route('dokumen-legalitas.index')
            ->with('success', 'Dokumen Legalitas berhasil diperbarui.');
    }

    public function destroy(DokumenLegalitas $dokumenLegalita)
    {
        $dokumenLegalitas = $dokumenLegalita;

        // Delete file if exists
        if ($dokumenLegalitas->FileDok) {
            Storage::disk('public')->delete('documents/legalitas/' . $dokumenLegalitas->FileDok);
        }

        $dokumenLegalitas->delete();

        return redirect()->route('dokumen-legalitas.index')
            ->with('success', 'Dokumen Legalitas berhasil dihapus.');
    }

    public function download(DokumenLegalitas $dokumenLegalita)
    {
        $dokumenLegalitas = $dokumenLegalita;

        if (!$dokumenLegalitas->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = storage_path('app/public/documents/legalitas/' . $dokumenLegalitas->FileDok);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File already has the formatted name in new system
        return response()->download($path, $dokumenLegalitas->FileDok);
    }

    public function monitoring()
    {
        $dokumen = DokumenLegalitas::with(['karyawan'])
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

        return view('dokumen-legalitas.monitoring', compact('expired30', 'expired60', 'expired90', 'expired'));
    }

    /**
     * Get document statistics for dashboard
     */
    public function getDocumentStats()
    {
        // Count expired documents based on TglPengingat or TglBerakhirDok
        $expiredCount = DokumenLegalitas::where(function ($query) {
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
        $warningCount = DokumenLegalitas::where(function ($query) {
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

    public function viewDocument(DokumenLegalitas $dokumenLegalita)
    {
        $dokumenLegalitas = $dokumenLegalita;

        // Ensure file exists
        if (!$dokumenLegalitas->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path
        $filePath = storage_path('app/public/documents/legalitas/' . $dokumenLegalitas->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (in case path in database is different)
            $alternativePath = storage_path('app/public/documents/legalitas/' . $dokumenLegalitas->FileDok);

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
                'Content-Disposition' => 'inline; filename="' . $dokumenLegalitas->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return redirect()->route('dokumen-legalitas.download', $dokumenLegalitas);
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