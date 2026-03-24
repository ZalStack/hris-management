<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departemens = Departemen::with('kepalaDepartemen')->paginate(10);
        $karyawans = Karyawan::whereIn('role', ['admin', 'hr'])->get();
        return view('admin.departemen.index', compact('departemens', 'karyawans'));
    }

    public function create()
    {
        $karyawans = Karyawan::whereIn('role', ['admin', 'hr'])->get();
        return view('admin.departemen.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_departemen' => 'required|unique:departemens|max:20',
            'nama_departemen' => 'required|max:100',
            'deskripsi' => 'nullable',
            'kepala_departemen_id' => 'nullable|exists:karyawans,id',
        ]);

        Departemen::create($request->all());

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan');
    }

    public function show($id)
    {
        $departemen = Departemen::with(['kepalaDepartemen', 'jabatan'])->findOrFail($id);
        return response()->json($departemen);
    }

    public function edit($id)
    {
        $departemen = Departemen::findOrFail($id);
        $karyawans = Karyawan::whereIn('role', ['admin', 'hr'])->get();
        return view('admin.departemen.edit', compact('departemen', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);

        $request->validate([
            'kode_departemen' => 'required|max:20|unique:departemens,kode_departemen,' . $id,
            'nama_departemen' => 'required|max:100',
            'deskripsi' => 'nullable',
            'kepala_departemen_id' => 'nullable|exists:karyawans,id',
        ]);

        $departemen->update($request->all());

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil diupdate');
    }

    public function destroy($id)
    {
        $departemen = Departemen::findOrFail($id);
        
        // Check if department has positions
        if ($departemen->jabatan()->count() > 0) {
            return redirect()->route('admin.departemen.index')
                ->with('error', 'Departemen tidak dapat dihapus karena masih memiliki jabatan');
        }
        
        $departemen->delete();
        
        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil dihapus');
    }
}