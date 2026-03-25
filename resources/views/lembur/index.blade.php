<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengajuan Lembur
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('lembur.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Ajukan Lembur
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Jam Mulai</th>
                                    <th class="py-3 px-6 text-left">Jam Selesai</th>
                                    <th class="py-3 px-6 text-left">Total Jam</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Keterangan</th>
                                    <th class="py-3 px-6 text-left">Catatan</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                  </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($lembur as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal_lembur->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}</td>
                                    <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</td>
                                    <td class="py-3 px-6 text-left">{{ number_format($item->total_jam, 2) }} jam</td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $item->status_color }}-200 text-{{ $item->status_color }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $item->status_text }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ Str::limit($item->keterangan, 50) }}</td>
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
                                                <a href="{{ route('lembur.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('lembur.destroy', $item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Yakin ingin membatalkan pengajuan lembur ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Batal
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Belum ada pengajuan lembur</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $lembur->links() }}
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