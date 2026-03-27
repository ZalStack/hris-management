<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Slip Gaji - {{ $penggajian->bulan_text }} {{ $penggajian->tahun }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-gray-800">SLIP GAJI</h1>
                        <p class="text-gray-600">Periode: {{ $penggajian->bulan_text }} {{ $penggajian->tahun }}</p>
                    </div>

                    <!-- Employee Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pb-4 border-b">
                        <div>
                            <p class="text-gray-600 text-sm">Nama Karyawan</p>
                            <p class="font-semibold">{{ $penggajian->nama_karyawan }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p>{!! $penggajian->status_badge !!}</p>
                        </div>
                    </div>

                    <!-- Salary Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Gaji</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Gaji Pokok</p>
                                    <p class="font-semibold">Rp {{ $penggajian->gaji_pokok }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Total Tunjangan</p>
                                    <p class="font-semibold text-green-600">+ Rp {{ $penggajian->total_tunjangan }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Uang Lembur</p>
                                    <p class="font-semibold text-green-600">+ Rp {{ $penggajian->uang_lembur }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Bonus</p>
                                    <p class="font-semibold text-green-600">+ Rp {{ $penggajian->bonus }}</p>
                                </div>
                            </div>
                            
                            <div class="border-t mt-4 pt-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-600 text-sm">Potongan Pajak</p>
                                        <p class="font-semibold text-red-600">- Rp {{ $penggajian->jumlah_pajak }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 text-sm">Potongan BPJS</p>
                                        <p class="font-semibold text-red-600">- Rp {{ $penggajian->potongan_bpjs }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 text-sm">Total Potongan</p>
                                        <p class="font-semibold text-red-600">- Rp {{ $penggajian->total_potongan }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t mt-4 pt-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-600 text-sm font-bold">Gaji Bersih</p>
                                        <p class="text-2xl font-bold text-green-600">Rp {{ $penggajian->gaji_bersih }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Lembur -->
                    @if($detailGaji && isset($detailGaji['detail_lembur']) && count($detailGaji['detail_lembur']) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Lembur</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-gray-600 text-sm">
                                        <th class="text-left">Tanggal</th>
                                        <th class="text-left">Total Jam</th>
                                        <th class="text-right">Nilai Lembur</th>
                                     </tr>
                                </thead>
                                <tbody>
                                    @foreach($detailGaji['detail_lembur'] as $lembur)
                                     <tr>
                                        <td class="py-1">{{ \Carbon\Carbon::parse($lembur['tanggal_lembur'])->format('d/m/Y') }}</td>
                                        <td class="py-1">{{ number_format($lembur['total_jam'], 2) }} jam</td>
                                        <td class="py-1 text-right">Rp {{ number_format($lembur['total_lembur'], 0, ',', '.') }}</td>
                                     </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Payment Info -->
                    @if($penggajian->status == 'paid')
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Tanggal Pembayaran</p>
                                    <p class="font-semibold">{{ $penggajian->tanggal_pembayaran ? $penggajian->tanggal_pembayaran->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Metode Pembayaran</p>
                                    <p class="font-semibold">{{ strtoupper($penggajian->metode_pembayaran) }}</p>
                                </div>
                                @if($penggajian->nama_bank)
                                <div>
                                    <p class="text-gray-600 text-sm">Bank</p>
                                    <p class="font-semibold">{{ $penggajian->nama_bank }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Nomor Rekening</p>
                                    <p class="font-semibold">{{ $penggajian->nomor_rekening }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($penggajian->catatan)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan</h3>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                            <p class="text-gray-700">{{ $penggajian->catatan }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('penggajian.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Cetak Slip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .py-12, .py-12 * {
                visibility: visible;
            }
            .py-12 {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
            .bg-gray-500, .bg-blue-500 {
                display: none;
            }
        }
    </style>
</x-app-layout>