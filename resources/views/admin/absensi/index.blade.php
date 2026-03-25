<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Absensi Karyawan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total</div>
                    <div class="text-2xl font-bold">{{ $statistics['total'] }}</div>
                </div>
                <div class="bg-yellow-50 rounded-lg shadow p-4">
                    <div class="text-yellow-600 text-sm">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $statistics['pending'] }}</div>
                </div>
                <div class="bg-green-50 rounded-lg shadow p-4">
                    <div class="text-green-600 text-sm">Hadir</div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistics['hadir'] }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg shadow p-4">
                    <div class="text-blue-600 text-sm">Izin</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['izin'] }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg shadow p-4">
                    <div class="text-purple-600 text-sm">Sakit</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $statistics['sakit'] }}</div>
                </div>
                <div class="bg-red-50 rounded-lg shadow p-4">
                    <div class="text-red-600 text-sm">Alpha</div>
                    <div class="text-2xl font-bold text-red-600">{{ $statistics['alpha'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Karyawan</th>
                                    <th class="py-3 px-6 text-left">Jam Masuk</th>
                                    <th class="py-3 px-6 text-left">Jam Pulang</th>
                                    <th class="py-3 px-6 text-left">Total Jam</th>
                                    <th class="py-3 px-6 text-left">Status Saat Ini</th>
                                    <th class="py-3 px-6 text-left">Aksi Persetujuan</th>
                                    <th class="py-3 px-6 text-center">Detail</th>
                                  </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($absensi as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $item->karyawan->nama_lengkap }}<br>
                                        <small class="text-gray-500">{{ $item->karyawan->nip }}</small>
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '-' }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->total_jam_kerja ? number_format($item->total_jam_kerja, 2) . ' jam' : '-' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @php
                                            $statusColors = [
                                                'pending' => 'yellow',
                                                'hadir' => 'green',
                                                'izin' => 'blue',
                                                'sakit' => 'purple',
                                                'alpha' => 'red'
                                            ];
                                            $color = $statusColors[$item->status_kehadiran] ?? 'gray';
                                        @endphp
                                        <span class="bg-{{ $color }}-200 text-{{ $color }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ strtoupper($item->status_kehadiran) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <form action="{{ route('admin.absensi.update-status', $item->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PUT')
                                            <select name="status_kehadiran" onchange="confirmStatusChange(this)" 
                                                class="text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="pending" {{ $item->status_kehadiran == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                <option value="hadir" {{ $item->status_kehadiran == 'hadir' ? 'selected' : '' }}>✅ Setujui - Hadir</option>
                                                <option value="izin" {{ $item->status_kehadiran == 'izin' ? 'selected' : '' }}>📝 Setujui - Izin</option>
                                                <option value="sakit" {{ $item->status_kehadiran == 'sakit' ? 'selected' : '' }}>🤒 Setujui - Sakit</option>
                                                <option value="alpha" {{ $item->status_kehadiran == 'alpha' ? 'selected' : '' }}>❌ Tolak - Alpha</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <button onclick="viewDetail({{ $item->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Tidak ada data absensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $absensi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detail Absensi</h3>
                <div id="detailContent"></div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeDetailModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmStatusChange(select) {
            const selectedValue = select.value;
            let message = '';
            
            switch(selectedValue) {
                case 'hadir':
                    message = 'Setujui absensi ini sebagai HADIR?';
                    break;
                case 'izin':
                    message = 'Setujui absensi ini sebagai IZIN?';
                    break;
                case 'sakit':
                    message = 'Setujui absensi ini sebagai SAKIT?';
                    break;
                case 'alpha':
                    message = 'Tolak absensi ini (ALPHA)?';
                    break;
                default:
                    message = 'Ubah status absensi?';
            }
            
            if (confirm(message)) {
                select.form.submit();
            } else {
                select.value = select.getAttribute('data-original-value') || 'pending';
            }
        }
        
        function viewDetail(id) {
            fetch(`/admin/absensi/${id}`)
                .then(response => response.json())
                .then(data => {
                    const content = `
                        <div class="space-y-2">
                            <p><strong>Nama:</strong> ${data.karyawan.nama_lengkap}</p>
                            <p><strong>NIP:</strong> ${data.karyawan.nip}</p>
                            <p><strong>Tanggal:</strong> ${new Date(data.tanggal).toLocaleDateString('id-ID')}</p>
                            <p><strong>Jam Masuk:</strong> ${data.jam_masuk ? data.jam_masuk.substring(0,5) : '-'}</p>
                            <p><strong>Lokasi Masuk:</strong> ${data.lokasi_masuk || '-'}</p>
                            <p><strong>Jam Pulang:</strong> ${data.jam_pulang ? data.jam_pulang.substring(0,5) : '-'}</p>
                            <p><strong>Lokasi Pulang:</strong> ${data.lokasi_pulang || '-'}</p>
                            <p><strong>Total Jam Kerja:</strong> ${data.total_jam_kerja ? data.total_jam_kerja + ' jam' : '-'}</p>
                            <p><strong>Keterangan:</strong> ${data.keterangan || '-'}</p>
                        </div>
                    `;
                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('detailModal').classList.remove('hidden');
                });
        }
        
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</x-app-layout>