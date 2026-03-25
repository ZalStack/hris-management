<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Notifikasi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    // For Admin/HR
    public function index()
    {
        $pengumuman = Pengumuman::with('pembuat')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.pengumuman.index', compact('pengumuman'));
    }
    
    public function create()
    {
        $karyawans = Karyawan::all();
        $departemens = \App\Models\Departemen::all();
        return view('admin.pengumuman.create', compact('karyawans', 'departemens'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:200',
            'konten' => 'required',
            'kategori' => 'nullable|max:50',
            'target_role' => 'nullable|in:all,admin,hr,karyawan',
            'target_departemen' => 'nullable',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku_hingga' => 'nullable|date|after_or_equal:tanggal_terbit',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        
        $data = $request->except(['lampiran']);
        $data['dibuat_oleh'] = Auth::id();
        $data['status'] = $request->has('status');
        
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $data['lampiran'] = $path;
        }
        
        if (empty($data['tanggal_terbit'])) {
            $data['tanggal_terbit'] = Carbon::now();
        }
        
        $pengumuman = Pengumuman::create($data);
        
        // Create notifications for targeted users
        $this->sendNotifications($pengumuman);
        
        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dibuat dan notifikasi telah dikirim');
    }
    
    public function edit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $karyawans = Karyawan::all();
        $departemens = \App\Models\Departemen::all();
        return view('admin.pengumuman.edit', compact('pengumuman', 'karyawans', 'departemens'));
    }
    
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        
        $request->validate([
            'judul' => 'required|max:200',
            'konten' => 'required',
            'kategori' => 'nullable|max:50',
            'target_role' => 'nullable|in:all,admin,hr,karyawan',
            'target_departemen' => 'nullable',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku_hingga' => 'nullable|date|after_or_equal:tanggal_terbit',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        
        $data = $request->except(['lampiran']);
        $data['status'] = $request->has('status');
        
        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $data['lampiran'] = $path;
        }
        
        if (empty($data['tanggal_terbit'])) {
            $data['tanggal_terbit'] = Carbon::now();
        }
        
        $pengumuman->update($data);
        
        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diupdate');
    }
    
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        
        if ($pengumuman->lampiran) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }
        
        $pengumuman->delete();
        
        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus');
    }
    
    private function sendNotifications($pengumuman)
    {
        $query = Karyawan::query();
        
        if ($pengumuman->target_role && $pengumuman->target_role !== 'all') {
            $query->where('role', $pengumuman->target_role);
        }
        
        $users = $query->get();
        
        foreach ($users as $user) {
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => $pengumuman->judul,
                'pesan' => substr(strip_tags($pengumuman->konten), 0, 200) . (strlen(strip_tags($pengumuman->konten)) > 200 ? '...' : ''),
                'tipe_notifikasi' => 'pengumuman',
                'status' => false,
            ]);
        }
    }
    
    // For Employees to view announcements
    public function employeeIndex()
    {
        $user = Auth::user();
        
        $pengumuman = Pengumuman::where('status', true)
            ->where(function($query) use ($user) {
                $query->where('target_role', 'all')
                    ->orWhere('target_role', $user->role)
                    ->orWhereNull('target_role');
            })
            ->where(function($query) {
                $query->whereNull('tanggal_berlaku_hingga')
                    ->orWhere('tanggal_berlaku_hingga', '>=', Carbon::today());
            })
            ->orderBy('tanggal_terbit', 'desc')
            ->paginate(10);
        
        return view('pengumuman.index', compact('pengumuman'));
    }
    
    public function employeeShow($id)
    {
        $pengumuman = Pengumuman::where('status', true)
            ->findOrFail($id);
        
        return view('pengumuman.show', compact('pengumuman'));
    }
}