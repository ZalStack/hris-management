<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Penilaian Performa Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6">
                    <div class="text-white text-sm">Rata-rata Performa</div>
                    <div class="text-white text-3xl font-bold">{{ round($averageScore ?? 0) }}</div>
                    <div class="text-blue-100 text-xs">Dari semua penilaian</div>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6">
                    <div class="text-white text-sm">Performa Terbaru</div>
                    <div class="text-white text-3xl font-bold">{{ $latestPerforma->performance_score ?? 0 }}</div>
                    <div class="text-green-100 text-xs">{{ $latestPerforma ? $latestPerforma->bulan_text . ' ' . $latestPerforma->tahun : '-' }}</div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6">
                    <div class="text-white text-sm">Rating Terbaru</div>
                    <div class="text-white text-xl font-bold">{{ $latestPerforma ? $latestPerforma->rating['label'] : '-' }}</div>
                    <div class="text-purple-100 text-xs">Kategori performa</div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Performa per Quarter</h3>
                    <div class="h-64">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance History Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Penilaian Performa</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Periode</th>
                                    <th class="py-3 px-6 text-left">Quarter</th>
                                    <th class="py-3 px-6 text-left">Attendance</th>
                                    <th class="py-3 px-6 text-left">Quality</th>
                                    <th class="py-3 px-6 text-left">Productivity</th>
                                    <th class="py-3 px-6 text-left">Teamwork</th>
                                    <th class="py-3 px-6 text-left">Discipline</th>
                                    <th class="py-3 px-6 text-left">KPI Score</th>
                                    <th class="py-3 px-6 text-left">Total Score</th>
                                    <th class="py-3 px-6 text-left">Rating</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                  </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($performas as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $item->bulan_text }} {{ $item->tahun }}</td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-gray-200 text-gray-800 py-1 px-2 rounded text-xs">{{ $item->quarter }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 rounded-full h-2" style="width: {{ $item->attendance_rate }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $item->attendance_rate }}%</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 rounded-full h-2" style="width: {{ $item->quality }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $item->quality }}%</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-600 rounded-full h-2" style="width: {{ $item->productivity }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $item->productivity }}%</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-purple-600 rounded-full h-2" style="width: {{ $item->teamwork }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $item->teamwork }}%</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 rounded-full h-2" style="width: {{ $item->discipline }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $item->discipline }}%</span>
                                    </td>
                                    <td class="py-3 px-6 text-left font-semibold">{{ $item->kpi_score }}%</td>
                                    <td class="py-3 px-6 text-left font-bold text-lg">{{ $item->performance_score }}</td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $item->rating['color'] }}-100 text-{{ $item->rating['color'] }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $item->rating['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('performa.show', $item->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">Belum ada data penilaian performa</td>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const quarterlyData = @json($quarterlyData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Q1 (Jan-Mar)', 'Q2 (Apr-Jun)', 'Q3 (Jul-Sep)', 'Q4 (Oct-Dec)'],
                datasets: [{
                    label: 'Skor Performa',
                    data: [
                        quarterlyData.Q1 || 0,
                        quarterlyData.Q2 || 0,
                        quarterlyData.Q3 || 0,
                        quarterlyData.Q4 || 0
                    ],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Skor Performa'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Skor: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>