<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $pengumuman->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex space-x-2">
                            <span class="text-xs font-semibold px-2 py-1 bg-{{ $pengumuman->kategori == 'penting' ? 'red' : ($pengumuman->kategori == 'event' ? 'green' : 'blue') }}-100 text-{{ $pengumuman->kategori == 'penting' ? 'red' : ($pengumuman->kategori == 'event' ? 'green' : 'blue') }}-800 rounded-full">
                                {{ strtoupper($pengumuman->kategori ?? 'UMUM') }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Dipublikasikan: {{ $pengumuman->tanggal_terbit ? $pengumuman->tanggal_terbit->format('d/m/Y H:i') : '-' }}
                            </span>
                            @if($pengumuman->tanggal_berlaku_hingga)
                                <span class="text-xs text-gray-500">
                                    Berlaku hingga: {{ $pengumuman->tanggal_berlaku_hingga->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="prose max-w-none">
                        {!! nl2br(e($pengumuman->konten)) !!}
                    </div>
                    
                    @if($pengumuman->lampiran)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Lampiran</h4>
                            <a href="{{ Storage::url($pengumuman->lampiran) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                Download Lampiran
                            </a>
                        </div>
                    @endif
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('pengumuman.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>