<div id="modal-container">
    {{-- Modal Tambah --}}
    <div id="add-modal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-full">
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-slate-800">Tambah Akun Baru</h3>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="modal-content overflow-y-auto" style="max-height: 80vh;">
                <div class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label for="add-name" class="block text-slate-700 text-sm font-bold mb-2">Nama</label>
                        <input type="text" name="name" id="add-name" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label for="add-email" class="block text-slate-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" id="add-email" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label for="add-jabatan" class="block text-slate-700 text-sm font-bold mb-2">Jabatan</label>
                        <input type="text" name="jabatan" id="add-jabatan" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label for="add-tanggal_bergabung" class="block text-slate-700 text-sm font-bold mb-2">Tanggal Bergabung</label>
                        <input type="date" name="tanggal_bergabung" id="add-tanggal_bergabung" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label for="add-profile_picture" class="block text-slate-700 text-sm font-bold mb-2">Foto Profil</label>
                        <img id="add-image-preview" src="" alt="Image Preview" class="w-20 h-20 rounded-full mb-2 object-cover hidden">
                        <input type="file" name="profile_picture" id="add-profile_picture" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700" accept="image/*">
                    </div>
                    <div>
                        <label for="add-role" class="block text-slate-700 text-sm font-bold mb-2">Role</label>
                        <select name="role" id="add-role" class="shadow-sm border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                            <option value="user" {{ $defaultRole === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $defaultRole === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label for="add-password" class="block text-slate-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password" id="add-password" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                </div>
                <div class="flex justify-end p-6 bg-slate-50 rounded-b-lg gap-2">
                    <button type="button" class="close-modal-btn bg-slate-200 hover:bg-slate-300 text-slate-800 py-2 px-4 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Modal Edit --}}
    <div id="edit-modal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-full">
            <div class="p-6 border-b">
                 <h3 class="text-xl font-bold text-slate-800">Edit Akun</h3>
            </div>
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="modal-content overflow-y-auto" style="max-height: 80vh;">
                <div class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit-name" class="block text-slate-700 text-sm font-bold mb-2">Nama</label>
                        <input type="text" name="name" id="edit-name" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label for="edit-email" class="block text-slate-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" id="edit-email" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label for="edit-jabatan" class="block text-slate-700 text-sm font-bold mb-2">Jabatan</label>
                        <input type="text" name="jabatan" id="edit-jabatan" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label for="edit-tanggal_bergabung" class="block text-slate-700 text-sm font-bold mb-2">Tanggal Bergabung</label>
                        <input type="date" name="tanggal_bergabung" id="edit-tanggal_bergabung" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label for="edit-profile_picture" class="block text-slate-700 text-sm font-bold mb-2">Ganti Foto Profil (Opsional)</label>
                        <img id="edit-image-preview" src="" alt="Image Preview" class="w-20 h-20 rounded-full mb-2 object-cover hidden">
                        <input type="file" name="profile_picture" id="edit-profile_picture" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700" accept="image/*">
                    </div>
                    <div>
                        <label for="edit-role" class="block text-slate-700 text-sm font-bold mb-2">Role</label>
                        <select name="role" id="edit-role" class="shadow-sm border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit-password" class="block text-slate-700 text-sm font-bold mb-2">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="edit-password" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>
                 <div class="flex justify-end p-6 bg-slate-50 rounded-b-lg gap-2">
                    <button type="button" class="close-modal-btn bg-slate-200 hover:bg-slate-300 text-slate-800 py-2 px-4 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg font-semibold">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>