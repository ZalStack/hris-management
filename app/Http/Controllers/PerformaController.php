<?php

namespace App\Http\Controllers;

use App\Models\Performa;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformaController extends Controller
{
    // For Employees - View only
    public function index()
    {
        $performas = Performa::where('karyawan_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(12);
        
        // Get latest performance
        $latestPerforma = Performa::where('karyawan_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();
        
        // Get average score for charts
        $averageScore = Performa::where('karyawan_id', Auth::id())
            ->avg('performance_score');
        
        // Get performance by quarter
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        $quarterlyData = [];
        foreach ($quarters as $quarter) {
            $quarterlyData[$quarter] = Performa::where('karyawan_id', Auth::id())
                ->where('quarter', $quarter)
                ->where('tahun', Carbon::now()->year)
                ->avg('performance_score');
        }
        
        return view('performa.index', compact('performas', 'latestPerforma', 'averageScore', 'quarterlyData'));
    }

    public function show($id)
    {
        $performa = Performa::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        return view('performa.show', compact('performa'));
    }

    // For Admin/HR
    public function adminIndex(Request $request)
    {
        $query = Performa::with('karyawan')->orderBy('created_at', 'desc');
        
        // Filter by month/year
        if ($request->bulan && $request->tahun) {
            $query->where('bulan', $request->bulan)->where('tahun', $request->tahun);
        } elseif ($request->bulan) {
            $query->where('bulan', $request->bulan);
        } elseif ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }
        
        // Filter by quarter
        if ($request->quarter) {
            $query->where('quarter', $request->quarter);
        }
        
        // Filter by karyawan
        if ($request->karyawan_id) {
            $query->where('karyawan_id', $request->karyawan_id);
        }
        
        // Filter by performance rating
        if ($request->rating) {
            if ($request->rating == 'excellent') {
                $query->where('performance_score', '>=', 90);
            } elseif ($request->rating == 'good') {
                $query->whereBetween('performance_score', [75, 89]);
            } elseif ($request->rating == 'average') {
                $query->whereBetween('performance_score', [60, 74]);
            } elseif ($request->rating == 'poor') {
                $query->whereBetween('performance_score', [50, 59]);
            } elseif ($request->rating == 'very_poor') {
                $query->where('performance_score', '<', 50);
            }
        }
        
        $performas = $query->paginate(15);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        
        $statistics = [
            'total_employees' => Performa::distinct('karyawan_id')->count('karyawan_id'),
            'total_assessments' => Performa::count(),
            'average_score' => round(Performa::avg('performance_score'), 2),
            'excellent_count' => Performa::where('performance_score', '>=', 90)->count(),
            'good_count' => Performa::whereBetween('performance_score', [75, 89])->count(),
            'average_count' => Performa::whereBetween('performance_score', [60, 74])->count(),
            'poor_count' => Performa::whereBetween('performance_score', [50, 59])->count(),
            'very_poor_count' => Performa::where('performance_score', '<', 50)->count(),
        ];
        
        return view('admin.performa.index', compact('performas', 'karyawans', 'statistics'));
    }

    public function adminCreate()
    {
        $karyawans = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bulan = range(1, 12);
        $tahun = range(2023, date('Y') + 1);
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        return view('admin.performa.create', compact('karyawans', 'bulan', 'tahun', 'currentMonth', 'currentYear'));
    }

    public function getKaryawanData($id)
    {
        $karyawan = Karyawan::with(['jabatanSaatIni.jabatan.departemen'])->findOrFail($id);
        
        $departemen = '-';
        $position = '-';
        
        if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
            $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
            if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
            }
        }
        
        return response()->json([
            'success' => true,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'departemen' => $departemen,
            'position' => $position,
            'email' => $karyawan->email,
            'phone' => $karyawan->nomor_telepon,
            'join_date' => $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('Y-m-d') : date('Y-m-d'),
        ]);
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'quality' => 'required|integer|min:0|max:100',
            'productivity' => 'required|integer|min:0|max:100',
            'teamwork' => 'required|integer|min:0|max:100',
            'discipline' => 'required|integer|min:0|max:100',
            'kpi_score' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable',
        ]);
        
        $karyawan = Karyawan::find($request->karyawan_id);
        
        // Get departemen and position
        $departemen = '-';
        $position = '-';
        
        if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
            $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
            if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
            }
        }
        
        // Calculate performance score
        $performanceScore = Performa::calculatePerformanceScore(
            $request->attendance_rate,
            $request->quality,
            $request->productivity,
            $request->teamwork,
            $request->discipline,
            $request->kpi_score
        );
        
        // Determine quarter
        $quarter = '';
        if ($request->bulan >= 1 && $request->bulan <= 3) {
            $quarter = 'Q1';
        } elseif ($request->bulan >= 4 && $request->bulan <= 6) {
            $quarter = 'Q2';
        } elseif ($request->bulan >= 7 && $request->bulan <= 9) {
            $quarter = 'Q3';
        } else {
            $quarter = 'Q4';
        }
        
        // Check if performance already exists
        $exists = Performa::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penilaian performa untuk karyawan ini pada periode tersebut sudah ada')
                ->withInput();
        }
        
        $performa = Performa::create([
            'karyawan_id' => $request->karyawan_id,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'departemen' => $departemen,
            'position' => $position,
            'email' => $karyawan->email,
            'phone' => $karyawan->nomor_telepon,
            'join_date' => $karyawan->tanggal_bergabung ?? now(),
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'attendance_rate' => $request->attendance_rate,
            'quality' => $request->quality,
            'productivity' => $request->productivity,
            'teamwork' => $request->teamwork,
            'discipline' => $request->discipline,
            'kpi_score' => $request->kpi_score,
            'performance_score' => $performanceScore,
            'catatan' => $request->catatan,
            'quarter' => $quarter,
        ]);
        
        // Create notification for employee
        Notifikasi::create([
            'user_id' => $karyawan->id,
            'judul' => 'Penilaian Performa Baru',
            'pesan' => "Penilaian performa untuk bulan {$performa->bulan_text} {$performa->tahun} telah tersedia. Skor Anda: {$performanceScore}",
            'tipe_notifikasi' => 'performa',
        ]);
        
        return redirect()->route('admin.performa.index')
            ->with('success', 'Penilaian performa berhasil ditambahkan');
    }

    public function adminEdit($id)
    {
        $performa = Performa::findOrFail($id);
        $karyawans = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bulan = range(1, 12);
        $tahun = range(2023, date('Y') + 1);
        
        return view('admin.performa.edit', compact('performa', 'karyawans', 'bulan', 'tahun'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $performa = Performa::findOrFail($id);
        
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'quality' => 'required|integer|min:0|max:100',
            'productivity' => 'required|integer|min:0|max:100',
            'teamwork' => 'required|integer|min:0|max:100',
            'discipline' => 'required|integer|min:0|max:100',
            'kpi_score' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable',
        ]);
        
        $karyawan = Karyawan::find($request->karyawan_id);
        
        // Get departemen and position
        $departemen = '-';
        $position = '-';
        
        if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
            $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
            if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
            }
        }
        
        // Calculate performance score
        $performanceScore = Performa::calculatePerformanceScore(
            $request->attendance_rate,
            $request->quality,
            $request->productivity,
            $request->teamwork,
            $request->discipline,
            $request->kpi_score
        );
        
        // Determine quarter
        $quarter = '';
        if ($request->bulan >= 1 && $request->bulan <= 3) {
            $quarter = 'Q1';
        } elseif ($request->bulan >= 4 && $request->bulan <= 6) {
            $quarter = 'Q2';
        } elseif ($request->bulan >= 7 && $request->bulan <= 9) {
            $quarter = 'Q3';
        } else {
            $quarter = 'Q4';
        }
        
        // Check if performance already exists for other record
        $exists = Performa::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penilaian performa untuk karyawan ini pada periode tersebut sudah ada')
                ->withInput();
        }
        
        $performa->update([
            'karyawan_id' => $request->karyawan_id,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'departemen' => $departemen,
            'position' => $position,
            'email' => $karyawan->email,
            'phone' => $karyawan->nomor_telepon,
            'join_date' => $karyawan->tanggal_bergabung ?? now(),
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'attendance_rate' => $request->attendance_rate,
            'quality' => $request->quality,
            'productivity' => $request->productivity,
            'teamwork' => $request->teamwork,
            'discipline' => $request->discipline,
            'kpi_score' => $request->kpi_score,
            'performance_score' => $performanceScore,
            'catatan' => $request->catatan,
            'quarter' => $quarter,
        ]);
        
        // Create notification for employee
        Notifikasi::create([
            'user_id' => $karyawan->id,
            'judul' => 'Penilaian Performa Diupdate',
            'pesan' => "Penilaian performa untuk bulan {$performa->bulan_text} {$performa->tahun} telah diupdate. Skor Anda: {$performanceScore}",
            'tipe_notifikasi' => 'performa',
        ]);
        
        return redirect()->route('admin.performa.index')
            ->with('success', 'Penilaian performa berhasil diupdate');
    }

    public function adminDestroy($id)
    {
        $performa = Performa::findOrFail($id);
        $performa->delete();
        
        return redirect()->route('admin.performa.index')
            ->with('success', 'Penilaian performa berhasil dihapus');
    }

    public function adminShow($id)
    {
        $performa = Performa::with('karyawan')->findOrFail($id);
        return response()->json($performa);
    }

    public function adminBulkCreate()
    {
        $karyawans = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        return view('admin.performa.bulk', compact('karyawans', 'currentMonth', 'currentYear'));
    }

    public function adminBulkStore(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'performas' => 'required|array',
            'performas.*.karyawan_id' => 'required|exists:karyawans,id',
            'performas.*.attendance_rate' => 'required|integer|min:0|max:100',
            'performas.*.quality' => 'required|integer|min:0|max:100',
            'performas.*.productivity' => 'required|integer|min:0|max:100',
            'performas.*.teamwork' => 'required|integer|min:0|max:100',
            'performas.*.discipline' => 'required|integer|min:0|max:100',
            'performas.*.kpi_score' => 'required|integer|min:0|max:100',
        ]);
        
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Determine quarter
        if ($bulan >= 1 && $bulan <= 3) {
            $quarter = 'Q1';
        } elseif ($bulan >= 4 && $bulan <= 6) {
            $quarter = 'Q2';
        } elseif ($bulan >= 7 && $bulan <= 9) {
            $quarter = 'Q3';
        } else {
            $quarter = 'Q4';
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($request->performas as $data) {
            $karyawan = Karyawan::find($data['karyawan_id']);
            
            // Get departemen and position
            $departemen = '-';
            $position = '-';
            
            if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
                $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
                if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                    $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
                }
            }
            
            // Calculate performance score
            $performanceScore = Performa::calculatePerformanceScore(
                $data['attendance_rate'],
                $data['quality'],
                $data['productivity'],
                $data['teamwork'],
                $data['discipline'],
                $data['kpi_score']
            );
            
            // Check if exists
            $exists = Performa::where('karyawan_id', $data['karyawan_id'])
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();
            
            if (!$exists) {
                Performa::create([
                    'karyawan_id' => $data['karyawan_id'],
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'departemen' => $departemen,
                    'position' => $position,
                    'email' => $karyawan->email,
                    'phone' => $karyawan->nomor_telepon,
                    'join_date' => $karyawan->tanggal_bergabung ?? now(),
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'attendance_rate' => $data['attendance_rate'],
                    'quality' => $data['quality'],
                    'productivity' => $data['productivity'],
                    'teamwork' => $data['teamwork'],
                    'discipline' => $data['discipline'],
                    'kpi_score' => $data['kpi_score'],
                    'performance_score' => $performanceScore,
                    'quarter' => $quarter,
                ]);
                $successCount++;
                
                // Create notification for employee
                Notifikasi::create([
                    'user_id' => $karyawan->id,
                    'judul' => 'Penilaian Performa Baru',
                    'pesan' => "Penilaian performa untuk bulan " . $this->getBulanText($bulan) . " {$tahun} telah tersedia.",
                    'tipe_notifikasi' => 'performa',
                ]);
            } else {
                $errorCount++;
            }
        }
        
        $message = "Berhasil menambahkan {$successCount} penilaian performa.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} data gagal ditambahkan karena sudah ada.";
        }
        
        return redirect()->route('admin.performa.index')
            ->with('success', $message);
    }
    
    private function getBulanText($bulan)
    {
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanNama[$bulan];
    }

    // Auto reset KPI score for new quarter
    public function checkAndResetKPI()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Determine current quarter
        if ($currentMonth >= 1 && $currentMonth <= 3) {
            $currentQuarter = 'Q1';
        } elseif ($currentMonth >= 4 && $currentMonth <= 6) {
            $currentQuarter = 'Q2';
        } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
            $currentQuarter = 'Q3';
        } else {
            $currentQuarter = 'Q4';
        }
        
        return response()->json(['success' => true, 'message' => 'System ready for new assessments']);
    }
}