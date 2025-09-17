<x-layout-admin>
    <x-slot:title>Kelola Karyawan</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Manajemen Akun Karyawan</h1>
        <button id="open-add-modal-btn"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Tambah Karyawan
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm rounded-md" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full text-zinc-300">
            <thead class="bg-zinc-700 text-left text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">Karyawan</th>
                    <th class="px-5 py-3">Posisi</th>
                    <th class="px-5 py-3">Tanggal Bergabung</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse($users as $user)
                    <tr class="hover:bg-zinc-700/50">
                        <td class="px-5 py-4">
                            <div class="flex items-center">
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=4f46e5&color=e0e7ff' }}"
                                    alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-4">
                                <div>
                                    <p class="font-semibold text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-zinc-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-white">{{ $user->jabatan ?? '-' }}</p>
                            <p class="text-sm text-zinc-400">{{ $user->divisi ?? 'Tidak ada divisi' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d M Y') : $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex justify-center gap-4">
                                <button class="open-edit-modal-btn text-amber-400 hover:text-amber-300 transition-colors"
                                    data-user='@json($user)' title="Edit">
                                    <i class="fas fa-edit fa-lg"></i>
                                </button>
                                <form action="{{ route('admin.employees.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Anda yakin ingin menghapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 transition-colors"
                                        title="Hapus">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-10 text-zinc-400">Belum ada data karyawan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- modal tambah karyawan --}}
    <div id="add-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-zinc-800 rounded-lg w-full max-w-4xl p-8 shadow-lg border border-zinc-700">
            <h2 class="text-xl font-bold mb-6 text-white">Formulir Tambah Karyawan Baru</h2>
            
            <form id="add-form" action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="role" value="user">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Kolom Kiri --}}
                    <div class="space-y-6">
                        <div>
                            <label for="add-name" class="block text-sm font-medium text-zinc-300">Nama Lengkap</label>
                            <input type="text" id="add-name" name="name"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                        </div>
                        <div>
                            <label for="add-email" class="block text-sm font-medium text-zinc-300">Alamat Email</label>
                            <input type="email" id="add-email" name="email"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                        </div>
                        <div>
                            <label for="add-password" class="block text-sm font-medium text-zinc-300">Password</label>
                            <div class="relative mt-1">
                                
                                {{-- Atribut validasi ditambahkan --}}
                                <input type="password" id="add-password" name="password"
                                    class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 pr-10 text-white"
                                    required minlength="8" title="Password minimal 8 karakter.">
                                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 px-3 flex items-center text-zinc-400 hover:text-zinc-200">
                                    <i class="fas fa-eye"></i>
                                    <i class="fas fa-eye-slash hidden"></i>
                                </button>
                            </div>
                            {{-- elemen untuk notif error --}}
                            <p id="add-password-error" class="hidden text-red-400 text-sm mt-1"></p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label for="add-jabatan" class="block text-sm font-medium text-zinc-300">Jabatan</label>
                            <input type="text" id="add-jabatan" name="jabatan"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="add-divisi-select" class="block text-sm font-medium text-zinc-300">Divisi</label>
                            <select id="add-divisi-select" name="divisi"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                <option value="">Pilih Divisi</option>
                                <option value="Teknologi Informasi">Teknologi Informasi</option>
                                <option value="Sumber Daya Manusia">Sumber Daya Manusia</option>
                                <option value="Keuangan">Keuangan</option>
                                <option value="Pemasaran">Pemasaran</option>
                                <option value="Operasional">Operasional</option>
                                <option value="lainnya">Lainnya (Isi Sendiri)...</option>
                            </select>
                            <div id="add-divisi-input-container" class="hidden mt-1">
                                <div class="flex items-center gap-2">
                                    <input type="text" id="add-divisi-input" name="divisi-disabled"
                                        class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white"
                                        placeholder="Ketik nama divisi baru..." disabled>
                                    <button type="button" id="add-divisi-cancel-btn"
                                        class="bg-zinc-600 hover:bg-zinc-500 text-white px-3 py-2 rounded-lg text-sm">Batal</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="add-tanggal_bergabung" class="block text-sm font-medium text-zinc-300">Tanggal Bergabung</label>
                            <input type="date" id="add-tanggal_bergabung" name="tanggal_bergabung"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="add-profile_picture" class="block text-sm font-medium text-zinc-300">Foto Profil (Opsional)</label>
                            <input type="file" id="add-profile_picture" name="profile_picture"
                                class="mt-1 w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/10 file:text-indigo-300 hover:file:bg-indigo-500/20">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-zinc-700">
                    <button type="button" class="close-modal bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- modal edit karyawan --}}
    <div id="edit-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-zinc-800 rounded-lg w-full max-w-4xl p-8 shadow-lg border border-zinc-700">
            <h2 class="text-xl font-bold mb-6 text-white">Edit Data Karyawan</h2>
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" id="edit-user-id">
                <input type="hidden" name="role" value="user">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Kolom Kiri --}}
                    <div class="space-y-6">
                        <div>
                            <label for="edit-name" class="block text-sm font-medium text-zinc-300">Nama Lengkap</label>
                            <input type="text" id="edit-name" name="name"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                        </div>
                        <div>
                            <label for="edit-email" class="block text-sm font-medium text-zinc-300">Alamat Email</label>
                            <input type="email" id="edit-email" name="email"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                        </div>
                        <div>
                            <label for="edit-password" class="block text-sm font-medium text-zinc-300">Password Baru (Opsional)</label>
                            <div class="relative mt-1">
                                {{-- Atribut validasi ditambahkan --}}
                                <input type="password" id="edit-password" name="password"
                                    class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 pr-10 text-white"
                                    placeholder="Kosongkan jika tidak diubah" minlength="8" title="Jika diisi, minimal 8 karakter.">
                                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 px-3 flex items-center text-zinc-400 hover:text-zinc-200">
                                    <i class="fas fa-eye"></i>
                                    <i class="fas fa-eye-slash hidden"></i>
                                </button>
                            </div>
                            {{-- Elemen untuk notifikasi error --}}
                            <p id="edit-password-error" class="hidden text-red-400 text-sm mt-1"></p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label for="edit-jabatan" class="block text-sm font-medium text-zinc-300">Jabatan</label>
                            <input type="text" id="edit-jabatan" name="jabatan"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="edit-divisi-select" class="block text-sm font-medium text-zinc-300">Divisi</label>
                            <select id="edit-divisi-select" name="divisi"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                <option value="">Pilih Divisi</option>
                                <option value="Teknologi Informasi">Teknologi Informasi</option>
                                <option value="Sumber Daya Manusia">Sumber Daya Manusia</option>
                                <option value="Keuangan">Keuangan</option>
                                <option value="Pemasaran">Pemasaran</option>
                                <option value="Operasional">Operasional</option>
                                <option value="lainnya">Lainnya (Isi Sendiri)...</option>
                            </select>
                            <div id="edit-divisi-input-container" class="hidden mt-1">
                                <div class="flex items-center gap-2">
                                    <input type="text" id="edit-divisi-input" name="divisi-disabled"
                                        class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white"
                                        placeholder="Ketik nama divisi..." disabled>
                                    <button type="button" id="edit-divisi-cancel-btn"
                                        class="bg-zinc-600 hover:bg-zinc-500 text-white px-3 py-2 rounded-lg text-sm">Batal</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="edit-tanggal_bergabung" class="block text-sm font-medium text-zinc-300">Tanggal Bergabung</label>
                            <input type="date" id="edit-tanggal_bergabung" name="tanggal_bergabung"
                                class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="edit-profile_picture" class="block text-sm font-medium text-zinc-300">Ganti Foto Profil (Opsional)</label>
                            <input type="file" id="edit-profile_picture" name="profile_picture"
                                class="mt-1 w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/10 file:text-indigo-300 hover:file:bg-indigo-500/20">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-zinc-700">
                    <button type="button" class="close-modal bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Update Data</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- KELOLA MODAL (ADD & EDIT) ---
            const addModal = document.getElementById('add-modal');
            const editModal = document.getElementById('edit-modal');
            document.getElementById('open-add-modal-btn')?.addEventListener('click', () => {
                addModal?.classList.remove('hidden');
                addModal.querySelector('form').reset();
                
                // Reset notifikasi error juga
                document.getElementById('add-password-error').classList.add('hidden');

                const addSelect = document.getElementById('add-divisi-select');
                const addInputContainer = document.getElementById('add-divisi-input-container');
                addSelect.classList.remove('hidden');
                addSelect.name = 'divisi';
                addInputContainer.classList.add('hidden');
            });
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('.modal')?.classList.add('hidden'));
            });

            // --- FUNGSI HELPER UNTUK MENUKAR INPUT DIVISI ---
            const setupDivisiSwitcher = (selectEl, inputContainerEl, inputEl, cancelBtnEl) => {
                const showInput = () => {
                    selectEl.classList.add('hidden');
                    selectEl.name = 'divisi-select-disabled';
                    inputContainerEl.classList.remove('hidden');
                    inputEl.disabled = false;
                    inputEl.name = 'divisi';
                    inputEl.focus();
                };
                const showSelect = () => {
                    inputContainerEl.classList.add('hidden');
                    inputEl.disabled = true;
                    inputEl.name = 'divisi-disabled';
                    selectEl.classList.remove('hidden');
                    selectEl.name = 'divisi';
                    selectEl.value = '';
                };
                selectEl.addEventListener('change', () => {
                    if (selectEl.value === 'lainnya') {
                        showInput();
                    }
                });
                cancelBtnEl.addEventListener('click', showSelect);
                return { showInput, showSelect };
            };

            // Inisialisasi Switcher
            const addSwitcher = setupDivisiSwitcher(
                document.getElementById('add-divisi-select'),
                document.getElementById('add-divisi-input-container'),
                document.getElementById('add-divisi-input'),
                document.getElementById('add-divisi-cancel-btn')
            );
            const editSwitcher = setupDivisiSwitcher(
                document.getElementById('edit-divisi-select'),
                document.getElementById('edit-divisi-input-container'),
                document.getElementById('edit-divisi-input'),
                document.getElementById('edit-divisi-cancel-btn')
            );

            // --- KELOLA VISIBILITAS PASSWORD ---
            document.querySelectorAll('.toggle-password-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const passwordInput = btn.closest('.relative').querySelector('input');
                    const eyeIcon = btn.querySelector('.fa-eye');
                    const eyeSlashIcon = btn.querySelector('.fa-eye-slash');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.classList.add('hidden');
                        eyeSlashIcon.classList.remove('hidden');
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.classList.remove('hidden');
                        eyeSlashIcon.classList.add('hidden');
                    }
                });
            });

            // --- LOGIKA BARU: VALIDASI FORM SEBELUM SUBMIT ---
            const setupFormValidation = (formId, passwordId, errorId) => {
                const form = document.getElementById(formId);
                const passwordInput = document.getElementById(passwordId);
                const errorContainer = document.getElementById(errorId);

                if (form) {
                    form.addEventListener('submit', function(event) {
                        const passwordValue = passwordInput.value.trim();

                        if (passwordInput.required && passwordValue.length === 0) {
                            event.preventDefault();
                            errorContainer.textContent = 'Password wajib diisi.';
                            errorContainer.classList.remove('hidden');
                            return;
                        }
                        
                        if (passwordValue.length > 0 && passwordValue.length < 8) {
                            event.preventDefault();
                            errorContainer.textContent = 'Password minimal harus 8 karakter.';
                            errorContainer.classList.remove('hidden');
                            return;
                        }
                        
                        errorContainer.classList.add('hidden');
                    });
                    
                    passwordInput.addEventListener('input', () => {
                        errorContainer.classList.add('hidden');
                    });
                }
            };

            // Terapkan validasi pada kedua form
            setupFormValidation('add-form', 'add-password', 'add-password-error');
            setupFormValidation('edit-form', 'edit-password', 'edit-password-error');

            // --- PENGISIAN DATA UNTUK MODAL EDIT ---
            document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const user = JSON.parse(btn.getAttribute('data-user'));
                    if (editModal) {
                        const form = editModal.querySelector('#edit-form');
                        const selectDivisi = form.querySelector('#edit-divisi-select');
                        const inputDivisi = form.querySelector('#edit-divisi-input');

                        // Ubah baris ini untuk rute baru
                        form.action = `{{ route('admin.employees.update') }}`; 

                        // Tambahkan baris ini untuk mengisi ID pengguna
                        form.querySelector('#edit-user-id').value = user.id;
                        form.querySelector('#edit-name').value = user.name;
                        form.querySelector('#edit-email').value = user.email;
                        form.querySelector('#edit-password').value = '';
                        form.querySelector('#edit-jabatan').value = user.jabatan ?? '';
                        if (user.tanggal_bergabung) {
                            // Ambil 10 karakter pertama (YYYY-MM-DD) dari timestamp
                            form.querySelector('#edit-tanggal_bergabung').value = user.tanggal_bergabung.substring(0, 10);
                        } else {
                            // Jika tidak ada tanggal sama sekali, pastikan kolomnya kosong
                            form.querySelector('#edit-tanggal_bergabung').value = '';
                        }

                        // Reset notifikasi error saat modal edit dibuka
                        document.getElementById('edit-password-error').classList.add('hidden');

                        const divisiOptions = Array.from(selectDivisi.options).map(opt => opt.value);
                        if (user.divisi && !divisiOptions.includes(user.divisi)) {
                            editSwitcher.showInput();
                            inputDivisi.value = user.divisi;
                        } else {
                            editSwitcher.showSelect();
                            selectDivisi.value = user.divisi ?? '';
                        }
                        editModal.classList.remove('hidden');
                    }
                });
            }); 
        });
    </script>
    @endpush

</x-layout-admin>