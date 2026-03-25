<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Karyawan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative">
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                        <span class="text-green-700">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 relative">
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                        <span class="text-red-700">&times;</span>
                    </button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <button type="button" onclick="openModal()" class="mb-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + Tambah Karyawan
                    </button>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">NIP</th>
                                    <th class="py-3 px-6 text-left">Nama Lengkap</th>
                                    <th class="py-3 px-6 text-left">Email</th>
                                    <th class="py-3 px-6 text-left">Role</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Password</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                 </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($karyawans as $karyawan)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $karyawan->nip }}</td>
                                    <td class="py-3 px-6 text-left">{{ $karyawan->nama_lengkap }}</td>
                                    <td class="py-3 px-6 text-left">{{ $karyawan->email }}</td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $karyawan->role == 'admin' ? 'red' : ($karyawan->role == 'hr' ? 'green' : 'blue') }}-200 text-{{ $karyawan->role == 'admin' ? 'red' : ($karyawan->role == 'hr' ? 'green' : 'blue') }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ strtoupper($karyawan->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-{{ $karyawan->status == 'aktif' ? 'green' : 'red' }}-200 text-{{ $karyawan->status == 'aktif' ? 'green' : 'red' }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $karyawan->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <button onclick="showPasswordModal({{ $karyawan->id }}, '{{ $karyawan->nama_lengkap }}')" 
                                            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Lihat Password
                                        </button>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <button onclick="editKaryawan({{ $karyawan->id }})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.karyawan.destroy', $karyawan->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $karyawans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Karyawan -->
    <div id="karyawanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Karyawan</h3>
                <form id="karyawanForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">NIP</label>
                        <input type="text" name="nip" id="nip" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" id="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                        <select name="role" id="role" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="karyawan">Karyawan</option>
                            <option value="hr">HR</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-4" id="passwordField">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password saat edit.</p>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Batal</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Password -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Password Karyawan</h3>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Nama: <strong id="karyawanName"></strong></p>
                    <p class="text-sm text-gray-600 mb-2">Password: <strong id="karyawanPassword" class="text-blue-600 text-lg"></strong></p>
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-xs text-yellow-800">
                            <strong>Catatan:</strong> Password ini akan muncul saat pertama kali dibuat atau saat direset.
                            Untuk keamanan, password hanya ditampilkan sekali saat pembuatan/reset.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closePasswordModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Karyawan';
            document.getElementById('karyawanForm').action = "{{ route('admin.karyawan.store') }}";
            document.getElementById('methodField').value = 'POST';
            document.getElementById('karyawanForm').reset();
            document.getElementById('karyawanModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('karyawanModal').classList.add('hidden');
        }

        function editKaryawan(id) {
            fetch(`/admin/karyawan/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').innerText = 'Edit Karyawan';
                    document.getElementById('karyawanForm').action = `/admin/karyawan/${id}`;
                    document.getElementById('methodField').value = 'PUT';
                    document.getElementById('nip').value = data.nip;
                    document.getElementById('nama_lengkap').value = data.nama_lengkap;
                    document.getElementById('email').value = data.email;
                    document.getElementById('role').value = data.role;
                    document.getElementById('password').value = '';
                    document.getElementById('karyawanModal').classList.remove('hidden');
                });
        }

        function showPasswordModal(id, name) {
            // For demo purposes, we'll show a message that password is stored
            // In production, you'd need to implement proper password retrieval
            document.getElementById('karyawanName').innerText = name;
            document.getElementById('karyawanPassword').innerHTML = '••••••••';
            document.getElementById('passwordModal').classList.remove('hidden');
            
            // Show instruction for password
            setTimeout(() => {
                document.getElementById('karyawanPassword').innerHTML = `
                    <span class="text-yellow-600 text-sm">
                        Password hanya tersimpan dalam bentuk hash.<br>
                        Gunakan fitur "Reset Password" jika karyawan lupa password.
                    </span>
                `;
            }, 500);
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }
    </script>
</x-app-layout>