<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Penggajian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Gaji Dibayar</div>
                    <div class="text-2xl font-bold text-green-600">Rp {{ number_format($statistics['total_gaji'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-yellow-50 rounded-lg shadow p-4">
                    <div class="text-yellow-600 text-sm">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $statistics['total_pending'] }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg shadow p-4">
                    <div class="text-blue-600 text-sm">Approved</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_approved'] }}</div>
                </div>
                <div class="bg-green-50 rounded-lg shadow p-4">
                    <div class="text-green-600 text-sm">Paid</div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistics['total_paid'] }}</div>
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
                                @for($i=1; $i<=12; $i++)
                                    @php
                                        $bulanNama = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                    @endphp
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ $bulanNama[$i] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                            <select name="tahun" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
                                @for($i=date('Y')-2; $i<=date('Y')+1; $i++)
                                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                            <select name="status" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan</label>
                            <select name="karyawan_id" class="shadow border rounded w-full py-2 px-3">
                                <option value="">Semua</option>
                                @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}" {{ request('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                        {{ $karyawan->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            <a href="{{ route('admin.penggajian.export', request()->all()) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Export CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4">
                <a href="{{ route('admin.penggajian.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Buat Penggajian Baru
                </a>
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
                                    <th class="py-3 px-6 text-left">Gaji Pokok</th>
                                    <th class="py-3 px-6 text-left">Gaji Bersih</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Dibuat Oleh</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                 </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($penggajian as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">
                                        {{ $item->nama_karyawan }}<br>
                                        <small class="text-gray-500">{{ $item->karyawan->nip ?? '-' }}</small>
                                     </td>
                                    <td class="py-3 px-6 text-left">{{ $item->bulan_text }} {{ $item->tahun }}</td>
                                    <td class="py-3 px-6 text-left">Rp {{ $item->gaji_pokok }}</td>
                                    <td class="py-3 px-6 text-left font-semibold text-green-600">Rp {{ $item->gaji_bersih }}</td>
                                    <td class="py-3 px-6 text-left">{!! $item->status_badge !!}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->dibuat_oleh }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <a href="{{ route('admin.penggajian.show', $item->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.penggajian.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                            @if($item->status != 'paid')
                                            <button onclick="updateStatus({{ $item->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Update Status
                                            </button>
                                            @endif
                                            <form action="{{ route('admin.penggajian.destroy', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus data penggajian ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                     </td>
                                 </tr>
                                @empty
                                 <tr>
                                    <td colspan="7" class="text-center py-4">Tidak ada data penggajian</td>
                                 </tr>
                                @endforelse
                            </tbody>
                         </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $penggajian->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Status -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Update Status Penggajian</h3>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <select name="status" id="statusSelect" required class="shadow border rounded w-full py-2 px-3">
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-4" id="paymentFields" style="display: none;">
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_pembayaran" class="shadow border rounded w-full py-2 px-3">
                        </div>
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="shadow border rounded w-full py-2 px-3">
                                <option value="transfer">Transfer</option>
                                <option value="tunai">Tunai</option>
                                <option value="cek">Cek</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Bank</label>
                            <input type="text" name="nama_bank" class="shadow border rounded w-full py-2 px-3">
                        </div>
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" class="shadow border rounded w-full py-2 px-3">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                        <textarea name="catatan" rows="3" class="shadow border rounded w-full py-2 px-3"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeStatusModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentId = null;
        
        function updateStatus(id) {
            currentId = id;
            document.getElementById('statusForm').action = `/admin/penggajian/${id}/status`;
            document.getElementById('statusModal').classList.remove('hidden');
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            currentId = null;
        }
        
        document.getElementById('statusSelect').addEventListener('change', function() {
            const paymentFields = document.getElementById('paymentFields');
            if (this.value === 'paid') {
                paymentFields.style.display = 'block';
            } else {
                paymentFields.style.display = 'none';
            }
        });
    </script>
</x-app-layout>