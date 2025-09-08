<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto">
        <div class="container mx-auto p-6">
            
            <!-- Pengajuan Dokumen Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Form Pengajuan Dokumen</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Nama Pemohon -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="nama-pemohon">
                            Nama Pemohon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama-pemohon" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama lengkap">
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

                    <!-- Jenis Dokumen -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2" for="jenis-dokumen">
                            Jenis Dokumen <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis-dokumen" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Pilih Dokumen</option>
                            <option value="slip-gaji">Slip Gaji</option>
                            <option value="surat-kerja">Surat Keterangan Kerja</option>
                            <option value="surat-cuti">Surat Cuti</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Deskripsi Keterangan -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2" for="deskripsi">
                            Keterangan Tambahan
                        </label>
                        <textarea id="deskripsi" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" rows="3" placeholder="Tuliskan alasan atau detail pengajuan dokumen"></textarea>
                    </div>

                    <!-- Upload Dokumen Pendukung -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2" for="file-pendukung">
                            Upload Dokumen Pendukung (Opsional)
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
                        Ajukan Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layout-users>
