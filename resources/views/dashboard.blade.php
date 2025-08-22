<x-layout-dash>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="flex flex-col h-full">

        <main class="flex-1 overflow-y-auto p-6">
    
    <div class="grid grid-cols-1 lg:grid-cols-8 gap-6">

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
                    <a href="#" class="bg-gray-50 hover:bg-gray-100 p-2 rounded-lg text-center flex flex-col items-center justify-center aspect-square transition">
                        <i class="fas fa-history text-2xl text-yellow-500 mb-2"></i>
                        <span class="font-semibold text-sm text-gray-700">Rekap Absen</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 lg:row-span-2">
             <div class="bg-white p-4 rounded-lg shadow-md h-full">
                <h3 class="font-bold text-gray-800 mb-4">Kalender</h3>
                <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg min-h-[300px]">
                    <p class="text-gray-500">Kalender akan ditempatkan di sini.</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="bg-white p-4 rounded-lg shadow-md h-full">
                <h3 class="font-bold text-gray-800 mb-4">Notifikasi</h3>
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
</x-layout-dash>