<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenggajianApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Penggajian::with('karyawan')->orderBy('id', 'desc');
        
        // Filter by karyawan_id
        if ($request->has('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }
        
        // Filter by bulan
        if ($request->has('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        
        // Filter by tahun
        if ($request->has('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $penggajian = $query->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Data penggajian berhasil diambil',
            'data' => $penggajian,
            'total' => $penggajian->count()
        ], 200);
    }
    
    public function show($id)
    {
        $penggajian = Penggajian::with('karyawan')->find($id);
        
        if (!$penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggajian tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Detail penggajian berhasil diambil',
            'data' => $penggajian
        ], 200);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'gaji_pokok' => 'required|numeric|min:0',
            'total_tunjangan' => 'required|numeric|min:0',
            'uang_lembur' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'total_potongan' => 'required|numeric|min:0',
            'jumlah_pajak' => 'required|numeric|min:0',
            'potongan_bpjs' => 'required|numeric|min:0',
            'gaji_bersih' => 'required|numeric|min:0',
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'dibuat_oleh' => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'catatan' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check duplicate
        $exists = Penggajian::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Penggajian untuk periode ini sudah ada'
            ], 409);
        }
        
        $karyawan = Karyawan::find($request->karyawan_id);
        
        $penggajian = Penggajian::create([
            'karyawan_id' => $request->karyawan_id,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'gaji_pokok' => $request->gaji_pokok,
            'total_tunjangan' => $request->total_tunjangan,
            'uang_lembur' => $request->uang_lembur,
            'bonus' => $request->bonus,
            'total_potongan' => $request->total_potongan,
            'jumlah_pajak' => $request->jumlah_pajak,
            'potongan_bpjs' => $request->potongan_bpjs,
            'gaji_bersih' => $request->gaji_bersih,
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_bank' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'dibuat_oleh' => $request->dibuat_oleh,
            'detail_gaji' => json_encode([
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->total_tunjangan,
                'uang_lembur' => $request->uang_lembur,
                'bonus' => $request->bonus,
                'total_kotor' => $request->gaji_pokok + $request->total_tunjangan + $request->uang_lembur + $request->bonus,
                'pajak' => $request->jumlah_pajak,
                'bpjs' => $request->potongan_bpjs,
                'total_potongan' => $request->total_potongan,
                'gaji_bersih' => $request->gaji_bersih,
                'created_by' => $request->dibuat_oleh,
                'created_at' => now()->toDateTimeString()
            ])
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Penggajian berhasil ditambahkan',
            'data' => $penggajian
        ], 201);
    }
    
    public function update(Request $request, $id)
    {
        $penggajian = Penggajian::find($id);
        
        if (!$penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggajian tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'sometimes|required|exists:karyawans,id',
            'bulan' => 'sometimes|required|integer|min:1|max:12',
            'tahun' => 'sometimes|required|integer|min:2020|max:2100',
            'gaji_pokok' => 'sometimes|required|numeric|min:0',
            'total_tunjangan' => 'sometimes|required|numeric|min:0',
            'uang_lembur' => 'sometimes|required|numeric|min:0',
            'bonus' => 'sometimes|required|numeric|min:0',
            'total_potongan' => 'sometimes|required|numeric|min:0',
            'jumlah_pajak' => 'sometimes|required|numeric|min:0',
            'potongan_bpjs' => 'sometimes|required|numeric|min:0',
            'gaji_bersih' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:draft,pending,approved,paid,cancelled',
            'tanggal_pembayaran' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'catatan' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $penggajian->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Penggajian berhasil diupdate',
            'data' => $penggajian
        ], 200);
    }
    
    public function destroy($id)
    {
        $penggajian = Penggajian::find($id);
        
        if (!$penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggajian tidak ditemukan'
            ], 404);
        }
        
        if ($penggajian->status == 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Penggajian yang sudah dibayar tidak dapat dihapus'
            ], 422);
        }
        
        $penggajian->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Penggajian berhasil dihapus'
        ], 200);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $penggajian = Penggajian::find($id);
        
        if (!$penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggajian tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'tanggal_pembayaran' => 'nullable|date',
            'metode_pembayaran' => 'nullable|string|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'catatan' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $updateData = ['status' => $request->status];
        
        if ($request->status == 'paid') {
            $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran ?: date('Y-m-d');
            $updateData['metode_pembayaran'] = $request->metode_pembayaran;
            $updateData['nama_bank'] = $request->nama_bank;
            $updateData['nomor_rekening'] = $request->nomor_rekening;
        }
        
        if ($request->has('catatan')) {
            $updateData['catatan'] = $request->catatan;
        }
        
        $penggajian->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Status penggajian berhasil diupdate',
            'data' => $penggajian
        ], 200);
    }

    public function getByKaryawan($karyawan_id)
    {
        $karyawan = Karyawan::find($karyawan_id);
        
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }
        
        $penggajian = Penggajian::where('karyawan_id', $karyawan_id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
        
        $statistics = [
            'total_gaji_diterima' => $penggajian->where('status', 'paid')->sum('gaji_bersih'),
            'total_penggajian' => $penggajian->count(),
            'total_paid' => $penggajian->where('status', 'paid')->count(),
            'total_pending' => $penggajian->where('status', 'pending')->count(),
            'rata_rata_gaji' => $penggajian->where('status', 'paid')->avg('gaji_bersih') ?? 0
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Data penggajian karyawan berhasil diambil',
            'karyawan' => [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama_lengkap,
                'nip' => $karyawan->nip,
                'email' => $karyawan->email
            ],
            'statistics' => $statistics,
            'data' => $penggajian
        ], 200);
    }
    
    public function getSummary(Request $request)
    {
        $query = Penggajian::query();
        
        $tahun = $request->get('tahun', date('Y'));
        $query->where('tahun', $tahun);
        
        if ($request->has('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        
        $summary = [
            'tahun' => $tahun,
            'bulan' => $request->get('bulan', 'semua'),
            'total_gaji_pokok' => (float) $query->sum('gaji_pokok'),
            'total_tunjangan' => (float) $query->sum('total_tunjangan'),
            'total_uang_lembur' => (float) $query->sum('uang_lembur'),
            'total_bonus' => (float) $query->sum('bonus'),
            'total_potongan' => (float) $query->sum('total_potongan'),
            'total_pajak' => (float) $query->sum('jumlah_pajak'),
            'total_bpjs' => (float) $query->sum('potongan_bpjs'),
            'total_gaji_bersih' => (float) $query->sum('gaji_bersih'),
            'total_karyawan' => $query->distinct('karyawan_id')->count('karyawan_id'),
            'total_transaksi' => $query->count(),
            'by_status' => [
                'draft' => (clone $query)->where('status', 'draft')->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'paid' => (clone $query)->where('status', 'paid')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count()
            ]
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Ringkasan penggajian berhasil diambil',
            'data' => $summary
        ], 200);
    }
}