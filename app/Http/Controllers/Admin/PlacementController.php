<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenempatanKaryawan;
use App\Models\Karyawan;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function index()
    {
        $placements = PenempatanKaryawan::with(['karyawan', 'jabatan.departemen'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $karyawans = Karyawan::all();
        $jabatans = Jabatan::with('departemen')->get();
        
        return view('admin.penempatan.index', compact('placements', 'karyawans', 'jabatans'));
    }

    public function create()
    {
        $karyawans = Karyawan::all();
        $jabatans = Jabatan::with('departemen')->get();
        return view('admin.penempatan.create', compact('karyawans', 'jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'boolean',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'status_kepegawaian' => 'nullable|in:Tetap,Kontrak,Magang,Probation',
            'nomor_kontrak' => 'nullable|max:50',
        ]);

        // Set previous placements as inactive
        PenempatanKaryawan::where('karyawan_id', $request->karyawan_id)
            ->where('status', true)
            ->update(['status' => false]);

        PenempatanKaryawan::create($request->all());

        return redirect()->route('admin.penempatan.index')
            ->with('success', 'Penempatan karyawan berhasil ditambahkan');
    }

    public function show($id)
    {
        $placement = PenempatanKaryawan::with(['karyawan', 'jabatan.departemen'])->findOrFail($id);
        return response()->json($placement);
    }

    public function edit($id)
    {
        $placement = PenempatanKaryawan::findOrFail($id);
        $karyawans = Karyawan::all();
        $jabatans = Jabatan::with('departemen')->get();
        return view('admin.penempatan.edit', compact('placement', 'karyawans', 'jabatans'));
    }

    public function update(Request $request, $id)
    {
        $placement = PenempatanKaryawan::findOrFail($id);

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'boolean',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'status_kepegawaian' => 'nullable|in:Tetap,Kontrak,Magang,Probation',
            'nomor_kontrak' => 'nullable|max:50',
        ]);

        $placement->update($request->all());

        return redirect()->route('admin.penempatan.index')
            ->with('success', 'Penempatan karyawan berhasil diupdate');
    }

    public function destroy($id)
    {
        $placement = PenempatanKaryawan::findOrFail($id);
        $placement->delete();
        
        return redirect()->route('admin.penempatan.index')
            ->with('success', 'Penempatan karyawan berhasil dihapus');
    }

    public function getPlacementByKaryawan($karyawan_id)
    {
        $placements = PenempatanKaryawan::with('jabatan')
            ->where('karyawan_id', $karyawan_id)
            ->get();
        return response()->json($placements);
    }
}