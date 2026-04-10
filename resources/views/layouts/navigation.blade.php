<nav x-data="{ open: false, dropdownOpen: false }" class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between h-16">
            <!-- Logo Section -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                HRIS Management
                            </a>
                        @else
                            <a href="{{ route('karyawan.dashboard') }}" class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                HRIS Management
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            HRIS Management
                        </a>
                    @endauth
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden md:flex md:ml-8 md:space-x-6">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                            <!-- Admin/HR Menu -->
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-gray-700 hover:text-blue-600 transition">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')" class="text-gray-700 hover:text-blue-600 transition">
                                Karyawan
                            </x-nav-link>
                            <x-nav-link :href="route('admin.departemen.index')" :active="request()->routeIs('admin.departemen.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Departemen
                            </x-nav-link>
                            <x-nav-link :href="route('admin.jabatan.index')" :active="request()->routeIs('admin.jabatan.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Jabatan
                            </x-nav-link>
                            <x-nav-link :href="route('admin.absensi.index')" :active="request()->routeIs('admin.absensi.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Absensi
                            </x-nav-link>
                            <x-nav-link :href="route('admin.cuti.index')" :active="request()->routeIs('admin.cuti.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Cuti
                            </x-nav-link>
                            <x-nav-link :href="route('admin.penggajian.index')" :active="request()->routeIs('admin.penggajian.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Penggajian
                            </x-nav-link>
                            <x-nav-link :href="route('admin.performa.index')" :active="request()->routeIs('admin.performa.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Performa
                            </x-nav-link>
                            <x-nav-link :href="route('admin.pengumuman.index')" :active="request()->routeIs('admin.pengumuman.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Pengumuman
                            </x-nav-link>
                        @else
                            <!-- Employee Menu -->
                            <x-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')" class="text-gray-700 hover:text-blue-600 transition">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Absensi
                            </x-nav-link>
                            <x-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Cuti
                            </x-nav-link>
                            <x-nav-link :href="route('penggajian.index')" :active="request()->routeIs('penggajian.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Slip Gaji
                            </x-nav-link>
                            <x-nav-link :href="route('performa.index')" :active="request()->routeIs('performa.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Performa
                            </x-nav-link>
                            <x-nav-link :href="route('pengumuman.index')" :active="request()->routeIs('pengumuman.*')" class="text-gray-700 hover:text-blue-600 transition">
                                Pengumuman
                            </x-nav-link>
                            <x-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')" class="text-gray-700 hover:text-blue-600 transition relative">
                                Notifikasi
                                <span id="notif-badge" class="absolute -top-1 -right-3 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 hidden"></span>
                            </x-nav-link>
                        @endif
                        
                        <!-- Profile Menu -->
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="text-gray-700 hover:text-blue-600 transition">
                            Profile
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                @auth
                    <div class="relative" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                            class="flex items-center space-x-2 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full transition">
                            <img class="h-9 w-9 rounded-full object-cover border-2 border-gray-200 hover:border-blue-500 transition"
                                src="{{ auth()->user()->foto_profil ? Storage::url(auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&bold=true&name=' . urlencode(auth()->user()->nama_lengkap) }}"
                                alt="{{ auth()->user()->nama_lengkap }}">
                            <div class="hidden lg:block text-left">
                                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->role == 'admin' ? 'Administrator' : (auth()->user()->role == 'hr' ? 'HR Staff' : 'Karyawan') }}</p>
                            </div>
                            <svg class="hidden lg:block h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg py-2 z-50 ring-1 ring-black ring-opacity-5">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        src="{{ auth()->user()->foto_profil ? Storage::url(auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&bold=true&name=' . urlencode(auth()->user()->nama_lengkap) }}"
                                        alt="{{ auth()->user()->nama_lengkap }}">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                        <p class="text-xs mt-1">
                                            @if(auth()->user()->role == 'admin')
                                                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full">Administrator</span>
                                            @elseif(auth()->user()->role == 'hr')
                                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full">HR Staff</span>
                                            @else
                                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Karyawan</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <!-- Mobile User Info -->
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <img class="h-12 w-12 rounded-full object-cover border-2 border-white shadow"
                            src="{{ auth()->user()->foto_profil ? Storage::url(auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&bold=true&name=' . urlencode(auth()->user()->nama_lengkap) }}"
                            alt="{{ auth()->user()->nama_lengkap }}">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->nama_lengkap }}</p>
                            <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                            @if(auth()->user()->role == 'admin')
                                <span class="inline-block mt-1 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">Administrator</span>
                            @elseif(auth()->user()->role == 'hr')
                                <span class="inline-block mt-1 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">HR Staff</span>
                            @else
                                <span class="inline-block mt-1 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Karyawan</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                    <!-- Admin/HR Mobile Menu -->
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="py-2">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')" class="py-2">
                        Data Karyawan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.departemen.index')" :active="request()->routeIs('admin.departemen.*')" class="py-2">
                        Departemen
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.jabatan.index')" :active="request()->routeIs('admin.jabatan.*')" class="py-2">
                        Jabatan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.absensi.index')" :active="request()->routeIs('admin.absensi.*')" class="py-2">
                        Absensi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.cuti.index')" :active="request()->routeIs('admin.cuti.*')" class="py-2">
                        Pengajuan Cuti
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.penggajian.index')" :active="request()->routeIs('admin.penggajian.*')" class="py-2">
                        Penggajian
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.performa.index')" :active="request()->routeIs('admin.performa.*')" class="py-2">
                        Penilaian Performa
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.pengumuman.index')" :active="request()->routeIs('admin.pengumuman.*')" class="py-2">
                        Pengumuman
                    </x-responsive-nav-link>
                @else
                    <!-- Employee Mobile Menu -->
                    <x-responsive-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')" class="py-2">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')" class="py-2">
                        Absensi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.*')" class="py-2">
                        Pengajuan Cuti
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('penggajian.index')" :active="request()->routeIs('penggajian.*')" class="py-2">
                        Slip Gaji
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('performa.index')" :active="request()->routeIs('performa.*')" class="py-2">
                        Penilaian Performa
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pengumuman.index')" :active="request()->routeIs('pengumuman.*')" class="py-2">
                        Pengumuman
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')" class="py-2 relative">
                        Notifikasi
                        <span id="notif-badge-mobile" class="ml-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 hidden"></span>
                    </x-responsive-nav-link>
                @endif
                
                <!-- Common Mobile Menu for All Users -->
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="py-2">
                        Profile Saya
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="py-2 text-red-600 hover:text-red-700">
                            Keluar
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>

<script>
    // Function to update notification badge
    function updateNotifCount() {
        @auth
        fetch('{{ route("notifikasi.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                // Update desktop badge
                const badge = document.getElementById('notif-badge');
                // Update mobile badge
                const mobileBadge = document.getElementById('notif-badge-mobile');
                
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    }
                    if (mobileBadge) {
                        mobileBadge.textContent = data.count;
                        mobileBadge.classList.remove('hidden');
                    }
                } else {
                    if (badge) {
                        badge.classList.add('hidden');
                    }
                    if (mobileBadge) {
                        mobileBadge.classList.add('hidden');
                    }
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
        @endauth
    }
    
    // Update notification count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateNotifCount();
        
        // Update every 30 seconds
        setInterval(updateNotifCount, 30000);
    });
</script>