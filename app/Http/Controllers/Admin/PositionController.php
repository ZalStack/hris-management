<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Departemen;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::with('departemen')->paginate(10);
        $departemens = Departemen::all();
        return view('admin.jabatan.index', compact('jabatans', 'departemens'));
    }

    public function create()
    {
        $departemens = Departemen::all();
        return view('admin.jabatan.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jabatan' => 'required|unique:jabatans|max:20',
            'nama_jabatan' => 'required|max:100',
            'departemen_id' => 'required|exists:departemens,id',
            'deskripsi' => 'nullable',
            'gaji_minimal' => 'nullable|numeric|min:0',
            'gaji_maksimal' => 'nullable|numeric|min:0|gte:gaji_minimal',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function show($id)
    {
        $jabatan = Jabatan::with('departemen')->findOrFail($id);
        return response()->json($jabatan);
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $departemens = Departemen::all();
        return view('admin.jabatan.edit', compact('jabatan', 'departemens'));
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $request->validate([
            'kode_jabatan' => 'required|max:20|unique:jabatans,kode_jabatan,' . $id,
            'nama_jabatan' => 'required|max:100',
            'departemen_id' => 'required|exists:departemens,id',
            'deskripsi' => 'nullable',
            'gaji_minimal' => 'nullable|numeric|min:0',
            'gaji_maksimal' => 'nullable|numeric|min:0|gte:gaji_minimal',
        ]);

        $jabatan->update($request->all());

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diupdate');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        
        // Check if position has placements
        if ($jabatan->penempatan()->count() > 0) {
            return redirect()->route('admin.jabatan.index')
                ->with('error', 'Jabatan tidak dapat dihapus karena masih memiliki karyawan yang ditempatkan');
        }
        
        $jabatan->delete();
        
        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }

    public function getByDepartemen($departemen_id)
    {
        $jabatans = Jabatan::where('departemen_id', $departemen_id)->get();
        return response()->json($jabatans);
    }
}