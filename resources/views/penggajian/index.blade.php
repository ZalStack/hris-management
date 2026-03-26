<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Slip Gaji Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Periode</th>
                                    <th class="py-3 px-6 text-left">Gaji Pokok</th>
                                    <th class="py-3 px-6 text-left">Tunjangan</th>
                                    <th class="py-3 px-6 text-left">Uang Lembur</th>
                                    <th class="py-3 px-6 text-left">Gaji Bersih</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                  </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($penggajian as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $item->bulan_text }} {{ $item->tahun }}</td>
                                    <td class="py-3 px-6 text-left">Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-left">Rp {{ number_format($item->total_tunjangan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-left">Rp {{ number_format($item->uang_lembur, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-left font-semibold text-green-600">Rp {{ number_format($item->gaji_bersih, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {!! $item->status_badge !!}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('penggajian.show', $item->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Belum ada data slip gaji</td>
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
</x-app-layout>