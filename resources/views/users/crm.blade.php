<x-layout-users :title="'Manajemen Klien (CRM)'">

    {{-- DITAMBAHKAN: Style khusus untuk tabel responsif & animasi modal --}}
    @push('styles')
    <style>
        /* CSS untuk mengubah tabel menjadi card di mobile */
        @media (max-width: 767px) {
            .responsive-table thead {
                display: none;
            }
            .responsive-table tr {
                display: block;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                margin-bottom: 1rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                background-color: #fff;
            }
            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                text-align: right;
                border-bottom: 1px solid #edf2f7;
            }
            .responsive-table td:last-child {
                border-bottom: none;
            }
            .responsive-table td::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                margin-right: 1rem;
                color: #4a5568;
            }
        }

        /* Kelas untuk animasi modal */
        .modal.modal-active {
            display: flex;
        }
    </style>
    @endpush

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Klien (CRM)</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola dan lacak interaksi dengan semua klien Anda.</p>
        </div>
        <button onclick="toggleModal('createClientModal')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105 flex items-center justify-center shrink-0">
            <i class="fas fa-plus mr-2"></i>
            Tambah Klien Baru
        </button>
    </div>

    {{-- CONTAINER UTAMA (CARD PUTIH) --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">
        
        <div class="mb-6">
            <input type="text" placeholder="Cari nama perusahaan atau kontak..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>

        {{-- TABEL KLIEN --}}
        <div class="overflow-x-auto">
            {{-- DIUBAH: Ditambahkan class 'responsive-table' --}}
            <table class="min-w-full responsive-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 sm:px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Perusahaan</th>
                        <th class="text-left py-3 px-4 sm:px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Kontak Person</th>
                        <th class="text-left py-3 px-4 sm:px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Aktivitas Terakhir</th>
                        <th class="text-center py-3 px-4 sm:px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 bg-white md:bg-transparent">
                    {{-- DIUBAH: Ditambahkan atribut data-label untuk mobile --}}
                    <tr class="md:border-b md:hover:bg-gray-50">
                        <td data-label="Perusahaan" class="py-3 px-4 sm:px-6 font-medium text-gray-900">PT. Sukses Selalu</td>
                        <td data-label="Kontak Person" class="py-3 px-4 sm:px-6">Bapak Budi</td>
                        <td data-label="Aktivitas Terakhir" class="py-3 px-4 sm:px-6 text-sm">Follow up penawaran harga via email.</td>
                        <td data-label="Aksi" class="py-3 px-4 sm:px-6 text-center">
                            <button onclick="toggleModal('showClientModal')" class="text-blue-600 hover:text-blue-800 font-semibold">Lihat Detail</button>
                        </td>
                    </tr>
                    
                    <tr class="md:border-b md:hover:bg-gray-50">
                        <td data-label="Perusahaan" class="py-3 px-4 sm:px-6 font-medium text-gray-900">CV. Jaya Abadi</td>
                        <td data-label="Kontak Person" class="py-3 px-4 sm:px-6">Ibu Citra</td>
                        <td data-label="Aktivitas Terakhir" class="py-3 px-4 sm:px-6 text-sm">Mengirimkan proposal project terbaru.</td>
                        <td data-label="Aksi" class="py-3 px-4 sm:px-6 text-center">
                            <button onclick="toggleModal('showClientModal')" class="text-blue-600 hover:text-blue-800 font-semibold">Lihat Detail</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CONTAINER --}}
    @php
        $modals = [
            'createClientModal' => [
                'title' => 'Tambah Klien Baru',
                'size' => 'md:max-w-2xl'
            ],
            'showClientModal' => [
                'title' => 'Detail Klien',
                'size' => 'md:max-w-4xl'
            ]
        ];
    @endphp

    @foreach ($modals as $id => $modal)
    {{-- DIUBAH: Struktur Modal untuk animasi & scrolling --}}
    <div id="{{ $id }}" class="modal fixed inset-0 bg-gray-900 bg-opacity-60 items-center justify-center z-40 hidden p-4 transition-opacity duration-300 ease-in-out opacity-0">
        <div class="bg-white rounded-xl shadow-2xl w-full {{ $modal['size'] }} mx-auto transform transition-transform duration-300 ease-in-out scale-95 max-h-[95vh] flex flex-col">
            <div class="flex justify-between items-center border-b p-4 sm:p-5 shrink-0">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800">{{ $modal['title'] }}</h3>
                <button onclick="toggleModal('{{ $id }}')" class="text-gray-400 hover:text-gray-800 text-2xl transition">&times;</button>
            </div>
            
            <div class="p-4 sm:p-6 overflow-y-auto">
                @if ($id === 'createClientModal')
                    {{-- FORM TAMBAH KLIEN BARU --}}
                    <form action="#" method="POST" class="space-y-5">
                        @csrf
                        <h4 class="font-semibold text-gray-600 border-b pb-2">Informasi Kontak</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="company_name" class="block mb-2 text-sm font-medium text-gray-700">Nama Perusahaan</label>
                                <input type="text" name="company_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="contact_person" class="block mb-2 text-sm font-medium text-gray-700">Kontak Person (PIC)</label>
                                <input type="text" name="contact_person" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                               <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                               <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                             <div>
                               <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-700">Nomor Telepon</label>
                               <input type="text" name="phone_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <h4 class="font-semibold text-gray-600 border-b pb-2 pt-4">Detail Perusahaan</h4>
                        <div>
                            <label for="address" class="block mb-2 text-sm font-medium text-gray-700">Alamat Perusahaan</label>
                            <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                               <label for="website" class="block mb-2 text-sm font-medium text-gray-700">Website</label>
                               <input type="url" name="website" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                               <label for="industry" class="block mb-2 text-sm font-medium text-gray-700">Industri</label>
                               <input type="text" name="industry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="foundation_date" class="block mb-2 text-sm font-medium text-gray-700">Tanggal Berdiri</label>
                                <input type="date" name="foundation_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        {{-- DIHAPUS: Input Sumber Prospek --}}
                    </form>
                @elseif ($id === 'showClientModal')
                    {{-- DETAIL KLIEN --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-5">
                            <h4 class="font-bold text-lg text-gray-700 border-b pb-2">Informasi Klien</h4>
                            <div>
                                <p class="font-semibold text-sm text-gray-800">Kontak Person (PIC)</p>
                                <p class="text-gray-600">Bapak Budi</p>
                            </div>
                             <div>
                                <p class="font-semibold text-sm text-gray-800">Email & Telepon</p>
                                <p class="text-gray-600">budi@sukses.com / 081298765432</p>
                            </div>
                             <div>
                                <p class="font-semibold text-sm text-gray-800">Alamat</p>
                                <p class="text-gray-600">Jl. Raya Pajajaran No. 123, Bogor, Jawa Barat 16143</p>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-gray-800">Website</p>
                                <a href="#" class="text-blue-600 hover:underline">https://sukseselalu.com</a>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-2">
                                 <div>
                                    <p class="font-semibold text-sm text-gray-800">Industri</p>
                                    <p class="text-gray-600">Manufaktur</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">Tanggal Berdiri</p>
                                    <p class="text-gray-600">15 Januari 1998</p>
                                </div>
                                {{-- DIHAPUS: Tampilan Sumber Prospek --}}
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-gray-700 border-b pb-2 mb-4">Riwayat Interaksi</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                 <form action="#" method="POST" class="space-y-3">
                                    <label class="block text-sm font-medium text-gray-700">Tambah Catatan Baru</label>
                                    <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis catatan interaksi..."></textarea>
                                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Catatan</button>
                                </form>
                            </div>
                            <div class="mt-4 space-y-3 max-h-60 overflow-y-auto">
                                <div class="p-3 border rounded-lg">
                                    <p class="text-gray-800">Follow up penawaran harga, klien minta diskon 10%.</p>
                                    <p class="text-xs text-gray-500 mt-1">10 Okt 2025, 14:30</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($id === 'createClientModal')
            <div class="flex justify-end p-4 bg-gray-50 border-t rounded-b-xl shrink-0">
                <button type="button" onclick="toggleModal('{{ $id }}')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 mr-3">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Klien</button>
            </div>
            @endif
        </div>
    </div>
    @endforeach


    {{-- SCRIPT UNTUK MODAL --}}
    @push('scripts')
    <script>
        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            const body = document.querySelector('body');
            const isHidden = modal.classList.contains('hidden');

            if (isHidden) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('modal-active');
                    modal.classList.remove('opacity-0');
                    modal.querySelector('div').classList.remove('scale-95');
                    body.classList.add('overflow-hidden');
                }, 10);
            } else {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                body.classList.remove('overflow-hidden');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('modal-active');
                }, 300); // Waktu transisi 300ms
            }
        }
    </script>
    @endpush

</x-layout-users>