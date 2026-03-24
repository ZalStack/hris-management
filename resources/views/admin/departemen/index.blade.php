<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Departemen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('admin.departemen.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Departemen
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Kode</th>
                                    <th class="py-3 px-6 text-left">Nama Departemen</th>
                                    <th class="py-3 px-6 text-left">Kepala Departemen</th>
                                    <th class="py-3 px-6 text-left">Jumlah Jabatan</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($departemens as $departemen)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $departemen->kode_departemen }}</td>
                                    <td class="py-3 px-6 text-left">{{ $departemen->nama_departemen }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $departemen->kepalaDepartemen ? $departemen->kepalaDepartemen->nama_lengkap : '-' }}
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $departemen->jabatan->count() }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <a href="{{ route('admin.departemen.edit', $departemen->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.departemen.destroy', $departemen->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus departemen ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">Tidak ada data departemen</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $departemens->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>