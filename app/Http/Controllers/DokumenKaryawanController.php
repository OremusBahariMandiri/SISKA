<?php

namespace App\Http\Controllers;

use App\Exports\DokumenKaryawanExport;
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
use Maatwebsite\Excel\Facades\Excel;

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

        // Get all jenis dokumen with their GolDok values
        $jenisDokList = DB::table('A07DmJenisDok')
            ->select('JenisDok', 'GolDok')
            ->get()
            ->keyBy('JenisDok');

        // Retrieve document data with employee information
        $dokumenKaryawan = DB::table('B01DmDokKaryawan')
            ->select(
                'B01DmDokKaryawan.*',
                'A04DmKaryawan.NamaKry',
                'A04DmKaryawan.NrkKry'
            )
            ->leftJoin('A04DmKaryawan', 'B01DmDokKaryawan.IdKodeA04', '=', 'A04DmKaryawan.IdKode')
            ->get();

        // Process document data to ensure proper date handling and add GolDok
        $processedDokumen = collect($dokumenKaryawan)->map(function ($item) use ($jenisDokList) {
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

            // Add GolDok value if JenisDok exists in jenisDokList
            if (isset($item->JenisDok) && isset($jenisDokList[$item->JenisDok])) {
                $item->GolDok = $jenisDokList[$item->JenisDok]->GolDok;
            } else {
                $item->GolDok = 999; // Default high value if not found
            }

            return $item;
        });

        // Convert to model objects
        $dokumenKaryawan = $processedDokumen->map(function ($item) {
            // Create a new model
            $dokumen = new DokumenKaryawan();

            // Copy all properties
            foreach ((array)$item as $key => $value) {
                $dokumen->$key = $value;
            }

            // Add karyawan and kategori objects
            $karyawan = new Karyawan();
            $karyawan->NamaKry = $item->NamaKry;
            $karyawan->NrkKry = $item->NrkKry;
            $karyawan->IdKode = $item->IdKodeA04;
            $dokumen->karyawan = $karyawan;

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

        return view('dokumen-karyawan.index', compact('dokumenKaryawan', 'userPermissions'));
    }

    /**
     * Process and sort documents by employee and GolDok
     */
    private function processAndSortDocuments($documents)
    {
        // Group documents by employee ID
        $groupedByEmployee = [];

        foreach ($documents as $document) {
            $employeeId = $document->IdKodeA04;
            if (!isset($groupedByEmployee[$employeeId])) {
                $groupedByEmployee[$employeeId] = [
                    'employee' => $document->karyawan,
                    'documents' => []
                ];
            }

            // Get the GolDok for the document
            $jenisDokumen = JenisDokumen::where('JenisDok', $document->JenisDok)->first();
            $golDok = $jenisDokumen ? $jenisDokumen->GolDok : 999; // Default high value if not found

            // Add GolDok to the document for sorting
            $document->GolDok = $golDok;
            $groupedByEmployee[$employeeId]['documents'][] = $document;
        }

        // Sort each employee's documents by GolDok
        foreach ($groupedByEmployee as &$employeeData) {
            usort($employeeData['documents'], function ($a, $b) {
                return $a->GolDok - $b->GolDok;
            });
        }

        // Sort employees by name for consistent display
        uksort($groupedByEmployee, function ($a, $b) use ($groupedByEmployee) {
            $nameA = $groupedByEmployee[$a]['employee'] ? $groupedByEmployee[$a]['employee']->NamaKry : '';
            $nameB = $groupedByEmployee[$b]['employee'] ? $groupedByEmployee[$b]['employee']->NamaKry : '';
            return strcmp($nameA, $nameB);
        });

        // Flatten the array for view display
        $result = [];
        foreach ($groupedByEmployee as $employeeData) {
            foreach ($employeeData['documents'] as $document) {
                $result[] = $document;
            }
        }

        return $result;
    }

    public function create()
    {
        $karyawan = Karyawan::all();

        // Ambil kategori dokumen dan urutkan berdasarkan GolDok
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
            'FileDok' => 'nullable',
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
            'FileDok' => 'nullable',
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

    public function exportExcel(Request $request)
{
    // Ambil parameter filter
    $filters = [
        'noreg' => $request->filter_noreg,
        'karyawan' => $request->filter_karyawan,
        'jenis' => $request->filter_jenis,
        'tgl_terbit_from' => $request->filter_tgl_terbit_from,
        'tgl_terbit_to' => $request->filter_tgl_terbit_to,
        'tgl_berakhir_from' => $request->filter_tgl_berakhir_from,
        'tgl_berakhir_to' => $request->filter_tgl_berakhir_to,
        'status' => $request->filter_status,
    ];

    // Query data berdasarkan filter
    $query = DokumenKaryawan::with(['karyawan']);

    // Filter No Registrasi
    if ($request->filled('filter_noreg')) {
        $query->where('NoRegDok', 'like', '%' . $request->filter_noreg . '%');
    }

    // Filter Karyawan (nama)
    if ($request->filled('filter_karyawan')) {
        $query->whereHas('karyawan', function($q) use ($request) {
            $q->where('NamaKry', 'like', '%' . $request->filter_karyawan . '%');
        });
    }

    // Filter Jenis Dokumen
    if ($request->filled('filter_jenis')) {
        $query->where('JenisDok', $request->filter_jenis);
    }

    // Filter Tanggal Terbit
    if ($request->filled('filter_tgl_terbit_from')) {
        $query->where('TglTerbitDok', '>=', $request->filter_tgl_terbit_from);
    }
    if ($request->filled('filter_tgl_terbit_to')) {
        $query->where('TglTerbitDok', '<=', $request->filter_tgl_terbit_to);
    }

    // Filter Tanggal Berakhir
    if ($request->filled('filter_tgl_berakhir_from')) {
        $query->where('TglBerakhirDok', '>=', $request->filter_tgl_berakhir_from);
    }
    if ($request->filled('filter_tgl_berakhir_to')) {
        $query->where('TglBerakhirDok', '<=', $request->filter_tgl_berakhir_to);
    }

    // Filter Status Dokumen
    if ($request->filled('filter_status')) {
        $statusValue = $request->filter_status;

        if ($statusValue === 'Berlaku') {
            $query->where('StatusDok', 'Berlaku');
        }
        else if ($statusValue === 'Tidak Berlaku') {
            $query->where('StatusDok', 'Tidak Berlaku');
        }
        else if ($statusValue === 'Warning' || $statusValue === 'Expired') {
            // For Warning and Expired, we need more complex logic
            // First, select documents with status "Berlaku"
            $query->where('StatusDok', 'Berlaku');

            // Then apply additional date conditions
            if ($statusValue === 'Warning') {
                // Documents that will expire within 30 days
                $query->where(function($q) {
                    $thirtyDaysFromNow = now()->addDays(30);
                    $q->whereNotNull('TglBerakhirDok')
                      ->where('TglBerakhirDok', '>', now())
                      ->where('TglBerakhirDok', '<=', $thirtyDaysFromNow);
                });
            } else if ($statusValue === 'Expired') {
                // Documents that are already expired
                $query->where(function($q) {
                    $q->whereNotNull('TglBerakhirDok')
                      ->where('TglBerakhirDok', '<', now());
                });
            }
        }
    }

    // Get jenis dokumen with their GolDok values
    $jenisDokList = DB::table('A07DmJenisDok')
        ->select('JenisDok', 'GolDok')
        ->get()
        ->keyBy('JenisDok');

    // Execute the query
    $dokumenKaryawan = $query->get();

    // Process document data to ensure proper date handling and add GolDok
    $dokumenKaryawan = $dokumenKaryawan->map(function ($item) use ($jenisDokList) {
        // Add GolDok value if JenisDok exists in jenisDokList
        if (isset($item->JenisDok) && isset($jenisDokList[$item->JenisDok])) {
            $item->GolDok = $jenisDokList[$item->JenisDok]->GolDok;
        } else {
            $item->GolDok = 999; // Default high value if not found
        }

        return $item;
    });

    // Sort by employee and GolDok
    $dokumenKaryawan = $this->processAndSortDocuments($dokumenKaryawan);

    // Format tanggal untuk nama file
    $currentDate = now()->format('d-m-Y_H-i-s');
    $fileName = 'Dokumen_Karyawan_' . $currentDate . '.xlsx';

    return Excel::download(new DokumenKaryawanExport($dokumenKaryawan, $filters), $fileName);
}
}
