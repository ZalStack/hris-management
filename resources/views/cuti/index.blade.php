<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengajuan Cuti
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Kuota Cuti Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg mb-6">
                <div class="p-6">
                    <h3 class="text-white text-lg font-semibold mb-4">Sisa Kuota Cuti Tahunan {{ date('Y') }}</h3>
                    <div class="flex items-center justify-between">
                        <div class="text-center">
                            <div class="text-white text-3xl font-bold">{{ $kuotaTotal }}</div>
                            <div class="text-blue-100 text-sm">Kuota Total</div>
                        </div>
                        <div class="text-center">
                            <div class="text-white text-3xl font-bold">{{ $kuotaTerpakai }}</div>
                            <div class="text-blue-100 text-sm">Terpakai</div>
                        </div>
                        <div class="text-center">
                            <div class="text-white text-3xl font-bold">{{ $sisaKuota }}</div>
                            <div class="text-blue-100 text-sm">Sisa</div>
                        </div>
                    </div>
                    <div class="mt-4 w-full bg-blue-400 rounded-full h-2">
                        <div class="bg-yellow-400 rounded-full h-2" style="width: {{ $kuotaTerpakai > 0 ? ($kuotaTerpakai / $kuotaTotal) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('cuti.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Ajukan Cuti
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
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
                                    <td class="py-3 px-6 text-left">{{ $item->jenis_cuti_label }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->total_hari }} hari</td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $item->status_color }}-200 text-{{ $item->status_color }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $item->status_text }}
                                        </span>
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
                                        @if($item->status == 'pending')
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('cuti.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('cuti.destroy', $item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Yakin ingin membatalkan pengajuan cuti ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Batal
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Belum ada pengajuan cuti</td>
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
        function showCatatan(catatan) {
            document.getElementById('catatanContent').innerHTML = catatan;
            document.getElementById('catatanModal').classList.remove('hidden');
        }
        
        function closeCatatanModal() {
            document.getElementById('catatanModal').classList.add('hidden');
        }
    </script>
</x-app-layout>