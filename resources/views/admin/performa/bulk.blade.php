<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bulk Penilaian Performa
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

                    <form method="POST" action="{{ route('admin.performa.bulk.store') }}" id="bulkForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Bulan *</label>
                                <select name="bulan" id="bulan" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $bulanNama = [
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
                                        @endphp
                                        <option value="{{ $i }}" {{ $currentMonth == $i ? 'selected' : '' }}>
                                            {{ $bulanNama[$i] }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun *</label>
                                <select name="tahun" id="tahun" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Tahun</option>
                                    @for ($i = 2023; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                        <th class="py-3 px-4 text-left">Karyawan</th>
                                        <th class="py-3 px-4 text-left">Quality</th>
                                        <th class="py-3 px-4 text-left">Productivity</th>
                                        <th class="py-3 px-4 text-left">Teamwork</th>
                                        <th class="py-3 px-4 text-left">Discipline</th>
                                        <th class="py-3 px-6 text-left">Attendance</th>
                                        <th class="py-3 px-6 text-left">KPI</th>
                                        <th class="py-3 px-6 text-left">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm font-light">
                                    @foreach ($karyawans as $index => $karyawan)
                                        @php
                                            // Get existing performance data for this employee
                                            $existingPerforma = \App\Models\Performa::where('karyawan_id', $karyawan->id)
                                                ->where('bulan', $currentMonth)
                                                ->where('tahun', $currentYear)
                                                ->first();
                                        @endphp
                                        <tr class="border-b border-gray-200">
                                            <td class="py-3 px-4">
                                                {{ $karyawan->nama_lengkap }}<br>
                                                <small class="text-gray-500">{{ $karyawan->nip }}</small>
                                                <input type="hidden" name="performas[{{ $index }}][karyawan_id]"
                                                    value="{{ $karyawan->id }}">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][quality]"
                                                    class="quality shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="{{ $existingPerforma->quality ?? 0 }}"
                                                    onchange="calculateRowTotal(this)"
                                                    onkeyup="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number"
                                                    name="performas[{{ $index }}][productivity]"
                                                    class="productivity shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="{{ $existingPerforma->productivity ?? 0 }}"
                                                    onchange="calculateRowTotal(this)"
                                                    onkeyup="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][teamwork]"
                                                    class="teamwork shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="{{ $existingPerforma->teamwork ?? 0 }}"
                                                    onchange="calculateRowTotal(this)"
                                                    onkeyup="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][discipline]"
                                                    class="discipline shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="{{ $existingPerforma->discipline ?? 0 }}"
                                                    onchange="calculateRowTotal(this)"
                                                    onkeyup="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                <span class="attendance-display font-semibold text-green-600">{{ $existingPerforma->attendance_rate ?? 0 }}%</span>
                                                <input type="hidden" class="attendance-input" name="performas[{{ $index }}][attendance_rate]" value="{{ $existingPerforma->attendance_rate ?? 0 }}">
                                            </td>
                                            <td class="py-3 px-6 text-left font-bold text-blue-600">
                                                <span class="kpi-display">{{ $existingPerforma->kpi_score ?? 0 }}</span>%
                                                <input type="hidden" class="kpi-input" name="performas[{{ $index }}][kpi_score]" value="{{ $existingPerforma->kpi_score ?? 0 }}">
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="total-display font-bold text-purple-600">{{ $existingPerforma->performance_score ?? 0 }}</span>
                                                <input type="hidden"
                                                    name="performas[{{ $index }}][performance_score]"
                                                    class="total-input" value="{{ $existingPerforma->performance_score ?? 0 }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.performa.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Semua Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateRowTotal(element) {
            const row = element.closest('tr');
            const quality = parseInt(row.querySelector('.quality').value) || 0;
            const productivity = parseInt(row.querySelector('.productivity').value) || 0;
            const teamwork = parseInt(row.querySelector('.teamwork').value) || 0;
            const discipline = parseInt(row.querySelector('.discipline').value) || 0;

            // Calculate KPI Score (average of quality, productivity, teamwork, discipline)
            const kpiScore = Math.round((quality + productivity + teamwork + discipline) / 4);

            // Calculate Attendance Rate (same as KPI Score)
            const attendanceRate = kpiScore;

            // Calculate Performance Score with weights
            const total = Math.round(
                (attendanceRate * 0.15) +
                (quality * 0.20) +
                (productivity * 0.20) +
                (teamwork * 0.15) +
                (discipline * 0.15) +
                (kpiScore * 0.15)
            );

            // Update displays
            const kpiDisplay = row.querySelector('.kpi-display');
            const kpiInput = row.querySelector('.kpi-input');
            if (kpiDisplay) kpiDisplay.innerText = kpiScore;
            if (kpiInput) kpiInput.value = kpiScore;

            const attendanceDisplay = row.querySelector('.attendance-display');
            const attendanceInput = row.querySelector('.attendance-input');
            if (attendanceDisplay) attendanceDisplay.innerText = attendanceRate;
            if (attendanceInput) attendanceInput.value = attendanceRate;

            const totalDisplay = row.querySelector('.total-display');
            const totalInput = row.querySelector('.total-input');
            if (totalDisplay) totalDisplay.innerText = total;
            if (totalInput) totalInput.value = total;
        }

        // Add event listeners to all inputs
        document.querySelectorAll('.quality, .productivity, .teamwork, .discipline').forEach(input => {
            input.addEventListener('change', function() {
                calculateRowTotal(this);
            });
            input.addEventListener('keyup', function() {
                calculateRowTotal(this);
            });
        });

        // Initialize all rows
        document.querySelectorAll('tbody tr').forEach(row => {
            const inputs = row.querySelectorAll('.quality, .productivity, .teamwork, .discipline');
            if (inputs.length > 0) {
                calculateRowTotal(inputs[0]);
            }
        });
    </script>
</x-app-layout>