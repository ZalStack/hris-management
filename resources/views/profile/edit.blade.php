<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profile Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Profile</h3>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">NIP</label>
                                <input type="text" value="{{ $karyawan->nip }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" readonly>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('nama_lengkap')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email', $karyawan->email) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                                <textarea name="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('alamat', $karyawan->alamat) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $karyawan->nik) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('nik')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">NPWP</label>
                                <input type="text" name="npwp" value="{{ old('npwp', $karyawan->npwp) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Agama</label>
                                <select name="agama" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih</option>
                                    <option value="Islam" {{ old('agama', $karyawan->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('agama', $karyawan->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Katolik" {{ old('agama', $karyawan->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ old('agama', $karyawan->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ old('agama', $karyawan->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Konghucu" {{ old('agama', $karyawan->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Pernikahan</label>
                                <select name="status_pernikahan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih</option>
                                    <option value="Belum Menikah" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                    <option value="Menikah" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="Cerai" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $karyawan->pendidikan_terakhir) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Universitas</label>
                                <input type="text" name="universitas" value="{{ old('universitas', $karyawan->universitas) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jurusan</label>
                                <input type="text" name="jurusan" value="{{ old('jurusan', $karyawan->jurusan) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Lulus</label>
                                <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', $karyawan->tahun_lulus) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kontak Darurat</label>
                                <input type="text" name="nama_kontak_darurat" value="{{ old('nama_kontak_darurat', $karyawan->nama_kontak_darurat) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Telepon Kontak Darurat</label>
                                <input type="text" name="telepon_kontak_darurat" value="{{ old('telepon_kontak_darurat', $karyawan->telepon_kontak_darurat) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Foto Profil</label>
                                @if($karyawan->foto_profil)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($karyawan->foto_profil) }}" class="w-32 h-32 rounded-full object-cover">
                                    </div>
                                @endif
                                <input type="file" name="foto_profil" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB, format JPG, JPEG, PNG</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h3>
                    
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Password Saat Ini</label>
                                <input type="password" name="current_password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('current_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Password Baru</label>
                                <input type="password" name="new_password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('new_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>