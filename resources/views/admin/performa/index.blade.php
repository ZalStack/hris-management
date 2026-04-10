<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Penilaian Performa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Karyawan Dinilai</div>
                    <div class="text-2xl font-bold">{{ $statistics['total_employees'] }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg shadow p-4">
                    <div class="text-blue-600 text-sm">Total Penilaian</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_assessments'] }}</div>
                </div>
                <div class="bg-green-50 rounded-lg shadow p-4">
                    <div class="text-green-600 text-sm">Rata-rata Skor</div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistics['average_score'] }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg shadow p-4">
                    <div class="text-purple-600 text-sm">Excellent (A)</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $statistics['excellent_count'] }}</div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Bulan</label>
                            <select name="bulan" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
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
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ $bulanNama[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                            <select name="tahun" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
                                @for ($i = 2023; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Quarter</label>
                            <select name="quarter" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
                                <option value="Q1" {{ request('quarter') == 'Q1' ? 'selected' : '' }}>Q1 (Jan-Mar)
                                </option>
                                <option value="Q2" {{ request('quarter') == 'Q2' ? 'selected' : '' }}>Q2 (Apr-Jun)
                                </option>
                                <option value="Q3" {{ request('quarter') == 'Q3' ? 'selected' : '' }}>Q3 (Jul-Sep)
                                </option>
                                <option value="Q4" {{ request('quarter') == 'Q4' ? 'selected' : '' }}>Q4 (Oct-Dec)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan</label>
                            <select name="karyawan_id" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
                                @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}"
                                        {{ request('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                        {{ $karyawan->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            <a href="{{ route('admin.performa.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4 flex justify-between">
                <div class="space-x-2">
                    <a href="{{ route('admin.performa.create') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + Tambah Penilaian
                    </a>
                    <a href="{{ route('admin.performa.bulk') }}"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Bulk Penilaian
                    </a>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Karyawan</th>
                                    <th class="py-3 px-6 text-left">Periode</th>
                                    <th class="py-3 px-6 text-left">Quarter</th>
                                    <th class="py-3 px-6 text-left">Attendance</th>
                                    <th class="py-3 px-6 text-left">Quality</th>
                                    <th class="py-3 px-6 text-left">Productivity</th>
                                    <th class="py-3 px-6 text-left">Teamwork</th>
                                    <th class="py-3 px-6 text-left">Discipline</th>
                                    <th class="py-3 px-6 text-left">KPI</th>
                                    <th class="py-3 px-6 text-left">Total</th>
                                    <th class="py-3 px-6 text-left">Rating</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($performas as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">
                                            {{ $item->nama_karyawan }}<br>
                                            <small class="text-gray-500">{{ $item->departemen }}</small>
                                        </td>
                                        <td class="py-3 px-6 text-left">{{ $item->bulan_text }} {{ $item->tahun }}
                                        <td class="py-3 px-6 text-left">
                                            <span
                                                class="bg-gray-200 text-gray-800 py-1 px-2 rounded text-xs">{{ $item->quarter }}</span>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <span class="font-semibold">{{ $item->attendance_rate }}%</span>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <span class="font-semibold">{{ $item->quality }}%</span>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <span class="font-semibold">{{ $item->productivity }}%</span>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <span class="font-semibold">{{ $item->teamwork }}%</span>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <span class="font-semibold">{{ $item->discipline }}%</span>
                                        </td>
                                        <td class="py-3 px-6 text-left font-bold text-blue-600">
                                            {{ $item->kpi_score }}%
                                        </td>
                                        <td class="py-3 px-6 text-left font-bold text-lg">
                                            {{ $item->performance_score }}
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <span
                                                class="bg-{{ $item->rating['color'] }}-100 text-{{ $item->rating['color'] }}-800 py-1 px-3 rounded-full text-xs">
                                                {{ $item->rating['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('admin.performa.edit', $item->id) }}"
                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.performa.destroy', $item->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Yakin ingin menghapus penilaian ini?')"
                                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Hapus
                                                    </button>
                                                </form>
                                                <button onclick="viewDetail({{ $item->id }})"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Detail
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-4">Tidak ada data penilaian performa
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $performas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detail Penilaian Performa</h3>
                <div id="detailContent"></div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeDetailModal()"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewDetail(id) {
            fetch(`/admin/performa/${id}`)
                .then(response => response.json())
                .then(data => {
                    const ratingColor = data.rating?.color || 'gray';
                    const ratingLabel = data.rating?.label || '-';

                    const content = `
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <p><strong>Nama Karyawan:</strong></p>
                                <p>${data.nama_karyawan}</p>
                                <p><strong>Departemen:</strong></p>
                                <p>${data.departemen}</p>
                                <p><strong>Posisi:</strong></p>
                                <p>${data.position}</p>
                                <p><strong>Periode:</strong></p>
                                <p>${getMonthName(data.bulan)} ${data.tahun}</p>
                                <p><strong>Quarter:</strong></p>
                                <p>${data.quarter}</p>
                                <p><strong>Attendance Rate:</strong></p>
                                <p>${data.attendance_rate}%</p>
                                <p><strong>Quality:</strong></p>
                                <p>${data.quality}%</p>
                                <p><strong>Productivity:</strong></p>
                                <p>${data.productivity}%</p>
                                <p><strong>Teamwork:</strong></p>
                                <p>${data.teamwork}%</p>
                                <p><strong>Discipline:</strong></p>
                                <p>${data.discipline}%</p>
                                <p><strong>KPI Score:</strong></p>
                                <p class="font-bold text-blue-600">${data.kpi_score}%</p>
                                <p><strong>Performance Score:</strong></p>
                                <p class="font-bold text-lg">${data.performance_score}</p>
                                <p><strong>Rating:</strong></p>
                                <p><span class="bg-${ratingColor}-100 text-${ratingColor}-800 py-1 px-3 rounded-full text-xs">${ratingLabel}</span></p>
                                ${data.catatan ? `<p><strong>Catatan:</strong></p><p>${data.catatan}</p>` : ''}
                            </div>
                        </div>
                    `;
                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('detailModal').classList.remove('hidden');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function getMonthName(month) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            return months[month - 1];
        }
    </script>
</x-app-layout>
