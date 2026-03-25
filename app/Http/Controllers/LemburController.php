<?php

namespace App\Http\Controllers;

use App\Models\PengajuanLembur;
use App\Models\Notifikasi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LemburController extends Controller
{
    // For Employees
    public function index()
    {
        $lembur = PengajuanLembur::where('karyawan_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('lembur.index', compact('lembur'));
    }
    
    public function create()
    {
        return view('lembur.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        
        $jamMulai = Carbon::parse($request->tanggal_lembur . ' ' . $request->jam_mulai);
        $jamSelesai = Carbon::parse($request->tanggal_lembur . ' ' . $request->jam_selesai);
        $totalJam = $jamMulai->diffInMinutes($jamSelesai) / 60;
        
        $data = $request->except(['lampiran']);
        $data['karyawan_id'] = Auth::id();
        $data['nama_karyawan'] = Auth::user()->nama_lengkap;
        $data['total_jam'] = round($totalJam, 2);
        $data['status'] = 'pending';
        
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lembur', $filename, 'public');
            $data['lampiran'] = $path;
        }
        
        PengajuanLembur::create($data);
        
        // Create notification for HR/Admin
        $admins = Karyawan::whereIn('role', ['admin', 'hr'])->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Pengajuan Lembur Baru',
                'pesan' => "{$data['nama_karyawan']} mengajukan lembur pada tanggal " . Carbon::parse($request->tanggal_lembur)->format('d/m/Y'),
                'tipe_notifikasi' => 'lembur',
            ]);
        }
        
        return redirect()->route('lembur.index')
            ->with('success', 'Pengajuan lembur berhasil dikirim, menunggu persetujuan HR/Admin');
    }
    
    public function edit($id)
    {
        $lembur = PengajuanLembur::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        return view('lembur.edit', compact('lembur'));
    }
    
    public function update(Request $request, $id)
    {
        $lembur = PengajuanLembur::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        
        $jamMulai = Carbon::parse($request->tanggal_lembur . ' ' . $request->jam_mulai);
        $jamSelesai = Carbon::parse($request->tanggal_lembur . ' ' . $request->jam_selesai);
        $totalJam = $jamMulai->diffInMinutes($jamSelesai) / 60;
        
        $data = $request->except(['lampiran']);
        $data['total_jam'] = round($totalJam, 2);
        
        if ($request->hasFile('lampiran')) {
            if ($lembur->lampiran) {
                Storage::disk('public')->delete($lembur->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lembur', $filename, 'public');
            $data['lampiran'] = $path;
        }
        
        $lembur->update($data);
        
        return redirect()->route('lembur.index')
            ->with('success', 'Pengajuan lembur berhasil diupdate');
    }
    
    public function destroy($id)
    {
        $lembur = PengajuanLembur::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        if ($lembur->lampiran) {
            Storage::disk('public')->delete($lembur->lampiran);
        }
        
        $lembur->delete();
        
        return redirect()->route('lembur.index')
            ->with('success', 'Pengajuan lembur berhasil dibatalkan');
    }
    
    // For Admin/HR
    public function adminIndex()
    {
        $lembur = PengajuanLembur::with(['karyawan', 'disetujuiOleh'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $statistics = [
            'total' => PengajuanLembur::count(),
            'pending' => PengajuanLembur::where('status', 'pending')->count(),
            'disetujui' => PengajuanLembur::where('status', 'disetujui')->count(),
            'ditolak' => PengajuanLembur::where('status', 'ditolak')->count(),
            'total_jam' => PengajuanLembur::where('status', 'disetujui')->sum('total_jam'),
        ];
        
        $karyawans = Karyawan::whereIn('role', ['admin', 'hr'])->get();
        
        return view('admin.lembur.index', compact('lembur', 'statistics', 'karyawans'));
    }
    
    public function adminUpdateStatus(Request $request, $id)
    {
        $lembur = PengajuanLembur::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,disetujui,ditolak',
            'disetujui_oleh' => 'nullable|exists:karyawans,id',
            'tarif_lembur_per_jam' => 'nullable|numeric|min:0',
            'total_lembur' => 'nullable|numeric|min:0',
            'catatan' => 'nullable',
            'tanggal_disetujui' => 'nullable|date',
        ]);
        
        $updateData = [
            'status' => $request->status,
            'catatan' => $request->catatan,
        ];
        
        if ($request->status == 'disetujui') {
            if ($request->tanggal_disetujui) {
                $updateData['tanggal_disetujui'] = Carbon::parse($request->tanggal_disetujui);
            } else {
                $updateData['tanggal_disetujui'] = now();
            }
            
            if ($request->disetujui_oleh) {
                $approver = Karyawan::find($request->disetujui_oleh);
                $updateData['disetujui_oleh'] = $request->disetujui_oleh;
                $updateData['nama_disetujui'] = $approver->nama_lengkap;
            }
            
            if ($request->tarif_lembur_per_jam) {
                $updateData['tarif_lembur_per_jam'] = $request->tarif_lembur_per_jam;
                $updateData['total_lembur'] = $request->tarif_lembur_per_jam * $lembur->total_jam;
            } elseif ($request->total_lembur) {
                $updateData['total_lembur'] = $request->total_lembur;
                $updateData['tarif_lembur_per_jam'] = $request->total_lembur / $lembur->total_jam;
            }
        }
        
        $lembur->update($updateData);
        
        // Create notification for employee
        $statusText = $request->status == 'disetujui' ? 'disetujui' : 'ditolak';
        Notifikasi::create([
            'user_id' => $lembur->karyawan_id,
            'judul' => 'Status Pengajuan Lembur',
            'pesan' => "Pengajuan lembur Anda tanggal {$lembur->tanggal_lembur->format('d/m/Y')} telah {$statusText}" . ($request->catatan ? " dengan catatan: {$request->catatan}" : ""),
            'tipe_notifikasi' => 'lembur',
        ]);
        
        return redirect()->route('admin.lembur.index')
            ->with('success', 'Status lembur berhasil diupdate');
    }
    
    public function adminShow($id)
    {
        $lembur = PengajuanLembur::with(['karyawan', 'disetujuiOleh'])->findOrFail($id);
        return response()->json($lembur);
    }
}