<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccess;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class A01UserController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:pengguna')->only('index');
        $this->middleware('check.access:pengguna,detail')->only('show');
        $this->middleware('check.access:pengguna,tambah')->only('create', 'store');
        $this->middleware('check.access:pengguna,ubah')->only('edit', 'update');
        $this->middleware('check.access:pengguna,hapus')->only('destroy');
    }

    public function index()
    {
        $users = User::all();
        return view('data-master.a01-users.index', compact('users'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A01', 'A01DmUser');

        return view('data-master.a01-users.create', compact('newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NikKry' => 'required',
            'NamaKry' => 'required',
            'DepartemenKry' => 'required',
            'JabatanKry' => 'required',
            'WilkerKry' => 'required',
            'PasswordKry' => 'required|min:6|confirmed',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A01', 'A01DmUser');
        } else {
            $IdKode = $request->IdKode;
        }

        $user = User::create([
            'IdKode' => $IdKode,
            'NikKry' => $request->NikKry,
            'NamaKry' => $request->NamaKry,
            'DepartemenKry' => $request->DepartemenKry,
            'JabatanKry' => $request->JabatanKry,
            'WilkerKry' => $request->WilkerKry,
            'PasswordKry' => $request->PasswordKry, // Hash is done via mutator in model
            'is_admin' => $request->has('is_admin') ? 1 : 0,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dibuat. Anda dapat mengatur hak akses melalui tombol kunci.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('data-master.a01-users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('data-master.a01-users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validationRules = [
            'NikKry' => 'required',
            'NamaKry' => 'required',
            'DepartemenKry' => 'required',
            'JabatanKry' => 'required',
            'WilkerKry' => 'required',
        ];

        // Add password validation rules if password is being updated
        if ($request->filled('PasswordKry')) {
            $validationRules['PasswordKry'] = 'required|min:6|confirmed';
        }

        $request->validate($validationRules);

        $data = [
            'NikKry' => $request->NikKry,
            'NamaKry' => $request->NamaKry,
            'DepartemenKry' => $request->DepartemenKry,
            'JabatanKry' => $request->JabatanKry,
            'WilkerKry' => $request->WilkerKry,
            'is_admin' => $request->has('is_admin') ? 1 : 0,
            'updated_by' => auth()->user()->IdKode ?? null,
        ];

        if ($request->filled('PasswordKry')) {
            $data['PasswordKry'] = $request->PasswordKry; // Hash is done via mutator in model
        }

        $user->update($data);

        // If user was changed to admin, remove all user access records
        if ($user->is_admin) {
            UserAccess::where('IdKodeA01', $user->IdKode)->delete();
        }

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete user access
        UserAccess::where('IdKodeA01', $user->IdKode)->delete();

        // Delete user
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}