<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Karyawan::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate NIP automatically
        $lastKaryawan = Karyawan::orderBy('id', 'desc')->first();
        $newNip = 'EMP' . str_pad(($lastKaryawan ? $lastKaryawan->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        $karyawan = Karyawan::create([
            'nip' => $newNip,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'kata_sandi' => Hash::make($request->password),
            'role' => 'karyawan',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        event(new Registered($karyawan));

        Auth::login($karyawan);

        if ($karyawan->isAdmin() || $karyawan->isHR()) {
            return redirect(route('admin.dashboard', absolute: false));
        }

        return redirect(route('karyawan.dashboard', absolute: false));
    }
}