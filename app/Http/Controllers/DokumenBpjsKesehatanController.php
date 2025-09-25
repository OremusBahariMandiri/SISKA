<?php

namespace App\Http\Controllers;

use App\Models\DokumenBpjsKesehatan;
use App\Models\Karyawan;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DokumenBpjsKesehatanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen-bpjs-kesehatan')->only('index', 'show');
        $this->middleware('check.access:dokumen-bpjs-kesehatan,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen-bpjs-kesehatan,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen-bpjs-kesehatan,hapus')->only('destroy');
        $this->middleware('check.access:dokumen-bpjs-kesehatan,download')->only('download');
    }

    public function index()
    {
        // Join with related tables to get necessary data
        $dokumenBpjsKesehatan = DB::table('B06DokBpjsKes')
            ->select(
                'B06DokBpjsKes.*',
                'A04DmKaryawan.NamaKry'
            )
            ->leftJoin('A04DmKaryawan', 'B06DokBpjsKes.IdKodeA04', '=', 'A04DmKaryawan.IdKode')
            ->get();

        // Convert to collection for easier manipulation
        $dokumenBpjsKesehatan = collect($dokumenBpjsKesehatan)->map(function($item) {
            // Convert dates to Carbon objects
            if (!empty($item->TglTerbitDok)) {
                $item->TglTerbitDok = \Carbon\Carbon::parse($item->TglTerbitDok);
            }
            if (!empty($item->TglBerakhirDok)) {
                $item->TglBerakhirDok = \Carbon\Carbon::parse($item->TglBerakhirDok);
            }

            return $item;
        });

        // Group by karyawan
        $dokumenByKaryawan = $dokumenBpjsKesehatan->groupBy('IdKodeA04');

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

        // Combine documents with the sorted order by karyawan
        $sortedDokumen = collect();
        foreach ($karyawanNames as $karyawanId => $name) {
            $docs = $dokumenByKaryawan[$karyawanId];
            $sortedDokumen = $sortedDokumen->concat($docs);
        }

        // Convert back to model objects
        $dokumenBpjsKesehatan = $sortedDokumen->map(function($item) {
            $dokumen = new DokumenBpjsKesehatan();
            foreach ((array)$item as $key => $value) {
                $dokumen->$key = $value;
            }

            // Add related objects
            $karyawan = new Karyawan();
            $karyawan->NamaKry = $item->NamaKry;
            $karyawan->IdKode = $item->IdKodeA04;
            $dokumen->karyawan = $karyawan;

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
                ];
            } else {
                // Get specific permissions from user access
                $access = $user->userAccess()->where('MenuAcs', 'dokumen-bpjs-kesehatan')->first();
                if ($access) {
                    $userPermissions = [
                        'tambah' => (bool)$access->TambahAcs,
                        'ubah' => (bool)$access->UbahAcs,
                        'hapus' => (bool)$access->HapusAcs,
                        'download' => (bool)$access->DownloadAcs,
                        'detail' => (bool)$access->DetailAcs,
                    ];
                }
            }
        }

        return view('dokumen-bpjs-kesehatan.index', compact('dokumenBpjsKesehatan', 'userPermissions'));
    }

    public function create()
    {
        $karyawan = Karyawan::all();

        // Get document categories
        $kategoriDokumen = KategoriDokumen::all();

        // Get document types
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();

        // Create array grouped by category for JavaScript
        $jenisDokumenByKategori = [];
        foreach ($kategoriDokumen as $kategori) {
            $kategoriId = $kategori->IdKode;

            // Get document types for this category
            $jenisForKategori = JenisDokumen::where('IdKodeA06', $kategoriId)->get();

            if (!isset($jenisDokumenByKategori[$kategoriId])) {
                $jenisDokumenByKategori[$kategoriId] = [];
            }

            foreach ($jenisForKategori as $jenis) {
                $jenisDokumenByKategori[$kategoriId][] = [
                    'id' => $jenis->id,
                    'IdKode' => $jenis->IdKode,
                    'JenisDok' => $jenis->JenisDok
                ];
            }
        }

        // Generate automatic ID
        $newId = $this->generateId('B06', 'B06DokBpjsKes');

        return view('dokumen-bpjs-kesehatan.create', compact('karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B06DokBpjsKes,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => 'nullable|date|after_or_equal:TglTerbitDok',
            'UpahKtrKry' => 'required|min:0',
            'UpahBrshKry' => 'required|min:0',
            'IuranPrshPersen' => 'required|numeric|min:0|max:100',
            'IuranPrshRp' => 'required|numeric|min:0',
            'IuranKryPersen' => 'required|numeric|min:0|max:100',
            'IuranKryRp' => 'required|numeric|min:0',
            'IuranKry1Rp' => 'nullable|numeric|min:0',
            'IuranKry2Rp' => 'nullable|numeric|min:0',
            'IuranKry3Rp' => 'nullable|numeric|min:0',
            'JmlPrshRp' => 'required|numeric|min:0',
            'JmlKryRp' => 'required|numeric|min:0',
            'TotIuran' => 'required|numeric|min:0',
            'FileDok' => 'nullable',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'TglBerakhirDok.after_or_equal' => 'Tanggal Berakhir harus setelah atau sama dengan Tanggal Terbit',
            'UpahKtrKry.required' => 'Upah Kotor harus diisi',
            'UpahBrshKry.required' => 'Upah Bersih harus diisi',
            'IuranPrshPersen.required' => 'Persentase Iuran Perusahaan harus diisi',
            'IuranPrshRp.required' => 'Nominal Iuran Perusahaan harus diisi',
            'IuranKryPersen.required' => 'Persentase Iuran Karyawan harus diisi',
            'IuranKryRp.required' => 'Nominal Iuran Karyawan harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('B06', 'B06DokBpjsKes');
        } else {
            $IdKode = $request->IdKode;
        }

        // Calculate validity period automatically
        $masaBerlaku = 'Tetap';
        if ($request->filled('TglTerbitDok') && $request->filled('TglBerakhirDok')) {
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

        $data = [
            'IdKode' => $IdKode,
            'IdKodeA04' => $request->IdKodeA04,
            'NoRegDok' => $request->NoRegDok,
            'KategoriDok' => $request->KategoriDok,
            'JenisDok' => $request->JenisDok,
            'KetDok' => $request->KetDok,
            'TglTerbitDok' => $request->TglTerbitDok,
            'TglBerakhirDok' => $request->TglBerakhirDok,
            'MasaBerlaku' => $masaBerlaku,
            'UpahKtrKry' => $request->UpahKtrKry,
            'UpahBrshKry' => $request->UpahBrshKry,
            'IuranPrshPersen' => $request->IuranPrshPersen,
            'IuranPrshRp' => $request->IuranPrshRp,
            'IuranKryPersen' => $request->IuranKryPersen,
            'IuranKryRp' => $request->IuranKryRp,
            'IuranKry1Rp' => $request->IuranKry1Rp,
            'IuranKry2Rp' => $request->IuranKry2Rp,
            'IuranKry3Rp' => $request->IuranKry3Rp,
            'JmlPrshRp' => $request->JmlPrshRp,
            'JmlKryRp' => $request->JmlKryRp,
            'TotIuran' => $request->TotIuran,
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

            // Format berakhir date
            $tglBerakhir = $request->filled('TglBerakhirDok') ? Carbon::parse($request->TglBerakhirDok)->format('Ymd') : 'Permanent';

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $namaKaryawan . '_' . $tglBerakhir . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/bpjs-kesehatan', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenBpjsKesehatan::create($data);

        return redirect()->route('dokumen-bpjs-kesehatan.index')
            ->with('success', 'Dokumen BPJS Kesehatan berhasil dibuat.');
    }

    public function show(DokumenBpjsKesehatan $dokumenBpjsKesehatan)
    {
        $dokumenBpjsKesehatan->load(['karyawan']);
        return view('dokumen-bpjs-kesehatan.show', compact('dokumenBpjsKesehatan'));
    }

    public function edit(DokumenBpjsKesehatan $dokumenBpjsKesehatan)
    {
        $karyawan = Karyawan::all();

        // Get document categories
        $kategoriDokumen = KategoriDokumen::all();

        // Get document types
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();

        // Create array grouped by category for JavaScript
        $jenisDokumenByKategori = [];
        foreach ($kategoriDokumen as $kategori) {
            $kategoriId = $kategori->IdKode;

            // Get document types for this category
            $jenisForKategori = JenisDokumen::where('IdKodeA06', $kategoriId)->get();

            if (!isset($jenisDokumenByKategori[$kategoriId])) {
                $jenisDokumenByKategori[$kategoriId] = [];
            }

            foreach ($jenisForKategori as $jenis) {
                $jenisDokumenByKategori[$kategoriId][] = [
                    'id' => $jenis->id,
                    'IdKode' => $jenis->IdKode,
                    'JenisDok' => $jenis->JenisDok
                ];
            }
        }

        return view('dokumen-bpjs-kesehatan.edit', compact('dokumenBpjsKesehatan', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori'));
    }

    public function update(Request $request, DokumenBpjsKesehatan $dokumenBpjsKesehatan)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B06DokBpjsKes,NoRegDok,' . $dokumenBpjsKesehatan->id . ',id',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => 'nullable|date|after_or_equal:TglTerbitDok',
            'UpahKtrKry' => 'required|min:0',
            'UpahBrshKry' => 'required|min:0',
            'IuranPrshPersen' => 'required|numeric|min:0|max:100',
            'IuranPrshRp' => 'required|numeric|min:0',
            'IuranKryPersen' => 'required|numeric|min:0|max:100',
            'IuranKryRp' => 'required|numeric|min:0',
            'IuranKry1Rp' => 'nullable|numeric|min:0',
            'IuranKry2Rp' => 'nullable|numeric|min:0',
            'IuranKry3Rp' => 'nullable|numeric|min:0',
            'JmlPrshRp' => 'required|numeric|min:0',
            'JmlKryRp' => 'required|numeric|min:0',
            'TotIuran' => 'required|numeric|min:0',
            'FileDok' => 'nullable',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'TglBerakhirDok.after_or_equal' => 'Tanggal Berakhir harus setelah atau sama dengan Tanggal Terbit',
            'UpahKtrKry.required' => 'Upah Kotor harus diisi',
            'UpahBrshKry.required' => 'Upah Bersih harus diisi',
            'IuranPrshPersen.required' => 'Persentase Iuran Perusahaan harus diisi',
            'IuranPrshRp.required' => 'Nominal Iuran Perusahaan harus diisi',
            'IuranKryPersen.required' => 'Persentase Iuran Karyawan harus diisi',
            'IuranKryRp.required' => 'Nominal Iuran Karyawan harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        // Calculate validity period automatically
        $masaBerlaku = 'Tetap';
        if ($request->filled('TglTerbitDok') && $request->filled('TglBerakhirDok')) {
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

        $data = [
            'IdKodeA04' => $request->IdKodeA04,
            'NoRegDok' => $request->NoRegDok,
            'KategoriDok' => $request->KategoriDok,
            'JenisDok' => $request->JenisDok,
            'KetDok' => $request->KetDok,
            'TglTerbitDok' => $request->TglTerbitDok,
            'TglBerakhirDok' => $request->TglBerakhirDok,
            'MasaBerlaku' => $masaBerlaku,
            'UpahKtrKry' => $request->UpahKtrKry,
            'UpahBrshKry' => $request->UpahBrshKry,
            'IuranPrshPersen' => $request->IuranPrshPersen,
            'IuranPrshRp' => $request->IuranPrshRp,
            'IuranKryPersen' => $request->IuranKryPersen,
            'IuranKryRp' => $request->IuranKryRp,
            'IuranKry1Rp' => $request->IuranKry1Rp,
            'IuranKry2Rp' => $request->IuranKry2Rp,
            'IuranKry3Rp' => $request->IuranKry3Rp,
            'JmlPrshRp' => $request->JmlPrshRp,
            'JmlKryRp' => $request->JmlKryRp,
            'TotIuran' => $request->TotIuran,
            'StatusDok' => $request->StatusDok,
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        if ($request->hasFile('FileDok')) {
            // Delete old file if exists
            if ($dokumenBpjsKesehatan->FileDok) {
                Storage::disk('public')->delete('documents/bpjs-kesehatan/' . $dokumenBpjsKesehatan->FileDok);
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

            // Format berakhir date
            $tglBerakhir = $request->filled('TglBerakhirDok') ? Carbon::parse($request->TglBerakhirDok)->format('Ymd') : 'Permanent';

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $namaKaryawan . '_' . $tglBerakhir . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/bpjs-kesehatan', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        $dokumenBpjsKesehatan->update($data);

        return redirect()->route('dokumen-bpjs-kesehatan.index')
            ->with('success', 'Dokumen BPJS Kesehatan berhasil diperbarui.');
    }

    public function destroy(DokumenBpjsKesehatan $dokumenBpjsKesehatan)
    {
        // Delete file if exists
        if ($dokumenBpjsKesehatan->FileDok) {
            Storage::disk('public')->delete('documents/bpjs-kesehatan/' . $dokumenBpjsKesehatan->FileDok);
        }

        $dokumenBpjsKesehatan->delete();

        return redirect()->route('dokumen-bpjs-kesehatan.index')
            ->with('success', 'Dokumen BPJS Kesehatan berhasil dihapus.');
    }

    public function download(DokumenBpjsKesehatan $dokumenBpjsKesehatan)
    {
        if (!$dokumenBpjsKesehatan->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = storage_path('app/public/documents/bpjs-kesehatan/' . $dokumenBpjsKesehatan->FileDok);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File already has the formatted name
        return response()->download($path, $dokumenBpjsKesehatan->FileDok);
    }

    public function viewDocument(DokumenBpjsKesehatan $dokumenBpjsKesehatan)
    {
        // Ensure file exists
        if (!$dokumenBpjsKesehatan->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path
        $filePath = storage_path('app/public/documents/bpjs-kesehatan/' . $dokumenBpjsKesehatan->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (in case path in database is different)
            $alternativePath = storage_path('app/public/documents/' . $dokumenBpjsKesehatan->FileDok);

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
                'Content-Disposition' => 'inline; filename="' . $dokumenBpjsKesehatan->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return response()->download($filePath, $dokumenBpjsKesehatan->FileDok);
    }

    public function getJenisByKategori($kategoriId)
    {
        $jenisDokumen = JenisDokumen::where('IdKodeA06', $kategoriId)
            ->select('id', 'IdKode', 'JenisDok')
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
}
