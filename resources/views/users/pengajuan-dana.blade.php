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
                    
                    <!-- Departemen -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="departemen">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select id="departemen" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Pilih Divisi</option>
                            <option value="hrd">HRD</option>
                            <option value="keuangan">Keuangan</option>
                            <option value="marketing">Marketing</option>
                            <option value="produksi">Produksi</option>
                            <option value="it">IT</option>
                        </select>
                    </div>
                    
                    <!-- Tanggal Pengajuan -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="tanggal-pengajuan">
                            Tanggal Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal-pengajuan" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <!-- Jumlah Dana -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="jumlah-dana">
                            Jumlah Dana (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">Rp.</span>
                            <input 
                                type="number" 
                                id="jumlah-dana" 
                                class="w-full pl-10 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" 
                                placeholder="Masukkan jumlah">
                        </div>
                    </div>
                    
                    <!-- Deskripsi Penggunaan -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2" for="deskripsi">
                            Deskripsi Penggunaan Dana <span class="text-red-500">*</span>
                        </label>
                        <textarea id="deskripsi" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" rows="3" placeholder="Jelaskan secara detail untuk apa dana ini akan digunakan"></textarea>
                    </div>
                    
                    <!-- Upload File Pendukung -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2" for="file-pendukung">
                            Upload File Pendukung
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input type="file" id="file-pendukung" class="hidden">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-cloud-upload-alt text-3xl text-primary mb-2"></i>
                                <p class="text-gray-600 mb-1">Drag & drop file here or</p>
                                <label for="file-pendukung" class="bg-primary hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-lg cursor-pointer transition duration-200">
                                    Pilih File
                                </label>
                                <p class="text-xs text-gray-500 mt-2">Format file: PDF, DOC, JPG, PNG (max. 5MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <button 
                        type="reset"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg border border-gray-300 transition duration-200">
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
            <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pengajuan</h2>

    <!-- Stepper -->
    <div class="flex items-center justify-between">
        <!-- Step 1 -->
        <div class="flex flex-col items-center w-1/5 text-center">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                1
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-800">Diajukan</p>
            <p class="text-xs text-gray-500">05/09/2025</p>
            <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
        </div>

        <div class="flex-1 h-1 bg-blue-600"></div>

        <!-- Step 2 -->
        <div class="flex flex-col items-center w-1/5 text-center">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-300 text-gray-700 font-bold shadow-md">
                2
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Atasan</p>
            <p class="text-xs text-gray-400">-</p>
            <p class="text-xs text-yellow-600 font-medium">⏳ Menunggu</p>
        </div>

        <div class="flex-1 h-1 bg-gray-300"></div>

        <!-- Step 3 -->
        <div class="flex flex-col items-center w-1/5 text-center">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-300 text-gray-700 font-bold shadow-md">
                3
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui HRD</p>
            <p class="text-xs text-gray-400">-</p>
            <p class="text-xs text-gray-500">Belum</p>
        </div>

        <div class="flex-1 h-1 bg-gray-300"></div>

        <!-- Step 4 -->
        <div class="flex flex-col items-center w-1/5 text-center">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-300 text-gray-700 font-bold shadow-md">
                4
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Direktur</p>
            <p class="text-xs text-gray-400">-</p>
            <p class="text-xs text-gray-500">Belum</p>
        </div>

        <div class="flex-1 h-1 bg-gray-300"></div>

        <!-- Step 5 -->
        <div class="flex flex-col items-center w-1/5 text-center">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-300 text-gray-700 font-bold shadow-md">
                5
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-800">Selesai</p>
            <p class="text-xs text-gray-400">-</p>
            <p class="text-xs text-gray-500">Belum</p>
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
                        <td class="px-4 py-2 text-gray-400 italic">Belum ada catatan</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-4 py-2">Direktur</td>
                        <td class="px-4 py-2 text-gray-400 italic">Belum ada catatan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


        </div>
    </div>
</x-layout-users>
