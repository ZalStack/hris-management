<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $karyawan = Karyawan::where('email', $request->email)->first();

        if ($karyawan && \Hash::check($request->password, $karyawan->kata_sandi)) {
            Auth::login($karyawan, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Store plain password temporarily for demo purposes
            // In production, NEVER store plain passwords
            session(['temp_password_' . $karyawan->id => $request->password]);

            if ($karyawan->isAdmin() || $karyawan->isHR()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('karyawan.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Clear temp password from session
        if (Auth::check()) {
            session()->forget('temp_password_' . Auth::id());
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}