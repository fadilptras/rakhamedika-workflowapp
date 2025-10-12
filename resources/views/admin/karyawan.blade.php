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
                    @foreach($employees->sortByDesc('is_kepala_divisi') as $user)
                        <tr class="hover:bg-zinc-700/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=4f46e5&color=e0e7ff' }}"
                                        alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-4">
                                    <div>
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
                                {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d M Y') : $user->created_at->format('d M Y') }}
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

    <div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-zinc-800 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b border-zinc-700">
                <h2 class="text-xl font-bold text-white">Detail Karyawan</h2>
                <button class="close-modal-detail text-zinc-400 hover:text-white text-2xl">&times;</button>
            </div>
            <div id="detail-modal-content" class="overflow-y-auto">
                {{-- Konten detail akan diisi oleh JavaScript --}}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... (Skrip untuk Modal Tambah & Edit tidak diubah) ...
            const addModal = document.getElementById('add-modal');
            const editModal = document.getElementById('edit-modal');
            document.getElementById('open-add-modal-btn')?.addEventListener('click', () => {
                addModal?.classList.remove('hidden');
            });
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('.modal')?.classList.add('hidden'));
            });
            document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const user = JSON.parse(e.currentTarget.getAttribute('data-user'));
                    if(editModal){
                        const form = editModal.querySelector('#edit-form');
                        form.action = `{{ route('admin.employees.update') }}`;
                        form.querySelector('#edit-user-id').value = user.id;
                        form.querySelector('#edit-name').value = user.name;
                        form.querySelector('#edit-email').value = user.email;
                        form.querySelector('#edit-jabatan').value = user.jabatan ?? '';
                        // ...
                        editModal.classList.remove('hidden');
                    }
                });
            }); 
            // Akhir Skrip Modal Tambah & Edit

            // --- SCRIPT UNTUK MODAL DETAIL ---
            const detailModal = document.getElementById('detail-modal');
            const detailModalContent = document.getElementById('detail-modal-content');

            document.querySelectorAll('.open-detail-modal-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const user = JSON.parse(btn.getAttribute('data-user'));
                    
                    const formatDate = (dateString) => {
                        if (!dateString) return '-';
                        const date = new Date(dateString);
                        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                    };

                    // PERBAIKAN UTAMA ADA DI SINI
                    detailModalContent.innerHTML = `
                        <div class="flex flex-col sm:flex-row items-center sm:items-start p-6 gap-6">
                            
                            <div class="w-32 h-32 aspect-square overflow-hidden rounded-full border-4 border-zinc-600 shadow-sm shrink-0">
                                <img class="w-full h-full object-cover" 
                                     src="${user.profile_picture ? '{{ asset('storage') }}/' + user.profile_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=random&color=fff&size=128'}" 
                                     alt="${user.name}">
                            </div>

                            <div class="text-center sm:text-left">
                                <h3 class="text-2xl font-bold text-white">${user.name}</h3>
                                <p class="text-indigo-400 font-semibold">${user.jabatan || '-'}</p>
                                <p class="text-zinc-400 text-sm mt-1">${user.divisi || 'Belum ada divisi'}</p>
                                <p class="text-zinc-400 text-sm">${user.email}</p>
                            </div>
                        </div>
                        <div class="border-t border-zinc-700 p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                            <div>
                                <p class="text-zinc-400">Telepon</p>
                                <p class="text-white font-semibold">${user.nomor_telepon || '-'}</p>
                            </div>
                            <div>
                                <p class="text-zinc-400">Tgl. Bergabung</p>
                                <p class="text-white font-semibold">${formatDate(user.tanggal_bergabung)}</p>
                            </div>
                            <div>
                                <p class="text-zinc-400">Pendidikan</p>
                                <p class="text-white font-semibold">${user.pendidikan_terakhir || '-'}</p>
                            </div>
                            <div>
                                <p class="text-zinc-400">Tempat & Tanggal Lahir</p>
                                <p class="text-white font-semibold">${user.tempat_lahir || '-'}${user.tempat_lahir && user.tanggal_lahir ? ', ' : ''}${formatDate(user.tanggal_lahir)}</p>
                            </div>
                            <div>
                                <p class="text-zinc-400">Jenis Kelamin</p>
                                <p class="text-white font-semibold">${user.jenis_kelamin || '-'}</p>
                            </div>
                            <div>
                                <p class="text-zinc-400">NIK</p>
                                <p class="text-white font-semibold">${user.nik || '-'}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-zinc-400">Alamat</p>
                                <p class="text-white font-semibold">${user.alamat || '-'}</p>
                            </div>
                            <div class="md:col-span-2 border-t border-zinc-700 pt-4">
                                <p class="text-zinc-400">Kontak Darurat</p>
                                <p class="text-white font-semibold">${user.kontak_darurat_nama || '-'} (${user.kontak_darurat_nomor || 'No. Tlp tidak ada'})</p>
                            </div>
                        </div>
                    `;
                    detailModal.classList.remove('hidden');
                });
            });

            document.querySelector('.close-modal-detail').addEventListener('click', () => {
                detailModal.classList.add('hidden');
            });
        });
    </script>
    @endpush
</x-layout-admin>