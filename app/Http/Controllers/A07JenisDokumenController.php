<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\KategoriDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;

class A07JenisDokumenController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:jenis-dokumen')->only('index');
        $this->middleware('check.access:jenis-dokumen,detail')->only('show');
        $this->middleware('check.access:jenis-dokumen,tambah')->only('create', 'store');
        $this->middleware('check.access:jenis-dokumen,ubah')->only('edit', 'update');
        $this->middleware('check.access:jenis-dokumen,hapus')->only('destroy');
    }

    public function index()
    {
        // Mengubah query untuk mengurutkan berdasarkan GolDok dari terkecil ke terbesar
        $jenisDokumens = JenisDokumen::with('kategoriDokumen')
            ->orderBy('GolDok', 'asc')
            ->get();

        // Sisa kode tetap sama
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
                $access = $user->userAccess()->where('MenuAcs', 'jenis-dokumen')->first();
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

        return view('data-master.a07-jenis-dokumen.index', compact('jenisDokumens', 'userPermissions'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A07', 'A07DmJenisDok');

        // Get all kategori dokumen for dropdown
        $kategoriDokumens = KategoriDokumen::orderBy('KategoriDok')->get();

        return view('data-master.a07-jenis-dokumen.create', compact('newId', 'kategoriDokumens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IdKodeA06' => 'required|exists:A06DmKategoriDok,IdKode',
            'GolDok' => 'required',
            'JenisDok' => 'required|unique:A07DmJenisDok,JenisDok',
        ], [
            'GolDok.required' => 'Golongan dokumen harus dipilih',
            'IdKodeA06.required' => 'Kategori dokumen harus dipilih',
            'IdKodeA06.exists' => 'Kategori dokumen tidak valid',
            'JenisDok.required' => 'Nama jenis dokumen harus diisi',
            'JenisDok.unique' => 'Nama jenis dokumen sudah ada'
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A07', 'A07DmJenisDok');
        } else {
            $IdKode = $request->IdKode;
        }

        JenisDokumen::create([
            'IdKode' => $IdKode,
            'IdKodeA06' => $request->IdKodeA06,
            'GolDok' => $request->GolDok,
            'JenisDok' => $request->JenisDok,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil dibuat.');
    }

    public function show($id)
    {
        $jenisDokumen = JenisDokumen::with(['kategoriDokumen', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('data-master.a07-jenis-dokumen.show', compact('jenisDokumen'));
    }

    public function edit($id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);
        $kategoriDokumens = KategoriDokumen::orderBy('KategoriDok')->get();
        return view('data-master.a07-jenis-dokumen.edit', compact('jenisDokumen', 'kategoriDokumens'));
    }

    public function update(Request $request, $id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);

        $request->validate([
            'IdKodeA06' => 'required|exists:A06DmKategoriDok,IdKode',
            'JenisDok' => 'required|unique:A07DmJenisDok,JenisDok,' . $id,
            'GolDok' => 'required',
        ], [
            'GolDok.required' => 'Golongan dokumen harus dipilih',
            'IdKodeA06.required' => 'Kategori dokumen harus dipilih',
            'IdKodeA06.exists' => 'Kategori dokumen tidak valid',
            'JenisDok.required' => 'Nama jenis dokumen harus diisi',
            'JenisDok.unique' => 'Nama jenis dokumen sudah ada'
        ]);

        $jenisDokumen->update([
            'IdKodeA06' => $request->IdKodeA06,
            'GolDok' => $request->GolDok,
            'JenisDok' => $request->JenisDok,
            'updated_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);

        // Check if there are related documents (if you have document model that relates to JenisDokumen)
        // If you have a Dokumen model with a relationship to JenisDokumen, uncomment this
        /*
        if ($jenisDokumen->dokumens()->count() > 0) {
            return redirect()->route('jenis-dokumen.index')
                ->with('error', 'Jenis dokumen tidak dapat dihapus karena masih memiliki dokumen terkait.');
        }
        */

        $jenisDokumen->delete();

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}
