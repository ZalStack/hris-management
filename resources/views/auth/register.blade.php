<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Daftar Akun Baru
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Buat akun untuk mengakses sistem HRIS
                </p>
            </div>
            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" autocomplete="name" required 
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}">
                        @error('nama_lengkap')
                            <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="Email" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="Password">
                        @error('password')
                            <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="Konfirmasi Password">
                    </div>
                </div>

                @if ($errors->any() && !$errors->has('nama_lengkap') && !$errors->has('email') && !$errors->has('password'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Daftar
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            Login disini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>