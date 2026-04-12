<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Absensi Saya
            </h2>
            @if($pendingChangeDays > 0)
                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                    {{ $pendingChangeDays }} Pengajuan Change Day Menunggu
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Card untuk Change Day -->
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Change Day / Penggantian Hari Kerja:</strong> Anda dapat mengajukan change day jika ingin mengganti jadwal kerja di hari lain. 
                            Pengajuan harus disetujui oleh HR/Admin terlebih dahulu.
                        </p>
                    </div>
                </div>
            </div>

            @if($absensiToday && !$absensiToday->jam_pulang)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                <p class="font-bold">Perhatian!</p>
                <p>Anda sudah melakukan absensi masuk hari ini. Jangan lupa untuk melakukan absensi pulang.</p>
                <button onclick="openPulangModal({{ $absensiToday->id }})" class="mt-2 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Absensi Pulang
                </button>
            </div>
            @elseif(!$absensiToday)
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <p>Anda belum melakukan absensi hari ini. Silakan lakukan absensi sekarang.</p>
                <div class="mt-2 space-x-2">
                    <a href="{{ route('absensi.create') }}?type=masuk" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Absensi Masuk
                    </a>
                    <a href="{{ route('absensi.create') }}?type=change_day" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Ajukan Change Day
                    </a>
                </div>
            </div>
            @endif

            <!-- Tabel Absensi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Absensi & Change Day</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Tipe</th>
                                    <th class="py-3 px-6 text-left">Jam Masuk/Mulai</th>
                                    <th class="py-3 px-6 text-left">Jam Pulang/Selesai</th>
                                    <th class="py-3 px-6 text-left">Total Jam</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Keterangan</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                 </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($absensi as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        @if($item->is_change_day)
                                            @if($item->change_day_tanggal_awal && $item->change_day_tanggal_akhir)
                                                @if($item->change_day_tanggal_awal->format('Y-m-d') == $item->change_day_tanggal_akhir->format('Y-m-d'))
                                                    {{ $item->change_day_tanggal_awal->format('d/m/Y') }}
                                                @else
                                                    {{ $item->change_day_tanggal_awal->format('d/m/Y') }} - {{ $item->change_day_tanggal_akhir->format('d/m/Y') }}
                                                @endif
                                            @else
                                                {{ $item->tanggal->format('d/m/Y') }}
                                            @endif
                                        @else
                                            {{ $item->tanggal->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">Change Day</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded">Regular</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day)
                                            {{ $item->change_day_jam_mulai ? substr($item->change_day_jam_mulai, 0, 5) : '-' }}
                                        @else
                                            {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day)
                                            {{ $item->change_day_jam_selesai ? substr($item->change_day_jam_selesai, 0, 5) : '-' }}
                                        @else
                                            {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '-' }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $item->total_jam_kerja ? number_format($item->total_jam_kerja, 2) . ' jam' : '-' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day)
                                            @php
                                                $statusColors = [
                                                    'pending' => 'yellow',
                                                    'approved' => 'green',
                                                    'rejected' => 'red'
                                                ];
                                                $color = $statusColors[$item->change_day_status] ?? 'gray';
                                                $statusText = [
                                                    'pending' => 'PENDING',
                                                    'approved' => 'DISETUJUI',
                                                    'rejected' => 'DITOLAK'
                                                ];
                                                $text = $statusText[$item->change_day_status] ?? strtoupper($item->change_day_status);
                                            @endphp
                                            <span class="bg-{{ $color }}-200 text-{{ $color }}-800 py-1 px-3 rounded-full text-xs">
                                                {{ $text }}
                                            </span>
                                        @else
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
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day && $item->change_day_alasan)
                                            <button onclick="showDetail('{{ addslashes($item->change_day_alasan) }}', 'Alasan Change Day')" class="text-blue-600 hover:text-blue-800">
                                                Lihat Alasan
                                            </button>
                                        @elseif($item->keterangan)
                                            <button onclick="showDetail('{{ addslashes($item->keterangan) }}', 'Keterangan')" class="text-blue-600 hover:text-blue-800">
                                                Lihat
                                            </button>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <button onclick="showDetailModal({{ $item->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Detail
                                            </button>
                                            
                                            @if($item->is_change_day && $item->change_day_status == 'pending')
                                                <a href="{{ route('absensi.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('absensi.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan pengajuan change day ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Batal
                                                    </button>
                                                </form>
                                            @elseif(!$item->is_change_day && $item->status_kehadiran == 'pending' && !$item->jam_pulang)
                                                <a href="{{ route('absensi.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Edit
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Belum ada data absensi</td>
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

    <!-- Modal Absensi Pulang -->
    <div id="pulangModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Absensi Pulang</h3>
                <form id="pulangForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="jam_pulang" required 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="{{ date('H:i') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Pulang</label>
                        <input type="text" name="lokasi_pulang" id="lokasi_pulang" required 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Masukkan lokasi pulang">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closePulangModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detail Absensi</h3>
                <div id="detailContent" class="space-y-2"></div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeDetailModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Keterangan -->
    <div id="keteranganModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="keteranganTitle">Detail</h3>
                <div id="keteranganContent" class="text-gray-600"></div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeKeteranganModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPulangModal(id) {
            document.getElementById('pulangForm').action = '/absensi/' + id + '/pulang';
            document.getElementById('pulangModal').classList.remove('hidden');
        }
        
        function closePulangModal() {
            document.getElementById('pulangModal').classList.add('hidden');
        }
        
        function showDetail(content, title) {
            document.getElementById('keteranganTitle').innerText = title;
            document.getElementById('keteranganContent').innerText = content;
            document.getElementById('keteranganModal').classList.remove('hidden');
        }
        
        function closeKeteranganModal() {
            document.getElementById('keteranganModal').classList.add('hidden');
        }

        function showDetailModal(id) {
            fetch(`/absensi/${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    let content = '';
                    
                    if (data.is_change_day) {
                        content = `
                            <p><strong>Nama Karyawan:</strong> ${data.karyawan?.nama_lengkap || data.nama_karyawan}</p>
                            <p><strong>Tipe:</strong> Change Day</p>
                            <p><strong>Tanggal Change Day:</strong> ${data.change_day_tanggal_awal ? new Date(data.change_day_tanggal_awal).toLocaleDateString('id-ID') : '-'} - ${data.change_day_tanggal_akhir ? new Date(data.change_day_tanggal_akhir).toLocaleDateString('id-ID') : '-'}</p>
                            <p><strong>Jam Change Day:</strong> ${data.change_day_jam_mulai?.substring(0,5) || '-'} - ${data.change_day_jam_selesai?.substring(0,5) || '-'}</p>
                            <p><strong>Alasan Change Day:</strong> ${data.change_day_alasan || '-'}</p>
                            <p><strong>Status Change Day:</strong> ${data.change_day_status}</p>
                            ${data.change_day_catatan_admin ? `<p><strong>Catatan Admin:</strong> ${data.change_day_catatan_admin}</p>` : ''}
                        `;
                    } else {
                        content = `
                            <p><strong>Nama Karyawan:</strong> ${data.karyawan?.nama_lengkap || data.nama_karyawan}</p>
                            <p><strong>Tanggal:</strong> ${new Date(data.tanggal).toLocaleDateString('id-ID')}</p>
                            <p><strong>Jam Masuk:</strong> ${data.jam_masuk ? data.jam_masuk.substring(0,5) : '-'}</p>
                            <p><strong>Lokasi Masuk:</strong> ${data.lokasi_masuk || '-'}</p>
                            <p><strong>Jam Pulang:</strong> ${data.jam_pulang ? data.jam_pulang.substring(0,5) : '-'}</p>
                            <p><strong>Lokasi Pulang:</strong> ${data.lokasi_pulang || '-'}</p>
                            <p><strong>Total Jam Kerja:</strong> ${data.total_jam_kerja ? data.total_jam_kerja + ' jam' : '-'}</p>
                            <p><strong>Status Kehadiran:</strong> ${data.status_kehadiran}</p>
                        `;
                    }
                    
                    content += `<p><strong>Keterangan:</strong> ${data.keterangan || '-'}</p>`;
                    
                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail data');
                });
        }
        
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</x-app-layout>