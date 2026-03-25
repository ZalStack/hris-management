<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Absensi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>Informasi:</strong> Silakan isi absensi Anda. Status absensi akan ditentukan oleh HR/Admin.
                            Anda dapat memilih jenis ketidakhadiran jika tidak masuk kerja.
                        </p>
                    </div>
                    
                    <form method="POST" action="{{ route('absensi.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Absensi</label>
                                <select name="jenis_absensi" id="jenis_absensi" required 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="masuk">Masuk Kerja (Hadir)</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih jenis absensi yang sesuai</p>
                            </div>
                            
                            <div id="jam_masuk_field">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jam Masuk</label>
                                <input type="time" name="jam_masuk" value="{{ old('jam_masuk', date('H:i')) }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('jam_masuk')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div id="lokasi_masuk_field">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi</label>
                                <input type="text" name="lokasi_masuk" value="{{ old('lokasi_masuk') }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Masukkan lokasi Anda">
                                @error('lokasi_masuk')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan</label>
                                <textarea name="keterangan" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Alasan izin/sakit (jika diperlukan)">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('absensi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('jenis_absensi').addEventListener('change', function() {
            const jenis = this.value;
            const jamMasukField = document.getElementById('jam_masuk_field');
            const lokasiMasukField = document.getElementById('lokasi_masuk_field');
            const jamMasukInput = document.querySelector('input[name="jam_masuk"]');
            const lokasiMasukInput = document.querySelector('input[name="lokasi_masuk"]');
            
            if (jenis === 'masuk') {
                jamMasukField.style.display = 'block';
                lokasiMasukField.style.display = 'block';
                jamMasukInput.required = true;
                lokasiMasukInput.required = true;
            } else {
                jamMasukField.style.display = 'none';
                lokasiMasukField.style.display = 'none';
                jamMasukInput.required = false;
                lokasiMasukInput.required = false;
                jamMasukInput.value = '';
                lokasiMasukInput.value = '';
            }
        });
        
        // Trigger on load
        document.getElementById('jenis_absensi').dispatchEvent(new Event('change'));
    </script>
</x-app-layout>