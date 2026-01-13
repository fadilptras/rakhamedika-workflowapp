<x-layout-admin>
    <x-slot:title>Kelola Karyawan</x-slot:title>

    {{-- Header Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Manajemen Akun Karyawan</h1>
        <button id="open-add-modal-btn" 
            class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Tambah Karyawan
        </button>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm rounded-md">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel Karyawan (Looping per Divisi) --}}
    @forelse($usersByDivision as $divisi => $employees)
        <h2 class="text-lg font-bold text-zinc-300 mt-8 mb-3 border-b border-zinc-700 pb-1 inline-block">
            {{ $divisi }}
        </h2>
        <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden mb-6">
            <table class="min-w-full text-zinc-300">
                <thead class="bg-zinc-700 text-left text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Karyawan</th>
                        <th class="px-5 py-3">Posisi</th>
                        <th class="px-5 py-3">Bergabung</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @foreach($employees->sortByDesc('is_kepala_divisi') as $user)
                        <tr class="hover:bg-zinc-700/50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0284c7&color=f0f9ff' }}"
                                        alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-4">
                                    <div>
                                        {{-- Nama Karyawan (Klik untuk Detail) --}}
                                        <button class="open-detail-modal-btn font-semibold text-white hover:text-sky-400 transition-colors text-left"
                                                data-user='@json($user)'>
                                            {{ $user->name }}
                                        </button>
                                        <p class="text-sm text-zinc-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-white text-sm font-medium">{{ $user->jabatan ?? '-' }}</p>
                                <div class="flex gap-2 mt-1">
                                    @if ($user->is_kepala_divisi)
                                        <span class="bg-sky-900/50 text-sky-300 border border-sky-700/50 text-[10px] px-2 py-0.5 rounded-full">
                                            HEAD
                                        </span>
                                    @endif
                                    @if($user->status_karyawan)
                                        <span class="bg-zinc-600/50 text-zinc-300 border border-zinc-600 text-[10px] px-2 py-0.5 rounded-full">
                                            {{ $user->status_karyawan }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center gap-3">
                                    {{-- Tombol Set Kepala Divisi --}}
                                    @if ($user->divisi && !$user->is_kepala_divisi)
                                        <form action="{{ route('admin.employees.setAsHead', $user->id) }}" method="POST" onsubmit="return confirm('Jadikan {{ $user->name }} sebagai Kepala Divisi?')">
                                            @csrf
                                            <button type="submit" class="text-zinc-500 hover:text-sky-400 transition-colors" title="Set Kepala Divisi">
                                                <i class="fas fa-crown"></i>
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Tombol Edit --}}
                                    <button class="open-edit-modal-btn text-amber-400 hover:text-amber-300 transition-colors" data-user='@json($user)' title="Edit Lengkap">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </button>
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.employees.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini permanen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors" title="Hapus">
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
        <div class="bg-zinc-800 rounded-lg p-8 text-center border border-zinc-700">
            <p class="text-zinc-400">Belum ada data karyawan.</p>
        </div>
    @endforelse

    {{-- MODAL TAMBAH (Style Admin) --}}
    <div id="add-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-zinc-800 rounded-lg w-full max-w-lg p-6 shadow-lg border border-zinc-700">
            <h2 class="text-xl font-bold mb-6 text-white">Tambah Karyawan Baru</h2>
            <form id="add-form" action="{{ route('admin.employees.store') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="user">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">Nama Lengkap</label>
                        <input type="text" name="name" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">Email</label>
                        <input type="email" name="email" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">Password</label>
                        <input type="password" name="password" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300">Jabatan</label>
                            <input type="text" name="jabatan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300">Divisi</label>
                            <select id="add-divisi-select" name="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                <option value="">-- Pilih --</option>
                                @foreach($usersByDivision->keys() as $divName)
                                    @if($divName != 'Tanpa Divisi') <option value="{{ $divName }}">{{ $divName }}</option> @endif
                                @endforeach
                                <option value="lainnya">+ Baru</option>
                            </select>
                            <div id="add-divisi-input-container" class="mt-2 hidden flex gap-1">
                                <input type="text" id="add-divisi-input" name="divisi-disabled" disabled class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-1 text-sm text-white" placeholder="Nama Divisi">
                                <button type="button" id="add-divisi-cancel-btn" class="bg-red-600 text-white px-2 rounded"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" class="close-modal bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT FULL PROFILE (Style Admin, Layout Grid) --}}
    <div id="edit-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        {{-- Menggunakan max-w-4xl agar muat banyak kolom, max-h-[90vh] untuk scroll --}}
        <div class="bg-zinc-800 rounded-lg w-full max-w-4xl max-h-[90vh] flex flex-col shadow-lg border border-zinc-700">
            
            {{-- Header Modal --}}
            <div class="p-6 border-b border-zinc-700 shrink-0">
                <h2 class="text-xl font-bold text-white">Edit Profil Karyawan Lengkap</h2>
                <p class="text-sm text-zinc-400">Silakan lengkapi data administrasi, bank, dan kontak darurat.</p>
            </div>

            {{-- Body Modal (Scrollable) --}}
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form id="edit-form" action="{{ route('admin.employees.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="user_id" id="edit-user-id">
                    <input type="hidden" name="role" value="user">

                    {{-- SECTION 1: Akun & Pekerjaan --}}
                    <div class="mb-6">
                        <h3 class="text-sky-400 font-bold uppercase text-xs tracking-wider mb-3 border-b border-zinc-700 pb-1">Akun & Pekerjaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Nama Lengkap</label>
                                <input type="text" name="name" id="edit-name" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Email</label>
                                <input type="email" name="email" id="edit-email" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Password (Opsional)</label>
                                <input type="password" name="password" id="edit-password" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" placeholder="Isi untuk ubah password">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Jabatan</label>
                                <input type="text" name="jabatan" id="edit-jabatan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Divisi</label>
                                <select id="edit-divisi-select" name="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach($usersByDivision->keys() as $divName)
                                        @if($divName != 'Tanpa Divisi') <option value="{{ $divName }}">{{ $divName }}</option> @endif
                                    @endforeach
                                    <option value="lainnya">+ Baru</option>
                                </select>
                                <div id="edit-divisi-input-container" class="mt-2 hidden flex gap-1">
                                    <input type="text" id="edit-divisi-input" name="divisi-disabled" disabled class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-1 text-sm text-white" placeholder="Nama Divisi">
                                    <button type="button" id="edit-divisi-cancel-btn" class="bg-red-600 text-white px-2 rounded"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">NIP</label>
                                <input type="text" name="nip" id="edit-nip" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Status Karyawan</label>
                                <select name="status_karyawan" id="edit-status_karyawan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                    <option value="">- Pilih -</option>
                                    <option value="Tetap">Tetap</option>
                                    <option value="Kontrak">Kontrak</option>
                                    <option value="Magang">Magang</option>
                                    <option value="Percobaan">Percobaan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Tanggal Bergabung</label>
                                <input type="date" name="tanggal_bergabung" id="edit-tanggal_bergabung" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Lokasi Kerja</label>
                                <input type="text" name="lokasi_kerja" id="edit-lokasi_kerja" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Data Pribadi --}}
                    <div class="mb-6">
                        <h3 class="text-sky-400 font-bold uppercase text-xs tracking-wider mb-3 border-b border-zinc-700 pb-1">Data Pribadi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">NIK (KTP)</label>
                                <input type="text" name="nik" id="edit-nik" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">No. Telepon / WA</label>
                                <input type="text" name="nomor_telepon" id="edit-nomor_telepon" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="edit-tempat_lahir" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="edit-tanggal_lahir" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="edit-jenis_kelamin" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                    <option value="">- Pilih -</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Status Pernikahan</label>
                                <select name="status_pernikahan" id="edit-status_pernikahan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                                    <option value="">- Pilih -</option>
                                    <option value="Lajang">Lajang</option>
                                    <option value="Menikah">Menikah</option>
                                    <option value="Cerai">Cerai</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300">Alamat Domisili</label>
                                <textarea name="alamat_domisili" id="edit-alamat_domisili" rows="2" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Bank & Admin --}}
                    <div class="mb-6">
                        <h3 class="text-sky-400 font-bold uppercase text-xs tracking-wider mb-3 border-b border-zinc-700 pb-1">Administrasi & Bank</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">NPWP</label>
                                <input type="text" name="npwp" id="edit-npwp" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">BPJS Kesehatan</label>
                                <input type="text" name="bpjs_kesehatan" id="edit-bpjs_kesehatan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Nama Bank</label>
                                <input type="text" name="nama_bank" id="edit-nama_bank" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">No. Rekening</label>
                                <input type="text" name="nomor_rekening" id="edit-nomor_rekening" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300">Atas Nama Rekening</label>
                                <input type="text" name="pemilik_rekening" id="edit-pemilik_rekening" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 4: Kontak Darurat --}}
                    <div>
                        <h3 class="text-sky-400 font-bold uppercase text-xs tracking-wider mb-3 border-b border-zinc-700 pb-1">Kontak Darurat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Nama Kontak</label>
                                <input type="text" name="kontak_darurat_nama" id="edit-kontak_darurat_nama" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300">Nomor Telepon Kontak</label>
                                <input type="text" name="kontak_darurat_nomor" id="edit-kontak_darurat_nomor" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer Modal --}}
            <div class="p-6 border-t border-zinc-700 shrink-0 flex justify-end gap-3 bg-zinc-800 rounded-b-lg">
                <button type="button" class="close-modal bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg">Batal</button>
                <button type="submit" form="edit-form" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL (Style Admin) --}}
    <div id="detail-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-zinc-800 rounded-lg w-full max-w-2xl max-h-[90vh] flex flex-col shadow-lg border border-zinc-700">
            <div class="p-6 border-b border-zinc-700 flex justify-between items-center shrink-0">
                <h2 class="text-xl font-bold text-white">Detail Karyawan</h2>
                <div class="flex items-center gap-3">
                    <a id="download-pdf-btn" href="#" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                    <button class="close-modal-detail text-zinc-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
                </div>
            </div>
            <div id="detail-modal-content" class="p-6 overflow-y-auto custom-scrollbar text-zinc-300">
                Memuat...
            </div>
            <div class="p-4 border-t border-zinc-700 text-right bg-zinc-800 rounded-b-lg shrink-0">
                <button class="close-modal-detail bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg">Tutup</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addModal = document.getElementById('add-modal');
            const editModal = document.getElementById('edit-modal');
            const detailModal = document.getElementById('detail-modal');
            const detailContent = document.getElementById('detail-modal-content');

            // --- Modal Toggles ---
            const toggleModal = (modal, show) => {
                if(show) modal.classList.remove('hidden');
                else modal.classList.add('hidden');
            };

            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', () => toggleModal(btn.closest('.modal'), false));
            });
            document.querySelectorAll('.close-modal-detail').forEach(btn => btn.addEventListener('click', () => toggleModal(detailModal, false)));

            // --- ADD MODAL ---
            document.getElementById('open-add-modal-btn')?.addEventListener('click', () => {
                document.getElementById('add-form').reset();
                toggleModal(addModal, true);
            });

            // --- Divisi Logic (Sama) ---
            const setupDivisi = (selId, contId, inpId, cancId) => {
                const sel = document.getElementById(selId);
                const cont = document.getElementById(contId);
                const inp = document.getElementById(inpId);
                const canc = document.getElementById(cancId);
                if(!sel) return;
                sel.addEventListener('change', function() {
                    if(this.value === 'lainnya') {
                        cont.classList.remove('hidden');
                        inp.disabled = false; inp.name = 'divisi'; sel.name = 'divisi-disabled';
                    } else {
                        cont.classList.add('hidden');
                        inp.disabled = true; inp.name = 'divisi-disabled'; sel.name = 'divisi';
                    }
                });
                canc.addEventListener('click', () => {
                    cont.classList.add('hidden');
                    inp.disabled = true; sel.name = 'divisi'; sel.value = '';
                });
            };
            setupDivisi('add-divisi-select', 'add-divisi-input-container', 'add-divisi-input', 'add-divisi-cancel-btn');
            setupDivisi('edit-divisi-select', 'edit-divisi-input-container', 'edit-divisi-input', 'edit-divisi-cancel-btn');

            // --- EDIT MODAL (Full Mapping) ---
            document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const user = JSON.parse(e.currentTarget.getAttribute('data-user'));
                    const form = editModal.querySelector('#edit-form');
                    
                    // Helper to safe set value
                    const setVal = (id, val) => { 
                        const el = form.querySelector(id); 
                        if(el) el.value = val ?? ''; 
                    };

                    setVal('#edit-user-id', user.id);
                    setVal('#edit-name', user.name);
                    setVal('#edit-email', user.email);
                    setVal('#edit-password', '');

                    setVal('#edit-jabatan', user.jabatan);
                    setVal('#edit-nip', user.nip);
                    setVal('#edit-status_karyawan', user.status_karyawan);
                    setVal('#edit-tanggal_bergabung', user.tanggal_bergabung);
                    setVal('#edit-lokasi_kerja', user.lokasi_kerja);

                    // Divisi Logic
                    const editSelect = form.querySelector('#edit-divisi-select');
                    const editInpCont = form.querySelector('#edit-divisi-input-container');
                    const editInp = form.querySelector('#edit-divisi-input');
                    const options = Array.from(editSelect.options).map(o => o.value);
                    
                    if (user.divisi && !options.includes(user.divisi) && user.divisi !== 'lainnya') {
                        editSelect.value = 'lainnya'; editSelect.name = 'divisi-disabled';
                        editInpCont.classList.remove('hidden'); editInp.disabled = false; editInp.value = user.divisi; editInp.name = 'divisi';
                    } else {
                        editSelect.value = user.divisi ?? ''; editSelect.name = 'divisi';
                        editInpCont.classList.add('hidden'); editInp.disabled = true; editInp.name = 'divisi-disabled';
                    }

                    // Data Pribadi
                    setVal('#edit-nik', user.nik);
                    setVal('#edit-nomor_telepon', user.nomor_telepon);
                    setVal('#edit-tempat_lahir', user.tempat_lahir);
                    setVal('#edit-tanggal_lahir', user.tanggal_lahir);
                    setVal('#edit-jenis_kelamin', user.jenis_kelamin);
                    setVal('#edit-status_pernikahan', user.status_pernikahan);
                    setVal('#edit-alamat_domisili', user.alamat_domisili);

                    // Bank & Admin
                    setVal('#edit-npwp', user.npwp);
                    setVal('#edit-bpjs_kesehatan', user.bpjs_kesehatan);
                    setVal('#edit-nama_bank', user.nama_bank);
                    setVal('#edit-nomor_rekening', user.nomor_rekening);
                    setVal('#edit-pemilik_rekening', user.pemilik_rekening);

                    // Kontak Darurat
                    setVal('#edit-kontak_darurat_nama', user.kontak_darurat_nama);
                    setVal('#edit-kontak_darurat_nomor', user.kontak_darurat_nomor);

                    toggleModal(editModal, true);
                });
            });

            // --- DETAIL MODAL ---
            document.querySelectorAll('.open-detail-modal-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const user = JSON.parse(e.currentTarget.getAttribute('data-user'));
                    const pdfBtn = document.getElementById('download-pdf-btn');
                    if(pdfBtn) pdfBtn.href = `{{ url('admin/employees') }}/${user.id}/download-pdf`;

                    const fmt = (d) => d ? new Date(d).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'}) : '-';
                    
                    detailContent.innerHTML = `
                        <div class="flex items-center gap-4 mb-6">
                            <img src="${user.profile_picture ? '{{ asset('storage') }}/'+user.profile_picture : 'https://ui-avatars.com/api/?name='+user.name+'&background=random'}" 
                                 class="w-16 h-16 rounded-full border-2 border-zinc-600">
                            <div>
                                <h3 class="text-xl font-bold text-white">${user.name}</h3>
                                <p class="text-sky-400">${user.jabatan || '-'} &bull; ${user.divisi || '-'}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <h4 class="font-bold text-white mb-2 border-b border-zinc-700 pb-1">Pribadi</h4>
                                <p>NIK: ${user.nik || '-'}</p>
                                <p>TTL: ${user.tempat_lahir || '-'}, ${fmt(user.tanggal_lahir)}</p>
                                <p>Domisili: ${user.alamat_domisili || '-'}</p>
                            </div>
                             <div>
                                <h4 class="font-bold text-white mb-2 border-b border-zinc-700 pb-1">Kontak & Bank</h4>
                                <p>HP: ${user.nomor_telepon || '-'}</p>
                                <p>Bank: ${user.nama_bank || '-'} (${user.nomor_rekening || '-'})</p>
                                <p>Darurat: ${user.kontak_darurat_nama || '-'} (${user.kontak_darurat_nomor || '-'})</p>
                            </div>
                        </div>
                    `;
                    toggleModal(detailModal, true);
                });
            });
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #27272a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #52525b; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #71717a; }
    </style>
    @endpush
</x-layout-admin>