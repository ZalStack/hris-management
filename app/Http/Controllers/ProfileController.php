<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $karyawan = auth()->user();
        return view('profile.edit', compact('karyawan'));
    }

    public function update(Request $request)
    {
        $karyawan = auth()->user();
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:karyawans,email,' . $karyawan->id,
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'nik' => 'nullable|string|max:16|unique:karyawans,nik,' . $karyawan->id,
            'npwp' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|max:10',
            'agama' => 'nullable|string|max:20',
            'status_pernikahan' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'universitas' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:100',
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . date('Y'),
            'nama_kontak_darurat' => 'nullable|string|max:100',
            'telepon_kontak_darurat' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['foto_profil']);

        if ($request->hasFile('foto_profil')) {
            if ($karyawan->foto_profil) {
                Storage::disk('public')->delete($karyawan->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profile-photos', 'public');
            $data['foto_profil'] = $path;
        }

        $karyawan->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile berhasil diupdate');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $karyawan = auth()->user();

        if (!Hash::check($request->current_password, $karyawan->kata_sandi)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        $karyawan->update([
            'kata_sandi' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diubah');
    }
}