<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\PengajuanLembur;
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
        $penggajian = Penggajian::where('karyawan_id', Auth::id())->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->paginate(10);

        return view('penggajian.index', compact('penggajian'));
    }

    public function show($id)
    {
        $penggajian = Penggajian::where('karyawan_id', Auth::id())->where('id', $id)->firstOrFail();

        $detailGaji = json_decode($penggajian->detail_gaji, true);

        return view('penggajian.show', compact('penggajian', 'detailGaji'));
    }

    // For Admin/HR
    public function adminIndex(Request $request)
    {
        $query = Penggajian::with('karyawan')->orderBy('created_at', 'desc');

        // Filter by month/year
        if ($request->bulan && $request->tahun) {
            $query->where('bulan', $request->bulan)->where('tahun', $request->tahun);
        } elseif ($request->bulan) {
            $query->where('bulan', $request->bulan);
        } elseif ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by karyawan
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

        $currentYear = date('Y');
        $tahun = range($currentYear - 2, $currentYear + 2);

        return view('admin.penggajian.create', compact('karyawans', 'tahun'));
    }

    public function calculateSalary(Request $request)
    {
        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Get base salary from latest placement
        $penempatan = $karyawan->penempatan()->where('status', true)->first();
        $gajiPokok = $penempatan ? $penempatan->gaji_pokok : 0;

        // Calculate overtime from approved lembur
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $approvedLembur = PengajuanLembur::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->sum('total_lembur');

        $uangLembur = $approvedLembur ?? 0;

        // Calculate tunjangan (allowances) - 20% of base salary
        $tunjangan = $gajiPokok * 0.2;

        // Calculate bonus (if any) - 0 for now
        $bonus = 0;

        // Calculate total
        $totalKotor = $gajiPokok + $tunjangan + $uangLembur + $bonus;

        // Calculate tax
        $pajak = Penggajian::hitungPajak($totalKotor);

        // Calculate BPJS
        $bpjs = Penggajian::hitungBPJS($gajiPokok);

        // Total potongan
        $totalPotongan = $pajak + $bpjs;

        // Gaji bersih
        $gajiBersih = $totalKotor - $totalPotongan;

        $detailGaji = [
            'gaji_pokok' => $gajiPokok,
            'tunjangan' => $tunjangan,
            'uang_lembur' => $uangLembur,
            'bonus' => $bonus,
            'total_kotor' => $totalKotor,
            'pajak' => $pajak,
            'bpjs' => $bpjs,
            'total_potongan' => $totalPotongan,
            'gaji_bersih' => $gajiBersih,
            'detail_lembur' => PengajuanLembur::where('karyawan_id', $karyawan->id)
                ->where('status', 'disetujui')
                ->whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->get(['tanggal_lembur', 'total_jam', 'total_lembur']),
        ];

        return response()->json([
            'success' => true,
            'data' => $detailGaji,
            'karyawan' => $karyawan,
        ]);
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'gaji_pokok' => 'required|numeric|min:0',
            'total_tunjangan' => 'required|numeric|min:0',
            'uang_lembur' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'total_potongan' => 'required|numeric|min:0',
            'jumlah_pajak' => 'required|numeric|min:0',
            'potongan_bpjs' => 'required|numeric|min:0',
            'gaji_bersih' => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'catatan' => 'nullable',
            'detail_gaji' => 'nullable',
        ]);

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
            'dibuat_oleh' => Auth::user()->nama_lengkap,
            'detail_gaji' => $request->detail_gaji,
        ]);

        // Create notification for employee
        Notifikasi::create([
            'user_id' => $karyawan->id,
            'judul' => 'Slip Gaji Tersedia',
            'pesan' => "Slip gaji untuk periode {$penggajian->bulan_text} {$penggajian->tahun} telah tersedia. Silakan cek di menu Penggajian.",
            'tipe_notifikasi' => 'penggajian',
        ]);

        return redirect()->route('admin.penggajian.index')->with('success', 'Data penggajian berhasil ditambahkan');
    }

    public function adminEdit($id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $karyawans = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();

        // Create bulan array with keys (1-12) and values (month names)
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $currentYear = date('Y');
        $tahun = range($currentYear - 2, $currentYear + 2);

        return view('admin.penggajian.edit', compact('penggajian', 'karyawans', 'bulan', 'tahun'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'gaji_pokok' => 'required|numeric|min:0',
            'total_tunjangan' => 'required|numeric|min:0',
            'uang_lembur' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'total_potongan' => 'required|numeric|min:0',
            'jumlah_pajak' => 'required|numeric|min:0',
            'potongan_bpjs' => 'required|numeric|min:0',
            'gaji_bersih' => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'catatan' => 'nullable',
        ]);

        $karyawan = Karyawan::find($request->karyawan_id);

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
        ]);

        // Create notification for employee if status changed
        if ($penggajian->wasChanged('status')) {
            Notifikasi::create([
                'user_id' => $karyawan->id,
                'judul' => 'Status Penggajian Diupdate',
                'pesan' => "Status penggajian periode {$penggajian->bulan_text} {$penggajian->tahun} telah diubah menjadi " . strtoupper($request->status),
                'tipe_notifikasi' => 'penggajian',
            ]);
        }

        return redirect()->route('admin.penggajian.index')->with('success', 'Data penggajian berhasil diupdate');
    }

    public function adminDestroy($id)
    {
        $penggajian = Penggajian::findOrFail($id);

        if ($penggajian->status == Penggajian::STATUS_PAID) {
            return redirect()->route('admin.penggajian.index')->with('error', 'Penggajian yang sudah dibayar tidak dapat dihapus');
        }

        $penggajian->delete();

        return redirect()->route('admin.penggajian.index')->with('success', 'Data penggajian berhasil dihapus');
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

        // Create notification for employee
        Notifikasi::create([
            'user_id' => $penggajian->karyawan_id,
            'judul' => 'Status Penggajian Diupdate',
            'pesan' => "Status penggajian periode {$penggajian->bulan_text} {$penggajian->tahun} telah diubah menjadi " . strtoupper($request->status),
            'tipe_notifikasi' => 'penggajian',
        ]);

        return redirect()->route('admin.penggajian.index')->with('success', 'Status penggajian berhasil diupdate');
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

        if ($penggajian->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport');
        }

        // Get month name for filename
        $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanText = $request->bulan ? $bulanNames[$request->bulan - 1] : 'Semua';
        $tahunText = $request->tahun ?: 'Semua';

        // Generate filename with proper naming
        $fileName = 'laporan_gaji_' . str_replace(' ', '_', $bulanText) . '_' . $tahunText . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($penggajian) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xef) . chr(0xbb) . chr(0xbf));

            // Headers
            fputcsv($file, ['ID', 'Nama Karyawan', 'NIP', 'Bulan', 'Tahun', 'Gaji Pokok', 'Tunjangan', 'Uang Lembur', 'Bonus', 'Total Potongan', 'Pajak', 'BPJS', 'Gaji Bersih', 'Status', 'Tanggal Pembayaran', 'Metode Pembayaran']);

            foreach ($penggajian as $item) {
                fputcsv($file, [$item->id, $item->nama_karyawan, $item->karyawan->nip ?? '-', $item->bulan_text, $item->tahun, number_format($item->gaji_pokok, 2, ',', '.'), number_format($item->total_tunjangan, 2, ',', '.'), number_format($item->uang_lembur, 2, ',', '.'), number_format($item->bonus, 2, ',', '.'), number_format($item->total_potongan, 2, ',', '.'), number_format($item->jumlah_pajak, 2, ',', '.'), number_format($item->potongan_bpjs, 2, ',', '.'), number_format($item->gaji_bersih, 2, ',', '.'), strtoupper($item->status), $item->tanggal_pembayaran ? $item->tanggal_pembayaran->format('d/m/Y') : '-', $item->metode_pembayaran ? strtoupper($item->metode_pembayaran) : '-']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
