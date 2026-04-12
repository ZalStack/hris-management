<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Absensi & Change Day
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
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
                <div class="bg-indigo-50 rounded-lg shadow p-4">
                    <div class="text-indigo-600 text-sm">Change Day Pending</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $statistics['change_day_pending'] }}</div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Karyawan</th>
                                    <th class="py-3 px-6 text-left">Tipe</th>
                                    <th class="py-3 px-6 text-left">Jam Masuk/Mulai</th>
                                    <th class="py-3 px-6 text-left">Jam Pulang/Selesai</th>
                                    <th class="py-3 px-6 text-left">Total Jam</th>
                                    <th class="py-3 px-6 text-left">Status</th>
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
                                                {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
                                            @endif
                                        @else
                                            {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $item->karyawan->nama_lengkap ?? $item->nama_karyawan }}<br>
                                        <small class="text-gray-500">{{ $item->karyawan->nip ?? '-' }}</small>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">Change Day</span>
                                            @if($item->change_day_status == 'pending')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded ml-1">Pending</span>
                                            @endif
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
                                    <td class="py-3 px-6 text-left">
                                        {{ $item->total_jam_kerja ? number_format($item->total_jam_kerja, 2) . ' jam' : '-' }}
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->is_change_day)
                                            {!! $item->change_day_status_badge !!}
                                        @else
                                            <form action="{{ route('admin.absensi.update-status', $item->id) }}" method="POST" class="inline-block" id="form-{{ $item->id }}">
                                                @csrf
                                                @method('PUT')
                                                <select name="status_kehadiran" onchange="updateStatus({{ $item->id }})" 
                                                    class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500
                                                    @if($item->status_kehadiran == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($item->status_kehadiran == 'hadir') bg-green-100 text-green-800
                                                    @elseif($item->status_kehadiran == 'izin') bg-blue-100 text-blue-800
                                                    @elseif($item->status_kehadiran == 'sakit') bg-purple-100 text-purple-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    <option value="pending" {{ $item->status_kehadiran == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="hadir" {{ $item->status_kehadiran == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="izin" {{ $item->status_kehadiran == 'izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="sakit" {{ $item->status_kehadiran == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="alpha" {{ $item->status_kehadiran == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <button onclick="showDetail({{ $item->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
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
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detail Absensi</h3>
                <div id="detailContent"></div>
                <div class="mt-4">
                    <form id="statusForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div id="changeDayStatusSection" style="display: none;">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status Change Day</label>
                            <select name="change_day_status" id="changeDaySelect" class="shadow border rounded w-full py-2 px-3 mb-4">
                                <option value="pending">Pending</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div id="catatanSection" style="display: none;">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                            <textarea name="change_day_catatan_admin" id="catatanAdmin" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="button" onclick="closeDetailModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Tutup
                            </button>
                            <button type="submit" id="submitBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentItem = null;
        
        function updateStatus(id) {
            if (confirm('Yakin ingin mengubah status absensi ini?')) {
                document.getElementById(`form-${id}`).submit();
            }
        }
        
        function showDetail(id) {
            fetch(`/admin/absensi/${id}`)
                .then(response => response.json())
                .then(data => {
                    currentItem = data;
                    
                    let content = `
                        <div class="space-y-2 mb-4">
                            <p><strong>Nama Karyawan:</strong> ${data.karyawan?.nama_lengkap || data.nama_karyawan}</p>
                            <p><strong>NIP:</strong> ${data.karyawan?.nip || '-'}</p>
                    `;
                    
                    if (data.is_change_day) {
                        content += `
                            <p><strong>Tipe:</strong> Change Day</p>
                            <p><strong>Tanggal Change Day:</strong> ${data.change_day_tanggal_awal ? new Date(data.change_day_tanggal_awal).toLocaleDateString('id-ID') : '-'} - ${data.change_day_tanggal_akhir ? new Date(data.change_day_tanggal_akhir).toLocaleDateString('id-ID') : '-'}</p>
                            <p><strong>Jam Change Day:</strong> ${data.change_day_jam_mulai?.substring(0,5) || '-'} - ${data.change_day_jam_selesai?.substring(0,5) || '-'}</p>
                            <p><strong>Alasan Change Day:</strong> ${data.change_day_alasan || '-'}</p>
                            <p><strong>Status Change Day:</strong> ${data.change_day_status}</p>
                        `;
                        
                        if (data.change_day_catatan_admin) {
                            content += `<p><strong>Catatan Admin:</strong> ${data.change_day_catatan_admin}</p>`;
                        }
                        
                        if (data.change_day_disetujui_pada) {
                            content += `<p><strong>Disetujui Pada:</strong> ${new Date(data.change_day_disetujui_pada).toLocaleString('id-ID')}</p>`;
                        }
                        
                        if (data.disetujui_oleh) {
                            content += `<p><strong>Disetujui Oleh:</strong> ${data.disetujui_oleh.nama_lengkap || '-'}</p>`;
                        }
                        
                        // Tampilkan form update status change day
                        document.getElementById('changeDayStatusSection').style.display = 'block';
                        document.getElementById('catatanSection').style.display = 'block';
                        document.getElementById('changeDaySelect').value = data.change_day_status || 'pending';
                        document.getElementById('catatanAdmin').value = data.change_day_catatan_admin || '';
                        
                    } else {
                        content += `
                            <p><strong>Tanggal:</strong> ${new Date(data.tanggal).toLocaleDateString('id-ID')}</p>
                            <p><strong>Jam Masuk:</strong> ${data.jam_masuk ? data.jam_masuk.substring(0,5) : '-'}</p>
                            <p><strong>Lokasi Masuk:</strong> ${data.lokasi_masuk || '-'}</p>
                            <p><strong>Jam Pulang:</strong> ${data.jam_pulang ? data.jam_pulang.substring(0,5) : '-'}</p>
                            <p><strong>Lokasi Pulang:</strong> ${data.lokasi_pulang || '-'}</p>
                            <p><strong>Total Jam Kerja:</strong> ${data.total_jam_kerja ? data.total_jam_kerja + ' jam' : '-'}</p>
                            <p><strong>Status Kehadiran:</strong> ${data.status_kehadiran}</p>
                        `;
                        
                        document.getElementById('changeDayStatusSection').style.display = 'none';
                        document.getElementById('catatanSection').style.display = 'none';
                    }
                    
                    content += `<p><strong>Keterangan:</strong> ${data.keterangan || '-'}</p>`;
                    content += `</div>`;
                    
                    document.getElementById('detailContent').innerHTML = content;
                    
                    // Setup form action
                    const statusForm = document.getElementById('statusForm');
                    statusForm.action = `/admin/absensi/${data.id}/status`;
                    
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail data');
                });
        }
        
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentItem = null;
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target == modal) {
                closeDetailModal();
            }
        }
    </script>
</x-app-layout>