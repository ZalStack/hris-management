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
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Judul</label>
                                <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('judul')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Konten</label>
                                <textarea name="konten" rows="8" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('konten', $pengumuman->konten) }}</textarea>
                                @error('konten')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="kategori" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Pilih Kategori</option>
                                    <option value="umum" {{ old('kategori', $pengumuman->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                                    <option value="kebijakan" {{ old('kategori', $pengumuman->kategori) == 'kebijakan' ? 'selected' : '' }}>Kebijakan</option>
                                    <option value="pengumuman" {{ old('kategori', $pengumuman->kategori) == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                    <option value="event" {{ old('kategori', $pengumuman->kategori) == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="penting" {{ old('kategori', $pengumuman->kategori) == 'penting' ? 'selected' : '' }}>Penting</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Target Role</label>
                                <select name="target_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="all" {{ old('target_role', $pengumuman->target_role) == 'all' ? 'selected' : '' }}>Semua Role</option>
                                    <option value="admin" {{ old('target_role', $pengumuman->target_role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="hr" {{ old('target_role', $pengumuman->target_role) == 'hr' ? 'selected' : '' }}>HR</option>
                                    <option value="karyawan" {{ old('target_role', $pengumuman->target_role) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Terbit</label>
                                <input type="datetime-local" name="tanggal_terbit" value="{{ old('tanggal_terbit', $pengumuman->tanggal_terbit ? $pengumuman->tanggal_terbit->format('Y-m-d\TH:i') : '') }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk terbit sekarang</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Berlaku Hingga</label>
                                <input type="date" name="tanggal_berlaku_hingga" value="{{ old('tanggal_berlaku_hingga', $pengumuman->tanggal_berlaku_hingga ? $pengumuman->tanggal_berlaku_hingga->format('Y-m-d') : '') }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Lampiran</label>
                                @if($pengumuman->lampiran)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($pengumuman->lampiran) }}" target="_blank" class="text-blue-600 hover:text-blue-800">Lampiran saat ini</a>
                                    </div>
                                @endif
                                <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah lampiran</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    <input type="checkbox" name="status" value="1" {{ old('status', $pengumuman->status) ? 'checked' : '' }} class="mr-2">
                                    Terbitkan
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.pengumuman.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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