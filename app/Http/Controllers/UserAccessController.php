<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:hak_akses')->only('edit', 'show');
        $this->middleware('check.access:hak_akses,ubah')->only('update');
    }

    /**
     * Show access rights for a user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        // Redirect to edit as they have the same view
        return redirect()->route('users.index', $user->id);
    }

    /**
     * Show the form for editing user access.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $menuList = [
            'pengguna' => 'Pengguna',
            'hak_akses' => 'Hak Akses',
            'perusahaan' => 'Perusahaan',
            'karyawan' => 'Karyawan',
            'keluarga-karyawan' => 'Keluarga Karyawan',
            'kategori-dokumen' => 'Kategori Dokumen',
            'jenis-dokumen' => 'Jenis Dokumen',
            'jabatan' => 'Jabatan',
            'departemen' => 'Departemen',
            'wilayah-kerja' => 'Wilayah Kerja',
            'formulir-dokumen' => 'Formulir Dokumen',
            'dokumen-karyawan' => 'Dokumen Karyawan',
            'dokumen-kontrak' => 'Dokumen Kontrak',
            'dokumen-karir' => 'Dokumen Karir',
            'dokumen-legalitas' => 'Dokumen Legalitas',
            'dokumen-bpjs-kesehatan' => 'Dokumen BPJS Kesehatan',
            'dokumen-bpjs-tenaga-kerja' => 'Dokumen BPJS Tenaga Kerja',

        // Add other menus here
        ];

        // Prepare user access data
        $userAccess = [];
        foreach ($user->userAccess as $access) {
            $userAccess[$access->MenuAcs] = [
                'tambah' => $access->TambahAcs,
                'ubah' => $access->UbahAcs,
                'hapus' => $access->HapusAcs,
                'download' => $access->DownloadAcs,
                'detail' => $access->DetailAcs,
                'monitoring' => $access->MonitoringAcs,
            ];
        }

        return view('user-access.edit', compact('user', 'menuList', 'userAccess'));
    }

    /**
     * Update the user access in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Update admin status if changed
        if ($user->is_admin != $request->has('is_admin')) {
            $user->update([
                'is_admin' => $request->has('is_admin') ? 1 : 0,
                'updated_by' => auth()->user()->IdKode ?? null,
            ]);
        }

        // If user is now an admin, delete all access records as they're not needed
        if ($request->has('is_admin')) {
            UserAccess::where('IdKodeA01', $user->IdKode)->delete();
            return redirect()->route('user-access.edit', $user->id)
                ->with('success', 'Pengguna berhasil diubah menjadi Administrator dengan akses penuh.');
        }

        // Delete existing access
        UserAccess::where('IdKodeA01', $user->IdKode)->delete();

        // Create new access if menu_access is provided
        if ($request->has('menu_access')) {
            foreach ($request->menu_access as $menu => $permissions) {
                UserAccess::create([
                    'IdKodeA01' => $user->IdKode,
                    'MenuAcs' => $menu,
                    'TambahAcs' => isset($permissions['tambah']) ? 1 : 0,
                    'UbahAcs' => isset($permissions['ubah']) ? 1 : 0,
                    'HapusAcs' => isset($permissions['hapus']) ? 1 : 0,
                    'DownloadAcs' => isset($permissions['download']) ? 1 : 0,
                    'DetailAcs' => isset($permissions['detail']) ? 1 : 0,
                    'MonitoringAcs' => isset($permissions['monitoring']) ? 1 : 0,
                    'created_by' => auth()->user()->IdKode ?? null,
                ]);
            }
        }

        return redirect()->route('users.index', $user->id)
            ->with('success', 'Hak akses pengguna berhasil diperbarui.');
    }
}