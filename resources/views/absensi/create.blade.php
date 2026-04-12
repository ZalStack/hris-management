<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(request('type') == 'change_day')
                Ajukan Change Day
            @else
                Absensi Masuk
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('absensi.store') }}" id="absensiForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Absensi *</label>
                            <select name="jenis_absensi" id="jenis_absensi" required class="shadow border rounded w-full py-2 px-3" onchange="toggleForm()">
                                <option value="masuk" {{ request('type') == 'masuk' || old('jenis_absensi') == 'masuk' ? 'selected' : '' }}>Absensi Masuk (Hadir)</option>
                                <option value="izin" {{ old('jenis_absensi') == 'izin' ? 'selected' : '' }}>Izin Tidak Masuk</option>
                                <option value="sakit" {{ old('jenis_absensi') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="change_day" {{ request('type') == 'change_day' || old('jenis_absensi') == 'change_day' ? 'selected' : '' }}>Change Day (Penggantian Hari Kerja)</option>
                            </select>
                            @error('jenis_absensi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form untuk Absensi Masuk -->
                        <div id="formMasuk" style="display: none;">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jam Masuk</label>
                                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk', date('H:i')) }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Masuk</label>
                                    <input type="text" name="lokasi_masuk" value="{{ old('lokasi_masuk') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="Masukkan lokasi Anda saat ini">
                                </div>
                            </div>
                        </div>

                        <!-- Form untuk Change Day -->
                        <div id="formChangeDay" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Awal Change Day *</label>
                                    <input type="date" name="change_day_tanggal_awal" value="{{ old('change_day_tanggal_awal') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Akhir Change Day *</label>
                                    <input type="date" name="change_day_tanggal_akhir" value="{{ old('change_day_tanggal_akhir') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jam Mulai *</label>
                                    <input type="time" name="change_day_jam_mulai" value="{{ old('change_day_jam_mulai', '08:00') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jam Selesai *</label>
                                    <input type="time" name="change_day_jam_selesai" value="{{ old('change_day_jam_selesai', '17:00') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Alasan Change Day *</label>
                                    <textarea name="change_day_alasan" rows="4" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('change_day_alasan') }}</textarea>
                                </div>
                            </div>
                            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-500 p-4">
                                <p class="text-yellow-700 text-sm">
                                    <strong>Informasi:</strong> Change day digunakan untuk mengganti hari kerja yang jatuh di hari libur atau 
                                    situasi khusus lainnya. Pengajuan harus disetujui oleh HR/Admin terlebih dahulu.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4" id="keteranganSection">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('keterangan') }}</textarea>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('absensi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                @if(request('type') == 'change_day')
                                    Ajukan Change Day
                                @else
                                    Simpan
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const jenis = document.getElementById('jenis_absensi').value;
            const formMasuk = document.getElementById('formMasuk');
            const formChangeDay = document.getElementById('formChangeDay');
            const keteranganSection = document.getElementById('keteranganSection');
            
            // Sembunyikan semua form terlebih dahulu
            formMasuk.style.display = 'none';
            formChangeDay.style.display = 'none';
            
            // Tampilkan form sesuai pilihan
            if (jenis === 'masuk') {
                formMasuk.style.display = 'block';
                // Buat field required
                document.querySelector('input[name="jam_masuk"]').required = true;
                document.querySelector('input[name="lokasi_masuk"]').required = true;
                // Non-required untuk change day
                document.querySelector('input[name="change_day_tanggal_awal"]').required = false;
                document.querySelector('input[name="change_day_tanggal_akhir"]').required = false;
                document.querySelector('input[name="change_day_jam_mulai"]').required = false;
                document.querySelector('input[name="change_day_jam_selesai"]').required = false;
                document.querySelector('textarea[name="change_day_alasan"]').required = false;
                keteranganSection.style.display = 'block';
            } else if (jenis === 'change_day') {
                formChangeDay.style.display = 'block';
                // Non-required untuk absensi masuk
                document.querySelector('input[name="jam_masuk"]').required = false;
                document.querySelector('input[name="lokasi_masuk"]').required = false;
                // Required untuk change day
                document.querySelector('input[name="change_day_tanggal_awal"]').required = true;
                document.querySelector('input[name="change_day_tanggal_akhir"]').required = true;
                document.querySelector('input[name="change_day_jam_mulai"]').required = true;
                document.querySelector('input[name="change_day_jam_selesai"]').required = true;
                document.querySelector('textarea[name="change_day_alasan"]').required = true;
                keteranganSection.style.display = 'block';
            } else {
                // Izin atau sakit
                formMasuk.style.display = 'none';
                formChangeDay.style.display = 'none';
                document.querySelector('input[name="jam_masuk"]').required = false;
                document.querySelector('input[name="lokasi_masuk"]').required = false;
                document.querySelector('input[name="change_day_tanggal_awal"]').required = false;
                document.querySelector('input[name="change_day_tanggal_akhir"]').required = false;
                document.querySelector('input[name="change_day_jam_mulai"]').required = false;
                document.querySelector('input[name="change_day_jam_selesai"]').required = false;
                document.querySelector('textarea[name="change_day_alasan"]').required = false;
                keteranganSection.style.display = 'block';
            }
        }
        
        // Panggil toggleForm saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            toggleForm();
        });
    </script>
</x-app-layout>