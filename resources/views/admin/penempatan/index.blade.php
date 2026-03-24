<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Penempatan Karyawan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('admin.penempatan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Penempatan
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Karyawan</th>
                                    <th class="py-3 px-6 text-left">Jabatan</th>
                                    <th class="py-3 px-6 text-left">Departemen</th>
                                    <th class="py-3 px-6 text-left">Tanggal Mulai</th>
                                    <th class="py-3 px-6 text-left">Tanggal Selesai</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Gaji Pokok</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                 </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($placements as $placement)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $placement->karyawan->nama_lengkap }}<br>
                                        <small class="text-gray-500">{{ $placement->karyawan->nip }}</small>
                                     </td>
                                    <td class="py-3 px-6 text-left">{{ $placement->jabatan->nama_jabatan }}<br>
                                        <small class="text-gray-500">{{ $placement->jabatan->kode_jabatan }}</small>
                                     </td>
                                    <td class="py-3 px-6 text-left">{{ $placement->jabatan->departemen->nama_departemen }} </td>
                                    <td class="py-3 px-6 text-left">{{ $placement->tanggal_mulai ? $placement->tanggal_mulai->format('d/m/Y') : '-' }} </td>
                                    <td class="py-3 px-6 text-left">{{ $placement->tanggal_selesai ? $placement->tanggal_selesai->format('d/m/Y') : '-' }} </td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $placement->status ? 'green' : 'red' }}-200 text-{{ $placement->status ? 'green' : 'red' }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $placement->status ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                     </td>
                                    <td class="py-3 px-6 text-left">Rp {{ number_format($placement->gaji_pokok ?? 0, 0, ',', '.') }} </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <a href="{{ route('admin.penempatan.edit', $placement->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.penempatan.destroy', $placement->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus penempatan ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                     </td>
                                 </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Tidak ada data penempatan karyawan</td>
                                 </tr>
                                @endforelse
                            </tbody>
                         </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $placements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>