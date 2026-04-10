<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Penilaian Performa - {{ $performa->bulan_text }} {{ $performa->tahun }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-gray-800">LAPORAN PENILAIAN PERFORMA</h1>
                        <p class="text-gray-600">Periode: {{ $performa->bulan_text }} {{ $performa->tahun }}</p>
                        <p class="text-gray-600">Quarter: {{ $performa->quarter_text }}</p>
                    </div>

                    <!-- Employee Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pb-4 border-b">
                        <div>
                            <p class="text-gray-600 text-sm">Nama Karyawan</p>
                            <p class="font-semibold">{{ $performa->nama_karyawan }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Departemen</p>
                            <p class="font-semibold">{{ $performa->departemen }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Posisi</p>
                            <p class="font-semibold">{{ $performa->position }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Email</p>
                            <p class="font-semibold">{{ $performa->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Tanggal Bergabung</p>
                            <p class="font-semibold">{{ $performa->join_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Rating</p>
                            <p><span class="bg-{{ $performa->rating['color'] }}-100 text-{{ $performa->rating['color'] }}-800 py-1 px-3 rounded-full text-sm">{{ $performa->rating['label'] }}</span></p>
                        </div>
                    </div>

                    <!-- Performance Components -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Komponen Penilaian</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-gray-700">Quality</span>
                                    <span class="font-semibold">{{ $performa->quality }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 rounded-full h-2" style="width: {{ $performa->quality }}%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-gray-700">Productivity</span>
                                    <span class="font-semibold">{{ $performa->productivity }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 rounded-full h-2" style="width: {{ $performa->productivity }}%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-gray-700">Teamwork</span>
                                    <span class="font-semibold">{{ $performa->teamwork }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 rounded-full h-2" style="width: {{ $performa->teamwork }}%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-gray-700">Discipline</span>
                                    <span class="font-semibold">{{ $performa->discipline }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 rounded-full h-2" style="width: {{ $performa->discipline }}%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-gray-700 font-bold">KPI Score</span>
                                    <span class="font-semibold text-blue-600">{{ $performa->kpi_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 rounded-full h-2" style="width: {{ $performa->kpi_score }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Rata-rata dari Quality, Productivity, Teamwork, Discipline</p>
                            </div>
                            
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-gray-700">Attendance Rate</span>
                                    <span class="font-semibold text-green-600">{{ $performa->attendance_rate }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 rounded-full h-2" style="width: {{ $performa->attendance_rate }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Sama dengan KPI Score</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Score -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 mb-6">
                        <div class="text-center">
                            <p class="text-white text-sm">Total Performance Score</p>
                            <p class="text-white text-5xl font-bold">{{ $performa->performance_score }}</p>
                            <p class="text-purple-100 text-sm mt-2">Dari skala 0-100</p>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($performa->catatan)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan</h3>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                            <p class="text-gray-700">{{ $performa->catatan }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('performa.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Cetak Laporan
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