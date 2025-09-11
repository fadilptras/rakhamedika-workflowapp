<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto bg-gray-100 py-12 px-4 md:px-8">
        <div class="max-w-6xl mx-auto">
            {{-- Container Notifikasi --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                
                {{-- Filter Notifikasi --}}
                <div class="flex border-b border-gray-200">
                    <button class="flex-1 py-4 px-6 text-center border-b-2 font-semibold text-blue-600 border-blue-600 transition-colors duration-200">
                        Semua
                    </button>
                    <button class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors duration-200">
                        Belum Dibaca
                    </button>
                </div>
                
                {{-- Daftar Notifikasi --}}
                <div class="divide-y divide-gray-200">
                    
                    {{-- Notifikasi Dana Disetujui (Belum Dibaca) --}}
                    <a href="#" class="block p-5 sm:p-8 hover:bg-gray-50 transition-colors duration-150 relative">
                        <div class="flex items-start space-x-5">
                            <span class="inline-block h-2 w-2 bg-blue-600 rounded-full flex-shrink-0 mt-2 animate-pulse"></span>
                            
                            <div class="flex-shrink-0 text-green-500 mt-0.5">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-gray-900">Dana Disetujui: Pembelian Peralatan IT</h3>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">Permintaan pengajuan dana Anda sebesar **Rp 5.000.000** telah disetujui oleh Manager Keuangan. Cek detailnya sekarang.</p>
                            </div>
                            <span class="text-xs text-gray-500 ml-auto flex-shrink-0 mt-1.5">10 menit lalu</span>
                        </div>
                    </a>

                    {{-- Notifikasi Pengajuan Dokumen (Sudah Dibaca) --}}
                    <a href="#" class="block p-5 sm:p-8 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start space-x-5">
                            <span class="inline-block h-2 w-2 flex-shrink-0"></span>
                            
                            <div class="flex-shrink-0 text-gray-400 mt-0.5">
                                <i class="fas fa-folder text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-medium text-gray-700">Dokumen Selesai: Slip Gaji Bulan Ini</h3>
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">Pengajuan dokumen Anda untuk Slip Gaji telah selesai diproses. Anda dapat mengunduh dokumen sekarang.</p>
                            </div>
                            <span class="text-xs text-gray-500 ml-auto flex-shrink-0 mt-1.5">1 jam lalu</span>
                        </div>
                    </a>
                    
                    {{-- Notifikasi Dana Ditolak (Belum Dibaca) --}}
                    <a href="#" class="block p-5 sm:p-8 hover:bg-gray-50 transition-colors duration-150 relative">
                        <div class="flex items-start space-x-5">
                            <span class="inline-block h-2 w-2 bg-blue-600 rounded-full flex-shrink-0 mt-2 animate-pulse"></span>

                            <div class="flex-shrink-0 text-red-500 mt-0.5">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-gray-900">Dana Ditolak: Rapat Tahunan</h3>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">Pengajuan dana Anda untuk **"Acara Rapat Tahunan"** telah **ditolak**. Silakan hubungi tim Keuangan untuk informasi lebih lanjut.</p>
                            </div>
                            <span class="text-xs text-gray-500 ml-auto flex-shrink-0 mt-1.5">2 hari lalu</span>
                        </div>
                    </a>

                    {{-- Notifikasi Umum (Sudah Dibaca) --}}
                    <a href="#" class="block p-5 sm:p-8 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start space-x-5">
                            <span class="inline-block h-2 w-2 flex-shrink-0"></span>
                            
                            <div class="flex-shrink-0 text-gray-400 mt-0.5">
                                <i class="fas fa-info-circle text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-medium text-gray-700">Pengumuman: Cuti Bersama Idul Adha</h3>
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">Pemberitahuan resmi mengenai jadwal cuti bersama pada perayaan Hari Raya Idul Adha telah diunggah. Silakan cek pengumuman di dashboard Anda.</p>
                            </div>
                            <span class="text-xs text-gray-500 ml-auto flex-shrink-0 mt-1.5">1 minggu lalu</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-layout-users>