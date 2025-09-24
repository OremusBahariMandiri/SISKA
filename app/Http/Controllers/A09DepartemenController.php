<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\GenerateIdTrait;

class A09DepartemenController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:departemen')->only('index');
        $this->middleware('check.access:departemen,detail')->only('show');
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
                $access = $user->userAccess()->where('MenuAcs', 'departemen')->first();
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

        return view('data-master.a09-departemen.index', compact('departemens', 'userPermissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate new ID code
        $newId = $this->generateId('A09', 'A09DmDepartemen');

        return view('data-master.a09-departemen.create', compact('newId'));
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
        return view('data-master.a09-departemen.show', compact('departemen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $departemen = Departemen::findOrFail($id);
        return view('data-master.a09-departemen.edit', compact('departemen'));
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
    public function destroy($id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->delete();

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}