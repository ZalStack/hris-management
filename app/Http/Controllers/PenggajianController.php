<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenggajianController extends Controller
{
    // For Employees - View their own payslips
    public function index()
    {
        $penggajian = Penggajian::where('karyawan_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(10);
        
        return view('penggajian.index', compact('penggajian'));
    }

    public function show($id)
    {
        $penggajian = Penggajian::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $detailGaji = json_decode($penggajian->detail_gaji, true);
        
        return view('penggajian.show', compact('penggajian', 'detailGaji'));
    }

    // For Admin/HR
    public function adminIndex(Request $request)
    {
        $query = Penggajian::with('karyawan')->orderBy('created_at', 'desc');
        
        if ($request->bulan && $request->tahun) {
            $query->where('bulan', $request->bulan)->where('tahun', $request->tahun);
        } elseif ($request->bulan) {
            $query->where('bulan', $request->bulan);
        } elseif ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->karyawan_id) {
            $query->where('karyawan_id', $request->karyawan_id);
        }
        
        $penggajian = $query->paginate(15);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        
        $statistics = [
            'total_gaji' => Penggajian::where('status', Penggajian::STATUS_PAID)->sum('gaji_bersih'),
            'total_pending' => Penggajian::where('status', Penggajian::STATUS_PENDING)->count(),
            'total_approved' => Penggajian::where('status', Penggajian::STATUS_APPROVED)->count(),
            'total_paid' => Penggajian::where('status', Penggajian::STATUS_PAID)->count(),
        ];
        
        return view('admin.penggajian.index', compact('penggajian', 'karyawans', 'statistics'));
    }

    public function adminCreate()
    {
        $karyawans = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bulan = range(1, 12);
        $tahun = range(date('Y') - 2, date('Y') + 1);
        
        return view('admin.penggajian.create', compact('karyawans', 'bulan', 'tahun'));
    }

    public function adminStore(Request $request)
    {
        // Store data as is (with dots for formatting)
        $karyawan = Karyawan::find($request->karyawan_id);
        
        // Check if salary already exists
        $exists = Penggajian::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penggajian untuk karyawan ini pada periode tersebut sudah ada')
                ->withInput();
        }
        
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
            'dibuat_oleh' => Auth::user()->nama_lengkap,
            'detail_gaji' => json_encode([
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->total_tunjangan,
                'uang_lembur' => $request->uang_lembur,
                'bonus' => $request->bonus,
                'total_kotor' => $this->calculateTotalKotor($request),
                'pajak' => $request->jumlah_pajak,
                'bpjs' => $request->potongan_bpjs,
                'total_potongan' => $request->total_potongan,
                'gaji_bersih' => $request->gaji_bersih,
                'created_by' => Auth::user()->nama_lengkap,
                'created_at' => now(),
            ]),
        ]);
        
        // Create notification for employee
        if (in_array($request->status, ['approved', 'paid'])) {
            Notifikasi::create([
                'user_id' => $karyawan->id,
                'judul' => 'Slip Gaji Tersedia',
                'pesan' => "Slip gaji untuk periode " . $this->getBulanText($request->bulan) . " {$request->tahun} telah tersedia.",
                'tipe_notifikasi' => 'penggajian',
            ]);
        }
        
        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Data penggajian berhasil ditambahkan');
    }

    public function adminEdit($id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $karyawans = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bulan = range(1, 12);
        $tahun = range(date('Y') - 2, date('Y') + 1);
        
        return view('admin.penggajian.edit', compact('penggajian', 'karyawans', 'bulan', 'tahun'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $karyawan = Karyawan::find($request->karyawan_id);
        
        // Check if salary already exists for other record
        $exists = Penggajian::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penggajian untuk karyawan ini pada periode tersebut sudah ada')
                ->withInput();
        }
        
        $penggajian->update([
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
            'detail_gaji' => json_encode([
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->total_tunjangan,
                'uang_lembur' => $request->uang_lembur,
                'bonus' => $request->bonus,
                'total_kotor' => $this->calculateTotalKotor($request),
                'pajak' => $request->jumlah_pajak,
                'bpjs' => $request->potongan_bpjs,
                'total_potongan' => $request->total_potongan,
                'gaji_bersih' => $request->gaji_bersih,
                'updated_by' => Auth::user()->nama_lengkap,
                'updated_at' => now(),
            ]),
        ]);
        
        // Create notification for employee if status changed
        if ($penggajian->wasChanged('status')) {
            Notifikasi::create([
                'user_id' => $karyawan->id,
                'judul' => 'Status Penggajian Diupdate',
                'pesan' => "Status penggajian periode " . $this->getBulanText($request->bulan) . " {$request->tahun} telah diubah menjadi " . strtoupper($request->status),
                'tipe_notifikasi' => 'penggajian',
            ]);
        }
        
        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Data penggajian berhasil diupdate');
    }

    public function adminDestroy($id)
    {
        $penggajian = Penggajian::findOrFail($id);
        
        if ($penggajian->status == Penggajian::STATUS_PAID) {
            return redirect()->route('admin.penggajian.index')
                ->with('error', 'Penggajian yang sudah dibayar tidak dapat dihapus');
        }
        
        $penggajian->delete();
        
        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Data penggajian berhasil dihapus');
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'tanggal_pembayaran' => 'nullable|date',
            'metode_pembayaran' => 'nullable|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'catatan' => 'nullable',
        ]);
        
        $updateData = [
            'status' => $request->status,
            'catatan' => $request->catatan,
        ];
        
        if ($request->status == Penggajian::STATUS_PAID) {
            $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran ?: now();
            $updateData['metode_pembayaran'] = $request->metode_pembayaran;
            $updateData['nama_bank'] = $request->nama_bank;
            $updateData['nomor_rekening'] = $request->nomor_rekening;
        }
        
        $penggajian->update($updateData);
        
        // Update detail_gaji
        $detailGaji = json_decode($penggajian->detail_gaji, true) ?? [];
        $detailGaji['status_updated_by'] = Auth::user()->nama_lengkap;
        $detailGaji['status_updated_at'] = now();
        $detailGaji['status_updated_to'] = $request->status;
        $penggajian->detail_gaji = json_encode($detailGaji);
        $penggajian->save();
        
        // Create notification for employee
        Notifikasi::create([
            'user_id' => $penggajian->karyawan_id,
            'judul' => 'Status Penggajian Diupdate',
            'pesan' => "Status penggajian periode " . $this->getBulanText($penggajian->bulan) . " {$penggajian->tahun} telah diubah menjadi " . strtoupper($request->status),
            'tipe_notifikasi' => 'penggajian',
        ]);
        
        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Status penggajian berhasil diupdate');
    }

    public function adminShow($id)
    {
        $penggajian = Penggajian::with('karyawan')->findOrFail($id);
        $detailGaji = json_decode($penggajian->detail_gaji, true);
        
        return view('admin.penggajian.show', compact('penggajian', 'detailGaji'));
    }

    public function exportReport(Request $request)
    {
        $query = Penggajian::with('karyawan');
        
        if ($request->bulan && $request->tahun) {
            $query->where('bulan', $request->bulan)->where('tahun', $request->tahun);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $penggajian = $query->get();
        
        $fileName = 'laporan_gaji_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($penggajian) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Karyawan', 'Bulan', 'Tahun', 'Gaji Pokok', 'Tunjangan', 'Uang Lembur', 'Bonus', 'Potongan', 'Pajak', 'BPJS', 'Gaji Bersih', 'Status', 'Tanggal Bayar']);
            
            foreach ($penggajian as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->nama_karyawan,
                    $this->getBulanText($item->bulan),
                    $item->tahun,
                    $item->gaji_pokok,
                    $item->total_tunjangan,
                    $item->uang_lembur,
                    $item->bonus,
                    $item->total_potongan,
                    $item->jumlah_pajak,
                    $item->potongan_bpjs,
                    $item->gaji_bersih,
                    strtoupper($item->status),
                    $item->tanggal_pembayaran ? $item->tanggal_pembayaran->format('d/m/Y') : '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function calculateTotalKotor($request)
    {
        $gajiPokok = (float) str_replace('.', '', $request->gaji_pokok);
        $tunjangan = (float) str_replace('.', '', $request->total_tunjangan);
        $uangLembur = (float) str_replace('.', '', $request->uang_lembur);
        $bonus = (float) str_replace('.', '', $request->bonus);
        $total = $gajiPokok + $tunjangan + $uangLembur + $bonus;
        return 'Rp ' . number_format($total, 0, ',', '.');
    }
    
    private function getBulanText($bulan)
    {
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanNama[$bulan] ?? '-';
    }
    
    public static function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}