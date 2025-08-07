<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\KategoriDokumen;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:jabatan')->only('index', 'show');
        $this->middleware('check.access:jabatan,tambah')->only('create', 'store');
        $this->middleware('check.access:jabatan,ubah')->only('edit', 'update');
        $this->middleware('check.access:jabatan,hapus')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatan = Jabatan::all();
        return view('jabatan.index', compact('jabatan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A08', 'A08DmJabatan');

        return view('jabatan.create', compact('newId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'GolonganJbt' => 'required',
            'Jabatan' => 'required',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A08', 'A08DmJabatan');
        } else {
            $IdKode = $request->IdKode;
        }

        Jabatan::create([
            'IdKode' => $IdKode,
            'GolonganJbt' => $request->GolonganJbt,
            'Jabatan' => $request->Jabatan,
            'SingkatanJbtn' => $request->SingkatanJbtn,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil dibuat.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('jabatan.show', compact('jabatan'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('jabatan.edit', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $request->validate([
            'GolonganJbt' => 'required',
            'Jabatan' => 'required',
        ]);

        $jabatan->update([
            'GolonganJbt' => $request->GolonganJbt,
            'Jabatan' => $request->Jabatan,
            'SingkatanJbtn' => $request->SingkatanJbtn,
            'updated_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('jabatan.index')
            ->with('success', 'jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);


        $jabatan->delete();

        return redirect()->route('jabatan.index')
            ->with('success', 'jabatan berhasil dihapus.');
    }
}
