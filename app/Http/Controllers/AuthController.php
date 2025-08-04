<?php

namespace App\Http\Controllers;

use App\Models\A01DmUser;
use App\Models\A02DmUserAccess;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'nik_kry' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('nik_kry', $request->nik_kry)->first();

        if (!$user || !Hash::check($request->password, $user->password_kry)) {
            return back()->withErrors([
                'nik_kry' => 'NIK Karyawan atau Password salah.',
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/home');
    }

    /**
     * Handle user logout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Show the registration form
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'nik_kry' => 'required|unique:a01_dm_users,nik_kry',
            'nama_kry' => 'required',
            'departemen_kry' => 'required',
            'jabatan_kry' => 'required',
            'wilker_kry' => 'required',
            'password' => 'required|min:6|confirmed',
            'is_admin' => 'boolean',
        ]);

        // Create new user
        $user = new A01DmUser();
        $user->id_kode = 'USR' . Str::random(8);
        $user->nik_kry = $request->nik_kry;
        $user->nama_kry = $request->nama_kry;
        $user->departemen_kry = $request->departemen_kry;
        $user->jabatan_kry = $request->jabatan_kry;
        $user->wilker_kry = $request->wilker_kry;
        $user->password_kry = Hash::make($request->password);
        $user->is_admin = $request->has('is_admin') ? 1 : 0;
        $user->created_by = Auth::user() ? Auth::user()->id_kode : 'SYSTEM';
        $user->save();

        // Create user access
        $userAccess = new A02DmUserAccess();
        $userAccess->id_kode_a01 = $user->id_kode;
        $userAccess->menu_acs = true; // Default access
        $userAccess->tambah_acs = $user->is_admin;
        $userAccess->ubah_acs = $user->is_admin;
        $userAccess->hapus_acs = $user->is_admin;
        $userAccess->download_acs = true;
        $userAccess->detail_acs = true;
        $userAccess->monitoring_acs = $user->is_admin;
        $userAccess->created_by = $user->created_by;
        $userAccess->save();

        // Assign default role
        PermissionService::assignDefaultRoleToUser($user);

        // Sync permissions
        PermissionService::syncUserAccessWithPermissions($userAccess);

        return redirect('/login')->with('success', 'Registrasi berhasil. Silahkan login.');
    }

    /**
     * Show the password reset request form
     *
     * @return \Illuminate\View\View
     */
    public function showResetRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Process the password reset request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'nik_kry' => 'required|exists:a01_dm_users,nik_kry',
        ]);

        // In a real application, you would send an email with a reset link
        // For now, we'll just redirect with a success message

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Show the password reset form
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Process the password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'nik_kry' => 'required|exists:a01_dm_users,nik_kry',
            'password' => 'required|min:6|confirmed',
        ]);

        // In a real application, you would verify the token and reset the password
        // For now, we'll just redirect with a success message

        return redirect('/login')->with('status', 'Password Anda telah diubah.');
    }
}