<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex flex-col h-full bg-gradient-to-br from-sky-50 to-blue-100">
        <main class="flex-1 overflow-y-auto min-h-screen p-4 lg:p-6">

            {{-- Menampilkan Pesan Sukses/Error Setelah Reload --}}
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

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- Kolom Kiri (Profil Pengguna) --}}
                <div class="xl:col-span-1 space-y-6">
                    <div class="bg-white/60 backdrop-blur-lg border border-white/30 shadow-xl shadow-blue-500/20 p-6 rounded-2xl">
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 aspect-square overflow-hidden rounded-full border-4 border-white/50 shadow-sm">
                                <img class="w-full h-full object-cover" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=random&color=fff&size=128' }}" alt="Foto Profil">
                            </div>
                        </div>
                        <div class="text-left space-y-3 mt-4">
                            <h2 class="text-2xl font-bold text-center">Welcome, {{ Auth::user()->name }}!</h2>
                            <div><label class="text-xs text-blue-900/80 font-semibold">Divisi</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->divisi ?? '-' }}</p></div>
                            <div><label class="text-xs text-blue-900/80 font-semibold">Posisi</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->jabatan ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan (Daftar Agenda) --}}
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white/60 backdrop-blur-lg border border-white/30 shadow-xl shadow-blue-500/20 p-6 rounded-2xl">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900 text-xl">Agenda Saya</h3>
                            <a href="{{ route('agendas.create') }}" class="open-modal-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tambah Agenda
                            </a>
                        </div>
                        
                        @php
                            $user = Auth::user();
                            $agendas = \App\Models\Agenda::where(function ($query) use ($user) {
                                $query->where('user_id', $user->id)
                                    ->orWhereHas('guests', function ($subQuery) use ($user) {
                                        $subQuery->where('user_id', $user->id); 
                                    });
                            })
                            ->with('creator')
                            ->orderBy('start_time', 'desc')
                            ->paginate(10);
                        @endphp

                        <div class="space-y-4">
                            @forelse($agendas as $agenda)
                                <div class="p-4 rounded-lg border bg-white flex items-center justify-between">
                                    <div class="border-l-4 pl-4" style="border-color: {{ $agenda->color ?? '#3B82F6' }}">
                                        <p class="font-semibold text-gray-800">{{ $agenda->title }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($agenda->start_time)->isoFormat('dddd, D MMMM YYYY \p\u\k\u\l H:mm') }}
                                        </p>
                                        <p class="text-xs text-gray-400">Penyelenggara: {{ $agenda->creator->name }}</p>
                                    </div>
                                    <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                                        @if($agenda->user_id === Auth::id())
                                            <a href="{{ route('agendas.edit', $agenda) }}" class="open-modal-btn text-amber-500 hover:text-amber-700" title="Edit">
                                                <i class="fas fa-edit fa-lg"></i>
                                            </a>
                                            <form action="{{ route('agendas.destroy', $agenda) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus agenda ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                    <i class="fas fa-trash fa-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 italic text-sm px-2">Diundang</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">Anda belum memiliki agenda.</p>
                            @endforelse
                        </div>

                        <div class="mt-6">
                            {{ $agendas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL CONTAINER KOSONG --}}
    <div id="agenda-modal" class="fixed inset-0 bg-black bg-opacity-60 z-40 hidden flex items-center justify-center p-4">
        <div class="bg-gray-100 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            {{-- Tombol Close di Pojok Kanan Atas --}}
            <div class="flex justify-end p-2">
                 <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            {{-- Konten Form akan dimuat di sini --}}
            <div id="modal-content" class="overflow-y-auto p-6 -mt-8">
                {{-- Loading Spinner --}}
                <div class="text-center p-8">Memuat formulir...</div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('agenda-modal');
            const modalContent = document.getElementById('modal-content');
            const closeModalBtn = document.getElementById('close-modal-btn');

            // Fungsi untuk membuka modal
            function openModal() {
                modal.classList.remove('hidden');
            }

            // Fungsi untuk menutup modal
            function closeModal() {
                modal.classList.add('hidden');
                modalContent.innerHTML = '<div class="text-center p-8">Memuat formulir...</div>'; // Reset konten
            }

            // Event listener untuk tombol close dan area luar modal
            closeModalBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Event listener utama untuk semua tombol "Tambah" atau "Edit"
            document.body.addEventListener('click', function(e) {
                // Cari apakah elemen yang diklik atau parent-nya memiliki class 'open-modal-btn'
                const button = e.target.closest('.open-modal-btn');

                if (button) {
                    e.preventDefault(); // Mencegah navigasi ke halaman baru
                    const url = button.getAttribute('href');

                    openModal(); // Tampilkan modal dengan pesan "Memuat..."

                    // Ambil konten form dari server
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Gagal memuat formulir.');
                            }
                            return response.text();
                        })
                        .then(html => {
                            // Masukkan HTML form ke dalam modal
                            modalContent.innerHTML = html;
                        })
                        .catch(error => {
                            modalContent.innerHTML = `<div class="text-center text-red-500 p-8">${error.message}</div>`;
                        });
                }
            });
        });
    </script>
    @endpush

</x-layout-users>