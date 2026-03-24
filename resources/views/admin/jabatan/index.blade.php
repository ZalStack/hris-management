<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Jabatan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('admin.jabatan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Jabatan
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Kode</th>
                                    <th class="py-3 px-6 text-left">Nama Jabatan</th>
                                    <th class="py-3 px-6 text-left">Departemen</th>
                                    <th class="py-3 px-6 text-left">Gaji Minimal</th>
                                    <th class="py-3 px-6 text-left">Gaji Maksimal</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($jabatans as $jabatan)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $jabatan->kode_jabatan }}</td>
                                    <td class="py-3 px-6 text-left">{{ $jabatan->nama_jabatan }}</td>
                                    <td class="py-3 px-6 text-left">{{ $jabatan->departemen->nama_departemen }}</td>
                                    <td class="py-3 px-6 text-left">Rp {{ number_format($jabatan->gaji_minimal ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-left">Rp {{ number_format($jabatan->gaji_maksimal ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <a href="{{ route('admin.jabatan.edit', $jabatan->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.jabatan.destroy', $jabatan->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus jabatan ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada data jabatan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $jabatans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>