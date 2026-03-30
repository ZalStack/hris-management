<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pengumuman
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.pengumuman.update', $pengumuman->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Judul -->
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Judul</label>
                                <input type="text" name="judul" 
                                    value="{{ old('judul', $pengumuman->judul) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>

                            <!-- Konten -->
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Konten</label>
                                <textarea name="konten" rows="8" required 
                                    class="shadow border rounded w-full py-2 px-3">{{ old('konten', $pengumuman->konten) }}</textarea>
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="kategori" class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Kategori</option>
                                    <option value="umum" {{ old('kategori', $pengumuman->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                                    <option value="kebijakan" {{ old('kategori', $pengumuman->kategori) == 'kebijakan' ? 'selected' : '' }}>Kebijakan</option>
                                    <option value="pengumuman" {{ old('kategori', $pengumuman->kategori) == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                    <option value="event" {{ old('kategori', $pengumuman->kategori) == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="penting" {{ old('kategori', $pengumuman->kategori) == 'penting' ? 'selected' : '' }}>Penting</option>
                                </select>
                            </div>

                            <!-- Target Role -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Target Role</label>
                                <select name="target_role" class="shadow border rounded w-full py-2 px-3">
                                    <option value="all" {{ old('target_role', $pengumuman->target_role) == 'all' ? 'selected' : '' }}>Semua Role</option>
                                    <option value="admin" {{ old('target_role', $pengumuman->target_role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="hr" {{ old('target_role', $pengumuman->target_role) == 'hr' ? 'selected' : '' }}>HR</option>
                                    <option value="karyawan" {{ old('target_role', $pengumuman->target_role) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                </select>
                            </div>

                            <!-- Tanggal Terbit -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Terbit</label>
                                <input type="datetime-local" name="tanggal_terbit"
                                    value="{{ old('tanggal_terbit', $pengumuman->tanggal_terbit ? $pengumuman->tanggal_terbit->format('Y-m-d\TH:i') : '') }}"
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>

                            <!-- Berlaku Hingga -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Berlaku Hingga</label>
                                <input type="date" name="tanggal_berlaku_hingga"
                                    value="{{ old('tanggal_berlaku_hingga', $pengumuman->tanggal_berlaku_hingga) }}"
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>

                            <!-- Lampiran -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Lampiran</label>
                                <input type="file" name="lampiran"
                                    class="shadow border rounded w-full py-2 px-3">
                                
                                @if($pengumuman->lampiran)
                                    <p class="text-xs mt-2">
                                        File sekarang: 
                                        <a href="{{ Storage::url($pengumuman->lampiran) }}" target="_blank" class="text-blue-500">
                                            Lihat
                                        </a>
                                    </p>
                                @endif
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    <input type="checkbox" name="status" value="1"
                                        {{ old('status', $pengumuman->status) ? 'checked' : '' }}>
                                    Terbitkan Sekarang
                                </label>
                            </div>

                        </div>

                        <!-- Action -->
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.pengumuman.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Update
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>