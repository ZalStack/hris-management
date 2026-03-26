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
                    <form id="salaryForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan</label>
                                <select id="karyawan_id" name="karyawan_id" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}
                                            ({{ $karyawan->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Bulan</label>
                                <select id="bulan" name="bulan" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Bulan</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                                <select id="tahun" name="tahun" required
                                    class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" onclick="calculateSalary()"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Hitung Gaji
                            </button>
                        </div>
                    </form>

                    <div id="resultContainer" style="display: none;" class="mt-6">
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Hasil Perhitungan Gaji</h3>
                            <div id="calculationResult"></div>

                            <form id="storeForm" method="POST" action="{{ route('admin.penggajian.store') }}"
                                class="mt-6">
                                @csrf
                                <input type="hidden" name="karyawan_id" id="store_karyawan_id">
                                <input type="hidden" name="bulan" id="store_bulan">
                                <input type="hidden" name="tahun" id="store_tahun">
                                <input type="hidden" name="gaji_pokok" id="store_gaji_pokok">
                                <input type="hidden" name="total_tunjangan" id="store_total_tunjangan">
                                <input type="hidden" name="uang_lembur" id="store_uang_lembur">
                                <input type="hidden" name="bonus" id="store_bonus">
                                <input type="hidden" name="total_potongan" id="store_total_potongan">
                                <input type="hidden" name="jumlah_pajak" id="store_jumlah_pajak">
                                <input type="hidden" name="potongan_bpjs" id="store_potongan_bpjs">
                                <input type="hidden" name="gaji_bersih" id="store_gaji_bersih">
                                <input type="hidden" name="detail_gaji" id="store_detail_gaji">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                        <select name="status" required class="shadow border rounded w-full py-2 px-3">
                                            <option value="draft">Draft</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Metode
                                            Pembayaran</label>
                                        <select name="metode_pembayaran" class="shadow border rounded w-full py-2 px-3">
                                            <option value="">Pilih Metode</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="tunai">Tunai</option>
                                            <option value="cek">Cek</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Bank</label>
                                        <input type="text" name="nama_bank"
                                            class="shadow border rounded w-full py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Nomor
                                            Rekening</label>
                                        <input type="text" name="nomor_rekening"
                                            class="shadow border rounded w-full py-2 px-3">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                        <textarea name="catatan" rows="3" class="shadow border rounded w-full py-2 px-3"></textarea>
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
        </div>
    </div>

    <script>
        function calculateSalary() {
            const karyawanId = document.getElementById('karyawan_id').value;
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;

            if (!karyawanId || !bulan || !tahun) {
                alert('Pilih karyawan, bulan, dan tahun terlebih dahulu');
                return;
            }

            fetch('{{ route('admin.penggajian.calculate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        karyawan_id: karyawanId,
                        bulan: bulan,
                        tahun: tahun
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const result = data.data;
                        const karyawan = data.karyawan;

                        let detailLemburHtml = '';
                        if (result.detail_lembur && result.detail_lembur.length > 0) {
                            detailLemburHtml = '<h4 class="font-semibold mt-2 mb-1">Detail Lembur:</h4>';
                            detailLemburHtml += '<ul class="list-disc pl-5">';
                            result.detail_lembur.forEach(item => {
                                detailLemburHtml +=
                                    `<li>${new Date(item.tanggal_lembur).toLocaleDateString('id-ID')}: ${item.total_jam} jam - Rp ${new Intl.NumberFormat('id-ID').format(item.total_lembur)}</li>`;
                            });
                            detailLemburHtml += '</ul>';
                        }

                        const html = `
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Nama Karyawan</p>
                                    <p class="font-semibold">${karyawan.nama_lengkap}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Periode</p>
                                    <p class="font-semibold">${getMonthName(bulan)} ${tahun}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold mb-2">Pendapatan</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>Gaji Pokok</div>
                                <div class="text-right">Rp ${new Intl.NumberFormat('id-ID').format(result.gaji_pokok)}</div>
                                <div>Tunjangan (20%)</div>
                                <div class="text-right text-green-600">+ Rp ${new Intl.NumberFormat('id-ID').format(result.tunjangan)}</div>
                                <div>Uang Lembur</div>
                                <div class="text-right text-green-600">+ Rp ${new Intl.NumberFormat('id-ID').format(result.uang_lembur)}</div>
                                <div>Bonus</div>
                                <div class="text-right text-green-600">+ Rp ${new Intl.NumberFormat('id-ID').format(result.bonus)}</div>
                                <div class="border-t pt-2 font-bold">Total Kotor</div>
                                <div class="border-t pt-2 text-right font-bold">Rp ${new Intl.NumberFormat('id-ID').format(result.total_kotor)}</div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold mb-2">Potongan</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>Pajak Penghasilan</div>
                                <div class="text-right text-red-600">- Rp ${new Intl.NumberFormat('id-ID').format(result.pajak)}</div>
                                <div>Potongan BPJS (1%)</div>
                                <div class="text-right text-red-600">- Rp ${new Intl.NumberFormat('id-ID').format(result.bpjs)}</div>
                                <div class="border-t pt-2 font-bold">Total Potongan</div>
                                <div class="border-t pt-2 text-right font-bold text-red-600">- Rp ${new Intl.NumberFormat('id-ID').format(result.total_potongan)}</div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-lg font-bold">Gaji Bersih</div>
                                <div class="text-right text-2xl font-bold text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(result.gaji_bersih)}</div>
                            </div>
                        </div>
                        
                        ${detailLemburHtml}
                    `;

                        document.getElementById('calculationResult').innerHTML = html;
                        document.getElementById('resultContainer').style.display = 'block';

                        // Set hidden form values
                        document.getElementById('store_karyawan_id').value = karyawanId;
                        document.getElementById('store_bulan').value = bulan;
                        document.getElementById('store_tahun').value = tahun;
                        document.getElementById('store_gaji_pokok').value = result.gaji_pokok;
                        document.getElementById('store_total_tunjangan').value = result.tunjangan;
                        document.getElementById('store_uang_lembur').value = result.uang_lembur;
                        document.getElementById('store_bonus').value = result.bonus;
                        document.getElementById('store_total_potongan').value = result.total_potongan;
                        document.getElementById('store_jumlah_pajak').value = result.pajak;
                        document.getElementById('store_potongan_bpjs').value = result.bpjs;
                        document.getElementById('store_gaji_bersih').value = result.gaji_bersih;
                        document.getElementById('store_detail_gaji').value = JSON.stringify(result);
                    }
                });
        }

        function getMonthName(month) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            return months[month - 1];
        }
    </script>
</x-app-layout>
