<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
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

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
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
                        @else
                            <x-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')">
                                Dashboard
                            </x-nav-link>
                        @endif
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                            Profile
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open"
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ auth()->user()->foto_profil ? Storage::url(auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode(auth()->user()->nama_lengkap) }}"
                                    alt="{{ auth()->user()->nama_lengkap }}">
                            </button>
                        </div>

                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

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

    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if (auth()->user()->isAdmin() || auth()->user()->isHR())
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
                @else
                    <x-responsive-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    Profile
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
