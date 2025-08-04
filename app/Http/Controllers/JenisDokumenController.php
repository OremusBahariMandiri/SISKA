<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\KategoriDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;

class JenisDokumenController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:jenis_dokumen')->only('index', 'show');
        $this->middleware('check.access:jenis_dokumen,tambah')->only('create', 'store');
        $this->middleware('check.access:jenis_dokumen,ubah')->only('edit', 'update');
        $this->middleware('check.access:jenis_dokumen,hapus')->only('destroy');
    }

    public function index()
    {
        $jenisDokumens = JenisDokumen::with('kategoriDokumen')->get();
        return view('jenis-dokumen.index', compact('jenisDokumens'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A07', 'A07DmJenisDok');

        // Get all kategori dokumen for dropdown
        $kategoriDokumens = KategoriDokumen::orderBy('KategoriDok')->get();

        return view('jenis-dokumen.create', compact('newId', 'kategoriDokumens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IdKodeA06' => 'required|exists:A06DmKategoriDok,IdKode',
            'JenisDok' => 'required|unique:A07DmJenisDok,JenisDok',
        ], [
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
            'JenisDok' => $request->JenisDok,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil dibuat.');
    }

    public function show($id)
    {
        $jenisDokumen = JenisDokumen::with(['kategoriDokumen', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('jenis-dokumen.show', compact('jenisDokumen'));
    }

    public function edit($id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);
        $kategoriDokumens = KategoriDokumen::orderBy('KategoriDok')->get();
        return view('jenis-dokumen.edit', compact('jenisDokumen', 'kategoriDokumens'));
    }

    public function update(Request $request, $id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);

        $request->validate([
            'IdKodeA06' => 'required|exists:A06DmKategoriDok,IdKode',
            'JenisDok' => 'required|unique:A07DmJenisDok,JenisDok,'.$id,
        ], [
            'IdKodeA06.required' => 'Kategori dokumen harus dipilih',
            'IdKodeA06.exists' => 'Kategori dokumen tidak valid',
            'JenisDok.required' => 'Nama jenis dokumen harus diisi',
            'JenisDok.unique' => 'Nama jenis dokumen sudah ada'
        ]);

        $jenisDokumen->update([
            'IdKodeA06' => $request->IdKodeA06,
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