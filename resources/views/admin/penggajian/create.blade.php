<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Penggajian Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.penggajian.store') }}" id="gajiForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan *</label>
                                <select name="karyawan_id" id="karyawan_id" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}"
                                            {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Bulan *</label>
                                <select name="bulan" id="bulan" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Bulan</option>
                                    @foreach ($bulan as $b)
                                        <option value="{{ $b }}" {{ old('bulan') == $b ? 'selected' : '' }}>
                                            @php
                                                $bulanNama = [
                                                    1 => 'Januari',
                                                    2 => 'Februari',
                                                    3 => 'Maret',
                                                    4 => 'April',
                                                    5 => 'Mei',
                                                    6 => 'Juni',
                                                    7 => 'Juli',
                                                    8 => 'Agustus',
                                                    9 => 'September',
                                                    10 => 'Oktober',
                                                    11 => 'November',
                                                    12 => 'Desember',
                                                ];
                                            @endphp
                                            {{ $bulanNama[$b] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun *</label>
                                <select name="tahun" id="tahun" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $t)
                                        <option value="{{ $t }}" {{ old('tahun') == $t ? 'selected' : '' }}>
                                            {{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komponen Gaji</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Gaji Pokok *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="gaji_pokok" name="gaji_pokok"
                                            value="{{ old('gaji_pokok', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Total Tunjangan *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="total_tunjangan" name="total_tunjangan"
                                            value="{{ old('total_tunjangan', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Uang Lembur *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="uang_lembur" name="uang_lembur"
                                            value="{{ old('uang_lembur', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Bonus *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="bonus" name="bonus"
                                            value="{{ old('bonus', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Potongan</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Pajak *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="jumlah_pajak" name="jumlah_pajak"
                                            value="{{ old('jumlah_pajak', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Potongan BPJS *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="potongan_bpjs" name="potongan_bpjs"
                                            value="{{ old('potongan_bpjs', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Total Potongan Lainnya
                                        *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="text" id="total_potongan" name="total_potongan"
                                            value="{{ old('total_potongan', '0') }}" required
                                            class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            onkeyup="formatRupiah(this); calculateTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Total Pendapatan
                                            Kotor</label>
                                        <div class="text-2xl font-bold text-green-600" id="total_kotor_display">Rp 0
                                        </div>
                                        <input type="hidden" id="total_kotor" name="total_kotor">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Gaji Bersih *</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                            <input type="text" id="gaji_bersih" name="gaji_bersih" required
                                                class="shadow border rounded w-full pl-10 pr-3 py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Status *</label>
                                    <select name="status" required class="shadow border rounded w-full py-2 px-3">
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                            Pending Approval</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" class="shadow border rounded w-full py-2 px-3">
                                        <option value="">Pilih Metode</option>
                                        <option value="transfer"
                                            {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer
                                            Bank</option>
                                        <option value="tunai"
                                            {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                        <option value="cek"
                                            {{ old('metode_pembayaran') == 'cek' ? 'selected' : '' }}>Cek</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Bank</label>
                                    <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                                        class="shadow border rounded w-full py-2 px-3">
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Rekening</label>
                                    <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening') }}"
                                        class="shadow border rounded w-full py-2 px-3">
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal
                                        Pembayaran</label>
                                    <input type="date" name="tanggal_pembayaran"
                                        value="{{ old('tanggal_pembayaran') }}"
                                        class="shadow border rounded w-full py-2 px-3">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                    <textarea name="catatan" rows="3" class="shadow border rounded w-full py-2 px-3">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.penggajian.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Penggajian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatRupiah(element) {
            let value = element.value.replace(/[^,\d]/g, '');
            value = value.replace(/\D/g, '');

            // Hapus leading zeros
            value = value.replace(/^0+(?=\d)/, '');

            if (value === '') {
                value = '0';
            }

            // Tambahkan titik sebagai pemisah ribuan
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            element.value = value;
        }

        function parseRupiah(value) {
            if (!value || value === '0') return 0;
            return parseInt(value.replace(/\./g, '')) || 0;
        }

        function calculateTotal() {
            let gajiPokok = parseRupiah(document.getElementById('gaji_pokok').value);
            let tunjangan = parseRupiah(document.getElementById('total_tunjangan').value);
            let uangLembur = parseRupiah(document.getElementById('uang_lembur').value);
            let bonus = parseRupiah(document.getElementById('bonus').value);
            let pajak = parseRupiah(document.getElementById('jumlah_pajak').value);
            let bpjs = parseRupiah(document.getElementById('potongan_bpjs').value);
            let potonganLain = parseRupiah(document.getElementById('total_potongan').value);

            let totalKotor = gajiPokok + tunjangan + uangLembur + bonus;
            let totalPotongan = pajak + bpjs + potonganLain;
            let gajiBersih = Math.max(0, totalKotor - totalPotongan);

            document.getElementById('total_kotor').value = totalKotor;
            document.getElementById('total_kotor_display').innerText =
                'Rp ' + new Intl.NumberFormat('id-ID').format(totalKotor);

            document.getElementById('gaji_bersih').value =
                new Intl.NumberFormat('id-ID').format(gajiBersih);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = ['gaji_pokok', 'total_tunjangan', 'uang_lembur', 'bonus',
                'jumlah_pajak', 'potongan_bpjs', 'total_potongan'
            ];

            inputs.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('keyup', function() {
                        formatRupiah(this);
                        calculateTotal();
                    });

                    // Format saat load
                    if (element.value && element.value !== '0') {
                        formatRupiah(element);
                    } else {
                        element.value = '0';
                    }
                }
            });

            calculateTotal();
        });
    </script>
</x-app-layout>
