<?php

namespace App\Http\Controllers;

use App\Models\DokumenBpjsTenagaKerja;
use App\Models\Karyawan;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DokumenBpjsTenagaKerjaController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:dokumen-bpjs-tenaga-kerja')->only('index', 'show');
        $this->middleware('check.access:dokumen-bpjs-tenaga-kerja,tambah')->only('create', 'store');
        $this->middleware('check.access:dokumen-bpjs-tenaga-kerja,ubah')->only('edit', 'update');
        $this->middleware('check.access:dokumen-bpjs-tenaga-kerja,hapus')->only('destroy');
        $this->middleware('check.access:dokumen-bpjs-tenaga-kerja,download')->only('download');
    }

    public function index()
    {
        // Join with related tables to get necessary data
        $dokumenBpjsTenagaKerja = DB::table('B07DokBpjsNaKer')
            ->select(
                'B07DokBpjsNaKer.*',
                'A04DmKaryawan.NamaKry'
            )
            ->leftJoin('A04DmKaryawan', 'B07DokBpjsNaKer.IdKodeA04', '=', 'A04DmKaryawan.IdKode')
            ->get();

        // Convert to collection for easier manipulation
        $dokumenBpjsTenagaKerja = collect($dokumenBpjsTenagaKerja)->map(function ($item) {
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
        $dokumenByKaryawan = $dokumenBpjsTenagaKerja->groupBy('IdKodeA04');

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
        $dokumenBpjsTenagaKerja = $sortedDokumen->map(function ($item) {
            $dokumen = new DokumenBpjsTenagaKerja();
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
                $access = $user->userAccess()->where('MenuAcs', 'dokumen-bpjs-tenaga-kerja')->first();
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

        return view('dokumen-bpjs-tenaga-kerja.index', compact('dokumenBpjsTenagaKerja', 'userPermissions'));
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
        $newId = $this->generateId('B07', 'B07DokBpjsNaKer');

        return view('dokumen-bpjs-tenaga-kerja.create', compact('karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B07DokBpjsNaKer,NoRegDok',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => 'nullable|date|after_or_equal:TglTerbitDok',
            'UpahKtrKry' => 'required|min:0',
            'UpahBrshKry' => 'required|min:0',
            'IuranJkkPrshPersen' => 'required|numeric|min:0|max:100',
            'IuranJkkPrshRp' => 'required|numeric|min:0',
            'IuranJkkKryPersen' => 'numeric|min:0|max:100',
            'IuranJkkKryRp' => 'required|numeric|min:0',
            'IuranJkmPrshPersen' => 'required|numeric|min:0|max:100',
            'IuranJkmPrshRp' => 'required|numeric|min:0',
            'IuranJkmKryPersen' => 'numeric|min:0|max:100',
            'IuranJkmKryRP' => 'required|numeric|min:0',
            'IuranJhtPrshPersen' => 'required|numeric|min:0|max:100',
            'IursanJhtPrshRp' => 'required|numeric|min:0',
            'IursanJhtKryPersen' => 'required|numeric|min:0|max:100',
            'IursanJhtKryRp' => 'required|numeric|min:0',
            'IuranJpPrshPersen' => 'nullable|numeric|min:0|max:100',
            'IuranJpPrshRp' => 'nullable|numeric|min:0',
            'IuranJpKryPersen' => 'nullable|numeric|min:0|max:100',
            'IuranJpKryRp' => 'nullable|numeric|min:0',
            'JmlPrshRp' => 'required|numeric|min:0',
            'JmlKryRp' => 'required|numeric|min:0',
            'TotSetoran' => 'required|numeric|min:0',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
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
            'IuranJkkPrshPersen.required' => 'Persentase Iuran JKK Perusahaan harus diisi',
            'IuranJkkPrshRp.required' => 'Nominal Iuran JKK Perusahaan harus diisi',
            'IuranJkkKryPersen.required' => 'Persentase Iuran JKK Karyawan harus diisi',
            'IuranJkkKryRp.required' => 'Nominal Iuran JKK Karyawan harus diisi',
            'IuranJkmPrshPersen.required' => 'Persentase Iuran JKM Perusahaan harus diisi',
            'IuranJkmPrshRp.required' => 'Nominal Iuran JKM Perusahaan harus diisi',
            'IuranJkmKryPersen.required' => 'Persentase Iuran JKM Karyawan harus diisi',
            'IuranJkmKryRP.required' => 'Nominal Iuran JKM Karyawan harus diisi',
            'IuranJhtPrshPersen.required' => 'Persentase Iuran JHT Perusahaan harus diisi',
            'IursanJhtPrshRp.required' => 'Nominal Iuran JHT Perusahaan harus diisi',
            'IursanJhtKryPersen.required' => 'Persentase Iuran JHT Karyawan harus diisi',
            'IursanJhtKryRp.required' => 'Nominal Iuran JHT Karyawan harus diisi',
            'JmlPrshRp.required' => 'Jumlah Iuran Perusahaan harus diisi',
            'JmlKryRp.required' => 'Jumlah Iuran Karyawan harus diisi',
            'TotSetoran.required' => 'Total Setoran harus diisi',
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
            $IdKode = $this->generateId('B07', 'B07DokBpjsNaKer');
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
            'IuranJkkPrshPersen' => $request->IuranJkkPrshPersen,
            'IuranJkkPrshRp' => $request->IuranJkkPrshRp,
            'IuranJkkKryPersen' => $request->IuranJkkKryPersen,
            'IuranJkkKryRp' => $request->IuranJkkKryRp,
            'IuranJkmPrshPersen' => $request->IuranJkmPrshPersen,
            'IuranJkmPrshRp' => $request->IuranJkmPrshRp,
            'IuranJkmKryPersen' => $request->IuranJkmKryPersen,
            'IuranJkmKryRP' => $request->IuranJkmKryRP,
            'IuranJhtPrshPersen' => $request->IuranJhtPrshPersen,
            'IursanJhtPrshRp' => $request->IursanJhtPrshRp,
            'IursanJhtKryPersen' => $request->IursanJhtKryPersen,
            'IursanJhtKryRp' => $request->IursanJhtKryRp,
            'IuranJpPrshPersen' => $request->IuranJpPrshPersen,
            'IuranJpPrshRp' => $request->IuranJpPrshRp,
            'IuranJpKryPersen' => $request->IuranJpKryPersen,
            'IuranJpKryRp' => $request->IuranJpKryRp,
            'JmlPrshRp' => $request->JmlPrshRp,
            'JmlKryRp' => $request->JmlKryRp,
            'TotSetoran' => $request->TotSetoran,
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
            $file->storeAs('documents/bpjs-tenaga-kerja', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        DokumenBpjsTenagaKerja::create($data);

        return redirect()->route('dokumen-bpjs-tenaga-kerja.index')
            ->with('success', 'Dokumen BPJS Tenaga Kerja berhasil dibuat.');
    }

    public function show(DokumenBpjsTenagaKerja $dokumenBpjsTenagaKerja)
    {
        $dokumenBpjsTenagaKerja->load(['karyawan']);
        return view('dokumen-bpjs-tenaga-kerja.show', compact('dokumenBpjsTenagaKerja'));
    }

    public function edit(DokumenBpjsTenagaKerja $dokumenBpjsTenagaKerja)
    {
        // Log the start of edit operation
        Log::info('User [' . auth()->user()->IdKode . '] accessed edit form for BPJS Tenaga Kerja document [' . $dokumenBpjsTenagaKerja->IdKode . ']');

        $karyawan = Karyawan::all();
        Log::debug('Loaded ' . $karyawan->count() . ' karyawan records');

        // Get document categories
        $kategoriDokumen = KategoriDokumen::all();
        Log::debug('Loaded ' . $kategoriDokumen->count() . ' kategori dokumen records');

        // Get document types
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();
        Log::debug('Loaded ' . $jenisDokumen->count() . ' jenis dokumen records');

        // Create array grouped by category for JavaScript
        $jenisDokumenByKategori = [];
        foreach ($kategoriDokumen as $kategori) {
            $kategoriId = $kategori->IdKode;

            // Get document types for this category
            $jenisForKategori = JenisDokumen::where('IdKodeA06', $kategoriId)->get();
            Log::debug('Loaded ' . $jenisForKategori->count() . ' jenis dokumen for kategori ' . $kategoriId);

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

        Log::info('Rendering edit view for BPJS Tenaga Kerja document [' . $dokumenBpjsTenagaKerja->IdKode . ']');
        return view('dokumen-bpjs-tenaga-kerja.edit', compact('dokumenBpjsTenagaKerja', 'karyawan', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori'));
    }
    public function update(Request $request, DokumenBpjsTenagaKerja $dokumenBpjsTenagaKerja)
    {
        Log::info('User [' . auth()->user()->IdKode . '] submitted update for BPJS Tenaga Kerja document [' . $dokumenBpjsTenagaKerja->IdKode . ']');
        Log::debug('Update request data: ' . json_encode($request->all()));

        // Validation
        Log::debug('Starting validation process');
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:B07DokBpjsNaKer,NoRegDok,' . $dokumenBpjsTenagaKerja->id . ',id',
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'TglBerakhirDok' => 'nullable|date|after_or_equal:TglTerbitDok',
            'UpahKtrKry' => 'required|min:0',
            'UpahBrshKry' => 'required|min:0',
            'IuranJkkPrshPersen' => 'required|numeric|min:0|max:100',
            'IuranJkkPrshRp' => 'required|numeric|min:0',
            'IuranJkkKryPersen' => 'required|numeric|min:0|max:100',
            'IuranJkkKryRp' => 'required|numeric|min:0',
            'IuranJkmPrshPersen' => 'required|numeric|min:0|max:100',
            'IuranJkmPrshRp' => 'required|numeric|min:0',
            'IuranJkmKryPersen' => 'required|numeric|min:0|max:100',
            'IuranJkmKryRP' => 'required|numeric|min:0',
            'IuranJhtPrshPersen' => 'required|numeric|min:0|max:100',
            'IursanJhtPrshRp' => 'required|numeric|min:0',
            'IursanJhtKryPersen' => 'required|numeric|min:0|max:100',
            'IursanJhtKryRp' => 'required|numeric|min:0',
            'IuranJpPrshPersen' => 'nullable|numeric|min:0|max:100',
            'IuranJpPrshRp' => 'nullable|numeric|min:0',
            'IuranJpKryPersen' => 'nullable|numeric|min:0|max:100',
            'IuranJpKryRp' => 'nullable|numeric|min:0',
            'JmlPrshRp' => 'required|numeric|min:0',
            'JmlKryRp' => 'required|numeric|min:0',
            'TotSetoran' => 'required|numeric|min:0',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
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
            'IuranJkkPrshPersen.required' => 'Persentase Iuran JKK Perusahaan harus diisi',
            'IuranJkkPrshRp.required' => 'Nominal Iuran JKK Perusahaan harus diisi',
            'IuranJkkKryPersen.required' => 'Persentase Iuran JKK Karyawan harus diisi',
            'IuranJkkKryRp.required' => 'Nominal Iuran JKK Karyawan harus diisi',
            'IuranJkmPrshPersen.required' => 'Persentase Iuran JKM Perusahaan harus diisi',
            'IuranJkmPrshRp.required' => 'Nominal Iuran JKM Perusahaan harus diisi',
            'IuranJkmKryPersen.required' => 'Persentase Iuran JKM Karyawan harus diisi',
            'IuranJkmKryRP.required' => 'Nominal Iuran JKM Karyawan harus diisi',
            'IuranJhtPrshPersen.required' => 'Persentase Iuran JHT Perusahaan harus diisi',
            'IursanJhtPrshRp.required' => 'Nominal Iuran JHT Perusahaan harus diisi',
            'IursanJhtKryPersen.required' => 'Persentase Iuran JHT Karyawan harus diisi',
            'IursanJhtKryRp.required' => 'Nominal Iuran JHT Karyawan harus diisi',
            'JmlPrshRp.required' => 'Jumlah Iuran Perusahaan harus diisi',
            'JmlKryRp.required' => 'Jumlah Iuran Karyawan harus diisi',
            'TotSetoran.required' => 'Total Setoran harus diisi',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for document update [' . $dokumenBpjsTenagaKerja->IdKode . ']: ' . json_encode($validator->errors()->toArray()));
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        Log::info('Validation passed for document update [' . $dokumenBpjsTenagaKerja->IdKode . ']');

        // Calculate validity period automatically
        Log::debug('Calculating masa berlaku');
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

            Log::debug('Calculated masa berlaku: ' . $masaBerlaku);
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
            'IuranJkkPrshPersen' => $request->IuranJkkPrshPersen,
            'IuranJkkPrshRp' => $request->IuranJkkPrshRp,
            'IuranJkkKryPersen' => $request->IuranJkkKryPersen,
            'IuranJkkKryRp' => $request->IuranJkkKryRp,
            'IuranJkmPrshPersen' => $request->IuranJkmPrshPersen,
            'IuranJkmPrshRp' => $request->IuranJkmPrshRp,
            'IuranJkmKryPersen' => $request->IuranJkmKryPersen,
            'IuranJkmKryRP' => $request->IuranJkmKryRP,
            'IuranJhtPrshPersen' => $request->IuranJhtPrshPersen,
            'IursanJhtPrshRp' => $request->IursanJhtPrshRp,
            'IursanJhtKryPersen' => $request->IursanJhtKryPersen,
            'IursanJhtKryRp' => $request->IursanJhtKryRp,
            'IuranJpPrshPersen' => $request->IuranJpPrshPersen,
            'IuranJpPrshRp' => $request->IuranJpPrshRp,
            'IuranJpKryPersen' => $request->IuranJpKryPersen,
            'IuranJpKryRp' => $request->IuranJpKryRp,
            'JmlPrshRp' => $request->JmlPrshRp,
            'JmlKryRp' => $request->JmlKryRp,
            'TotSetoran' => $request->TotSetoran,
            'StatusDok' => $request->StatusDok,
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        // File handling
        if ($request->hasFile('FileDok')) {
            Log::info('File upload detected for document ' . $dokumenBpjsTenagaKerja->IdKode);

            // Delete old file if exists
            if ($dokumenBpjsTenagaKerja->FileDok) {
                Log::debug('Old file: ' . $dokumenBpjsTenagaKerja->FileDok);
                try {
                    Storage::disk('public')->delete('documents/bpjs-tenaga-kerja/' . $dokumenBpjsTenagaKerja->FileDok);
                    Log::debug('Old file deleted successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to delete old file: ' . $e->getMessage());
                }
            }

            $file = $request->file('FileDok');
            Log::debug('New file: ' . $file->getClientOriginalName() . ', size: ' . $file->getSize() . ' bytes, type: ' . $file->getMimeType());

            // Get file extension
            $extension = $file->getClientOriginalExtension();
            Log::debug('File extension: ' . $extension);

            Log::debug('Sanitizing filename components');
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
            Log::debug('Karyawan name for filename: ' . $namaKaryawan);

            // Format berakhir date
            $tglBerakhir = $request->filled('TglBerakhirDok') ? Carbon::parse($request->TglBerakhirDok)->format('Ymd') : 'Permanent';
            Log::debug('Formatted expiry date: ' . $tglBerakhir);

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $namaKaryawan . '_' . $tglBerakhir . '.' . $extension;
            Log::debug('New filename: ' . $fileName);

            try {
                // Store file with the new name
                $file->storeAs('documents/bpjs-tenaga-kerja', $fileName, 'public');
                Log::info('File stored successfully: ' . $fileName);
                $data['FileDok'] = $fileName;
            } catch (\Exception $e) {
                Log::error('Failed to store file: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan file. Silakan coba lagi.');
            }
        }

        try {
            $dokumenBpjsTenagaKerja->update($data);
            Log::info('Document ' . $dokumenBpjsTenagaKerja->IdKode . ' successfully updated');
        } catch (\Exception $e) {
            Log::error('Failed to update document ' . $dokumenBpjsTenagaKerja->IdKode . ': ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan perubahan. Silakan coba lagi.');
        }

        Log::info('Redirecting user after successful update');
        return redirect()->route('dokumen-bpjs-tenaga-kerja.index')
            ->with('success', 'Dokumen BPJS Tenaga Kerja berhasil diperbarui.');
    }

    public function destroy(DokumenBpjsTenagaKerja $dokumenBpjsTenagaKerja)
    {
        // Delete file if exists
        if ($dokumenBpjsTenagaKerja->FileDok) {
            Storage::disk('public')->delete('documents/bpjs-tenaga-kerja/' . $dokumenBpjsTenagaKerja->FileDok);
        }

        $dokumenBpjsTenagaKerja->delete();

        return redirect()->route('dokumen-bpjs-tenaga-kerja.index')
            ->with('success', 'Dokumen BPJS Tenaga Kerja berhasil dihapus.');
    }

    public function download(DokumenBpjsTenagaKerja $dokumenBpjsTenagaKerja)
    {
        if (!$dokumenBpjsTenagaKerja->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = storage_path('app/public/documents/bpjs-tenaga-kerja/' . $dokumenBpjsTenagaKerja->FileDok);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File already has the formatted name
        return response()->download($path, $dokumenBpjsTenagaKerja->FileDok);
    }

    public function viewDocument(DokumenBpjsTenagaKerja $dokumenBpjsTenagaKerja)
    {
        // Ensure file exists
        if (!$dokumenBpjsTenagaKerja->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path
        $filePath = storage_path('app/public/documents/bpjs-tenaga-kerja/' . $dokumenBpjsTenagaKerja->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (in case path in database is different)
            $alternativePath = storage_path('app/public/documents/' . $dokumenBpjsTenagaKerja->FileDok);

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
                'Content-Disposition' => 'inline; filename="' . $dokumenBpjsTenagaKerja->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return response()->download($filePath, $dokumenBpjsTenagaKerja->FileDok);
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
            ->orWhere('NrkKry', 'LIKE', '%' . $term . '%')
            ->select('IdKode', 'NamaKry', 'NrkKry')
            ->limit(10)
            ->get();

        return response()->json($karyawan);
    }
}
