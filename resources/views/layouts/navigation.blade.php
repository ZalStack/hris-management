<nav x-data="{ open: false, dropdownOpen: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                                HRIS Management
                            </a>
                        @else
                            <a href="{{ route('karyawan.dashboard') }}" class="text-xl font-bold text-gray-800">
                                HRIS Management
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-xl font-bold text-gray-800">
                            HRIS Management
                        </a>
                    @endauth
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                            <!-- Admin/HR Menu -->
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')">
                                Data Karyawan
                            </x-nav-link>
                            <x-nav-link :href="route('admin.departemen.index')" :active="request()->routeIs('admin.departemen.*')">
                                Departemen
                            </x-nav-link>
                            <x-nav-link :href="route('admin.jabatan.index')" :active="request()->routeIs('admin.jabatan.*')">
                                Jabatan
                            </x-nav-link>
                            <x-nav-link :href="route('admin.penempatan.index')" :active="request()->routeIs('admin.penempatan.*')">
                                Penempatan
                            </x-nav-link>
                            <x-nav-link :href="route('admin.absensi.index')" :active="request()->routeIs('admin.absensi.*')">
                                Absensi
                            </x-nav-link>
                            <x-nav-link :href="route('admin.cuti.index')" :active="request()->routeIs('admin.cuti.*')">
                                Pengajuan Cuti
                            </x-nav-link>
                            <x-nav-link :href="route('admin.lembur.index')" :active="request()->routeIs('admin.lembur.*')">
                                Pengajuan Lembur
                            </x-nav-link>
                            <x-nav-link :href="route('admin.pengumuman.index')" :active="request()->routeIs('admin.pengumuman.*')">
                                Pengumuman
                            </x-nav-link>
                        @else
                            <!-- Employee Menu -->
                            <x-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')">
                                Absensi
                            </x-nav-link>
                            <x-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.*')">
                                Pengajuan Cuti
                            </x-nav-link>
                            <x-nav-link :href="route('lembur.index')" :active="request()->routeIs('lembur.*')">
                                Pengajuan Lembur
                            </x-nav-link>
                            <x-nav-link :href="route('pengumuman.index')" :active="request()->routeIs('pengumuman.*')">
                                Pengumuman
                            </x-nav-link>
                            <x-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')">
                                Notifikasi
                                <span id="notif-badge" class="ml-1 bg-red-500 text-white text-xs rounded-full px-1 py-0.5 hidden"></span>
                            </x-nav-link>
                        @endif
                        
                        <!-- Common Menu for All Users -->
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                            Profile
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <div class="relative ml-3" x-data="{ dropdownOpen: false }">
                        <div>
                            <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                                class="flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full">
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-300 hover:border-blue-500 transition"
                                    src="{{ auth()->user()->foto_profil ? Storage::url(auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode(auth()->user()->nama_lengkap) }}"
                                    alt="{{ auth()->user()->nama_lengkap }}">
                                <span class="ml-2 text-sm font-medium text-gray-700 hidden md:inline">
                                    {{ auth()->user()->nama_lengkap }}
                                </span>
                                <svg class="ml-1 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ strtoupper(auth()->user()->role) }}</span>
                                </p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
        class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <!-- Mobile User Info -->
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover"
                            src="{{ auth()->user()->foto_profil ? Storage::url(auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode(auth()->user()->nama_lengkap) }}"
                            alt="{{ auth()->user()->nama_lengkap }}">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ strtoupper(auth()->user()->role) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                    <!-- Admin/HR Mobile Menu -->
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')">
                        Data Karyawan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.departemen.index')" :active="request()->routeIs('admin.departemen.*')">
                        Departemen
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.jabatan.index')" :active="request()->routeIs('admin.jabatan.*')">
                        Jabatan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.penempatan.index')" :active="request()->routeIs('admin.penempatan.*')">
                        Penempatan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.absensi.index')" :active="request()->routeIs('admin.absensi.*')">
                        Absensi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.cuti.index')" :active="request()->routeIs('admin.cuti.*')">
                        Pengajuan Cuti
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.lembur.index')" :active="request()->routeIs('admin.lembur.*')">
                        Pengajuan Lembur
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.pengumuman.index')" :active="request()->routeIs('admin.pengumuman.*')">
                        Pengumuman
                    </x-responsive-nav-link>
                @else
                    <!-- Employee Mobile Menu -->
                    <x-responsive-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')">
                        Absensi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.*')">
                        Pengajuan Cuti
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lembur.index')" :active="request()->routeIs('lembur.*')">
                        Pengajuan Lembur
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pengumuman.index')" :active="request()->routeIs('pengumuman.*')">
                        Pengumuman
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')">
                        Notifikasi
                        <span id="notif-badge-mobile" class="ml-1 bg-red-500 text-white text-xs rounded-full px-1 py-0.5 hidden"></span>
                    </x-responsive-nav-link>
                @endif
                
                <!-- Common Mobile Menu for All Users -->
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    Profile Saya
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Logout
                    </x-responsive-nav-link>
                </form>
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