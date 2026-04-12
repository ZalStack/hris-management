<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pengajuan Change Day
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('absensi.update', $absensi->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Awal Change Day *</label>
                                <input type="date" name="change_day_tanggal_awal" value="{{ old('change_day_tanggal_awal', $absensi->change_day_tanggal_awal ? $absensi->change_day_tanggal_awal->format('Y-m-d') : '') }}" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('change_day_tanggal_awal')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Akhir Change Day *</label>
                                <input type="date" name="change_day_tanggal_akhir" value="{{ old('change_day_tanggal_akhir', $absensi->change_day_tanggal_akhir ? $absensi->change_day_tanggal_akhir->format('Y-m-d') : '') }}" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('change_day_tanggal_akhir')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jam Mulai *</label>
                                <input type="time" name="change_day_jam_mulai" value="{{ old('change_day_jam_mulai', $absensi->change_day_jam_mulai ? substr($absensi->change_day_jam_mulai, 0, 5) : '') }}" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('change_day_jam_mulai')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jam Selesai *</label>
                                <input type="time" name="change_day_jam_selesai" value="{{ old('change_day_jam_selesai', $absensi->change_day_jam_selesai ? substr($absensi->change_day_jam_selesai, 0, 5) : '') }}" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('change_day_jam_selesai')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Alasan Change Day *</label>
                                <textarea name="change_day_alasan" rows="4" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('change_day_alasan', $absensi->change_day_alasan) }}</textarea>
                                @error('change_day_alasan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan Tambahan (Opsional)</label>
                                <textarea name="keterangan" rows="3" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('keterangan', $absensi->keterangan) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-4">
                            <p class="text-yellow-700 text-sm">
                                <strong>Informasi:</strong> Pengajuan change day akan menggantikan jadwal kerja Anda pada tanggal yang dipilih. 
                                Perubahan jam kerja akan dihitung ulang secara otomatis setelah disetujui oleh HR/Admin.
                            </p>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('absensi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>