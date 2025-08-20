<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:perusahaan')->only('index');
        $this->middleware('check.access:perusahaan,detail')->only('show');
        $this->middleware('check.access:perusahaan,tambah')->only('create', 'store');
        $this->middleware('check.access:perusahaan,ubah')->only('edit', 'update');
        $this->middleware('check.access:perusahaan,hapus')->only('destroy');
    }

    public function index()
    {
        $perusahaans = Perusahaan::all();

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
                $access = $user->userAccess()->where('MenuAcs', 'perusahaan')->first();
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

        return view('perusahaan.index', compact('perusahaans', 'userPermissions'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A03', 'A03DmPerusahaan');

        return view('perusahaan.create', compact('newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NamaPrsh' => 'required',
            'AlamatPrsh' => 'required',
            'TelpPrsh' => 'required',
            'EmailPrsh' => 'required|email',
            'BidangUsh' => 'required',
            'DirekturUtm' => 'required',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A03', 'A03DmPerusahaan');
        } else {
            $IdKode = $request->IdKode;
        }

        $perusahaan = Perusahaan::create([
            'IdKode' => $IdKode,
            'NamaPrsh' => $request->NamaPrsh,
            'SingkatanPrsh' => $request->SingkatanPrsh,
            'AlamatPrsh' => $request->AlamatPrsh,
            'TelpPrsh' => $request->TelpPrsh,
            'TelpPrsh2' => $request->TelpPrsh2,
            'EmailPrsh' => $request->EmailPrsh,
            'EmailPrsh2' => $request->EmailPrsh2,
            'WebPrsh' => $request->WebPrsh,
            'TglBerdiri' => $request->TglBerdiri,
            'BidangUsh' => $request->BidangUsh,
            'IzinUsh' => $request->IzinUsh,
            'GolonganUsh' => $request->GolonganUsh,
            'DirekturUtm' => $request->DirekturUtm,
            'Direktur' => $request->Direktur,
            'KomisarisUtm' => $request->KomisarisUtm,
            'Komisaris' => $request->Komisaris,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('perusahaan.index')
            ->with('success', 'Perusahaan berhasil dibuat.');
    }

    public function show($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        return view('perusahaan.show', compact('perusahaan'));
    }

    public function edit($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        return view('perusahaan.edit', compact('perusahaan'));
    }

    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::findOrFail($id);

        $request->validate([
            'NamaPrsh' => 'required',
            'AlamatPrsh' => 'required',
            'TelpPrsh' => 'required',
            'EmailPrsh' => 'required|email',
            'BidangUsh' => 'required',
            'DirekturUtm' => 'required',
        ]);

        $data = [
            'NamaPrsh' => $request->NamaPrsh,
            'SingkatanPrsh' => $request->SingkatanPrsh,
            'AlamatPrsh' => $request->AlamatPrsh,
            'TelpPrsh' => $request->TelpPrsh,
            'TelpPrsh2' => $request->TelpPrsh2,
            'EmailPrsh' => $request->EmailPrsh,
            'EmailPrsh2' => $request->EmailPrsh2,
            'WebPrsh' => $request->WebPrsh,
            'TglBerdiri' => $request->TglBerdiri,
            'BidangUsh' => $request->BidangUsh,
            'IzinUsh' => $request->IzinUsh,
            'GolonganUsh' => $request->GolonganUsh,
            'DirekturUtm' => $request->DirekturUtm,
            'Direktur' => $request->Direktur,
            'KomisarisUtm' => $request->KomisarisUtm,
            'Komisaris' => $request->Komisaris,
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        $perusahaan->update($data);

        return redirect()->route('perusahaan.index')
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $perusahaan->delete();

        return redirect()->route('perusahaan.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
