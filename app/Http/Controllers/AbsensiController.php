<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // For Karyawan
    public function index()
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())->orderBy('tanggal', 'desc')->paginate(10);

        $today = Carbon::today();
        $absensiToday = AbsensiKaryawan::where('karyawan_id', Auth::id())->whereDate('tanggal', $today)->first();

        return view('absensi.index', compact('absensi', 'absensiToday'));
    }

    public function create()
    {
        $today = Carbon::today();
        $absensiToday = AbsensiKaryawan::where('karyawan_id', Auth::id())->whereDate('tanggal', $today)->first();

        if ($absensiToday) {
            return redirect()->route('absensi.index')->with('error', 'Anda sudah melakukan absensi hari ini');
        }

        return view('absensi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_absensi' => 'required|in:masuk,izin,sakit',
            'jam_masuk' => 'required_if:jenis_absensi,masuk',
            'lokasi_masuk' => 'required_if:jenis_absensi,masuk',
            'keterangan' => 'nullable',
        ]);

        $karyawan = Auth::user();

        $data = [
            'karyawan_id' => $karyawan->id,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'tanggal' => Carbon::today(),
            'status_kehadiran' => 'pending',
            'keterangan' => $request->keterangan,
        ];

        if ($request->jenis_absensi === 'masuk') {
            $data['jam_masuk'] = $request->jam_masuk;
            $data['lokasi_masuk'] = $request->lokasi_masuk;
        } else {
            // For izin or sakit, set default status to the type
            $data['status_kehadiran'] = $request->jenis_absensi;
            $data['keterangan'] = $request->keterangan ?: ($request->jenis_absensi === 'izin' ? 'Izin tidak masuk' : 'Sakit');
        }

        $absensi = AbsensiKaryawan::create($data);

        $message = $request->jenis_absensi === 'masuk' ? 'Absensi berhasil ditambahkan, menunggu persetujuan HR/Admin' : 'Pengajuan ' . ($request->jenis_absensi === 'izin' ? 'izin' : 'sakit') . ' berhasil dikirim, menunggu persetujuan HR/Admin';

        return redirect()->route('absensi.index')->with('success', $message);
    }

    public function edit($id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($absensi->status_kehadiran !== 'pending') {
            return redirect()->route('absensi.index')->with('error', 'Absensi yang sudah disetujui tidak dapat diedit');
        }

        return view('absensi.edit', compact('absensi'));
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($absensi->status_kehadiran !== 'pending') {
            return redirect()->route('absensi.index')->with('error', 'Absensi yang sudah disetujui tidak dapat diedit');
        }

        $request->validate([
            'jam_masuk' => 'required',
            'lokasi_masuk' => 'required|string|max:255',
            'keterangan' => 'nullable',
        ]);

        $absensi->update([
            'jam_masuk' => $request->jam_masuk,
            'lokasi_masuk' => $request->lokasi_masuk,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil diupdate');
    }

    public function absensiPulang(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())->where('id', $id)->firstOrFail();

        $request->validate([
            'jam_pulang' => 'required',
            'lokasi_pulang' => 'required|string|max:255',
        ]);

        // Perbaiki: Gunakan cara yang lebih aman untuk menghitung total jam kerja
        try {
            // Format tanggal
            $tanggalAbsensi = $absensi->tanggal->format('Y-m-d');

            // Parse jam masuk - jika jam_masuk sudah berupa waktu saja
            $jamMasukValue = $absensi->jam_masuk;
            // Jika jam_masuk sudah dalam format lengkap (Y-m-d H:i:s), ambil waktunya saja
            if (strpos($jamMasukValue, ' ') !== false) {
                $jamMasukValue = substr($jamMasukValue, 11, 5);
            }

            // Buat datetime untuk jam masuk
            $jamMasukDateTime = Carbon::parse($tanggalAbsensi . ' ' . $jamMasukValue);

            // Parse jam pulang
            $jamPulangDateTime = Carbon::parse($tanggalAbsensi . ' ' . $request->jam_pulang);

            // Jika jam pulang lebih kecil dari jam masuk, berarti pulang keesokan harinya
            if ($jamPulangDateTime < $jamMasukDateTime) {
                $jamPulangDateTime->addDay();
            }

            // Hitung selisih dalam menit untuk akurasi lebih baik
            $selisihMenit = $jamMasukDateTime->diffInMinutes($jamPulangDateTime);
            $totalJam = $selisihMenit / 60;

            // Update absensi
            $absensi->update([
                'jam_pulang' => $request->jam_pulang,
                'lokasi_pulang' => $request->lokasi_pulang,
                'total_jam_kerja' => round($totalJam, 2),
            ]);

            return redirect()
                ->route('absensi.index')
                ->with('success', 'Absensi pulang berhasil dicatat. Total jam kerja: ' . number_format($totalJam, 2) . ' jam');
        } catch (\Exception $e) {
            // Jika terjadi error, simpan tanpa perhitungan total jam
            $absensi->update([
                'jam_pulang' => $request->jam_pulang,
                'lokasi_pulang' => $request->lokasi_pulang,
            ]);

            return redirect()->route('absensi.index')->with('warning', 'Absensi pulang berhasil dicatat, tetapi perhitungan total jam kerja gagal. Silakan hubungi HR.');
        }
    }

    // For Admin/HR
    public function adminIndex()
    {
        $absensi = AbsensiKaryawan::with('karyawan')->orderBy('tanggal', 'desc')->paginate(15);

        $statistics = [
            'total' => AbsensiKaryawan::count(),
            'pending' => AbsensiKaryawan::where('status_kehadiran', 'pending')->count(),
            'hadir' => AbsensiKaryawan::where('status_kehadiran', 'hadir')->count(),
            'izin' => AbsensiKaryawan::where('status_kehadiran', 'izin')->count(),
            'sakit' => AbsensiKaryawan::where('status_kehadiran', 'sakit')->count(),
            'alpha' => AbsensiKaryawan::where('status_kehadiran', 'alpha')->count(),
        ];

        return view('admin.absensi.index', compact('absensi', 'statistics'));
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::findOrFail($id);

        $request->validate([
            'status_kehadiran' => 'required|in:pending,hadir,izin,sakit,alpha',
            'keterangan' => 'nullable',
        ]);

        $oldStatus = $absensi->status_kehadiran;
        $newStatus = $request->status_kehadiran;

        $absensi->update([
            'status_kehadiran' => $newStatus,
            'keterangan' => $request->keterangan ?: $absensi->keterangan,
        ]);

        // Create notification for employee
        $statusMessages = [
            'hadir' => 'disetujui dan dinyatakan HADIR',
            'izin' => 'disetujui sebagai IZIN',
            'sakit' => 'disetujui sebagai SAKIT',
            'alpha' => 'dinyatakan ALPHA (Tidak Hadir Tanpa Keterangan)',
            'pending' => 'masih dalam status PENDING',
        ];

        $message = $statusMessages[$newStatus] ?? 'diupdate menjadi ' . strtoupper($newStatus);

        \App\Models\Notifikasi::create([
            'user_id' => $absensi->karyawan_id,
            'judul' => 'Status Absensi Diupdate',
            'pesan' => "Absensi tanggal {$absensi->tanggal->format('d/m/Y')} telah {$message}",
            'tipe_notifikasi' => 'absensi',
        ]);

        return redirect()->route('admin.absensi.index')->with('success', 'Status absensi berhasil diupdate');
    }

    public function adminShow($id)
    {
        $absensi = AbsensiKaryawan::with('karyawan')->findOrFail($id);
        return response()->json($absensi);
    }
}
