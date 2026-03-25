<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifikasi
                @if($unreadCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
                @endif
            </h2>
            @if($unreadCount > 0)
                <form action="{{ route('notifikasi.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($notifikasi as $item)
                        <div class="border-b border-gray-200 pb-4 last:border-0 {{ !$item->status ? 'bg-blue-50' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        @if(!$item->status)
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                        @endif
                                        <h3 class="font-semibold text-gray-800">{{ $item->judul }}</h3>
                                        <span class="ml-3 text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ $item->pesan }}</p>
                                    <div class="mt-2 flex items-center text-xs text-gray-500">
                                        <span class="px-2 py-1 bg-gray-100 rounded">
                                            {{ strtoupper($item->tipe_notifikasi) }}
                                        </span>
                                    </div>
                                </div>
                                @if(!$item->status)
                                    <button onclick="markAsRead({{ $item->id }})" class="ml-4 text-blue-600 hover:text-blue-800 text-sm">
                                        Tandai Dibaca
                                    </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada notifikasi baru.</p>
                        </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-6">
                        {{ $notifikasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAsRead(id) {
            fetch(`/notifikasi/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</x-app-layout>