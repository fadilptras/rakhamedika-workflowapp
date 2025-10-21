<x-layout-admin>
    <x-slot:title>Kelola Karyawan</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Manajemen Akun Karyawan</h1>
        <button id="open-add-modal-btn"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Tambah Karyawan
        </button>
    </div>

    {{-- Notifikasi --}}
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

    {{-- Daftar Karyawan per Divisi --}}
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
                    @foreach($employees->sortByDesc('is_kepala_divisi') as $user)
                        <tr class="hover:bg-zinc-700/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=4f46e5&color=e0e7ff' }}"
                                        alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-4">
                                    <div>
                                        {{-- Tombol untuk buka modal detail --}}
                                        <button class="open-detail-modal-btn font-semibold text-white text-left hover:underline focus:outline-none"
                                                data-user='@json($user)'>
                                            {{ $user->name }}
                                        </button>
                                        <p class="text-sm text-zinc-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-white">{{ $user->jabatan ?? '-' }}</p>
                                @if ($user->is_kepala_divisi)
                                    <span class="mt-1 inline-block bg-cyan-500/20 text-cyan-300 text-xs font-semibold px-2 py-1 rounded-full">
                                        Kepala Divisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d M Y') : ($user->created_at ? $user->created_at->format('d M Y') : '-') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center gap-4">
                                    @if ($user->divisi && !$user->is_kepala_divisi)
                                        <form action="{{ route('admin.employees.setAsHead', $user->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menjadikan {{ $user->name }} sebagai Kepala Divisi?')">
                                            @csrf
                                            <button type="submit" class="text-cyan-400 hover:text-cyan-300 transition-colors" title="Jadikan Kepala Divisi">
                                                <i class="fas fa-crown fa-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Tombol untuk buka modal edit --}}
                                    <button class="open-edit-modal-btn text-amber-400 hover:text-amber-300 transition-colors" data-user='@json($user)' title="Edit">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </button>
                                    <form action="{{ route('admin.employees.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
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
        <div class="bg-zinc-800 rounded-lg shadow-lg p-10 text-center">
            <p class="text-zinc-400">Belum ada data karyawan.</p>
        </div>
    @endforelse

    {{-- Modal Tambah & Edit Karyawan (dari file partial) --}}
    @include('admin.partials.modal-karyawan')

    {{-- === MODAL DETAIL KARYAWAN (DENGAN TOMBOL PDF) === --}}
    <div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-zinc-800 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">
            {{-- Header Modal --}}
            <div class="flex justify-between items-center p-4 border-b border-zinc-700">
                <h2 class="text-xl font-bold text-white">Detail Karyawan</h2>
                {{-- Tombol Cetak PDF --}}
                <div>
                    {{-- href akan diisi oleh JavaScript --}}
                    <a id="download-profile-pdf-btn" href="#" {{-- Tambah target blank jika ingin buka di tab baru --}}
                       class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-xs font-medium flex items-center shadow-sm transition-colors"
                       title="Cetak Profil Karyawan">
                        <i class="fas fa-file-pdf mr-2"></i>
                        <span>Cetak Profil</span>
                    </a>
                </div>
            </div>
            {{-- Konten Modal (Scrollable) --}}
            <div id="detail-modal-content" class="overflow-y-auto">
                {{-- Konten detail akan diisi oleh JavaScript --}}
                <div class="p-6 text-center text-zinc-400">Memuat data...</div>
            </div>
            {{-- Footer Modal --}}
            <div class="p-4 border-t border-zinc-700 text-right">
                 <button class="close-modal-detail bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg text-sm">Tutup</button>
            </div>
        </div>
    </div>
    {{-- === AKHIR MODAL DETAIL === --}}


    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addModal = document.getElementById('add-modal');
            const editModal = document.getElementById('edit-modal');
            const detailModal = document.getElementById('detail-modal');
            const detailModalContent = document.getElementById('detail-modal-content');
            const downloadPdfBtn = document.getElementById('download-profile-pdf-btn'); // Ambil tombol PDF

            // --- Logika Modal Tambah ---
            document.getElementById('open-add-modal-btn')?.addEventListener('click', () => {
                console.log("Opening Add Modal"); // Debugging
                const addForm = document.getElementById('add-form');
                if (addForm) {
                    addForm.reset(); // Reset field standar

                    // --- Reset Manual untuk Logika Divisi "Lainnya" ---
                    const addSelect = document.getElementById('add-divisi-select');
                    const addContainer = document.getElementById('add-divisi-input-container');
                    const addInput = document.getElementById('add-divisi-input');
                    const addPasswordError = document.getElementById('add-password-error');

                    if (addSelect) {
                        addSelect.name = 'divisi'; // Pastikan nama select benar
                        addSelect.value = '';     // Reset pilihan dropdown
                    }
                    if (addContainer) {
                        addContainer.classList.add('hidden'); // Sembunyikan container input
                    }
                    if (addInput) {
                        addInput.name = 'divisi-disabled'; // Ganti nama input
                        addInput.disabled = true;         // Disable input
                        addInput.value = '';              // Kosongkan input
                    }
                    if(addPasswordError) {
                        addPasswordError.classList.add('hidden'); // Sembunyikan error password
                    }
                    // --- Akhir Reset Manual ---

                    addModal?.classList.remove('hidden');
                } else {
                    console.error("Add form not found!"); // Debugging
                }
            });

            // --- Tombol Close Umum & Detail ---
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('.modal')?.classList.add('hidden'));
            });
            document.querySelector('.close-modal-detail')?.addEventListener('click', () => {
                detailModal.classList.add('hidden');
            });

            // --- Logika Toggle Divisi "Lainnya" ---
            function setupDivisiToggle(selectId, containerId, inputId, cancelBtnId) {
                console.log("Attempting setup for:", selectId); // Debugging
                const selectEl = document.getElementById(selectId);
                const containerEl = document.getElementById(containerId);
                const inputEl = document.getElementById(inputId);
                const cancelBtn = document.getElementById(cancelBtnId);

                if (!selectEl || !containerEl || !inputEl || !cancelBtn) {
                    console.error("One or more elements not found for Divisi Toggle:", selectId, containerId, inputId, cancelBtnId); // Debugging
                    return;
                }
                console.log("Elements found for:", selectId); // Debugging

                selectEl.addEventListener('change', function() {
                    console.log(`Select changed [${selectId}]: Value = ${this.value}`); // Debugging
                    if (this.value === 'lainnya') {
                        containerEl.classList.remove('hidden');
                        inputEl.disabled = false;
                        inputEl.name = 'divisi';
                        selectEl.name = 'divisi-disabled'; // Ganti nama select
                        inputEl.focus();
                        console.log(`Input enabled [${inputId}]`); // Debugging
                    } else {
                        containerEl.classList.add('hidden');
                        inputEl.disabled = true;
                        inputEl.name = 'divisi-disabled';
                        selectEl.name = 'divisi'; // Kembalikan nama select
                        inputEl.value = '';
                        console.log(`Input disabled [${inputId}]`); // Debugging
                    }
                });

                cancelBtn.addEventListener('click', function() {
                    console.log(`Cancel clicked for [${selectId}]`); // Debugging
                    containerEl.classList.add('hidden');
                    inputEl.disabled = true;
                    inputEl.name = 'divisi-disabled';
                    selectEl.name = 'divisi';
                    selectEl.value = ''; // Reset pilihan
                    inputEl.value = '';
                });
                console.log("Setup complete for:", selectId); // Debugging
            }

            // --- Panggil setupDivisiToggle UNTUK KEDUA MODAL ---
            setupDivisiToggle('add-divisi-select', 'add-divisi-input-container', 'add-divisi-input', 'add-divisi-cancel-btn');
            setupDivisiToggle('edit-divisi-select', 'edit-divisi-input-container', 'edit-divisi-input', 'edit-divisi-cancel-btn');
            // --------------------------------------------------

            // --- Logika Modal Edit ---
            document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const user = JSON.parse(e.currentTarget.getAttribute('data-user'));
                    if(editModal){
                        const form = editModal.querySelector('#edit-form');
                        form.querySelector('#edit-user-id').value = user.id;
                        form.querySelector('#edit-name').value = user.name;
                        form.querySelector('#edit-email').value = user.email;
                        form.querySelector('#edit-jabatan').value = user.jabatan ?? '';
                        form.querySelector('#edit-password').value = '';
                        form.querySelector('#edit-password-error').classList.add('hidden');
                        const editSelect = form.querySelector('#edit-divisi-select');
                        const editInputContainer = form.querySelector('#edit-divisi-input-container');
                        const editInput = form.querySelector('#edit-divisi-input');
                        const standardDivisions = Array.from(editSelect.options).map(opt => opt.value).filter(val => val && val !== 'lainnya');
                        if (user.divisi && !standardDivisions.includes(user.divisi)) {
                            editSelect.value = 'lainnya'; editSelect.name = 'divisi-disabled';
                            editInputContainer.classList.remove('hidden'); editInput.value = user.divisi;
                            editInput.disabled = false; editInput.name = 'divisi';
                        } else {
                            editSelect.value = user.divisi ?? ''; editSelect.name = 'divisi';
                            editInputContainer.classList.add('hidden'); editInput.value = '';
                            editInput.disabled = true; editInput.name = 'divisi-disabled';
                        }
                        form.querySelector('#edit-tanggal_bergabung').value = user.tanggal_bergabung;
                        editModal.classList.remove('hidden');
                    }
                });
            });

            // --- Script Modal Detail ---
            document.querySelectorAll('.open-detail-modal-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const user = JSON.parse(e.currentTarget.getAttribute('data-user'));

                    // 1. Set URL tombol download PDF
                    if (downloadPdfBtn) {
                        const downloadUrl = `{{ url('admin/employees') }}/${user.id}/download-pdf`;
                        downloadPdfBtn.href = downloadUrl;
                    }

                    // 2. Helper Functions (LENGKAP)
                    const formatDate = (dateString) => {
                        if (!dateString) return '-';
                        // Coba parsing dengan format YYYY-MM-DD
                        const parts = dateString.split('-');
                        if (parts.length === 3) {
                            const date = new Date(parts[0], parts[1] - 1, parts[2]);
                            if (!isNaN(date.getTime())) { // Cek apakah tanggal valid
                                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                            }
                        }
                        // Fallback jika format tidak sesuai atau parsing gagal
                        try {
                            const date = new Date(dateString);
                            if (!isNaN(date.getTime())) {
                                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                            }
                        } catch (error) {
                            console.error("Error parsing date:", dateString, error);
                        }
                        return '-'; // Kembalikan strip jika gagal format
                    };

                    const renderRiwayatPendidikan = (pendidikan) => {
                        if (!pendidikan || pendidikan.length === 0) {
                            return '<tr><td colspan="4" class="py-2 text-center text-zinc-500">Tidak ada data.</td></tr>';
                        }
                        // Urutkan descending berdasarkan tahun lulus, handle null/kosong
                        const sortedPendidikan = pendidikan.sort((a, b) => {
                            const yearA = parseInt(a.tahun_lulus) || 0;
                            const yearB = parseInt(b.tahun_lulus) || 0;
                            return yearB - yearA; // Descending
                        });

                        return sortedPendidikan.map(p => `
                            <tr class="divide-y divide-zinc-700">
                                <td class="py-2 pr-4">${p.jenjang || '-'}</td>
                                <td class="py-2 pr-4">${p.nama_institusi || '-'}</td>
                                <td class="py-2 pr-4">${p.jurusan || '-'}</td>
                                <td class="py-2">${p.tahun_lulus || '-'}</td>
                            </tr>
                        `).join('');
                    };

                    const renderRiwayatPekerjaan = (pekerjaan) => {
                        if (!pekerjaan || pekerjaan.length === 0) {
                            return '<tr><td colspan="3" class="py-2 text-center text-zinc-500">Tidak ada data.</td></tr>'; // Colspan jadi 3
                        }
                        // Urutkan descending berdasarkan tanggal selesai, handle null/kosong
                        const sortedPekerjaan = pekerjaan.sort((a, b) => {
                            const dateA = a.tanggal_selesai ? new Date(a.tanggal_selesai) : new Date(0); // Epoch jika null
                            const dateB = b.tanggal_selesai ? new Date(b.tanggal_selesai) : new Date(0);
                            // Handle invalid dates
                            if (isNaN(dateA.getTime())) return 1;
                            if (isNaN(dateB.getTime())) return -1;
                            return dateB - dateA; // Descending
                        });

                        return sortedPekerjaan.map(p => `
                            <tr class="divide-y divide-zinc-700">
                                <td class="py-2 pr-4">${p.nama_perusahaan || '-'}</td>
                                <td class="py-2 pr-4">${p.posisi || '-'}</td>
                                <td class="py-2 pr-4 whitespace-nowrap">${formatDate(p.tanggal_mulai)} - ${formatDate(p.tanggal_selesai)}</td>
                            </tr>
                        `).join('');
                    };


                    // 3. Isi Konten Modal (LENGKAP)
                    detailModalContent.innerHTML = `
                        <div class="p-6">
                            {{-- Header Info Dasar --}}
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 mb-6 pb-6 border-b border-zinc-700">
                                <div class="w-24 h-24 sm:w-32 sm:h-32 aspect-square overflow-hidden rounded-full border-4 border-zinc-600 shadow-sm shrink-0">
                                    <img class="w-full h-full object-cover"
                                        src="${user.profile_picture ? '{{ asset('storage') }}/' + user.profile_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=random&color=fff&size=128'}"
                                        alt="${user.name}">
                                </div>
                                <div class="text-center sm:text-left flex-grow">
                                    <h3 class="text-2xl font-bold text-white">${user.name}</h3>
                                    <p class="text-indigo-400 font-semibold">${user.jabatan || '-'}</p>
                                    <p class="text-zinc-400 text-sm mt-1">
                                        ${user.divisi || 'Tanpa Divisi'}
                                        ${user.is_kepala_divisi ? '<span class="ml-2 text-xs font-semibold text-cyan-300">(Kepala Divisi)</span>' : ''}
                                    </p>
                                    <p class="text-zinc-400 text-sm">${user.email}</p>
                                </div>
                            </div>

                            {{-- Bagian Data dalam Grid --}}
                            <div class="space-y-6 text-sm">

                                {{-- Data Ketenagakerjaan --}}
                                <div class="bg-zinc-900/50 p-4 rounded-lg">
                                    <h4 class="font-bold text-white mb-3 text-base border-b border-zinc-700 pb-2">Informasi Ketenagakerjaan</h4>
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">NIP</dt><dd class="text-white font-semibold">${user.nip || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Status Karyawan</dt><dd class="text-white font-semibold">${user.status_karyawan || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Tgl. Bergabung</dt><dd class="text-white font-semibold">${formatDate(user.tanggal_bergabung)}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Lokasi Kerja</dt><dd class="text-white font-semibold">${user.lokasi_kerja || '-'}</dd></div>
                                        ${user.status_karyawan === 'Kontrak' ? `
                                            <div class="sm:col-span-1"><dt class="text-zinc-400">Mulai Kontrak</dt><dd class="text-white font-semibold">${formatDate(user.tanggal_mulai_kontrak)}</dd></div>
                                            <div class="sm:col-span-1"><dt class="text-zinc-400">Akhir Kontrak</dt><dd class="text-white font-semibold">${formatDate(user.tanggal_akhir_kontrak)}</dd></div>
                                        ` : ''}
                                    </dl>
                                </div>

                                {{-- Data Pribadi --}}
                                <div class="bg-zinc-900/50 p-4 rounded-lg">
                                    <h4 class="font-bold text-white mb-3 text-base border-b border-zinc-700 pb-2">Data Pribadi</h4>
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">NIK</dt><dd class="text-white font-semibold">${user.nik || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Telepon</dt><dd class="text-white font-semibold">${user.nomor_telepon || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Tempat & Tgl Lahir</dt><dd class="text-white font-semibold">${user.tempat_lahir || '-'}${user.tempat_lahir && user.tanggal_lahir ? ', ' : ''}${formatDate(user.tanggal_lahir)}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Jenis Kelamin</dt><dd class="text-white font-semibold">${user.jenis_kelamin || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Agama</dt><dd class="text-white font-semibold">${user.agama || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Status Pernikahan</dt><dd class="text-white font-semibold">${user.status_pernikahan || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Golongan Darah</dt><dd class="text-white font-semibold">${user.golongan_darah || '-'}</dd></div>
                                        <div class="sm:col-span-2"><dt class="text-zinc-400">Alamat KTP</dt><dd class="text-white font-semibold">${user.alamat_ktp || '-'}</dd></div>
                                        <div class="sm:col-span-2"><dt class="text-zinc-400">Alamat Domisili</dt><dd class="text-white font-semibold">${user.alamat_domisili || '-'}</dd></div>
                                    </dl>
                                </div>

                                {{-- Riwayat Pendidikan --}}
                                <div class="bg-zinc-900/50 p-4 rounded-lg">
                                    <h4 class="font-bold text-white mb-3 text-base border-b border-zinc-700 pb-2">Riwayat Pendidikan</h4>
                                    <table class="w-full text-left text-sm">
                                        <thead class="text-zinc-400"><tr><th class="py-1 pr-4">Jenjang</th><th class="py-1 pr-4">Institusi</th><th class="py-1 pr-4">Jurusan</th><th class="py-1">Lulus</th></tr></thead>
                                        <tbody class="text-white">${renderRiwayatPendidikan(user.riwayat_pendidikan)}</tbody>
                                    </table>
                                </div>

                                {{-- Riwayat Pekerjaan --}}
                                <div class="bg-zinc-900/50 p-4 rounded-lg">
                                    <h4 class="font-bold text-white mb-3 text-base border-b border-zinc-700 pb-2">Riwayat Pekerjaan</h4>
                                    <table class="w-full text-left text-sm">
                                        <thead class="text-zinc-400"><tr><th class="py-1 pr-4">Perusahaan</th><th class="py-1 pr-4">Posisi</th><th class="py-1 pr-4">Masa Kerja</th></tr></thead>
                                        <tbody class="text-white">${renderRiwayatPekerjaan(user.riwayat_pekerjaan)}</tbody>
                                    </table>
                                </div>

                                {{-- Data Administrasi & Bank --}}
                                <div class="bg-zinc-900/50 p-4 rounded-lg">
                                    <h4 class="font-bold text-white mb-3 text-base border-b border-zinc-700 pb-2">Administrasi & Bank</h4>
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">NPWP</dt><dd class="text-white font-semibold">${user.npwp || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Status PTKP</dt><dd class="text-white font-semibold">${user.ptkp || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">BPJS Kesehatan</dt><dd class="text-white font-semibold">${user.bpjs_kesehatan || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">BPJS Ketenagakerjaan</dt><dd class="text-white font-semibold">${user.bpjs_ketenagakerjaan || '-'}</dd></div>
                                        <div class="sm:col-span-2 pt-3 mt-3 border-t border-zinc-700"><dt class="text-zinc-400">Bank</dt><dd class="text-white font-semibold">${user.nama_bank || '-'} (${user.nomor_rekening || '-'}) a.n. ${user.pemilik_rekening || '-'}</dd></div>
                                    </dl>
                                </div>

                                {{-- Kontak Darurat --}}
                                <div class="bg-zinc-900/50 p-4 rounded-lg">
                                    <h4 class="font-bold text-white mb-3 text-base border-b border-zinc-700 pb-2">Kontak Darurat</h4>
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Nama</dt><dd class="text-white font-semibold">${user.kontak_darurat_nama || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Nomor Telepon</dt><dd class="text-white font-semibold">${user.kontak_darurat_nomor || '-'}</dd></div>
                                        <div class="sm:col-span-1"><dt class="text-zinc-400">Hubungan</dt><dd class="text-white font-semibold">${user.kontak_darurat_hubungan || '-'}</dd></div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    `;

                    // 4. Tampilkan Modal
                    detailModal.classList.remove('hidden');
                });
            });

            // --- Logika Toggle Password ---
            document.querySelectorAll('.toggle-password-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const input = btn.previousElementSibling;
                    const eyeIcon = btn.querySelector('.fa-eye');
                    const eyeSlashIcon = btn.querySelector('.fa-eye-slash');
                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeIcon.classList.add('hidden');
                        eyeSlashIcon.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        eyeIcon.classList.remove('hidden');
                        eyeSlashIcon.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    @endpush
</x-layout-admin>