<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\GenerateIdTrait;

class DepartemenController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:departemen')->only('index', 'show');
        $this->middleware('check.access:departemen,tambah')->only('create', 'store');
        $this->middleware('check.access:departemen,ubah')->only('edit', 'update');
        $this->middleware('check.access:departemen,hapus')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemens = Departemen::all();
        return view('departemen.index', compact('departemens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate new ID code
        $newId = $this->generateId('A09', 'A09DmDepartemen');

        return view('departemen.create', compact('newId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'IdKode' => 'required',
            'GolonganDep' => 'required|string|max:255',
            'Departemen' => 'required|string|max:255',
            'SingkatanDep' => 'required|string|max:50',
        ]);

        // Dapatkan IdKode user yang sedang login
        $user = Auth::user();
        $userIdKode = $user->IdKode;

        Departemen::create([
            'IdKode' => $request->IdKode,
            'GolonganDep' => $request->GolonganDep,
            'Departemen' => $request->Departemen,
            'SingkatanDep' => $request->SingkatanDep,
            'created_by' => $userIdKode,
            'updated_by' => $userIdKode,
        ]);

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $departemen = Departemen::findOrFail($id);
        return view('departemen.show', compact('departemen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $departemen = Departemen::findOrFail($id);
        return view('departemen.edit', compact('departemen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);

        $request->validate([
            'GolonganDep' => 'required|string|max:255',
            'Departemen' => 'required|string|max:255',
            'SingkatanDep' => 'required|string|max:50',
        ]);

        // Dapatkan IdKode user yang sedang login
        $user = Auth::user();
        $userIdKode = $user->IdKode;

        $departemen->update([
            'GolonganDep' => $request->GolonganDep,
            'Departemen' => $request->Departemen,
            'SingkatanDep' => $request->SingkatanDep,
            'updated_by' => $userIdKode,
        ]);

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Departemen $departemen)
    // {
    //     try {
    //         $departemen->delete();
    //         return redirect()->route('departemen.index')
    //             ->with('success', 'Departemen berhasil dihapus.');
    //     } catch (\Exception $e) {
    //         return redirect()->route('departemen.index')
    //             ->with('error', 'Departemen tidak dapat dihapus karena masih digunakan.');
    //     }
    // }

    public function destroy($id)
    {
        $departemen = Departemen::findOrFail($id);


        $departemen->delete();

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
