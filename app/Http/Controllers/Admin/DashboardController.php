<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::count();
        $karyawanAktif = Karyawan::where('status', 'aktif')->count();
        $karyawanPending = Karyawan::where('status', 'pending')->count();
        $adminHrCount = Karyawan::whereIn('role', ['admin', 'hr'])->count();
        
        return view('admin.dashboard', compact('totalKaryawan', 'karyawanAktif', 'karyawanPending', 'adminHrCount'));
    }

    public function karyawan()
    {
        $karyawans = Karyawan::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.karyawan.index', compact('karyawans'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:karyawans,nip',
            'email' => 'required|email|unique:karyawans,email',
            'nama_lengkap' => 'required|string|max:255',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,hr,karyawan',
        ]);

        $karyawan = Karyawan::create([
            'nip' => $request->nip,
            'email' => $request->email,
            'kata_sandi' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'role' => $request->role,
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function editKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return response()->json($karyawan);
    }

    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        $request->validate([
            'nip' => 'required|unique:karyawans,nip,' . $id,
            'email' => 'required|email|unique:karyawans,email,' . $id,
            'nama_lengkap' => 'required|string|max:255',
            'role' => 'required|in:admin,hr,karyawan',
        ]);

        $updateData = [
            'nip' => $request->nip,
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['kata_sandi'] = Hash::make($request->password);
        }

        $karyawan->update($updateData);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroyKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        // Prevent deleting own account
        if ($karyawan->id === auth()->id()) {
            return redirect()->route('admin.karyawan')->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }
        
        $karyawan->delete();
        
        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil dihapus');
    }

    public function showKaryawanPassword($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return response()->json([
            'hashed_password' => $karyawan->kata_sandi,
            'message' => 'This is the hashed password. In production, password reset functionality should be used instead.'
        ]);
    }
}