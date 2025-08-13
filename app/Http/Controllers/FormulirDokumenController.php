<?php

namespace App\Http\Controllers;

use App\Models\FormulirDokumen;
use App\Models\KategoriDokumen;
use App\Models\JenisDokumen;
use App\Models\Perusahaan;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FormulirDokumenController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:formulir-dokumen')->only('index', 'show');
        $this->middleware('check.access:formulir-dokumen,tambah')->only('create', 'store');
        $this->middleware('check.access:formulir-dokumen,ubah')->only('edit', 'update');
        $this->middleware('check.access:formulir-dokumen,hapus')->only('destroy');
        $this->middleware('check.access:formulir-dokumen,download')->only('download');
    }

    public function index()
    {
        $formulirDokumen = FormulirDokumen::with(['kategori'])->get();

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
                $access = $user->userAccess()->where('MenuAcs', 'formulir-dokumen')->first();
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

        return view('formulir-dokumen.index', compact('formulirDokumen', 'userPermissions'));
    }

    public function create()
    {
        $kategoriDokumen = KategoriDokumen::all();
        $jenisDokumen = JenisDokumen::with('kategoriDokumen')->get();
        $perusahaan = Perusahaan::all(); // Add this line

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
        $newId = $this->generateId('A11', 'A11DmFormulirDok');

        return view('formulir-dokumen.create', compact('kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'newId', 'perusahaan'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:A11DmFormulirDok,NoRegDok',
            'NamaPrsh' => 'required|exists:A03DmPerusahaan,IdKode', // Add this validation
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'FileDok' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'StatusDok' => 'required',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'NamaPrsh.required' => 'Perusahaan harus dipilih', // Add this message
            'NamaPrsh.exists' => 'Perusahaan yang dipilih tidak valid', // Add this message
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'FileDok.required' => 'File Dokumen harus diunggah',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
            'StatusDok.required' => 'Status Dokumen harus dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A11', 'A11DmFormulirDok');
        } else {
            $IdKode = $request->IdKode;
        }

        $data = [
            'IdKode' => $IdKode,
            'NoRegDok' => $request->NoRegDok,
            'NamaPrsh' => $request->NamaPrsh,
            'KategoriDok' => $request->KategoriDok,
            'JenisDok' => $request->JenisDok,
            'KetDok' => $request->KetDok,
            'TglTerbitDok' => $request->TglTerbitDok,
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

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);
            $formattedDate = Carbon::parse($request->TglTerbitDok)->format('Ymd');

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $formattedDate . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/formulir', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        FormulirDokumen::create($data);

        return redirect()->route('formulir-dokumen.index')
            ->with('success', 'Formulir Dokumen berhasil dibuat.');
    }

    public function show($id)
    {
        $formulirDokumen = FormulirDokumen::findOrFail($id);
        $formulirDokumen->load(['kategori']);
        return view('formulir-dokumen.show', compact('formulirDokumen'));
    }

    public function edit($id)
    {
        $formulirDokumen = FormulirDokumen::findOrFail($id);
        $kategoriDokumen = KategoriDokumen::all();
        $perusahaan = Perusahaan::all(); // Add this line
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

        return view('formulir-dokumen.edit', compact('formulirDokumen', 'kategoriDokumen', 'jenisDokumen', 'jenisDokumenByKategori', 'perusahaan'));
    }

    public function update(Request $request, $id)
    {
        $formulirDokumen = FormulirDokumen::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'NoRegDok' => 'required|unique:A11DmFormulirDok,NoRegDok,' . $formulirDokumen->id . ',id',
            'NamaPrsh' => 'required|exists:A03DmPerusahaan,IdKode', // Add this validation
            'KategoriDok' => 'required',
            'JenisDok' => 'required',
            'TglTerbitDok' => 'required|date',
            'FileDok' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'StatusDok' => 'required',
        ], [
            'NoRegDok.required' => 'Nomor Registrasi harus diisi',
            'NoRegDok.unique' => 'Nomor Registrasi sudah digunakan',
            'NamaPrsh.required' => 'Perusahaan harus dipilih', // Add this message
            'NamaPrsh.exists' => 'Perusahaan yang dipilih tidak valid', // Add this message
            'KategoriDok.required' => 'Kategori Dokumen harus dipilih',
            'JenisDok.required' => 'Jenis Dokumen harus dipilih',
            'TglTerbitDok.required' => 'Tanggal Terbit harus diisi',
            'FileDok.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG',
            'StatusDok.required' => 'Status Dokumen harus dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang dimasukkan.');
        }

        $data = [
            'NoRegDok' => $request->NoRegDok,
            'NamaPrsh' => $request->NamaPrsh,
            'KategoriDok' => $request->KategoriDok,
            'JenisDok' => $request->JenisDok,
            'KetDok' => $request->KetDok,
            'TglTerbitDok' => $request->TglTerbitDok,
            'StatusDok' => $request->StatusDok,
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        if ($request->hasFile('FileDok')) {
            // Delete old file if exists
            if ($formulirDokumen->FileDok) {
                Storage::disk('public')->delete('documents/formulir/' . $formulirDokumen->FileDok);
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

            // Clean filename components
            $cleanNoReg = $sanitizeFileName($request->NoRegDok);
            $cleanJenisDok = $sanitizeFileName($request->JenisDok);
            $formattedDate = Carbon::parse($request->TglTerbitDok)->format('Ymd');

            // Build the filename
            $fileName = $cleanNoReg . '_' . $cleanJenisDok . '_' . $formattedDate . '.' . $extension;

            // Store file with the new name
            $file->storeAs('documents/formulir', $fileName, 'public');
            $data['FileDok'] = $fileName;
        }

        $formulirDokumen->update($data);

        return redirect()->route('formulir-dokumen.index')
            ->with('success', 'Formulir Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $formulirDokumen = FormulirDokumen::findOrFail($id);

        // Delete file if exists
        if ($formulirDokumen->FileDok) {
            Storage::disk('public')->delete('documents/formulir/' . $formulirDokumen->FileDok);
        }

        $formulirDokumen->delete();

        return redirect()->route('formulir-dokumen.index')
            ->with('success', 'Formulir Dokumen berhasil dihapus.');
    }

    public function download($id)
    {
        $formulirDokumen = FormulirDokumen::findOrFail($id);

        if (!$formulirDokumen->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = storage_path('app/public/documents/formulir/' . $formulirDokumen->FileDok);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File already has the formatted name
        return response()->download($path, $formulirDokumen->FileDok);
    }

    public function viewDocument($id)
    {
        $formulirDokumen = FormulirDokumen::findOrFail($id);

        // Ensure file exists
        if (!$formulirDokumen->FileDok) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // File path
        $filePath = storage_path('app/public/documents/formulir/' . $formulirDokumen->FileDok);

        // Check if file exists
        if (!file_exists($filePath)) {
            // Try alternative path (in case path in database is different)
            $alternativePath = storage_path('app/public/documents/' . $formulirDokumen->FileDok);

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
                'Content-Disposition' => 'inline; filename="' . $formulirDokumen->FileDok . '"'
            ]);
        }

        // If file cannot be previewed, redirect to download
        return response()->download($filePath, $formulirDokumen->FileDok);
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
