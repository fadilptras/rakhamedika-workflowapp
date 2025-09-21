<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-slate-100 min-h-screen">
        <div class="container mx-auto p-0 md:p-0">
            
            <div class="space-y-8">
                {{-- KARTU FORM PENGAJUAN --}}
                <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 md:p-8 bg-[#333446] text-white">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-file-signature text-3xl opacity-80"></i>
                            <div>
                                <h2 class="text-xl font-bold">Formulir Pengajuan Dokumen</h2>
                                <p class="text-sm text-slate-300">Isi detail di bawah untuk meminta dokumen resmi.</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Ganti action ke route yang sesuai jika ada --}}
                    <form action="#" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            {{-- Nama Pemohon (Otomatis) --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Pemohon</label>
                                <input type="text" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ Auth::user()->name }}" readonly>
                            </div>

                            {{-- Departemen (Otomatis) --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Departemen</label>
                                <input type="text" class="w-full p-3 bg-slate-100 border-transparent rounded-lg" value="{{ Auth::user()->divisi ?? 'Belum Diatur' }}" readonly>
                            </div>

                            {{-- Jenis Dokumen --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1" for="jenis-dokumen">
                                    Jenis Dokumen <span class="text-red-500">*</span>
                                </label>
                                <select id="jenis-dokumen" name="jenis_dokumen" class="w-full p-3 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:shadow-inner" required>
                                    <option value="">Pilih Dokumen</option>
                                    <option value="slip-gaji">Slip Gaji</option>
                                    <option value="surat-kerja">Surat Keterangan Kerja</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            {{-- Deskripsi Keterangan --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1" for="deskripsi">
                                    Keterangan Tambahan
                                </label>
                                <textarea id="deskripsi" name="deskripsi" class="w-full p-3 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:shadow-inner" rows="3" placeholder="Contoh: Untuk keperluan pengajuan KPR, mohon sertakan slip gaji 3 bulan terakhir."></textarea>
                            </div>

                            {{-- Upload Dokumen Pendukung --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1" for="file-pendukung">
                                    Upload Dokumen Pendukung (Opsional)
                                </label>
                                <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center bg-slate-50 hover:bg-slate-100 transition cursor-pointer" onclick="document.getElementById('file-pendukung').click()">
                                    <input type="file" id="file-pendukung" name="file_pendukung" class="hidden">
                                    <div id="file-upload-ui">
                                        <div class="w-12 h-12 mx-auto bg-slate-200 rounded-full flex items-center justify-center shadow-inner"><i class="fas fa-cloud-upload-alt text-2xl text-slate-500"></i></div>
                                        <p class="text-slate-600 mt-3 font-semibold">Klik untuk <span class="text-indigo-600 font-bold">pilih file</span></p><p class="text-xs text-slate-500 mt-1">PDF, DOC, JPG, PNG (max. 5MB)</p>
                                    </div>
                                    <div id="file-name-display" class="hidden font-semibold text-slate-700"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4 pt-6 mt-6 border-t border-slate-200">
                            <button type="reset" class="bg-white hover:bg-slate-100 text-slate-700 font-semibold py-2 px-6 rounded-lg border border-slate-300 transition">Reset</button>
                            <button type="submit" class="bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">Ajukan Dokumen</button>
                        </div>
                    </form>
                </div>

                {{-- KARTU HASIL DOKUMEN (setelah disetujui) --}}
                @php
                    // Logika ini harus disesuaikan dengan data status pengajuan yang sebenarnya
                    // Anggap saja $statusSelesai bernilai true jika semua tahapan sudah di-ACC
                    $statusSelesai = true; 
                @endphp
                @if($statusSelesai)
                <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-check-circle text-3xl text-green-500"></i>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Dokumen Telah Terbit</h2>
                                <p class="text-sm text-slate-500">Dokumen yang Anda ajukan telah disetujui dan siap diunduh.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 md:p-8 border-t border-slate-200 bg-slate-50">
                        <div class="flex flex-col sm:flex-row items-center justify-between">
                            <div class="mb-4 sm:mb-0">
                                <p class="font-bold text-slate-700">Surat Keterangan Kerja</p>
                                <p class="text-sm text-slate-500">Disetujui pada: 21 September 2025</p>
                            </div>
                            <a href="#" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-5 rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                Unduh Dokumen
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
    
    @push('scripts')
    {{-- Script untuk menampilkan nama file setelah dipilih --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('file-pendukung');
        const fileUploadUI = document.getElementById('file-upload-ui');
        const fileNameDisplay = document.getElementById('file-name-display');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    if(fileUploadUI) fileUploadUI.classList.add('hidden');
                    if(fileNameDisplay) {
                        fileNameDisplay.innerHTML = `<i class="fas fa-check-circle text-green-500 mr-2"></i> ${this.files[0].name}`;
                        fileNameDisplay.classList.remove('hidden');
                    }
                }
            });
        }
    });
    </script>
    @endpush
</x-layout-users>