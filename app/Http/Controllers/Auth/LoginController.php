<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\RecaptchaRule;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        // Validasi input termasuk reCAPTCHA
        $request->validate([
            'NikKry' => ['required', 'string'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required', new RecaptchaRule],
        ], [
            'NikKry.required' => 'NRK Karyawan wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'g-recaptcha-response.required' => 'Silakan centang "Saya bukan robot"',
        ]);

        // Cari user berdasarkan NikKry
        $user = User::where('NikKry', $request->NikKry)->first();

        // Debug: Uncomment untuk debugging
        // dd([
        //     'input_password' => $request->password,
        //     'stored_password' => $user ? $user->PasswordKry : 'User not found',
        //     'hash_check' => $user ? Hash::check($request->password, $user->PasswordKry) : false
        // ]);

        // Cek apakah user ditemukan dan password cocok
        if ($user && Hash::check($request->password, $user->PasswordKry)) {
            // Login berhasil
            Auth::login($user, $request->boolean('remember'));

            $request->session()->regenerate();

            // Redirect ke home atau intended URL
            return redirect()->intended('/home');
        }

        // Login gagal - kembalikan dengan error
        return back()->withErrors([
            'NikKry' => 'NRK atau password yang Anda masukkan tidak valid.',
        ])->onlyInput('NikKry');
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}