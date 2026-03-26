<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Penggajian - {{ $penggajian->bulan_text }} {{ $penggajian->tahun }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.penggajian.update', $penggajian->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan</label>
                                <select name="karyawan_id" required class="shadow border rounded w-full py-2 px-3">
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $penggajian->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Bulan</label>
                                <select name="bulan" required class="shadow border rounded w-full py-2 px-3">
                                    @foreach($bulan as $key => $value)
                                        <option value="{{ $key }}" {{ old('bulan', $penggajian->bulan) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                                <select name="tahun" required class="shadow border rounded w-full py-2 px-3">
                                    @foreach($tahun as $t)
                                        <option value="{{ $t }}" {{ old('tahun', $penggajian->tahun) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Gaji Pokok</label>
                                <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', $penggajian->gaji_pokok) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Tunjangan</label>
                                <input type="number" name="total_tunjangan" value="{{ old('total_tunjangan', $penggajian->total_tunjangan) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Uang Lembur</label>
                                <input type="number" name="uang_lembur" value="{{ old('uang_lembur', $penggajian->uang_lembur) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Bonus</label>
                                <input type="number" name="bonus" value="{{ old('bonus', $penggajian->bonus) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Potongan</label>
                                <input type="number" name="total_potongan" value="{{ old('total_potongan', $penggajian->total_potongan) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Pajak</label>
                                <input type="number" name="jumlah_pajak" value="{{ old('jumlah_pajak', $penggajian->jumlah_pajak) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Potongan BPJS</label>
                                <input type="number" name="potongan_bpjs" value="{{ old('potongan_bpjs', $penggajian->potongan_bpjs) }}" required 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Gaji Bersih</label>
                                <input type="number" name="gaji_bersih" value="{{ old('gaji_bersih', $penggajian->gaji_bersih) }}" required 
                                    class="shadow border rounded w-full py-2 px-3 bg-gray-100" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select name="status" required class="shadow border rounded w-full py-2 px-3">
                                    <option value="draft" {{ old('status', $penggajian->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending" {{ old('status', $penggajian->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $penggajian->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="paid" {{ old('status', $penggajian->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="cancelled" {{ old('status', $penggajian->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="shadow border rounded w-full py-2 px-3">
                                    <option value="">Pilih Metode</option>
                                    <option value="transfer" {{ old('metode_pembayaran', $penggajian->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="tunai" {{ old('metode_pembayaran', $penggajian->metode_pembayaran) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="cek" {{ old('metode_pembayaran', $penggajian->metode_pembayaran) == 'cek' ? 'selected' : '' }}>Cek</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $penggajian->tanggal_pembayaran ? $penggajian->tanggal_pembayaran->format('Y-m-d') : '') }}" 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Bank</label>
                                <input type="text" name="nama_bank" value="{{ old('nama_bank', $penggajian->nama_bank) }}" 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $penggajian->nomor_rekening) }}" 
                                    class="shadow border rounded w-full py-2 px-3">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                <textarea name="catatan" rows="3" class="shadow border rounded w-full py-2 px-3">{{ old('catatan', $penggajian->catatan) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('admin.penggajian.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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