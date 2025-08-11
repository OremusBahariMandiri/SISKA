<?php

namespace App\Http\Controllers;

use App\Models\KategoriDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;

class KategoriDokumenController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:kategori-dokumen')->only('index', 'show');
        $this->middleware('check.access:kategori-dokumen,tambah')->only('create', 'store');
        $this->middleware('check.access:kategori-dokumen,ubah')->only('edit', 'update');
        $this->middleware('check.access:kategori-dokumen,hapus')->only('destroy');
    }

    public function index()
    {
        $kategoriDokumens = KategoriDokumen::all();

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
                $access = $user->userAccess()->where('MenuAcs', 'kategori-dokumen')->first();
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

        return view('kategori-dokumen.index', compact('kategoriDokumens', 'userPermissions'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A06', 'A06DmKategoriDok');

        return view('kategori-dokumen.create', compact('newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'KategoriDok' => 'required',
            'GolDok' => 'required',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A06', 'A06DmKategoriDok');
        } else {
            $IdKode = $request->IdKode;
        }

        KategoriDokumen::create([
            'IdKode' => $IdKode,
            'KategoriDok' => $request->KategoriDok,
            'GolDok' => $request->GolDok,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('kategori-dokumen.index')
            ->with('success', 'Kategori dokumen berhasil dibuat.');
    }

    public function show($id)
    {
        $kategoriDokumen = KategoriDokumen::findOrFail($id);
        return view('kategori-dokumen.show', compact('kategoriDokumen'));
    }

    public function edit($id)
    {
        $kategoriDokumen = KategoriDokumen::findOrFail($id);
        return view('kategori-dokumen.edit', compact('kategoriDokumen'));
    }

    public function update(Request $request, $id)
    {
        $kategoriDokumen = KategoriDokumen::findOrFail($id);

        $request->validate([
            'KategoriDok' => 'required',
            'GolDok' => 'required',
        ]);

        $kategoriDokumen->update([
            'KategoriDok' => $request->KategoriDok,
            'GolDok' => $request->GolDok,
            'updated_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('kategori-dokumen.index')
            ->with('success', 'Kategori dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategoriDokumen = KategoriDokumen::findOrFail($id);

        // Check if there are related JenisDokumen records
        if ($kategoriDokumen->jenisDokumen()->count() > 0) {
            return redirect()->route('kategori-dokumen.index')
                ->with('error', 'Kategori dokumen tidak dapat dihapus karena masih memiliki Jenis Dokumen terkait.');
        }

        $kategoriDokumen->delete();

        return redirect()->route('kategori-dokumen.index')
            ->with('success', 'Kategori dokumen berhasil dihapus.');
    }
}