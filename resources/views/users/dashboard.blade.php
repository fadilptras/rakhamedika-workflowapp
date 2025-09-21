<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    <div class="flex flex-col h-full bg-gray-50">
        <main class="flex-1 overflow-y-auto min-h-screen">
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                {{-- Kolom Kiri - Informasi Pengguna & Navigasi --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Sambutan Pengguna --}}
                    <div class="bg-blue-600 text-white p-6 rounded-2xl shadow-md">
                        <h2 class="text-2xl font-bold">Welcome Back, {{ Auth::user()->name }}!</h2>
                        <p class="text-sm mt-1 text-blue-100">Semoga harimu produktif.</p>
                    </div>

                    {{-- Profil Pengguna - Dibuat lengkap --}}
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200">
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 aspect-square overflow-hidden rounded-full border-4 border-blue-200 shadow-sm">
                                <img class="w-full h-full object-cover"
                                    src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=random&color=fff&size=128' }}"
                                    alt="Foto Profil">
                            </div>
                        </div>
                        <div class="text-left space-y-3 mt-4">
                            <div>
                                <label class="text-xs text-gray-600 font-semibold">Nama</label>
                                <p class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-semibold">Divisi</label>
                                <p class="font-bold text-base text-gray-800">{{ Auth::user()->divisi ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-semibold">Posisi</label>
                                <p class="font-bold text-base text-gray-800">{{ Auth::user()->jabatan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-semibold">Email</label>
                                <p class="font-bold text-base text-gray-800">{{ Auth::user()->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-semibold">Tanggal Bergabung</label>
                                <p class="font-bold text-base text-gray-800">{{ Auth::user()->tanggal_bergabung ? Auth::user()->tanggal_bergabung->format('d F Y') : '-' }}</p>
                                {{-- {{ Auth::user()->tanggal_bergabung ? \Carbon\Carbon::parse(Auth::user()->tanggal_bergabung)->format('d F Y') : '-' }}
</p --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Tengah & Kanan - Konten Utama --}}
                <div class="lg:col-span-2 xl:col-span-3 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 justify-items-center">
                        {{-- grid grid-cols-1 sm:grid-cols-3 gap-4 justify-items-center items-center --}}

                        {{-- Kartu Absensi --}}
                        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-8 text-xl">Absensi</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <a href="{{ route('absen') }}" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-xl text-center flex flex-col items-center justify-center aspect-square transition border border-blue-200">
                                    <i class="fas fa-fingerprint text-2xl text-blue-600 mb-2"></i>
                                    <span class="font-semibold text-sm text-gray-700">Absen</span>
                                </a>
                                <a href="{{ route('cuti') }}" class="bg-green-50 hover:bg-green-100 p-4 rounded-xl text-center flex flex-col items-center justify-center aspect-square transition border border-green-200">
                                    <i class="fas fa-calendar-alt text-2xl text-green-600 mb-2"></i>
                                    <span class="font-semibold text-sm text-gray-700">Pengajuan Cuti</span>
                                </a>
                                <a href="{{ route('rekap_absen.index') }}" class="bg-yellow-50 hover:bg-yellow-100 p-4 rounded-xl text-center flex flex-col items-center justify-center aspect-square transition border border-yellow-200">
                                    <i class="fas fa-history text-2xl text-yellow-600 mb-2"></i>
                                    <span class="font-semibold text-sm text-gray-700">Rekap Absen</span>
                                </a>
                            </div>
                        </div>

                        {{-- Kartu Notifikasi - Sekarang dengan background gelap --}}
                        <div class="bg-gray-900 text-white p-6 rounded-2xl shadow-md border border-gray-700">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-white text-xl">Notifikasi</h3>
                                <a href="{{ route('notifikasi.index') }}" class="flex items-center space-x-2 text-gray-300 hover:text-blue-400 transition-colors duration-200">
                                    <span class="text-sm font-semibold">Lihat Semua</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    @php
                                        $unreadNotifications = Auth::user()->unreadNotifications->count();
                                    @endphp
                                    @if ($unreadNotifications > 0)
                                        <span class="absolute top-0 right-0 inline-flex items-center justify-center h-4 w-4 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                            {{ $unreadNotifications }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                            <div class="space-y-3">
                                @forelse(Auth::user()->notifications->take(2) as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-3 rounded-lg {{ $notification->read_at ? 'bg-gray-800' : 'bg-blue-800' }} hover:bg-gray-700 transition-colors duration-150">
                                    <div class="flex items-start">
                                        <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }} text-xl text-white mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-100">{{ $notification->data['title'] ?? 'Notifikasi Baru' }}</p>
                                            <p class="text-xs text-gray-300">{{ $notification->data['message'] ?? 'Tidak ada detail' }}</p>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <p class="text-center text-gray-400 py-4 text-sm">Tidak ada notifikasi baru.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    {{-- Kartu Kalender --}}
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-4 text-xl">Kalender</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Kalender Bulanan</h4>
                                <iframe 
                                    src="https://calendar.google.com/calendar/embed?src=b14c3781c2e07b17fed844b9d3b2553d6cd63cf3664a8a32fe68cdc30a02a278%40group.calendar.google.com&ctz=Asia%2FJakarta" 
                                    style="border: 0; border-radius: 0.5rem;" width="100%" height="300px" 
                                    frameborder="0" 
                                    scrolling="no">
                                </iframe>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Agenda Mendatang</h4>
                                <iframe 
                                    src="https://calendar.google.com/calendar/embed?height=300&wkst=1&ctz=Asia%2FJakarta&showPrint=0&mode=AGENDA&showTabs=0&src=YjE0YzM3ODFjMmUwN2IxN2ZlZDg0NGI5ZDNiMjU1M2Q2Y2Q2M2NmMzY2NGE4YTMyZmU2OGNkYzMwYTAyYTI3OEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&color=%238e24aa" 
                                    style="border:solid 1px #e5e7eb; border-radius: 0.5rem;" width="100%" height="300" 
                                    frameborder="0" 
                                    scrolling="no">
                                </iframe>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</x-layout-users>