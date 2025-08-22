{{-- resources/views/admin/users/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal { transition: opacity 0.25s ease; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="container mx-auto p-4 md:p-8">
        
        {{-- Menampilkan pesan sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Menampilkan error validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Kelola Akun Pengguna</h1>
            <button id="open-add-modal-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center">
                + Tambah Akun
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3">Pengguna</th> <th class="px-5 py-3">Jabatan</th> <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Tgl Bergabung</th> <th class="px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-5 py-4 text-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <img class="w-full h-full rounded-full" 
                                             src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" 
                                             alt="Foto profil {{ $user->name }}">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap font-semibold">{{ $user->name }}</p>
                                        <p class="text-gray-600 whitespace-no-wrap">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $user->jabatan ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if($user->role == 'admin')
                                <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                                    <span class="relative">Admin</span>
                                </span>
                                @else
                                <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                    <span class="relative">User</span>
                                </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $user->tanggal_bergabung ? $user->tanggal_bergabung->format('d M Y') : '-' }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-sm flex items-center gap-2">
                                <button class="open-edit-modal-btn text-yellow-600 hover:text-yellow-900" data-user='{{ $user->toJson() }}' title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data akun.</td> </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="add-modal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6 my-8">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3">Tambah Akun Baru</h3>
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="add-name" class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                    <input type="text" name="name" id="add-name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div>
                    <label for="add-email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="add-email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div>
                    <label for="add-jabatan" class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                    <input type="text" name="jabatan" id="add-jabatan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="add-tanggal_bergabung" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Bergabung</label>
                    <input type="date" name="tanggal_bergabung" id="add-tanggal_bergabung" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="add-profile_picture" class="block text-gray-700 text-sm font-bold mb-2">Foto Profil</label>
                    <input type="file" name="profile_picture" id="add-profile_picture" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="add-role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                    <select name="role" id="add-role" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="add-password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="add-password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div class="flex justify-end pt-4 gap-2">
                    <button type="button" class="close-modal-btn bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Modal Edit --}}
    <div id="edit-modal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6 my-8">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3">Edit Akun</h3>
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-name" class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                    <input type="text" name="name" id="edit-name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div>
                    <label for="edit-email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="edit-email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div>
                    <label for="edit-jabatan" class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                    <input type="text" name="jabatan" id="edit-jabatan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="edit-tanggal_bergabung" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Bergabung</label>
                    <input type="date" name="tanggal_bergabung" id="edit-tanggal_bergabung" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="edit-profile_picture" class="block text-gray-700 text-sm font-bold mb-2">Ganti Foto Profil (Opsional)</label>
                    <img id="edit-image-preview" src="" alt="Image Preview" class="w-20 h-20 rounded-full mb-2 object-cover hidden">
                    <input type="file" name="profile_picture" id="edit-profile_picture" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="edit-role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                    <select name="role" id="edit-role" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="edit-password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="edit-password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="flex justify-end pt-4 gap-2">
                    <button type="button" class="close-modal-btn bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full flex items-center px-4 py-2.5 font-semibold text-red-800 rounded-lg transition-colors duration-200 hover:bg-red-100">
            <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            <span>Logout</span>
        </button>
    </form>
    
    <script>
        const addModal = document.getElementById('add-modal');
        const editModal = document.getElementById('edit-modal');
        
        document.getElementById('open-add-modal-btn').addEventListener('click', () => addModal.classList.remove('hidden'));

        document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const user = JSON.parse(btn.dataset.user);
                const form = document.getElementById('edit-form');
                
                // URL action untuk form edit
                form.action = `/admin/users/${user.id}`;
                
                // Isi data yang sudah ada
                form.querySelector('#edit-name').value = user.name;
                form.querySelector('#edit-email').value = user.email;
                form.querySelector('#edit-role').value = user.role;
                
                // UBAH DISINI: Isi data untuk field baru
                form.querySelector('#edit-jabatan').value = user.jabatan || '';
                form.querySelector('#edit-tanggal_bergabung').value = user.tanggal_bergabung || '';
                
                // UBAH DISINI: Tampilkan preview gambar yang sudah ada
                const imagePreview = form.querySelector('#edit-image-preview');
                if (user.profile_picture) {
                    imagePreview.src = `/storage/${user.profile_picture}`;
                    imagePreview.classList.remove('hidden');
                } else {
                    imagePreview.classList.add('hidden');
                }
                
                editModal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                addModal.classList.add('hidden');
                editModal.classList.add('hidden');
            });
        });

        // Menutup modal jika klik di luar area modal
        window.addEventListener('click', (event) => {
            if (event.target == addModal || event.target == editModal) {
                addModal.classList.add('hidden');
                editModal.classList.add('hidden');
            }
        });

    </script>
</body>
</html>