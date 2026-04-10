<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Penilaian Performa - {{ $performa->bulan_text }} {{ $performa->tahun }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4">
                        <p class="text-blue-700 text-sm">
                            <strong>Informasi Perhitungan Otomatis:</strong><br>
                            - KPI Score = Rata-rata dari (Productivity + Discipline + Quality + Teamwork)<br>
                            - Attendance Rate = Sama dengan KPI Score<br>
                            - Performance Score = Perhitungan berbobot dari semua komponen
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.performa.update', $performa->id) }}" id="performaForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan *</label>
                                <select name="karyawan_id" id="karyawan_id" required class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $performa->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('karyawan_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Bulan *</label>
                                <select name="bulan" id="bulan" required class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Bulan</option>
                                    @foreach($bulan as $b)
                                        <option value="{{ $b }}" {{ old('bulan', $performa->bulan) == $b ? 'selected' : '' }}>
                                            @php
                                                $bulanNama = [
                                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                                ];
                                            @endphp
                                            {{ $bulanNama[$b] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bulan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun *</label>
                                <select name="tahun" id="tahun" required class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Tahun</option>
                                    @foreach($tahun as $t)
                                        <option value="{{ $t }}" {{ old('tahun', $performa->tahun) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                                @error('tahun')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komponen Penilaian (0-100)</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Quality *</label>
                                    <input type="number" id="quality" name="quality" value="{{ old('quality', $performa->quality) }}" 
                                        min="0" max="100" required
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        onchange="calculateAll()" onkeyup="calculateAll()">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div id="quality_bar" class="bg-green-600 rounded-full h-2" style="width: {{ $performa->quality }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Productivity *</label>
                                    <input type="number" id="productivity" name="productivity" value="{{ old('productivity', $performa->productivity) }}" 
                                        min="0" max="100" required
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        onchange="calculateAll()" onkeyup="calculateAll()">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div id="productivity_bar" class="bg-yellow-600 rounded-full h-2" style="width: {{ $performa->productivity }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Teamwork *</label>
                                    <input type="number" id="teamwork" name="teamwork" value="{{ old('teamwork', $performa->teamwork) }}" 
                                        min="0" max="100" required
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        onchange="calculateAll()" onkeyup="calculateAll()">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div id="teamwork_bar" class="bg-purple-600 rounded-full h-2" style="width: {{ $performa->teamwork }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Discipline *</label>
                                    <input type="number" id="discipline" name="discipline" value="{{ old('discipline', $performa->discipline) }}" 
                                        min="0" max="100" required
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        onchange="calculateAll()" onkeyup="calculateAll()">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div id="discipline_bar" class="bg-indigo-600 rounded-full h-2" style="width: {{ $performa->discipline }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Hasil Perhitungan Otomatis</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">KPI Score (Otomatis)</label>
                                    <div class="text-3xl font-bold text-blue-600" id="kpi_score_display">{{ $performa->kpi_score }}</div>
                                    <input type="hidden" id="kpi_score" name="kpi_score" value="{{ $performa->kpi_score }}">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div id="kpi_bar" class="bg-blue-600 rounded-full h-2" style="width: {{ $performa->kpi_score }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="bg-green-50 rounded-lg p-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Attendance Rate (Otomatis)</label>
                                    <div class="text-3xl font-bold text-green-600" id="attendance_rate_display">{{ $performa->attendance_rate }}</div>
                                    <input type="hidden" id="attendance_rate" name="attendance_rate" value="{{ $performa->attendance_rate }}">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div id="attendance_bar" class="bg-green-600 rounded-full h-2" style="width: {{ $performa->attendance_rate }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Total Performance Score</label>
                                        <div class="text-3xl font-bold text-purple-600" id="performance_score_display">{{ $performa->performance_score }}</div>
                                        <input type="hidden" id="performance_score" name="performance_score" value="{{ $performa->performance_score }}">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Rating</label>
                                        <div id="rating_display" class="text-lg font-semibold">
                                            <span class="bg-{{ $performa->rating['color'] }}-100 text-{{ $performa->rating['color'] }}-800 py-1 px-3 rounded-full text-xs">
                                                {{ $performa->rating['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                                <textarea name="catatan" rows="3" class="shadow border rounded w-full py-2 px-3">{{ old('catatan', $performa->catatan) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.performa.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Update Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateAll() {
            // Get all values
            let quality = parseInt(document.getElementById('quality').value) || 0;
            let productivity = parseInt(document.getElementById('productivity').value) || 0;
            let teamwork = parseInt(document.getElementById('teamwork').value) || 0;
            let discipline = parseInt(document.getElementById('discipline').value) || 0;
            
            // Update progress bars for input fields
            document.getElementById('quality_bar').style.width = quality + '%';
            document.getElementById('productivity_bar').style.width = productivity + '%';
            document.getElementById('teamwork_bar').style.width = teamwork + '%';
            document.getElementById('discipline_bar').style.width = discipline + '%';
            
            // Calculate KPI Score (average of quality, productivity, teamwork, discipline)
            let kpiScore = Math.round((quality + productivity + teamwork + discipline) / 4);
            
            // Calculate Attendance Rate (same as KPI Score)
            let attendanceRate = kpiScore;
            
            // Update KPI Score display
            document.getElementById('kpi_score').value = kpiScore;
            document.getElementById('kpi_score_display').innerText = kpiScore;
            document.getElementById('kpi_bar').style.width = kpiScore + '%';
            
            // Update Attendance Rate display
            document.getElementById('attendance_rate').value = attendanceRate;
            document.getElementById('attendance_rate_display').innerText = attendanceRate;
            document.getElementById('attendance_bar').style.width = attendanceRate + '%';
            
            // Calculate Performance Score with weights
            let total = (attendanceRate * 0.15) + 
                       (quality * 0.20) + 
                       (productivity * 0.20) + 
                       (teamwork * 0.15) + 
                       (discipline * 0.15) + 
                       (kpiScore * 0.15);
            
            total = Math.round(total);
            
            document.getElementById('performance_score').value = total;
            document.getElementById('performance_score_display').innerText = total;
            
            // Determine rating
            let rating = '';
            let ratingColor = '';
            if (total >= 90) {
                rating = 'Sangat Baik (A)';
                ratingColor = 'green';
            } else if (total >= 75) {
                rating = 'Baik (B)';
                ratingColor = 'blue';
            } else if (total >= 60) {
                rating = 'Cukup (C)';
                ratingColor = 'yellow';
            } else if (total >= 50) {
                rating = 'Kurang (D)';
                ratingColor = 'orange';
            } else {
                rating = 'Sangat Kurang (E)';
                ratingColor = 'red';
            }
            
            document.getElementById('rating_display').innerHTML = `<span class="bg-${ratingColor}-100 text-${ratingColor}-800 py-1 px-3 rounded-full text-xs">${rating}</span>`;
        }
        
        // Initial calculation
        calculateAll();
    </script>
</x-app-layout>