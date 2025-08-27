<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto">
            <div class="container mx-auto p-6">
                
                <!-- Pengajuan Dana Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Nama Pengaju -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="nama-pengaju">
                                Nama Pengaju <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama-pengaju" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama lengkap">
                        </div>
                        
                        <!-- Departemen -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="departemen">
                                Departemen <span class="text-red-500">*</span>
                            </label>
                            <select id="departemen" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Departemen</option>
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
                                <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                <input type="number" id="jumlah-dana" class="w-full pl-8 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan jumlah">
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
                        
                        <!-- Pilih Diajukan Ke -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">
                                Diajukan Kepada <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <!-- Checkbox options -->
                                <div class="flex items-center">
                                    <input type="checkbox" id="atasan-langsung" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="atasan-langsung" class="ml-2 text-gray-700">Atasan Langsung</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="hrd" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="hrd" class="ml-2 text-gray-700">HRD</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="keuangan" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="keuangan" class="ml-2 text-gray-700">Keuangan</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="direktur" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="direktur" class="ml-2 text-gray-700">Direktur</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="lainnya" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="lainnya" class="ml-2 text-gray-700">Lainnya</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg shadow transition duration-200">
                            Reset
                        </button>
                        <button class="bg-primary hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg shadow transition duration-200">
                            Ajukan Dana
                        </button>
                    </div>
                </div>
            </div>
        </div>

</x-layout-users>