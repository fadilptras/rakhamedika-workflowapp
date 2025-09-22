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
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            {{ session('error') }}
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

    @forelse($usersByDivision as $divisi => $employees)
        
        <h2 class="text-xl font-bold text-white mt-8 mb-4">{{ $divisi }}</h2>

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
                    @foreach($employees as $user)
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
                                {{-- PENYESUAIAN 1: Tampilkan badge jika dia Kepala Divisi --}}
                                @if ($user->is_kepala_divisi)
                                    <span class="mt-1 inline-block bg-cyan-500/20 text-cyan-300 text-xs font-semibold px-2 py-1 rounded-full">
                                        Kepala Divisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d M Y') : $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center gap-4">
                                    {{-- PENYESUAIAN 2: Tambah form untuk set Kepala Divisi --}}
                                    @if ($user->divisi && !$user->is_kepala_divisi)
                                        <form action="{{ route('admin.employees.setAsHead', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Anda yakin ingin menjadikan {{ $user->name }} sebagai Kepala Divisi?')">
                                            @csrf
                                            <button type="submit" class="text-cyan-400 hover:text-cyan-300 transition-colors"
                                                title="Jadikan Kepala Divisi">
                                                <i class="fas fa-crown fa-lg"></i>
                                            </button>
                                        </form>
                                    @endif

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
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="bg-zinc-800 rounded-lg shadow-lg p-10 text-center">
            <p class="text-zinc-400">Belum ada data karyawan.</p>
        </div>
    @endforelse

    @include('admin.partials.modal-karyawan')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addModal = document.getElementById('add-modal');
            const editModal = document.getElementById('edit-modal');
            document.getElementById('open-add-modal-btn')?.addEventListener('click', () => {
                addModal?.classList.remove('hidden');
                addModal.querySelector('form').reset();
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
                    if (selectEl.value === 'lainnya') showInput();
                });
                cancelBtnEl.addEventListener('click', showSelect);
                return { showInput, showSelect };
            };

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

            const setupFormValidation = (formId, passwordId, errorId) => {
                const form = document.getElementById(formId);
                const passwordInput = document.getElementById(passwordId);
                const errorContainer = document.getElementById(errorId);
                if (!form) return;
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
                passwordInput.addEventListener('input', () => errorContainer.classList.add('hidden'));
            };
            setupFormValidation('add-form', 'add-password', 'add-password-error');
            setupFormValidation('edit-form', 'edit-password', 'edit-password-error');

            document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const user = JSON.parse(e.currentTarget.getAttribute('data-user'));
                    if(editModal){
                        const form = editModal.querySelector('#edit-form');
                        form.action = `{{ route('admin.employees.update') }}`;
                        form.querySelector('#edit-user-id').value = user.id;
                        form.querySelector('#edit-name').value = user.name;
                        form.querySelector('#edit-email').value = user.email;
                        form.querySelector('#edit-password').value = '';
                        form.querySelector('#edit-jabatan').value = user.jabatan ?? '';
                        if (user.tanggal_bergabung) {
                            form.querySelector('#edit-tanggal_bergabung').value = user.tanggal_bergabung.substring(0, 10);
                        } else {
                            form.querySelector('#edit-tanggal_bergabung').value = '';
                        }

                        document.getElementById('edit-password-error').classList.add('hidden');

                        const selectDivisi = form.querySelector('#edit-divisi-select');
                        const inputDivisi = form.querySelector('#edit-divisi-input');

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