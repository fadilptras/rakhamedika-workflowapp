<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto">
        <div class="container mx-auto p-6">
            
            <!-- Pengajuan Dana Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Form Pengajuan Dana</h2>

                <!-- Form Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Nama Pengaju -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="nama-pengaju">
                            Nama Pemohon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama-pengaju" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <!-- Divisi -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="divisi">
                            Divisi<span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="divisi" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukan Divisi">
                    </div>

                    <!-- Bank & No Rekening -->
                    <div>
                        <!-- Container untuk Bank & No Rekening dengan grid 2 kolom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Kolom Bank -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2" for="pilih-bank">
                                    Pilih Bank <span class="text-red-500">*</span>
                                </label>
                                <select id="pilih-bank" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="" disabled selected>Pilih salah satu</option>
                                    <option value="BCA">BCA</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            
                            <!-- Kolom Nomor Rekening -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2" for="no-rekening">
                                    Nomor Rekening <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="no-rekening" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nomor rekening">
                            </div>
                        </div>

                        <!-- Input untuk "Bank Lainnya" (tersembunyi secara default) -->
                        <div id="bank-lainnya-container" class="mt-4 hidden">
                            <label class="block text-gray-700 font-medium mb-2" for="input-bank-lainnya">
                                Nama Bank Lainnya <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="input-bank-lainnya" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama bank">
                        </div>
                    </div>
                    
                    <!-- Tanggal Pengajuan -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="tanggal-pengajuan">
                            Tanggal Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal-pengajuan" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- Judul Pengajuan (tambahan) -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="judul-pengajuan">
                            Judul Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul-pengajuan" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Contoh: Pembelian Perlengkapan Kantor">
                    </div>
                    
                    <!-- Input Dummy untuk menjaga layout -->
                    <div class="hidden md:block"></div>
                </div>

                <!-- Bagian Rincian Penggunaan Dana -->
                <div class="md:col-span-2 mt-6">
                    <label class="block text-gray-700 font-medium mb-2">
                        Rincian Penggunaan Dana <span class="text-red-500">*</span>
                    </label>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 w-2/3">Deskripsi Pengeluaran</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 w-1/3">Dana yang Dibutuhkan (Rp)</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="rincian-dana-body">
                                <!-- Baris-baris rincian akan ditambahkan di sini -->
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 text-right">Total:</td>
                                    <td class="px-4 py-2 font-bold text-gray-800">
                                        Rp <span id="total-dana">0</span>
                                        <input type="hidden" id="jumlah-dana-total" name="jumlah-dana-total">
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button id="tambah-baris-btn" type="button" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-4 rounded-lg transition duration-200">
                        Tambah Baris
                    </button>
                </div>
                
                <!-- Upload File Pendukung -->
                <div class="md:col-span-2 mt-6">
                    <label class="block text-gray-700 font-medium mb-2" for="file-pendukung">
                        Upload File Pendukung
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <input type="file" id="file-pendukung" class="hidden">
                        <div class="flex flex-col items-center justify-center py-4">
                            <i class="fas fa-cloud-upload-alt text-3xl text-blue-600 mb-2"></i>
                            <p class="text-gray-600 mb-1">Drag & drop file here or</p>
                            <label for="file-pendukung" class="bg-blue-600 hover:bg-blue-800 text-white font-medium text-xs py-0.5 px-1 rounded-lg cursor-pointer transition duration-200">
                                Pilih File
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Format file: PDF, DOC, JPG, PNG (max. 5MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 mt-8">
                    <button 
                        type="reset"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-4 rounded-lg border border-gray-300 transition duration-200">
                        Reset
                    </button>
                    <button 
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                        Ajukan Dana
                    </button>
                </div>
            </div>

            <!-- Progress Tahapan ACC -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pengajuan</h2>
                <!-- Stepper -->
                <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center w-full md:w-1/5 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                            1
                        </div>
                        <p class="mt-2 text-sm font-semibold text-gray-800">Diajukan</p>
                        <p class="text-xs text-gray-500">05/09/2025</p>
                        <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                    </div>

                    <div class="h-4 w-1 md:h-1 md:w-full bg-blue-600"></div>

                    <!-- Step 2 -->
                    <div class="flex flex-col items-center w-full md:w-1/5 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                            2
                        </div>
                        <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Atasan</p>
                        <p class="text-xs text-gray-500">06/09/2025</p>
                        <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                    </div>

                    <div class="h-4 w-1 md:h-1 md:w-full bg-blue-600"></div>

                    <!-- Step 3 -->
                    <div class="flex flex-col items-center w-full md:w-1/5 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                            3
                        </div>
                        <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui HRD</p>
                        <p class="text-xs text-gray-500">07/09/2025</p>
                        <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                    </div>

                    <div class="h-4 w-1 md:h-1 md:w-full bg-blue-600"></div>

                    <!-- Step 4 -->
                    <div class="flex flex-col items-center w-full md:w-1/5 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                            4
                        </div>
                        <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Direktur</p>
                        <p class="text-xs text-gray-500">08/09/2025</p>
                        <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                    </div>

                    <div class="h-4 w-1 md:h-1 md:w-full bg-blue-600"></div>

                    <!-- Step 5 -->
                    <div class="flex flex-col items-center w-full md:w-1/5 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                            5
                        </div>
                        <p class="mt-2 text-sm font-semibold text-gray-800">Selesai</p>
                        <p class="text-xs text-gray-500">09/09/2025</p>
                        <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Catatan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Dari</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t">
                                    <td class="px-4 py-2">Atasan</td>
                                    <td class="px-4 py-2">Mohon detail penggunaan dana lebih rinci.</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2">HRD</td>
                                    <td class="px-4 py-2">Dokumen pendukung sudah lengkap.</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2">Direktur</td>
                                    <td class="px-4 py-2">Pengajuan dana telah disetujui.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bagian Bukti Transfer & Invoice (muncul setelah status Selesai) -->
            @php
                // Logika ini harus disesuaikan dengan data status pengajuan yang sebenarnya
                // Anggap saja $statusSelesai bernilai true jika semua tahapan sudah di-ACC
                $statusSelesai = true; 
            @endphp
            @if($statusSelesai)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Bukti Transfer & Invoice</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bukti Transfer -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Bukti Transfer</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            <p class="text-gray-500 italic">Transfer dana sudah dilakukan pada tanggal 09/09/2025.</p>
                            <a href="#" class="mt-2 inline-block text-blue-600 hover:underline">Lihat Bukti Transfer</a>
                            
                        </div>
                    </div>
                    <!-- Invoice -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Invoice</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            <p class="text-gray-500 italic">Invoice telah dibuat dan siap diunduh.</p>
                            <a href="#" class="mt-2 inline-block text-blue-600 hover:underline">Lihat Invoice</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Script untuk menampilkan input "Bank Lainnya" dan fungsionalitas tabel -->
    <script>
        document.getElementById('pilih-bank').addEventListener('change', function() {
            const bankContainer = document.getElementById('bank-lainnya-container');
            if (this.value === 'other') {
                bankContainer.classList.remove('hidden');
            } else {
                bankContainer.classList.add('hidden');
            }
        });

        // Fungsionalitas tabel dinamis
        const tambahBarisBtn = document.getElementById('tambah-baris-btn');
        const rincianDanaBody = document.getElementById('rincian-dana-body');
        const totalDanaSpan = document.getElementById('total-dana');
        const jumlahDanaTotalInput = document.getElementById('jumlah-dana-total');

        function updateTotal() {
            let total = 0;
            const jumlahInputs = document.querySelectorAll('.jumlah-input');
            jumlahInputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            totalDanaSpan.textContent = total.toLocaleString('id-ID');
            jumlahDanaTotalInput.value = total; // Simpan nilai total di hidden input
        }

        function addRow() {
            const newRow = document.createElement('tr');
            newRow.classList.add('border-t');
            newRow.innerHTML = `
                <td class="px-4 py-2">
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan deskripsi pengeluaran">
                </td>
                <td class="px-4 py-2 relative">
                    <input type="number" class="w-full p-2 pl-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary jumlah-input" placeholder="0">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                </td>
            `;
            rincianDanaBody.appendChild(newRow);
            newRow.querySelector('.jumlah-input').addEventListener('input', updateTotal);
            newRow.querySelector('button').addEventListener('click', function() {
                newRow.remove();
                updateTotal();
            });
        }

        // Tambahkan satu baris awal saat halaman dimuat
        addRow();

        tambahBarisBtn.addEventListener('click', addRow);
    </script>
</x-layout-users>
