<x-layout-users :title="'Sistem Informasi Sales (CRM)'">

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r shadow-sm flex items-center animate-fade-in-down">
            <i class="fas fa-check-circle mr-3 text-lg"></i>
            <div>
                <p class="font-bold">Sukses</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Customer Relationship Management</h2>
            <p class="text-gray-500 mt-1">Monitoring Area, PIC, dan Total Kontribusi Klien.</p>
        </div>
        
        {{-- Group Tombol Aksi --}}
        <div class="flex flex-wrap gap-3">
            {{-- Tombol Laporan Matrix (HIJAU) --}}
            <a href="{{ route('crm.matrix') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center transition transform hover:-translate-y-0.5">
                <i class="fas fa-table mr-2"></i> Laporan Sales Tahunan
            </a>

            {{-- Tombol Tambah Klien (BIRU) --}}
            <button onclick="toggleModal('createClientModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Tambah Klien Baru
            </button>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header Tabel --}}
        <div class="px-8 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Daftar Klien Aktif</h3>
            <span class="bg-white border border-gray-200 text-gray-500 text-xs px-3 py-1 rounded-full">
                Total: {{ $clients->count() }} Klien
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase font-bold text-xs tracking-wider">
                    <tr>
                        <th class="px-8 py-4">Area / PIC</th>
                        <th class="px-8 py-4">User (Klien) & Perusahaan</th>
                        <th class="px-8 py-4">Kontak</th>
                        <th class="px-8 py-4 text-right">Total Sales (IDR)</th>
                        <th class="px-8 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($clients as $client)
                    <tr class="hover:bg-blue-50 transition duration-150">
                        {{-- Area & PIC --}}
                        <td class="px-8 py-5 align-middle">
                            <div class="font-bold text-gray-800">{{ $client->area ?? '-' }}</div>
                            <div class="mt-1">
                                <span class="text-xs text-blue-800 bg-blue-100 px-2 py-0.5 rounded border border-blue-200">
                                    PIC : {{ $client->pic ?? '-' }}
                                </span>
                            </div>
                        </td>

                        {{-- Nama User & Perusahaan --}}
                        <td class="px-8 py-5 align-middle">
                            <div class="font-bold text-blue-900 text-base mb-1">{{ $client->nama_user }}</div>
                            
                            <div class="flex items-center text-indigo-700 font-semibold text-xs mb-1.5">
                                <i class="fas fa-hospital mr-1.5"></i> {{ $client->nama_perusahaan }}
                            </div>

                            {{-- Tampilan Tanggal Berdiri (Teks Simpel) --}}
                            @if($client->tanggal_berdiri)
                                <div class="mt-1 text-xs text-gray-500 font-medium">
                                    Didirikan pada {{ \Carbon\Carbon::parse($client->tanggal_berdiri)->format('d M Y') }}
                                </div>
                            @endif
                        </td>

                        {{-- Kontak --}}
                        <td class="px-8 py-5 align-middle text-gray-600 space-y-1">
                            @if($client->email)
                                <div class="flex items-center gap-2 text-xs">
                                    <i class="fas fa-envelope text-gray-400 w-4"></i> {{ $client->email }}
                                </div>
                            @endif
                            @if($client->no_telpon)
                                <div class="flex items-center gap-2 text-xs">
                                    <i class="fas fa-phone text-gray-400 w-4"></i> {{ $client->no_telpon }}
                                </div>
                            @endif
                        </td>

                        {{-- Total Sales --}}
                        <td class="px-8 py-5 align-middle text-right">
                            <span class="block font-mono font-bold text-green-600 text-base">
                                Rp {{ number_format($client->total_kontribusi, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Tombol Aksi --}}
                        <td class="px-8 py-5 align-middle text-center">
                            <a href="{{ route('crm.show', $client->id) }}" class="inline-flex items-center justify-center bg-white border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                Detail & Input
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-gray-400 bg-gray-50">
                            <i class="fas fa-folder-open text-4xl mb-3 block opacity-50"></i>
                            <span class="block">Belum ada data klien.</span>
                            <span class="text-xs block mt-1">Klik tombol "Tambah Klien Baru" di atas.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL INPUT CLIENT BARU --}}
    {{-- Menggunakan @push('modals') agar dirender di Layout (Luar Main Content) --}}
    @push('modals')
    <div id="createClientModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-100">
            {{-- Header Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">Input Data Klien Baru</h3>
                <button onclick="toggleModal('createClientModal')" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
            </div>
            
            <form action="{{ route('crm.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    {{-- Baris 1: Area & PIC --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Area</label>
                            <input type="text" name="area" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5" placeholder="Ex: JakSel">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">PIC Sales</label>
                            <input type="text" name="pic" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5" placeholder="Ex: Budi">
                        </div>
                    </div>

                    {{-- Baris 2: Data Utama --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama User / Dokter <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_user" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" placeholder="Nama Lengkap User">
                    </div>

                    {{-- Baris 3: Data Perusahaan & Tanggal Berdiri --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama RS / PT <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_perusahaan" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" placeholder="Nama Instansi">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tgl. Berdiri / HUT</label>
                            <input type="date" name="tanggal_berdiri" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        </div>
                    </div>

                    {{-- Baris 4: Kontak --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label>
                            <input type="email" name="email" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Telpon</label>
                            <input type="text" name="no_telpon" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        </div>
                    </div>
                    
                    {{-- Alamat --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5"></textarea>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('createClientModal')" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-blue-700 transition">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
    @endpush
</x-layout-users>