<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    <div class="flex flex-col h-full">
        <main class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-1 lg:grid-cols-8 gap-6">

                {{-- Kolom Profil Pengguna --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-6 rounded-lg shadow-md h-full">
                        <div class="flex flex-col items-center">
                            <img class="w-full h-32 object-cover mb-1 shadow-md"
                                src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=random&size=128' }}"
                                alt="Foto Profil">
                        </div>
                        <div class="text-left space-y-2 mt-2">
                            <div>
                                <label class="text-xs text-gray-500">Nama</label>
                                <p class="font-semibold text-base text-gray-800">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Divisi</label>
                                <p class="font-semibold text-base text-gray-800">{{ Auth::user()->divisi ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Posisi</label>
                                <p class="font-semibold text-base text-gray-800">{{ Auth::user()->jabatan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Email</label>
                                <p class="font-semibold text-base text-gray-800">{{ Auth::user()->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Tanggal Bergabung</label>
                                <p class="font-semibold text-base text-gray-800">{{ Auth::user()->tanggal_bergabung ? Auth::user()->tanggal_bergabung->format('d F Y') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Absensi --}}
                <div class="lg:col-span-3">
                    <div class="bg-white p-4 rounded-lg shadow-md h-full">
                        <h3 class="font-bold text-gray-800 mb-4">Absensi</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('absen') }}" class="bg-gray-50 hover:bg-gray-100 p-2 rounded-lg text-center flex flex-col items-center justify-center aspect-square transition">
                                <i class="fas fa-fingerprint text-2xl text-blue-500 mb-2"></i>
                                <span class="font-semibold text-sm text-gray-700">Absen</span>
                            </a>
                            <a href="{{ route('cuti') }}" class="bg-gray-50 hover:bg-gray-100 p-2 rounded-lg text-center flex flex-col items-center justify-center aspect-square transition">
                                <i class="fas fa-calendar-alt text-2xl text-green-500 mb-2"></i>
                                <span class="font-semibold text-sm text-gray-700">Pengajuan Cuti</span>
                            </a>
                            <a href="{{ route('rekap_absen.index') }}" class="bg-gray-50 hover:bg-gray-100 p-2 rounded-lg text-center flex flex-col items-center justify-center aspect-square transition">
                                <i class="fas fa-history text-2xl text-yellow-500 mb-2"></i>
                                <span class="font-semibold text-sm text-gray-700">Rekap Absen</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kalender --}}
                <div class="lg:col-span-3 lg:row-span-2">
                    <div class="bg-white p-4 rounded-lg shadow-md h-full">
                        <h3 class="font-bold text-gray-800 mb-4">Kalender</h3>

                        <iframe 
                            src="https://calendar.google.com/calendar/embed?src=b14c3781c2e07b17fed844b9d3b2553d6cd63cf3664a8a32fe68cdc30a02a278%40group.calendar.google.com&ctz=Asia%2FJakarta" 
                            style="border: 0" width="100%" height="300px" 
                            frameborder="0" 
                            scrolling="no">
                        </iframe>
                        
                        <h4 class="font-bold text-gray-700 mt-6 mb-4">Agenda Mendatang</h4>

                        <iframe 
                            src="https://calendar.google.com/calendar/embed?height=300&wkst=1&ctz=Asia%2FJakarta&showPrint=0&mode=AGENDA&showTabs=0&src=YjE0YzM3ODFjMmUwN2IxN2ZlZDg0NGI5ZDNiMjU1M2Q2Y2Q2M2NmMzY2NGE4YTMyZmU2OGNkYzMwYTAyYTI3OEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&color=%238e24aa" 
                            style="border:solid 1px #777" width="100%" height="300" 
                            frameborder="0" 
                            scrolling="no">
                        </iframe>
                    </div>
                </div>

<div class="lg:col-span-5">
    <div class="bg-white p-4 rounded-lg shadow-md h-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">Notifikasi</h3>
            <a href="{{ route('email') }}" class="flex items-center space-x-2 text-gray-500 hover:text-blue-600 transition-colors duration-200">
                <span class="text-sm font-semibold">Lihat Semua</span>
                <span class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                    @php
                        $unreadNotifications = 3; // Contoh: Ambil dari database
                    @endphp
                    @if ($unreadNotifications > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center h-4 w-4 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            {{ $unreadNotifications }}
                        </span>
                    @endif
                </span>
            </a>
        </div>
        <div class="space-y-3">
            <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Pengajuan Dana Disetujui</p>
                    <p class="text-xs text-gray-500">Pengajuan dana untuk pembelian ATK telah disetujui oleh manajer.</p>
                </div>
            </div>
            <div class="flex items-start p-3 bg-yellow-50 rounded-lg">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-semibold text-sm text-gray-800">Peringatan: Laporan Bulanan</p>
                    <p class="text-xs text-gray-500">Jangan lupa untuk menyerahkan laporan bulanan sebelum akhir pekan ini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

            </div>
        </main>
    </div>
</x-layout-users>