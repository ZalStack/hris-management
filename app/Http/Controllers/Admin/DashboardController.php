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
        $karyawans = Karyawan::with(['jabatanSaatIni.jabatan.departemen'])->orderBy('created_at', 'desc')->paginate(10);
        $departemens = Departemen::all();
        $jabatans = Jabatan::with('departemen')->get();
        
        return view('admin.karyawan.index', compact('karyawans', 'departemens', 'jabatans'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:20|unique:karyawans,nip',
            'email' => 'required|email|unique:karyawans,email',
            'nama_lengkap' => 'required|string|max:255',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,hr,karyawan',
        ]);

        try {
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
        } catch (\Exception $e) {
            return redirect()->route('admin.karyawan')->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    public function editKaryawan($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            return response()->json($karyawan);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
        }
    }

    public function updateKaryawan(Request $request, $id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            
            $request->validate([
                'nip' => 'required|string|max:20|unique:karyawans,nip,' . $id,
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
        } catch (\Exception $e) {
            return redirect()->route('admin.karyawan')->with('error', 'Gagal mengupdate karyawan: ' . $e->getMessage());
        }
    }

    public function destroyKaryawan($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            
            // Prevent deleting own account
            if ($karyawan->id === auth()->id()) {
                return redirect()->route('admin.karyawan')->with('error', 'Anda tidak dapat menghapus akun sendiri');
            }
            
            // Prevent deleting if it's the last admin
            if ($karyawan->role === 'admin' && Karyawan::where('role', 'admin')->count() <= 1) {
                return redirect()->route('admin.karyawan')->with('error', 'Tidak dapat menghapus admin terakhir');
            }
            
            $karyawan->delete();
            
            return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.karyawan')->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }

    public function showKaryawanPassword($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            return response()->json([
                'hashed_password' => $karyawan->kata_sandi,
                'message' => 'This is the hashed password. In production, password reset functionality should be used instead.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
        }
    }
}