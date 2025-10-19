<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-gradient-to-br from-sky-50 to-blue-100 min-h-screen">
        <div class="container mx-auto p-0 md:p-0">
            
            <form action="{{ route('pengajuan_dana.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-8">
                    
                    {{-- KARTU 1: DETAIL PENGAJUAN --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                        <div class="p-6 md:p-8 bg-[#333446] text-white">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-file-alt text-3xl opacity-80"></i>
                                <div>
                                    <h2 class="text-xl font-bold">1. Detail Pengajuan</h2>
                                    <p class="text-sm text-slate-300">Informasi dasar mengenai pemohon dan judul pengajuan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Pemohon</label>
                                    <input type="text" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ Auth::user()->name }}" readonly>
                                    <input type="hidden" name="nama_pemohon" value="{{ Auth::user()->name }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Divisi</label>
                                    <input type="text" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ Auth::user()->divisi }}" readonly>
                                    <input type="hidden" name="divisi" value="{{ Auth::user()->divisi }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
                                    <input type="text" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ Auth::user()->jabatan ?? '-' }}" readonly>
                                </div>
                                 <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                    <input type="email" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ Auth::user()->email }}" readonly>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1" for="judul-pengajuan">Judul Pengajuan <span class="text-red-500">*</span></label>
                                    <input type="text" id="judul-pengajuan" name="judul_pengajuan" class="w-full p-3 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:shadow-inner" placeholder="Contoh: Pembelian Perlengkapan Kantor" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pengajuan</label>
                                    <input type="text" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ date('d F Y') }}" readonly>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KARTU 2: INFORMASI REKENING --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                        <div class="p-6 md:p-8 bg-[#333446] text-white">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-university text-3xl opacity-80"></i>
                                <div>
                                    <h2 class="text-xl font-bold">2. Informasi Rekening</h2>
                                    <p class="text-sm text-slate-300">Tujuan transfer dana jika pengajuan disetujui.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1" for="pilih-bank">Pilih Bank <span class="text-red-500">*</span></label>
                                        <select id="pilih-bank" name="nama_bank" class="w-full p-3 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:shadow-inner" required>
                                            <option value="" disabled selected>Pilih salah satu</option>
                                            <option value="BCA">BCA</option><option value="BRI">BRI</option><option value="BNI">BNI</option><option value="Mandiri">Mandiri</option><option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                    <div id="bank-lainnya-container" class="hidden">
                                        <label class="block text-sm font-medium text-slate-700 mb-1" for="input-bank-lainnya">Nama Bank <span class="text-red-500">*</span></label>
                                        <input type="text" id="input-bank-lainnya" name="nama_bank_lainnya" class="w-full p-3 bg-white border border-slate-300 rounded-lg focus:shadow-inner focus:ring-2 focus:ring-indigo-500" placeholder="Nama bank">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1" for="no-rekening">Nomor Rekening <span class="text-red-500">*</span></label>
                                    <input type="text" id="no-rekening" name="no_rekening" class="w-full p-3 bg-white border border-slate-300 rounded-lg focus:shadow-inner focus:ring-2 focus:ring-indigo-500" placeholder="Masukkan nomor rekening" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KARTU 3: RINCIAN DANA --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                         <div class="p-6 md:p-8 bg-[#333446] text-white">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-list-ul text-3xl opacity-80"></i>
                                <div>
                                    <h2 class="text-xl font-bold">3. Rincian Penggunaan Dana <span class="text-red-500">*</span></h2>
                                    <p class="text-sm text-slate-300">Tambahkan satu atau lebih item pengeluaran.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="hidden md:block rounded-lg border border-slate-200">
                                <table class="min-w-full text-sm"><thead class="bg-slate-100 text-slate-600 uppercase"><tr><th class="px-4 py-3 text-left font-semibold w-2/3">Deskripsi</th><th class="px-4 py-3 text-left font-semibold w-1/3">Jumlah (Rp)</th><th class="px-4 py-3 text-center font-semibold w-16"></th></tr></thead><tbody id="rincian-dana-body" class="bg-white divide-y divide-slate-200"></tbody></table>
                            </div>
                            <div id="rincian-dana-container-mobile" class="block md:hidden space-y-3"></div>
                            <button id="tambah-baris-btn" type="button" class="mt-3 bg-slate-700 hover:bg-slate-800 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center gap-2 transition"><i class="fas fa-plus"></i> Tambah Item</button>
                            <div class="mt-4 pt-4 border-t-2 border-slate-200 flex items-center justify-end">
                                <span class="text-slate-700 font-bold mr-4">Total:</span>
                                <span class="text-slate-900 font-extrabold text-2xl">Rp <span id="total-dana-display">0</span></span>
                                <input type="hidden" id="jumlah-dana-total" name="jumlah_dana_total">
                            </div>
                        </div>
                    </div>

                    {{-- KARTU 4: FILE PENDUKUNG --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                        <div class="p-6 md:p-8 bg-[#333446] text-white">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-paperclip text-3xl opacity-80"></i>
                                <div>
                                    <h2 class="text-xl font-bold">4. File Pendukung (Opsional)</h2>
                                    <p class="text-sm text-slate-300">Anda bisa melampirkan lebih dari satu file (nota, invoice, dll).</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div id="file-pendukung-container" class="space-y-3">
                                {{-- Input file akan ditambahkan secara dinamis oleh script --}}
                            </div>
                            <button id="tambah-lampiran-btn" type="button" class="mt-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2 px-4 rounded-lg text-sm flex items-center gap-2 transition">
                                <i class="fas fa-plus"></i> Tambah File Lampiran
                            </button>
                            <p class="text-xs text-slate-500 mt-2">Tipe file: PDF, DOC, JPG, PNG (Maks. 5MB per file)</p>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI FINAL --}}
                    <div class="flex justify-end space-x-4 pt-4">
                        {{-- Tombol Reset diubah menjadi type="button" dengan ID --}}
                        <button type="button" id="reset-form-btn" class="bg-white hover:bg-slate-100 text-slate-700 font-semibold py-3 px-8 rounded-lg border border-slate-300 transition">Reset</button>
                        <button type="submit" class="bg-gradient-to-r from-blue-700 to-blue-600 hover:from-indigo-600 hover:to-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">Ajukan Dana</button>
                    </div>

                </div>
            </form>

            {{-- KARTU STATUS PENGAJUAN --}}
            <div class="mt-12 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-slate-800">Status Pengajuan Anda</h2>
                    <p class="text-sm text-slate-500 mt-1">Riwayat semua pengajuan dana yang telah Anda buat.</p>
                </div>
                
                <div class="overflow-x-auto">
                    {{-- Konten tabel dan kartu status --}}
                    <table class="hidden md:table min-w-full border-t border-slate-200 text-sm"><thead class="bg-slate-50 text-slate-600"><tr><th class="px-6 py-3 text-left font-semibold">Tanggal</th><th class="px-6 py-3 text-left font-semibold">Judul Pengajuan</th><th class="px-6 py-3 text-left font-semibold">Total Dana</th><th class="px-6 py-3 text-left font-semibold">Status</th><th class="px-6 py-3 text-center font-semibold">Aksi</th></tr></thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($pengajuanDanas as $pengajuan)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 text-slate-600">{{ $pengajuan->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $pengajuan->judul_pengajuan }}</td>
                                <td class="px-6 py-4 font-medium">Rp {{ number_format($pengajuan->total_dana, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if ($pengajuan->status == 'diajukan')
                                        <span class="font-bold bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">Diajukan</span>
                                    @elseif ($pengajuan->status == 'diproses')
                                        <span class="font-bold bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Diproses</span>
                                    @elseif ($pengajuan->status == 'disetujui')
                                        <span class="font-bold bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Disetujui</span>
                                    @elseif ($pengajuan->status == 'ditolak')
                                        <span class="font-bold bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Ditolak</span>
                                    @elseif ($pengajuan->status == 'dibatalkan')
                                        <span class="font-bold bg-slate-100 text-slate-800 px-2 py-1 rounded-full text-xs">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center"><a href="{{ route('pengajuan_dana.show', $pengajuan->id) }}" class="text-indigo-600 hover:underline font-semibold">Lihat Detail</a></td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-10 text-center text-slate-500" colspan="5">Belum ada pengajuan dana yang dibuat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="block md:hidden p-4 space-y-4 border-t border-slate-200">
                        @forelse ($pengajuanDanas as $pengajuan)
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-bold text-slate-800 text-base pr-4">{{ $pengajuan->judul_pengajuan }}</div>
                                @if ($pengajuan->status == 'diajukan')
                                    <span class="flex-shrink-0 font-bold bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">Diajukan</span>
                                @elseif ($pengajuan->status == 'diproses')
                                    <span class="flex-shrink-0 font-bold bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Diproses</span>
                                @elseif ($pengajuan->status == 'disetujui')
                                    <span class="flex-shrink-0 font-bold bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Disetujui</span>
                                @elseif ($pengajuan->status == 'ditolak')
                                    <span class="flex-shrink-0 font-bold bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Ditolak</span>
                                @elseif ($pengajuan->status == 'dibatalkan')
                                    <span class="flex-shrink-0 font-bold bg-slate-100 text-slate-800 px-2 py-1 rounded-full text-xs">Dibatalkan</span>
                                @endif
                            </div>
                            <div class="text-sm text-slate-500 mb-3">{{ $pengajuan->created_at->format('d M Y') }}</div>
                            <div class="flex justify-between items-center">
                                <div class="text-slate-600">Total: <span class="font-bold text-slate-800">Rp {{ number_format($pengajuan->total_dana, 0, ',', '.') }}</span></div>
                                <a href="{{ route('pengajuan_dana.show', $pengajuan->id) }}" class="text-blue-600 hover:underline text-sm font-semibold">Lihat Detail</a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-slate-500 py-8">Belum ada pengajuan dana yang dibuat.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Script untuk show/hide input bank lainnya
        document.getElementById('pilih-bank')?.addEventListener('change', function() {const bankContainer = document.getElementById('bank-lainnya-container'); const otherBankInput = document.getElementById('input-bank-lainnya'); if (this.value === 'other') {bankContainer.classList.remove('hidden'); otherBankInput.setAttribute('required', 'required');} else {bankContainer.classList.add('hidden'); otherBankInput.removeAttribute('required');}});
        
        // Script untuk menambah baris rincian dana
        const tambahBarisBtn = document.getElementById('tambah-baris-btn'); 
        const rincianDanaBodyDesktop = document.getElementById('rincian-dana-body'); 
        const rincianDanaContainerMobile = document.getElementById('rincian-dana-container-mobile'); 
        const totalDanaDisplay = document.getElementById('total-dana-display'); 
        const jumlahDanaTotalInput = document.getElementById('jumlah-dana-total');
        function updateTotal() {
            let total = 0; 
            document.querySelectorAll('input[name="rincian_jumlah[]"]').forEach(input => {
                total += parseInt(input.value.replace(/[^0-9]/g, '')) || 0;
            }); 
            const formattedTotal = total.toLocaleString('id-ID'); 
            if (totalDanaDisplay) totalDanaDisplay.textContent = formattedTotal; 
            if (jumlahDanaTotalInput) jumlahDanaTotalInput.value = total;
        }
        function formatCurrency(input) {
            let value = input.value.replace(/[^0-9]/g, ''); 
            input.value = value ? parseInt(value).toLocaleString('id-ID') : '';
        }
        function addRow() {
            const isMobile = window.innerWidth < 768; 
            const container = isMobile ? rincianDanaContainerMobile : rincianDanaBodyDesktop; 
            const newRow = document.createElement(isMobile ? 'div' : 'tr');
            if (isMobile) {
                newRow.className = 'bg-white rounded-lg p-4 border border-slate-200 space-y-2'; 
                newRow.innerHTML = `<div><input type="text" name="rincian_deskripsi[]" class="w-full p-2 border border-slate-300 rounded-lg text-sm" placeholder="Deskripsi pengeluaran" required></div><div><input type="text" name="rincian_jumlah[]" class="w-full p-2 border border-slate-300 rounded-lg text-sm jumlah-input" placeholder="Jumlah (Rp)" required></div><button type="button" class="delete-row-btn text-red-500 hover:text-red-700 text-xs font-semibold w-full text-left">HAPUS ITEM</button>`;
            } else {
                newRow.innerHTML = `<td class="px-4 py-2"><input type="text" name="rincian_deskripsi[]" class="w-full p-2 border border-slate-300 rounded-lg text-sm" placeholder="Masukkan deskripsi" required></td><td class="px-4 py-2"><div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span><input type="text" name="rincian_jumlah[]" class="w-full p-2 pl-8 border border-slate-300 rounded-lg text-sm jumlah-input" placeholder="0" required></div></td><td class="px-4 py-2 text-center"><button type="button" class="delete-row-btn text-slate-400 hover:text-red-600 text-lg"><i class="fas fa-trash-alt"></i></button></td>`;
            }
            container.appendChild(newRow); 
            const amountInput = newRow.querySelector('.jumlah-input'); 
            amountInput.addEventListener('input', () => { formatCurrency(amountInput); updateTotal(); }); 
            newRow.querySelector('.delete-row-btn').addEventListener('click', () => { newRow.remove(); updateTotal(); });
        }
        if (tambahBarisBtn) { 
            addRow(); 
            tambahBarisBtn.addEventListener('click', addRow); 
        }

        // Script untuk upload lampiran dengan progress
        const tambahLampiranBtn = document.getElementById('tambah-lampiran-btn');
        const lampiranContainer = document.getElementById('file-pendukung-container');
        const mainForm = document.querySelector('form[action="{{ route('pengajuan_dana.store') }}"]');
        const submitButton = mainForm.querySelector('button[type="submit"]');

        function addLampiranInput() {
            const uniqueId = 'file_' + Date.now() + Math.random().toString(36).substr(2, 9);
            const newFileWrapper = document.createElement('div');
            newFileWrapper.className = 'bg-slate-50 p-3 rounded-lg border border-slate-200';

            const fileInputHtml = `
                <div class="flex items-center gap-3">
                    <div class="flex-grow">
                        <input type="file" name="file_pendukung[]" id="${uniqueId}" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                    </div>
                    <button type="button" class="delete-lampiran-btn flex-shrink-0 text-slate-400 hover:text-red-600 text-lg">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
                <div id="progress-wrapper-${uniqueId}" class="mt-2 hidden">
                    <div class="flex justify-between items-center mb-1">
                        <span id="file-name-${uniqueId}" class="text-xs font-medium text-slate-700 truncate pr-2 w-4/5"></span>
                        <span id="status-text-${uniqueId}" class="text-xs font-medium text-green-700 w-1/5 text-right"></span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5">
                        <div id="progress-bar-${uniqueId}" class="h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            `;

            newFileWrapper.innerHTML = fileInputHtml;
            lampiranContainer.appendChild(newFileWrapper);

            const fileInput = newFileWrapper.querySelector(`#${uniqueId}`);
            const progressWrapper = newFileWrapper.querySelector(`#progress-wrapper-${uniqueId}`);
            const progressBar = newFileWrapper.querySelector(`#progress-bar-${uniqueId}`);
            const fileNameSpan = newFileWrapper.querySelector(`#file-name-${uniqueId}`);
            const statusTextSpan = newFileWrapper.querySelector(`#status-text-${uniqueId}`);

            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    fileNameSpan.textContent = file.name;
                    statusTextSpan.textContent = 'Ready';
                    statusTextSpan.classList.remove('text-blue-700');
                    statusTextSpan.classList.add('text-green-700');
                    progressBar.style.width = '100%';
                    progressBar.classList.remove('bg-blue-600');
                    progressBar.classList.add('bg-green-500');
                    progressWrapper.classList.remove('hidden');
                } else {
                    progressWrapper.classList.add('hidden');
                }
            });

            newFileWrapper.querySelector('.delete-lampiran-btn').addEventListener('click', function() {
                newFileWrapper.remove();
            });
        }
        
        if (tambahLampiranBtn) {
            tambahLampiranBtn.addEventListener('click', addLampiranInput);
            addLampiranInput(); // Tambah satu input file saat halaman dimuat
        }

        // Script untuk mengubah status saat submit
        mainForm.addEventListener('submit', function() {
            document.querySelectorAll('input[type="file"][name="file_pendukung[]"]').forEach(input => {
                if (input.files && input.files.length > 0) {
                    const uniqueId = input.id;
                    const statusText = document.getElementById(`status-text-${uniqueId}`);
                    const progressBar = document.getElementById(`progress-bar-${uniqueId}`);
                    if (statusText) {
                        statusText.textContent = 'Uploading...';
                        statusText.classList.remove('text-green-700');
                        statusText.classList.add('text-blue-700');
                    }
                    if (progressBar) {
                        progressBar.classList.remove('bg-green-500');
                        progressBar.classList.add('bg-blue-600');
                    }
                }
            });

            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengirim...
            `;
            submitButton.classList.add('inline-flex', 'items-center');
        });

        // ======================== KODE BARU UNTUK FUNGSI FULL RESET ========================
        const resetBtn = document.getElementById('reset-form-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                // 1. Reset semua input field standar ke nilai default
                mainForm.reset();

                // 2. Hapus semua baris rincian dana yang ada
                if (rincianDanaBodyDesktop) rincianDanaBodyDesktop.innerHTML = '';
                if (rincianDanaContainerMobile) rincianDanaContainerMobile.innerHTML = '';
                
                // 3. Buat kembali satu baris rincian dana yang kosong
                addRow();

                // 4. Hapus semua input file lampiran yang ada
                if (lampiranContainer) lampiranContainer.innerHTML = '';
                
                // 5. Buat kembali satu input file lampiran yang kosong
                addLampiranInput();

                // 6. Update tampilan total dana menjadi 0
                updateTotal();

                // 7. Trigger event change pada pilihan bank untuk memastikan
                //    input 'bank lainnya' tersembunyi jika perlu.
                document.getElementById('pilih-bank').dispatchEvent(new Event('change'));
            });
        }
    });
    </script>
    @endpush
</x-layout-users>