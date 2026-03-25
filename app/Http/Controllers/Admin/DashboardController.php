<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::count();
        $totalDepartemen = Departemen::count();
        $totalJabatan = Jabatan::count();
        $karyawanAktif = Karyawan::where('status', 'aktif')->count();
        
        return view('admin.dashboard', compact('totalKaryawan', 'totalDepartemen', 'totalJabatan', 'karyawanAktif'));
    }

    public function karyawan()
    {
        $karyawans = Karyawan::with(['jabatanSaatIni.jabatan.departemen'])->paginate(10);
        $departemens = Departemen::all();
        $jabatans = Jabatan::with('departemen')->get();
        
        return view('admin.karyawan.index', compact('karyawans', 'departemens', 'jabatans'));
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
            'plain_password' => $request->password, // Store plain password temporarily
            'nama_lengkap' => $request->nama_lengkap,
            'role' => $request->role,
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil ditambahkan. Password: ' . $request->password);
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
            $updateData['plain_password'] = $request->password;
        }

        $karyawan->update($updateData);

        $message = 'Karyawan berhasil diupdate';
        if ($request->filled('password')) {
            $message .= '. Password baru: ' . $request->password;
        }

        return redirect()->route('admin.karyawan')->with('success', $message);
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

    public function showPassword($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        // For security, we need to store plain password temporarily
        // In production, you should use a password reset system
        // This is just for demonstration as requested
        $plainPassword = session('temp_password_' . $karyawan->id);
        
        if (!$plainPassword) {
            // If password not in session, show message to reset
            return response()->json([
                'success' => false,
                'message' => 'Password tidak tersedia. Silakan reset password jika diperlukan.'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'password' => $plainPassword,
            'message' => 'Password untuk akun ' . $karyawan->nama_lengkap
        ]);
    }
}