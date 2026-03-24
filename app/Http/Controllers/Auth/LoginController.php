<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $karyawan = Karyawan::where('email', $credentials['email'])->first();

        if ($karyawan && Hash::check($credentials['password'], $karyawan->kata_sandi)) {
            Auth::login($karyawan, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($karyawan->isAdmin() || $karyawan->isHR()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('karyawan.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}