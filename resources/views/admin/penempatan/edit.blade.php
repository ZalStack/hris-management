<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Penempatan Karyawan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.penempatan.update', $placement->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan</label>
                                <select name="karyawan_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $placement->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nip }} - {{ $karyawan->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('karyawan_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                                <select name="jabatan_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id }}" {{ old('jabatan_id', $placement->jabatan_id) == $jabatan->id ? 'selected' : '' }}>
                                            {{ $jabatan->kode_jabatan }} - {{ $jabatan->nama_jabatan }} ({{ $jabatan->departemen->nama_departemen }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $placement->tanggal_mulai ? $placement->tanggal_mulai->format('Y-m-d') : '') }}" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('tanggal_mulai')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $placement->tanggal_selesai ? $placement->tanggal_selesai->format('Y-m-d') : '') }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('tanggal_selesai')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="1" {{ old('status', $placement->status) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status', $placement->status) == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Gaji Pokok</label>
                                <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', $placement->gaji_pokok) }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('gaji_pokok')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Kepegawaian</label>
                                <select name="status_kepegawaian" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih Status</option>
                                    <option value="Tetap" {{ old('status_kepegawaian', $placement->status_kepegawaian) == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                                    <option value="Kontrak" {{ old('status_kepegawaian', $placement->status_kepegawaian) == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                    <option value="Magang" {{ old('status_kepegawaian', $placement->status_kepegawaian) == 'Magang' ? 'selected' : '' }}>Magang</option>
                                    <option value="Probation" {{ old('status_kepegawaian', $placement->status_kepegawaian) == 'Probation' ? 'selected' : '' }}>Probation</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Kontrak</label>
                                <input type="text" name="nomor_kontrak" value="{{ old('nomor_kontrak', $placement->nomor_kontrak) }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('nomor_kontrak')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.penempatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>