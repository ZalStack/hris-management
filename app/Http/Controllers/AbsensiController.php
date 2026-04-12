<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // ==================== EMPLOYEE SECTION ====================
    
    public function index()
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        $today = Carbon::today();
        $absensiToday = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->whereDate('tanggal', $today)
            ->first();

        $pendingChangeDays = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)
            ->count();

        return view('absensi.index', compact('absensi', 'absensiToday', 'pendingChangeDays'));
    }

    public function create()
    {
        $today = Carbon::today();
        $absensiToday = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->whereDate('tanggal', $today)
            ->where('is_change_day', false)
            ->first();

        if ($absensiToday) {
            return redirect()->route('absensi.index')
                ->with('error', 'Anda sudah melakukan absensi hari ini');
        }

        return view('absensi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_absensi' => 'required|in:masuk,izin,sakit,change_day',
            'jam_masuk' => 'required_if:jenis_absensi,masuk',
            'lokasi_masuk' => 'required_if:jenis_absensi,masuk',
            'keterangan' => 'nullable',
            'change_day_tanggal_awal' => 'required_if:jenis_absensi,change_day|date',
            'change_day_tanggal_akhir' => 'required_if:jenis_absensi,change_day|date|after_or_equal:change_day_tanggal_awal',
            'change_day_jam_mulai' => 'required_if:jenis_absensi,change_day',
            'change_day_jam_selesai' => 'required_if:jenis_absensi,change_day',
            'change_day_alasan' => 'required_if:jenis_absensi,change_day',
        ]);

        $karyawan = Auth::user();

        DB::beginTransaction();
        try {
            if ($request->jenis_absensi === 'change_day') {
                // Handle change day request
                $tanggalAwal = Carbon::parse($request->change_day_tanggal_awal);
                
                $data = [
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => $tanggalAwal,
                    'status_kehadiran' => AbsensiKaryawan::STATUS_PENDING,
                    'keterangan' => $request->keterangan,
                    'is_change_day' => true,
                    'change_day_tanggal_awal' => $request->change_day_tanggal_awal,
                    'change_day_tanggal_akhir' => $request->change_day_tanggal_akhir,
                    'change_day_jam_mulai' => $request->change_day_jam_mulai,
                    'change_day_jam_selesai' => $request->change_day_jam_selesai,
                    'change_day_alasan' => $request->change_day_alasan,
                    'change_day_status' => AbsensiKaryawan::CHANGE_DAY_PENDING,
                ];

                AbsensiKaryawan::create($data);

                // Notify HR/Admin
                $admins = Karyawan::whereIn('role', ['admin', 'hr'])->get();
                foreach ($admins as $admin) {
                    Notifikasi::create([
                        'user_id' => $admin->id,
                        'judul' => 'Pengajuan Change Day Baru',
                        'pesan' => "{$karyawan->nama_lengkap} mengajukan change day pada tanggal {$tanggalAwal->format('d/m/Y')}",
                        'tipe_notifikasi' => 'absensi',
                    ]);
                }

                DB::commit();
                return redirect()->route('absensi.index')
                    ->with('success', 'Pengajuan change day berhasil dikirim, menunggu persetujuan HR/Admin');

            } elseif ($request->jenis_absensi === 'masuk') {
                // Regular attendance check-in
                $data = [
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => Carbon::today(),
                    'jam_masuk' => $request->jam_masuk,
                    'lokasi_masuk' => $request->lokasi_masuk,
                    'status_kehadiran' => AbsensiKaryawan::STATUS_PENDING,
                    'keterangan' => $request->keterangan,
                    'is_change_day' => false,
                ];

                AbsensiKaryawan::create($data);
                
                DB::commit();
                return redirect()->route('absensi.index')
                    ->with('success', 'Absensi berhasil ditambahkan, menunggu persetujuan HR/Admin');
            } else {
                // For izin or sakit
                $data = [
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => Carbon::today(),
                    'status_kehadiran' => $request->jenis_absensi,
                    'keterangan' => $request->keterangan ?: ($request->jenis_absensi === 'izin' ? 'Izin tidak masuk' : 'Sakit'),
                    'is_change_day' => false,
                ];

                AbsensiKaryawan::create($data);

                DB::commit();
                $message = $request->jenis_absensi === 'izin' ? 'Pengajuan izin berhasil dikirim' : 'Pengajuan sakit berhasil dikirim';
                return redirect()->route('absensi.index')
                    ->with('success', $message . ', menunggu persetujuan HR/Admin');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('absensi.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($absensi->is_change_day) {
            if ($absensi->change_day_status !== AbsensiKaryawan::CHANGE_DAY_PENDING) {
                return redirect()->route('absensi.index')
                    ->with('error', 'Pengajuan change day yang sudah diproses tidak dapat diedit');
            }
            return view('absensi.edit_change_day', compact('absensi'));
        }

        if ($absensi->status_kehadiran !== AbsensiKaryawan::STATUS_PENDING) {
            return redirect()->route('absensi.index')
                ->with('error', 'Absensi yang sudah disetujui tidak dapat diedit');
        }

        return view('absensi.edit', compact('absensi'));
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($absensi->is_change_day) {
            if ($absensi->change_day_status !== AbsensiKaryawan::CHANGE_DAY_PENDING) {
                return redirect()->route('absensi.index')
                    ->with('error', 'Pengajuan change day yang sudah diproses tidak dapat diedit');
            }

            $request->validate([
                'change_day_tanggal_awal' => 'required|date',
                'change_day_tanggal_akhir' => 'required|date|after_or_equal:change_day_tanggal_awal',
                'change_day_jam_mulai' => 'required',
                'change_day_jam_selesai' => 'required',
                'change_day_alasan' => 'required',
                'keterangan' => 'nullable',
            ]);

            $absensi->update([
                'change_day_tanggal_awal' => $request->change_day_tanggal_awal,
                'change_day_tanggal_akhir' => $request->change_day_tanggal_akhir,
                'change_day_jam_mulai' => $request->change_day_jam_mulai,
                'change_day_jam_selesai' => $request->change_day_jam_selesai,
                'change_day_alasan' => $request->change_day_alasan,
                'keterangan' => $request->keterangan,
                'tanggal' => $request->change_day_tanggal_awal,
            ]);

            return redirect()->route('absensi.index')
                ->with('success', 'Pengajuan change day berhasil diupdate');
        }

        // Regular attendance update
        if ($absensi->status_kehadiran !== AbsensiKaryawan::STATUS_PENDING) {
            return redirect()->route('absensi.index')
                ->with('error', 'Absensi yang sudah disetujui tidak dapat diedit');
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

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil diupdate');
    }

    public function absensiPulang(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Cek apakah sudah pulang
        if ($absensi->jam_pulang) {
            return redirect()->route('absensi.index')
                ->with('error', 'Anda sudah melakukan absensi pulang');
        }

        $request->validate([
            'jam_pulang' => 'required',
            'lokasi_pulang' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $tanggalAbsensi = $absensi->tanggal->format('Y-m-d');
            $jamMasukValue = $absensi->jam_masuk;
            
            if (strpos($jamMasukValue, ' ') !== false) {
                $jamMasukValue = substr($jamMasukValue, 11, 5);
            }

            $jamMasukDateTime = Carbon::parse($tanggalAbsensi . ' ' . $jamMasukValue);
            $jamPulangDateTime = Carbon::parse($tanggalAbsensi . ' ' . $request->jam_pulang);

            if ($jamPulangDateTime < $jamMasukDateTime) {
                $jamPulangDateTime->addDay();
            }

            $selisihMenit = $jamMasukDateTime->diffInMinutes($jamPulangDateTime);
            $totalJam = $selisihMenit / 60;

            $absensi->update([
                'jam_pulang' => $request->jam_pulang,
                'lokasi_pulang' => $request->lokasi_pulang,
                'total_jam_kerja' => round($totalJam, 2),
            ]);

            DB::commit();
            return redirect()->route('absensi.index')
                ->with('success', 'Absensi pulang berhasil dicatat. Total jam kerja: ' . number_format($totalJam, 2) . ' jam');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('absensi.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancelChangeDay($id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)
            ->firstOrFail();

        $absensi->delete();

        return redirect()->route('absensi.index')
            ->with('success', 'Pengajuan change day berhasil dibatalkan');
    }

    // ==================== ADMIN/HR SECTION ====================

    public function adminIndex()
    {
        $absensi = AbsensiKaryawan::with('karyawan')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $statistics = [
            'total' => AbsensiKaryawan::count(),
            'pending' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_PENDING)
                ->where('is_change_day', false)
                ->count(),
            'hadir' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_HADIR)->count(),
            'izin' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_IZIN)->count(),
            'sakit' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_SAKIT)->count(),
            'alpha' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_ALPHA)->count(),
            'change_day_pending' => AbsensiKaryawan::where('is_change_day', true)
                ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)->count(),
        ];

        return view('admin.absensi.index', compact('absensi', 'statistics'));
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($absensi->is_change_day) {
                // Handle change day approval/rejection
                $request->validate([
                    'change_day_status' => 'required|in:pending,approved,rejected',
                    'change_day_catatan_admin' => 'nullable',
                ]);

                $updateData = [
                    'change_day_status' => $request->change_day_status,
                    'change_day_catatan_admin' => $request->change_day_catatan_admin,
                ];

                if ($request->change_day_status === AbsensiKaryawan::CHANGE_DAY_APPROVED) {
                    $updateData['change_day_disetujui_pada'] = now();
                    $updateData['change_day_disetujui_oleh'] = Auth::id();
                    $updateData['status_kehadiran'] = AbsensiKaryawan::STATUS_HADIR;
                    
                    // Hitung total jam kerja
                    $totalJam = $absensi->hitungTotalJamChangeDay();
                    if ($totalJam > 0) {
                        $updateData['total_jam_kerja'] = $totalJam;
                    }
                    
                    // Set jam masuk dan pulang dari change day
                    $updateData['jam_masuk'] = $absensi->change_day_jam_mulai;
                    $updateData['jam_pulang'] = $absensi->change_day_jam_selesai;
                } elseif ($request->change_day_status === AbsensiKaryawan::CHANGE_DAY_REJECTED) {
                    $updateData['status_kehadiran'] = AbsensiKaryawan::STATUS_ALPHA;
                }

                $absensi->update($updateData);

                $statusText = $request->change_day_status === 'approved' ? 'disetujui' : 'ditolak';
                $message = "Pengajuan change day Anda pada tanggal " . 
                    ($absensi->change_day_tanggal_awal ? $absensi->change_day_tanggal_awal->format('d/m/Y') : '-') . 
                    " telah {$statusText}";
                
                if ($request->change_day_catatan_admin) {
                    $message .= " dengan catatan: {$request->change_day_catatan_admin}";
                }

                Notifikasi::create([
                    'user_id' => $absensi->karyawan_id,
                    'judul' => 'Status Change Day Diupdate',
                    'pesan' => $message,
                    'tipe_notifikasi' => 'absensi',
                ]);

                DB::commit();
                return redirect()->route('admin.absensi.index')
                    ->with('success', 'Status change day berhasil diupdate');

            } else {
                // Handle regular attendance status update
                $request->validate([
                    'status_kehadiran' => 'required|in:pending,hadir,izin,sakit,alpha',
                    'keterangan' => 'nullable',
                ]);

                $newStatus = $request->status_kehadiran;

                $absensi->update([
                    'status_kehadiran' => $newStatus,
                    'keterangan' => $request->keterangan ?: $absensi->keterangan,
                ]);

                $statusMessages = [
                    'hadir' => 'disetujui dan dinyatakan HADIR',
                    'izin' => 'disetujui sebagai IZIN',
                    'sakit' => 'disetujui sebagai SAKIT',
                    'alpha' => 'dinyatakan ALPHA (Tidak Hadir Tanpa Keterangan)',
                    'pending' => 'masih dalam status PENDING',
                ];

                $message = $statusMessages[$newStatus] ?? 'diupdate menjadi ' . strtoupper($newStatus);

                Notifikasi::create([
                    'user_id' => $absensi->karyawan_id,
                    'judul' => 'Status Absensi Diupdate',
                    'pesan' => "Absensi tanggal {$absensi->tanggal->format('d/m/Y')} telah {$message}",
                    'tipe_notifikasi' => 'absensi',
                ]);

                DB::commit();
                return redirect()->route('admin.absensi.index')
                    ->with('success', 'Status absensi berhasil diupdate');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.absensi.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function adminShow($id)
    {
        $absensi = AbsensiKaryawan::with(['karyawan', 'disetujuiOleh'])->findOrFail($id);
        return response()->json($absensi);
    }
}