<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Absensi Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <a href="{{ route('absensi.create') }}" class="inline-block mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Absensi Masuk
                </a>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Absensi</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Jam Masuk</th>
                                    <th class="py-3 px-6 text-left">Jam Pulang</th>
                                    <th class="py-3 px-6 text-left">Total Jam</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                 </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($absensi as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal->format('d/m/Y') }}</td>
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
                                    <td class="py-3 px-6 text-center">
                                        @if($item->status_kehadiran == 'pending')
                                            <a href="{{ route('absensi.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                        @endif
                                     </td>
                                 </tr>
                                @empty
                                 <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada data absensi</td>
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
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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

    <script>
        function openPulangModal(id) {
            document.getElementById('pulangForm').action = `/absensi/${id}/pulang`;
            document.getElementById('pulangModal').classList.remove('hidden');
        }
        
        function closePulangModal() {
            document.getElementById('pulangModal').classList.add('hidden');
        }
    </script>
</x-app-layout>