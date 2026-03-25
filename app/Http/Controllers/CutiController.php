<?php

namespace App\Http\Controllers;

use App\Models\PengajuanCuti;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CutiController extends Controller
{
    // For Employees
    public function index()
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get current year's quota
        $currentYear = Carbon::now()->year;
        $cutiTahunan = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('tahun_cuti', $currentYear)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->sum('total_hari');
        
        $kuotaTotal = 12; // Default 12 days per year
        $kuotaTerpakai = $cutiTahunan;
        $sisaKuota = $kuotaTotal - $kuotaTerpakai;
        
        return view('cuti.index', compact('cuti', 'kuotaTotal', 'kuotaTerpakai', 'sisaKuota'));
    }
    
    public function create()
    {
        $currentYear = Carbon::now()->year;
        $cutiTahunan = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('tahun_cuti', $currentYear)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->sum('total_hari');
        
        $kuotaTotal = 12;
        $sisaKuota = $kuotaTotal - $cutiTahunan;
        
        return view('cuti.create', compact('sisaKuota'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,peting,ibadah,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        
        // Check quota for annual leave
        if ($request->jenis_cuti == 'tahunan') {
            $currentYear = Carbon::now()->year;
            $cutiTerpakai = PengajuanCuti::where('karyawan_id', Auth::id())
                ->where('tahun_cuti', $currentYear)
                ->where('jenis_cuti', 'tahunan')
                ->where('status', 'disetujui')
                ->sum('total_hari');
            
            $sisaKuota = 12 - $cutiTerpakai;
            
            if ($totalHari > $sisaKuota) {
                return back()->with('error', "Sisa kuota cuti tahunan Anda hanya {$sisaKuota} hari");
            }
        }
        
        $data = $request->except(['lampiran']);
        $data['karyawan_id'] = Auth::id();
        $data['nama_karyawan'] = Auth::user()->nama_lengkap;
        $data['total_hari'] = $totalHari;
        $data['tahun_cuti'] = Carbon::now()->year;
        $data['status'] = 'pending';
        
        if ($request->jenis_cuti == 'tahunan') {
            $data['kuota_total'] = 12;
            $data['kuota_terpakai'] = $cutiTerpakai ?? 0;
            $data['sisa_kuota'] = 12 - ($cutiTerpakai ?? 0);
        }
        
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cuti', $filename, 'public');
            $data['lampiran'] = $path;
        }
        
        PengajuanCuti::create($data);
        
        // Create notification for HR/Admin
        $admins = \App\Models\Karyawan::whereIn('role', ['admin', 'hr'])->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Pengajuan Cuti Baru',
                'pesan' => "{$data['nama_karyawan']} mengajukan cuti {$data['jenis_cuti']} selama {$totalHari} hari",
                'tipe_notifikasi' => 'cuti',
            ]);
        }
        
        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim, menunggu persetujuan HR/Admin');
    }
    
    public function edit($id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        $currentYear = Carbon::now()->year;
        $cutiTahunan = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('tahun_cuti', $currentYear)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->sum('total_hari');
        
        $sisaKuota = 12 - $cutiTahunan;
        
        return view('cuti.edit', compact('cuti', 'sisaKuota'));
    }
    
    public function update(Request $request, $id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,peting,ibadah,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        
        $data = $request->except(['lampiran']);
        $data['total_hari'] = $totalHari;
        
        if ($request->hasFile('lampiran')) {
            if ($cuti->lampiran) {
                Storage::disk('public')->delete($cuti->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cuti', $filename, 'public');
            $data['lampiran'] = $path;
        }
        
        $cuti->update($data);
        
        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti berhasil diupdate');
    }
    
    public function destroy($id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        if ($cuti->lampiran) {
            Storage::disk('public')->delete($cuti->lampiran);
        }
        
        $cuti->delete();
        
        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dibatalkan');
    }
    
    // For Admin/HR
    public function adminIndex()
    {
        $cuti = PengajuanCuti::with('karyawan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $statistics = [
            'total' => PengajuanCuti::count(),
            'pending' => PengajuanCuti::where('status', 'pending')->count(),
            'disetujui' => PengajuanCuti::where('status', 'disetujui')->count(),
            'ditolak' => PengajuanCuti::where('status', 'ditolak')->count(),
            'total_hari' => PengajuanCuti::where('status', 'disetujui')->sum('total_hari'),
        ];
        
        return view('admin.cuti.index', compact('cuti', 'statistics'));
    }
    
    public function adminUpdateStatus(Request $request, $id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,disetujui,ditolak',
            'catatan' => 'nullable',
        ]);
        
        $updateData = [
            'status' => $request->status,
            'catatan' => $request->catatan,
        ];
        
        if ($request->status == 'disetujui') {
            $updateData['tanggal_disetujui'] = now();
            
            // Update quota for annual leave
            if ($cuti->jenis_cuti == 'tahunan') {
                $cuti->kuota_terpakai += $cuti->total_hari;
                $cuti->sisa_kuota = $cuti->kuota_total - $cuti->kuota_terpakai;
                $cuti->save();
            }
        }
        
        $cuti->update($updateData);
        
        // Create notification for employee
        $statusText = $request->status == 'disetujui' ? 'disetujui' : 'ditolak';
        Notifikasi::create([
            'user_id' => $cuti->karyawan_id,
            'judul' => 'Status Pengajuan Cuti',
            'pesan' => "Pengajuan cuti Anda tanggal {$cuti->tanggal_mulai->format('d/m/Y')} telah {$statusText}" . ($request->catatan ? " dengan catatan: {$request->catatan}" : ""),
            'tipe_notifikasi' => 'cuti',
        ]);
        
        return redirect()->route('admin.cuti.index')
            ->with('success', 'Status cuti berhasil diupdate');
    }
    
    public function adminShow($id)
    {
        $cuti = PengajuanCuti::with('karyawan')->findOrFail($id);
        return response()->json($cuti);
    }
}