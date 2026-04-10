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
                        <p class="text-blue-700">Fitur ini memungkinkan Anda menambahkan penilaian performa untuk semua
                            karyawan sekaligus dalam satu periode.</p>
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
                                        <th class="py-3 px-4 text-left">Attendance</th>
                                        <th class="py-3 px-4 text-left">Quality</th>
                                        <th class="py-3 px-4 text-left">Productivity</th>
                                        <th class="py-3 px-4 text-left">Teamwork</th>
                                        <th class="py-3 px-4 text-left">Discipline</th>
                                        <th class="py-3 px-4 text-left">KPI</th>
                                        <th class="py-3 px-4 text-left">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm font-light">
                                    @foreach ($karyawans as $index => $karyawan)
                                        <tr class="border-b border-gray-200">
                                            <td class="py-3 px-4">
                                                {{ $karyawan->nama_lengkap }}<br>
                                                <small class="text-gray-500">{{ $karyawan->nip }}</small>
                                                <input type="hidden" name="performas[{{ $index }}][karyawan_id]"
                                                    value="{{ $karyawan->id }}">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number"
                                                    name="performas[{{ $index }}][attendance_rate]"
                                                    class="attendance shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="0"
                                                    onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][quality]"
                                                    class="quality shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="0"
                                                    onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number"
                                                    name="performas[{{ $index }}][productivity]"
                                                    class="productivity shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="0"
                                                    onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][teamwork]"
                                                    class="teamwork shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="0"
                                                    onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][discipline]"
                                                    class="discipline shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="0"
                                                    onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" name="performas[{{ $index }}][kpi_score]"
                                                    class="kpi shadow border rounded w-20 py-1 px-2 text-center"
                                                    min="0" max="100" value="0"
                                                    onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="row-total font-bold text-green-600">0</span>
                                            </td>
                            </table>
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
            const attendance = parseInt(row.querySelector('.attendance').value) || 0;
            const quality = parseInt(row.querySelector('.quality').value) || 0;
            const productivity = parseInt(row.querySelector('.productivity').value) || 0;
            const teamwork = parseInt(row.querySelector('.teamwork').value) || 0;
            const discipline = parseInt(row.querySelector('.discipline').value) || 0;
            const kpi = parseInt(row.querySelector('.kpi').value) || 0;

            const total = Math.round(
                (attendance * 0.15) +
                (quality * 0.20) +
                (productivity * 0.20) +
                (teamwork * 0.15) +
                (discipline * 0.15) +
                (kpi * 0.15)
            );

            row.querySelector('.row-total').innerText = total;
        }

        // Add event listeners to all inputs
        document.querySelectorAll('.attendance, .quality, .productivity, .teamwork, .discipline, .kpi').forEach(input => {
            input.addEventListener('change', function() {
                calculateRowTotal(this);
            });
            input.addEventListener('keyup', function() {
                calculateRowTotal(this);
            });
        });
    </script>
</x-app-layout>
