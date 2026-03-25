<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengumuman
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pengumuman as $item)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold px-2 py-1 bg-{{ $item->kategori == 'penting' ? 'red' : ($item->kategori == 'event' ? 'green' : 'blue') }}-100 text-{{ $item->kategori == 'penting' ? 'red' : ($item->kategori == 'event' ? 'green' : 'blue') }}-800 rounded-full">
                                {{ strtoupper($item->kategori ?? 'UMUM') }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $item->tanggal_terbit ? $item->tanggal_terbit->diffForHumans() : '-' }}</span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-800 mt-2 mb-3">
                            {{ $item->judul }}
                        </h3>
                        
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                            {{ Str::limit(strip_tags($item->konten), 150) }}
                        </p>
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route('pengumuman.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Baca Selengkapnya →
                            </a>
                            
                            @if($item->lampiran)
                                <a href="{{ Storage::url($item->lampiran) }}" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    Lampiran
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14M9 11h6m-6 4h3"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengumuman</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada pengumuman yang tersedia saat ini.</p>
                </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $pengumuman->links() }}
            </div>
        </div>
    </div>
</x-app-layout>