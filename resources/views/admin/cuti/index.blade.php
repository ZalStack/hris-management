<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Pengajuan Cuti
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Pengajuan</div>
                    <div class="text-2xl font-bold">{{ $statistics['total'] }}</div>
                </div>
                <div class="bg-yellow-50 rounded-lg shadow p-4">
                    <div class="text-yellow-600 text-sm">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $statistics['pending'] }}</div>
                </div>
                <div class="bg-green-50 rounded-lg shadow p-4">
                    <div class="text-green-600 text-sm">Disetujui</div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistics['disetujui'] }}</div>
                </div>
                <div class="bg-red-50 rounded-lg shadow p-4">
                    <div class="text-red-600 text-sm">Ditolak</div>
                    <div class="text-2xl font-bold text-red-600">{{ $statistics['ditolak'] }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg shadow p-4">
                    <div class="text-blue-600 text-sm">Total Hari</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_hari'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Nama Karyawan</th>
                                    <th class="py-3 px-6 text-left">Jenis Cuti</th>
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Total Hari</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Alasan</th>
                                    <th class="py-3 px-6 text-left">Catatan</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                  </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($cuti as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">
                                        {{ $item->nama_karyawan }}<br>
                                        <small class="text-gray-500">{{ $item->karyawan->nip ?? '-' }}</small>
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $item->jenis_cuti_label }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->total_hari }} hari</td>
                                    <td class="py-3 px-6 text-left">
                                        <form action="{{ route('admin.cuti.update-status', $item->id) }}" method="POST" class="inline-block" id="status-form-{{ $item->id }}">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="document.getElementById('status-form-{{ $item->id }}').submit()" 
                                                class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500
                                                @if($item->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($item->status == 'disetujui') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="disetujui" {{ $item->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                                <option value="ditolak" {{ $item->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ Str::limit($item->alasan, 50) }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->catatan)
                                            <button onclick="showCatatan('{{ addslashes($item->catatan) }}')" class="text-blue-600 hover:text-blue-800 text-xs">
                                                Lihat Catatan
                                            </button>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <button onclick="showDetail({{ $item->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Detail & Update
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Tidak ada pengajuan cuti</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $cuti->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detail Pengajuan Cuti</h3>
                <div id="detailContent"></div>
                <div class="mt-4">
                    <form id="catatanForm" method="POST">
                        @csrf
                        @method('PUT')
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatanText" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        <div class="mt-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Update Status</label>
                            <select name="status" id="statusSelect" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="pending">Pending</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="button" onclick="closeDetailModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Tutup
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Catatan & Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Catatan -->
    <div id="catatanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Catatan</h3>
                <div id="catatanContent" class="text-gray-600"></div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeCatatanModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentCutiId = null;
        
        function showDetail(id) {
            currentCutiId = id;
            fetch(`/admin/cuti/${id}`)
                .then(response => response.json())
                .then(data => {
                    const content = `
                        <div class="space-y-2 mb-4">
                            <p><strong>Nama Karyawan:</strong> ${data.nama_karyawan}</p>
                            <p><strong>NIP:</strong> ${data.karyawan?.nip || '-'}</p>
                            <p><strong>Jenis Cuti:</strong> ${data.jenis_cuti_label}</p>
                            <p><strong>Tanggal:</strong> ${new Date(data.tanggal_mulai).toLocaleDateString('id-ID')} - ${new Date(data.tanggal_selesai).toLocaleDateString('id-ID')}</p>
                            <p><strong>Total Hari:</strong> ${data.total_hari} hari</p>
                            <p><strong>Alasan:</strong> ${data.alasan}</p>
                            ${data.lampiran ? `<p><strong>Lampiran:</strong> <a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 hover:text-blue-800">Lihat Lampiran</a></p>` : ''}
                            ${data.catatan ? `<p><strong>Catatan Sebelumnya:</strong> ${data.catatan}</p>` : ''}
                        </div>
                    `;
                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('catatanText').value = data.catatan || '';
                    document.getElementById('statusSelect').value = data.status;
                    document.getElementById('catatanForm').action = `/admin/cuti/${id}/status`;
                    document.getElementById('detailModal').classList.remove('hidden');
                });
        }
        
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentCutiId = null;
        }
        
        function showCatatan(catatan) {
            document.getElementById('catatanContent').innerHTML = catatan;
            document.getElementById('catatanModal').classList.remove('hidden');
        }
        
        function closeCatatanModal() {
            document.getElementById('catatanModal').classList.add('hidden');
        }
    </script>
</x-app-layout>