<?php

namespace App\Http\Controllers;

use App\Models\WilayahKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\GenerateIdTrait;

class WilayahKerjaController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:wilayah-kerja')->only('index');
        $this->middleware('check.access:wilayah-kerja,detail')->only( 'show');
        $this->middleware('check.access:wilayah-kerja,tambah')->only('create', 'store');
        $this->middleware('check.access:wilayah-kerja,ubah')->only('edit', 'update');
        $this->middleware('check.access:wilayah-kerja,hapus')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wilayahKerjas = WilayahKerja::all();
        return view('wilayah-kerja.index', compact('wilayahKerjas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate new ID code
        $newId = $this->generateId('A10', 'A10DmWilayahKrj');

        return view('wilayah-kerja.create', compact('newId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'IdKode' => 'required',
            'GolonganWilker' => 'required|string|max:255',
            'WilayahKerja' => 'required|string|max:255',
            'SingkatanWilker' => 'required|string|max:50',
        ]);

        // Dapatkan IdKode user yang sedang login
        $user = Auth::user();
        $userIdKode = $user->IdKode;

        WilayahKerja::create([
            'IdKode' => $request->IdKode,
            'GolonganWilker' => $request->GolonganWilker,
            'WilayahKerja' => $request->WilayahKerja,
            'SingkatanWilker' => $request->SingkatanWilker,
            'created_by' => $userIdKode,
            'updated_by' => $userIdKode,
        ]);

        return redirect()->route('wilayah-kerja.index')
            ->with('success', 'Wilayah Kerja berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WilayahKerja $wilayahKerja)
    {
        return view('wilayah-kerja.show', compact('wilayahKerja'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WilayahKerja $wilayahKerja)
    {
        return view('wilayah-kerja.edit', compact('wilayahKerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WilayahKerja $wilayahKerja)
    {
        $request->validate([
            'GolonganWilker' => 'required|string|max:255',
            'WilayahKerja' => 'required|string|max:255',
            'SingkatanWilker' => 'required|string|max:50',
        ]);

        // Dapatkan IdKode user yang sedang login
        $user = Auth::user();
        $userIdKode = $user->IdKode;

        $wilayahKerja->update([
            'GolonganWilker' => $request->GolonganWilker,
            'WilayahKerja' => $request->WilayahKerja,
            'SingkatanWilker' => $request->SingkatanWilker,
            'updated_by' => $userIdKode,
        ]);

        return redirect()->route('wilayah-kerja.index')
            ->with('success', 'Wilayah Kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WilayahKerja $wilayahKerja)
    {
        try {
            $wilayahKerja->delete();
            return redirect()->route('wilayah-kerja.index')
                ->with('success', 'Wilayah Kerja berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('wilayah-kerja.index')
                ->with('error', 'Wilayah Kerja tidak dapat dihapus karena masih digunakan.');
        }
    }
}