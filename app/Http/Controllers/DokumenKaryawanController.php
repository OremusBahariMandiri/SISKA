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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DokumenKaryawanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen-karyawan')->only('index', 'show');
        $this->middleware('check.access:dokumen-karyawan,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen-karyawan,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen-karyawan,hapus')->only('destroy');
        $this->middleware('check.access:dokumen-karyawan,download')->only('download');
        $this->middleware('check.access:dokumen-karyawan,monitoring')->only('monitoring');
    }

    // Update the index method in DokumenKaryawanController
    public function index()
    {
        $dokumenKaryawan = DokumenKaryawan::with(['karyawan'])->get();

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
                $access = $user->userAccess()->where('MenuAcs', 'dokumen-karyawan')->first();
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

        return view('dokumen-karyawan.index', compact('dokumenKaryawan', 'userPermissions'));
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
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B01DmDokKaryawan,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => $request->ValidasiDok == 'Perpanjangan' ? 'required|date|after:TglTerbitDok' : 'nullable|date',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA04.exists' => 'Karyawan yang dipilih tidak valid',
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
            $file->storeAs('documents/karyawan', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenKaryawan::create($data);

        return redirect()->route('dokumen-karyawan.index')
            ->with('success', 'Dokumen Karyawan berhasil dibuat.');
    }

    public function show(DokumenKaryawan $dokumenKaryawan)
    {
        $dokumenKaryawan->load(['karyawan', 'kategori']);
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

    // This is the updated update method for the DokumenKaryawanController.php file

    public function update(Request $request, DokumenKaryawan $dokumenKaryawan)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B01DmDokKaryawan,NoRegDok,' . $dokumenKaryawan->id . ',id',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => $request->ValidasiDok == 'Perpanjangan' ? 'required|date|after:TglTerbitDok' : 'nullable|date',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'KetDok' => 'nullable|string', // Add explicit validation for KetDok
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA04.exists' => 'Karyawan yang dipilih tidak valid',
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

        // Debug: Log the raw request data and the KetDok value
        Log::info('Update request raw data:', [
            'all' => $request->all(),
            'KetDok' => $request->input('KetDok'),
            'KetDokBackup' => $request->input('KetDokBackup'),
            'post_params' => $_POST,
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
            $ketDokValue = $dokumenKaryawan->KetDok;
        }

        // Log the determined value
        Log::info('Determined KetDok value:', [
            'ketDokValue' => $ketDokValue,
        ]);

        // Explicitly include KetDok in the data array
        $data = [
            'IdKodeA04' => $request->IdKodeA04,
            'NoRegDok' => $request->NoRegDok,
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
            if ($dokumenKaryawan->FileDok) {
                Storage::disk('public')->delete('documents/karyawan/' . $dokumenKaryawan->FileDok);
                // Check alternative location if file not found in main location
                if (!Storage::disk('public')->exists('documents/karyawan/' . $dokumenKaryawan->FileDok)) {
                    Storage::disk('public')->delete('documents/' . $dokumenKaryawan->FileDok);
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
            $file->storeAs('documents/karyawan', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        // Debug: Log the data that will be used for the update
        Log::info('Update data prepared:', [
            'data' => $data,
            'KetDok_in_data' => $data['KetDok'],
        ]);

        // Update the model with the prepared data - Use direct DB update for reliability
        $updateResult = DB::table('B01DmDokKaryawan')
            ->where('id', $dokumenKaryawan->id)
            ->update($data);

        Log::info('DB update result:', [
            'updateResult' => $updateResult,
        ]);

        // Refresh the model to get the latest data
        $dokumenKaryawan = DokumenKaryawan::find($dokumenKaryawan->id);

        // Debug: Log the updated model to verify changes
        Log::info('Updated model:', [
            'model' => $dokumenKaryawan->toArray(),
            'KetDok_after_update' => $dokumenKaryawan->KetDok,
        ]);

        return redirect()->route('dokumen-karyawan.index')
            ->with('success', 'Dokumen Karyawan berhasil diperbarui.');
    }

    public function destroy(DokumenKaryawan $dokumenKaryawan)
    {
        // Delete file if exists
        if ($dokumenKaryawan->FileDok) {
            Storage::disk('public')->delete('documents/karyawan/' . $dokumenKaryawan->FileDok);
            // Check alternative location if file not found in main location
            if (!Storage::disk('public')->exists('documents/karyawan/' . $dokumenKaryawan->FileDok)) {
                Storage::disk('public')->delete('documents/' . $dokumenKaryawan->FileDok);
            }
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

        // Check in the new location first
        $path = storage_path('app/public/documents/karyawan/' . $dokumenKaryawan->FileDok);

        // If not found, try the old location
        if (!file_exists($path)) {
            $path = storage_path('app/public/documents/' . $dokumenKaryawan->FileDok);

            if (!file_exists($path)) {
                return back()->with('error', 'File tidak ditemukan.');
            }
        }

        // File already has the formatted name
        return response()->download($path, $dokumenKaryawan->FileDok);
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

        // File path - check in new location first
        $filePath = storage_path('app/public/documents/karyawan/' . $dokumenKaryawan->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (old location)
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
