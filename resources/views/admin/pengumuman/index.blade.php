<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Pengumuman
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('admin.pengumuman.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Buat Pengumuman
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Judul</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Target</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Tanggal Terbit</th>
                                    <th class="py-3 px-6 text-left">Berlaku Hingga</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                 </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($pengumuman as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $item->judul }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->kategori ?? '-' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if($item->target_role && $item->target_role != 'all')
                                            Role: {{ ucfirst($item->target_role) }}
                                        @else
                                            Semua Role
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $item->status ? 'green' : 'gray' }}-200 text-{{ $item->status ? 'green' : 'gray' }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $item->status ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->tanggal_berlaku_hingga ? $item->tanggal_berlaku_hingga->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <a href="{{ route('admin.pengumuman.edit', $item->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.pengumuman.destroy', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus pengumuman ini?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Tidak ada pengumuman</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pengumuman->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>