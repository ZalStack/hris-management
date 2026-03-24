<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Karyawan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Selamat Datang, {{ auth()->user()->nama_lengkap }}!</h3>
                    <p class="text-gray-600 mb-6">Anda login sebagai <strong class="text-blue-600">{{ strtoupper(auth()->user()->role) }}</strong>.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-700 mb-3 text-lg">Informasi Profile</h4>
                            <div class="space-y-2">
                                <p class="flex justify-between"><span class="font-medium">NIP:</span> <span>{{ auth()->user()->nip }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Email:</span> <span>{{ auth()->user()->email }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Nomor Telepon:</span> <span>{{ auth()->user()->nomor_telepon ?? '-' }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Tanggal Bergabung:</span> <span>{{ auth()->user()->tanggal_bergabung ? auth()->user()->tanggal_bergabung->format('d/m/Y') : '-' }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Status:</span> <span class="text-green-600">{{ ucfirst(auth()->user()->status) }}</span></p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-700 mb-3 text-lg">Informasi Personal</h4>
                            <div class="space-y-2">
                                <p class="flex justify-between"><span class="font-medium">Tempat/Tanggal Lahir:</span> <span>{{ auth()->user()->tempat_lahir ?? '-' }} / {{ auth()->user()->tanggal_lahir ? auth()->user()->tanggal_lahir->format('d/m/Y') : '-' }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Jenis Kelamin:</span> <span>{{ auth()->user()->jenis_kelamin ?? '-' }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Agama:</span> <span>{{ auth()->user()->agama ?? '-' }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Status Pernikahan:</span> <span>{{ auth()->user()->status_pernikahan ?? '-' }}</span></p>
                                <p class="flex justify-between"><span class="font-medium">Pendidikan Terakhir:</span> <span>{{ auth()->user()->pendidikan_terakhir ?? '-' }}</span></p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->jabatanSaatIni && auth()->user()->jabatanSaatIni->jabatan)
                    <div class="mt-6 bg-blue-50 p-6 rounded-lg shadow-sm border border-blue-200">
                        <h4 class="font-semibold text-blue-700 mb-3 text-lg">Informasi Pekerjaan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <p class="flex justify-between"><span class="font-medium">Jabatan:</span> <span>{{ auth()->user()->jabatanSaatIni->jabatan->nama_jabatan }}</span></p>
                            <p class="flex justify-between"><span class="font-medium">Departemen:</span> <span>{{ auth()->user()->jabatanSaatIni->jabatan->departemen->nama_departemen ?? '-' }}</span></p>
                            <p class="flex justify-between"><span class="font-medium">Gaji Pokok:</span> <span>Rp {{ number_format(auth()->user()->jabatanSaatIni->gaji_pokok, 0, ',', '.') }}</span></p>
                            <p class="flex justify-between"><span class="font-medium">Status Kepegawaian:</span> <span>{{ auth()->user()->jabatanSaatIni->status_kepegawaian ?? '-' }}</span></p>
                            <p class="flex justify-between"><span class="font-medium">Tanggal Mulai:</span> <span>{{ auth()->user()->jabatanSaatIni->tanggal_mulai->format('d/m/Y') }}</span></p>
                        </div>
                    </div>
                    @endif

                    <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <span class="font-bold">Informasi:</span> Anda dapat mengupdate profile Anda melalui menu Profile di sidebar.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>