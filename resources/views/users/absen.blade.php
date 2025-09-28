<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-gray-50 p-0 md:p-8 min-h-screen">
        <div class="max-w-6xl mx-auto">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif
            
            @if ($isWeekend)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-4" role="alert">
                    <p class="font-bold">Akhir Pekan</p>
                    <p>Hari ini adalah hari libur</p>
                </div>
            @endif

            @if ($absensiHariIni)
                {{-- TAMPILAN JIKA PENGGUNA SUDAH ABSEN MASUK --}}
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="w-full lg:w-2/3 bg-white p-6 md:p-8 rounded-xl shadow-sm">
                        <div class="flex flex-col md:flex-row items-start justify-between px-0">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Absensi Hari Ini</h2>
                                <p class="text-gray-600 mt-1">
                                    Status Kehadiran Anda : <span class="font-semibold capitalize">{{ $absensiHariIni->status }}</span>
                                </p>
                                <p class="text-gray-600 mt-0">
                                    Keterangan : <span class="font-semibold capitalize">{{ $absensiHariIni->keterangan }}</span>
                                </p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="mt-4 md:mt-0 text-blue-600 hover:underline font-semibold">Kembali ke Dashboard</a>
                        </div>
                        <div class="mt-6 border-t pt-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                                    <div class="flex items-center text-emerald-800 mb-2">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        <p class="font-semibold">Absen Masuk</p>
                                    </div>
                                    <p class="text-3xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }} <span class="text-lg font-medium">WIB</span></p>
                                    @if($absensiHariIni->latitude && $absensiHariIni->longitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $absensiHariIni->latitude }},{{ $absensiHariIni->longitude }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Lihat Lokasi
                                        </a>
                                    @endif
                                </div>
                                <div class="bg-rose-50 p-4 rounded-lg border border-rose-200 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center text-rose-800 mb-2">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            <p class="font-semibold">Absen Keluar</p>
                                        </div>
                                        @if ($absensiHariIni->jam_keluar)
                                            <p class="text-3xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }} <span class="text-lg font-medium">WIB</span></p>
                                            @if($absensiHariIni->latitude_keluar && $absensiHariIni->longitude_keluar)
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $absensiHariIni->latitude_keluar }},{{ $absensiHariIni->longitude_keluar }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>Lihat Lokasi
                                                </a>
                                            @endif
                                        @else
                                            <p class="text-3xl font-bold text-gray-400">--:--</p>
                                        @endif
                                    </div>
                                    @if (is_null($absensiHariIni->jam_keluar) && $absensiHariIni->status == 'hadir')
                                        <button type="button" id="btn-absen-keluar" class="w-full mt-3 bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition text-sm">
                                            Absen Keluar Sekarang
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @if ($absensiHariIni->jam_keluar && $absensiHariIni->status == 'hadir')
                                @if (is_null($lemburHariIni))
                                    <button type="button" id="btn-absen-lembur" class="w-full mt-6 bg-purple-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-purple-700 transition">
                                        Absen Lembur Sekarang
                                    </button>
                                @elseif (is_null($lemburHariIni->jam_keluar_lembur))
                                    <button type="button" id="btn-absen-keluar-lembur" class="w-full mt-6 bg-red-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-red-700 transition">
                                        Absen Keluar Lembur Sekarang
                                    </button>
                                @else
                                     <div class="mt-6 p-4 rounded-lg bg-green-100 text-green-700 font-semibold text-center">
                                         Absensi Lembur Hari Ini Selesai.
                                         <p class="text-xs font-normal">Waktu Lembur: {{ \Carbon\Carbon::parse($lemburHariIni->jam_masuk_lembur)->format('H:i') }} - {{ \Carbon\Carbon::parse($lemburHariIni->jam_keluar_lembur)->format('H:i') }}</p>
                                     </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="w-full lg:w-1/3 space-y-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Rekap Bulan Ini</h2>
                            <p class="text-center text-gray-500 text-sm mb-6">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="bg-green-100 p-4 rounded-lg flex items-center justify-between">
                                    <p class="font-semibold text-sm text-green-700">Hadir</p>
                                    <div class="text-base font-bold text-green-700">{{ $rekapAbsen['hadir'] }}</div>
                                </div>
                                <div class="bg-red-100 p-4 rounded-lg flex items-center justify-between">
                                    <p class="font-semibold text-sm text-red-700">Sakit</p>
                                    <div class="text-base font-bold text-red-700">{{ $rekapAbsen['sakit'] }}</div>
                                </div>
                                <div class="bg-yellow-100 p-4 rounded-lg flex items-center justify-between">
                                    <p class="font-semibold text-sm text-yellow-700">Izin</p>
                                    <div class="text-base font-bold text-yellow-700">{{ $rekapAbsen['izin'] }}</div>
                                </div>
                                <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-between">
                                    <p class="font-semibold text-sm text-blue-700">Cuti</p>
                                    <div class="text-base font-bold text-blue-700">{{ $rekapAbsen['cuti'] }}</div>
                                </div>
                                <div class="bg-gray-100 p-4 rounded-lg flex items-center justify-between">
                                    <p class="font-semibold text-sm text-gray-700">Tidak Hadir</p>
                                    <div class="text-base font-bold text-gray-700">{{ $rekapAbsen['tidak hadir'] }}</div>
                                </div>
                                <div class="bg-orange-100 p-4 rounded-lg text-center">
                                    <p class="font-semibold text-xs text-orange-700">Terlambat Hadir</p>
                                    <div class="text-base font-bold text-orange-700 mt-1">{{ $rekapAbsen['terlambat'] }}</div>
                                </div>
                            </div>
                        </div>
                        @if(isset($daftarRekan) && count($daftarRekan) > 0)
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h2 class="text-xl font-bold text-gray-800 text-center mb-4">
                                Absensi Tim 
                            </h2>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                @foreach($daftarRekan as $rekan)
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <img src="{{ $rekan->user->profile_picture ? asset('storage/' . $rekan->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($rekan->user->name ?? 'U').'&background=random&color=fff&size=32' }}" alt="{{ $rekan->user->name ?? '' }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                        <p class="font-semibold text-gray-700 text-sm">{{ $rekan->user->name }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-semibold leading-tight rounded-full capitalize
                                        @switch($rekan->status)
                                            @case('hadir') bg-green-100 text-green-800 @break
                                            @case('sakit') bg-red-100 text-red-800 @break
                                            @case('izin') bg-yellow-100 text-yellow-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ str_replace('_', ' ', $rekan->status) }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            @else
                @if (isset($unfinishedAbsensi) && $unfinishedAbsensi)
                    {{-- TAMPILAN JIKA ADA ABSEN KELUAR HARI SEBELUMNYA YANG BELUM SELESAI --}}
                    <div class="flex flex-col lg:flex-row gap-8">
                        <div class="w-full lg:w-2/3 bg-white p-6 md:p-8 rounded-xl shadow-sm">
                            <h2 class="text-2xl font-bold text-gray-800">Selesaikan Absen Keluar Sebelumnya</h2>
                            <p class="text-gray-600 mt-2">
                                Anda belum melakukan absen keluar untuk tanggal <strong>{{ \Carbon\Carbon::parse($unfinishedAbsensi->tanggal)->translatedFormat('l, j F Y') }}</strong>.
                                Silakan lengkapi data absensi Anda untuk melanjutkan.
                            </p>
                            <div class="mt-6 border-t pt-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                                        <div class="flex items-center text-emerald-800 mb-2">
                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                            <p class="font-semibold">Absen Masuk (Hari Sebelumnya)</p>
                                        </div>
                                        <p class="text-3xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($unfinishedAbsensi->jam_masuk)->format('H:i') }} <span class="text-lg font-medium">WIB</span></p>
                                    </div>
                                    <div class="bg-rose-50 p-4 rounded-lg border border-rose-200 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center text-rose-800 mb-2">
                                                <i class="fas fa-sign-out-alt mr-2"></i>
                                                <p class="font-semibold">Absen Keluar (Sekarang)</p>
                                            </div>
                                            <p class="text-3xl font-bold text-gray-400">--:--</p>
                                        </div>
                                        <button type="button" id="btn-absen-keluar-unfinished" data-id="{{ $unfinishedAbsensi->id }}" class="w-full mt-3 bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition text-sm">
                                            Absen Keluar Sekarang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full lg:w-1/3 space-y-8">
                            <div class="bg-white p-6 rounded-xl shadow-sm">
                                <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Rekap Bulan Ini</h2>
                                <p class="text-center text-gray-500 text-sm mb-6">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="bg-green-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-green-700">Hadir</p>
                                        <div class="text-base font-bold text-green-700">{{ $rekapAbsen['hadir'] }}</div>
                                    </div>
                                    <div class="bg-red-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-red-700">Sakit</p>
                                        <div class="text-base font-bold text-red-700">{{ $rekapAbsen['sakit'] }}</div>
                                    </div>
                                    <div class="bg-yellow-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-yellow-700">Izin</p>
                                        <div class="text-base font-bold text-yellow-700">{{ $rekapAbsen['izin'] }}</div>
                                    </div>
                                    <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-blue-700">Cuti</p>
                                        <div class="text-base font-bold text-blue-700">{{ $rekapAbsen['cuti'] }}</div>
                                    </div>
                                    <div class="bg-gray-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-gray-700">Tidak Hadir</p>
                                        <div class="text-base font-bold text-gray-700">{{ $rekapAbsen['tidak hadir'] }}</div>
                                    </div>
                                    <div class="bg-orange-100 p-4 rounded-lg text-center">
                                        <p class="font-semibold text-xs text-orange-700">Terlambat Hadir</p>
                                        <div class="text-base font-bold text-orange-700 mt-1">{{ $rekapAbsen['terlambat'] }}</div>
                                    </div>
                                </div>
                            </div>
                            @if(isset($daftarRekan) && count($daftarRekan) > 0)
                            <div class="bg-white p-6 rounded-xl shadow-sm">
                                <h2 class="text-xl font-bold text-gray-800 text-center mb-4">
                                    Absensi Tim 
                                </h2>
                                <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                    @foreach($daftarRekan as $rekan)
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <img src="{{ $rekan->user->profile_picture ? asset('storage/' . $rekan->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($rekan->user->name ?? 'U').'&background=random&color=fff&size=32' }}" alt="{{ $rekan->user->name ?? '' }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                            <p class="font-semibold text-gray-700 text-sm">{{ $rekan->user->name }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold leading-tight rounded-full capitalize
                                            @switch($rekan->status)
                                                @case('hadir') bg-green-100 text-green-800 @break
                                                @case('sakit') bg-red-100 text-red-800 @break
                                                @case('izin') bg-yellow-100 text-yellow-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ str_replace('_', ' ', $rekan->status) }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div id="modal-absen-keluar-unfinished" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
                            <form action="{{ route('absen.keluar', $unfinishedAbsensi->id) }}" method="POST" enctype="multipart/form-data" id="form-absen-keluar-unfinished">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="latitude_keluar" id="latitude-keluar-unfinished">
                                <input type="hidden" name="longitude_keluar" id="longitude-keluar-unfinished">
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar</h3>
                                    <p class="text-gray-500 mt-1">Ambil foto selfie untuk konfirmasi absen keluar hari sebelumnya.</p>
                                    <div class="mt-6">
                                        <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Keluar <span class="text-red-500">*</span></label>
                                        <div id="camera-container-keluar-unfinished" class="relative aspect-square rounded-lg overflow-hidden bg-gray-900">
                                            <video id="video-keluar-unfinished" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay></video>
                                            <canvas id="canvas-keluar-unfinished" class="hidden"></canvas>
                                            <div id="snap-ui-keluar-unfinished" class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                                <button type="button" id="snap-keluar-unfinished" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                            </div>
                                            <div id="preview-ui-keluar-unfinished" class="absolute inset-0 hidden">
                                                <img id="preview-image-keluar-unfinished" src="" class="w-full h-full object-cover" alt="Pratinjau Foto"/>
                                                <div class="absolute inset-0 flex items-end justify-center p-4 gap-3 bg-black bg-opacity-40">
                                                    <button type="button" id="retake-btn-keluar-unfinished" class="bg-red-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-sync-alt mr-1.5"></i>Ambil Ulang</button>
                                                    <button type="button" id="use-photo-btn-keluar-unfinished" class="bg-green-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-check mr-1.5"></i>Gunakan</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="upload-label-keluar-unfinished" class="hidden">
                                            <input name="lampiran_keluar" id="lampiran-keluar-unfinished" type="file" class="hidden" accept="image/*" />
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                                    <button type="button" id="btn-tutup-modal-keluar-unfinished" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                                    <button type="submit" id="submit-button-keluar-unfinished" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400">Kirim Absen Keluar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- TAMPILAN JIKA BELUM ABSEN --}}
                    <div class="flex flex-col lg:flex-row gap-8">
                        <form action="{{ route('absen.store') }}" method="POST" enctype="multipart/form-data" id="form-absen" class="w-full lg:w-2/3">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <div class="bg-white p-6 rounded-xl shadow-sm space-y-6 min-h-[500px] flex flex-col justify-between">
                                <div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="bg-gray-100 p-4 rounded-lg">
                                            <label class="text-sm text-gray-500">Hari & Tanggal</label>
                                            <p class="font-bold text-lg text-gray-800">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</p>
                                        </div>
                                        <div class="bg-gray-100 p-4 rounded-lg">
                                            <label class="text-sm text-gray-500">Jam Saat Ini</label>
                                            <p class="font-bold text-lg text-gray-800" id="jam-realtime"></p>
                                        </div>
                                    </div>
                                    <div class="mt-6"> 
                                        <label class="block text-md font-medium text-gray-700 mb-3">Pilih Status Kehadiran</label>
                                        <input type="hidden" name="status" id="status" value="hadir">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mt-2" id="status-buttons">
                                            <button type="button" data-status="hadir" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Hadir</button>
                                            <button type="button" data-status="izin" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Izin</button>
                                            <button type="button" data-status="sakit" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Sakit</button>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <label for="keterangan" class="block text-md font-medium text-gray-700 mb-3">
                                            Keterangan & Lampiran <span id="keterangan-wajib" class="text-red-500 font-normal hidden">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                            <div class="md:col-span-3">
                                                <textarea name="keterangan" id="keterangan" rows="5" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Ada keperluan keluarga.">{{ old('keterangan') }}</textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <div id="camera-container" class="relative aspect-square rounded-lg overflow-hidden bg-gray-900">
                                                    <video id="video" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay></video>
                                                    <canvas id="canvas" class="hidden"></canvas>
                                                    <div id="snap-ui" class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                                        <button type="button" id="snap" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                                            <i class="fas fa-camera"></i>
                                                        </button>
                                                    </div>
                                                    <div id="preview-ui" class="absolute inset-0 hidden">
                                                        <img id="preview-image" src="" class="w-full h-full object-cover" alt="Pratinjau Foto"/>
                                                        <div class="absolute inset-0 flex items-end justify-center p-4 gap-3 bg-black bg-opacity-40">
                                                            <button type="button" id="retake-btn" class="bg-red-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-sync-alt mr-1.5"></i>Ambil Ulang</button>
                                                            <button type="button" id="use-photo-btn" class="bg-green-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-check mr-1.5"></i>Gunakan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label for="lampiran" id="upload-label" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[250px] hidden">
                                                    <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui">
                                                        <i id="upload-icon" class="fas fa-paperclip text-3xl text-gray-400"></i>
                                                        <p id="upload-text" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Sertakan Lampiran</span></p>
                                                    </div>
                                                    <input name="lampiran" id="lampiran" type="file" class="hidden" accept="image/*,application/pdf" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-2">
                                    <button type="submit" id="submit-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                        Kirim Absensi
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="w-full lg:w-1/3 space-y-8">
                            <div class="flex justify-end mb-4">
                                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline font-semibold hidden lg:block">Kembali ke Dashboard</a>
                            </div>
                            <div class="bg-white p-6 rounded-xl shadow-sm">
                                <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Rekap Bulan Ini</h2>
                                <p class="text-center text-gray-500 text-sm mb-6">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="bg-green-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-green-700">Hadir</p>
                                        <div class="text-base font-bold text-green-700">{{ $rekapAbsen['hadir'] }}</div>
                                    </div>
                                    <div class="bg-red-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-red-700">Sakit</p>
                                        <div class="text-base font-bold text-red-700">{{ $rekapAbsen['sakit'] }}</div>
                                    </div>
                                    <div class="bg-yellow-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-yellow-700">Izin</p>
                                        <div class="text-base font-bold text-yellow-700">{{ $rekapAbsen['izin'] }}</div>
                                    </div>
                                    <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-blue-700">Cuti</p>
                                        <div class="text-base font-bold text-blue-700">{{ $rekapAbsen['cuti'] }}</div>
                                    </div>
                                    <div class="bg-gray-100 p-4 rounded-lg flex items-center justify-between">
                                        <p class="font-semibold text-sm text-gray-700">Tidak Hadir</p>
                                        <div class="text-base font-bold text-gray-700">{{ $rekapAbsen['tidak hadir'] }}</div>
                                    </div>
                                    <div class="bg-orange-100 p-4 rounded-lg text-center">
                                        <p class="font-semibold text-xs text-orange-700">Terlambat Hadir</p>
                                        <div class="text-base font-bold text-orange-700 mt-1">{{ $rekapAbsen['terlambat'] }}</div>
                                    </div>
                                </div>
                            </div>
                            @if(isset($daftarRekan) && count($daftarRekan) > 0)
                                <div class="bg-white p-6 rounded-xl shadow-sm">
                                    <h2 class="text-xl font-bold text-gray-800 text-center mb-4">
                                        Absensi Tim 
                                    </h2>
                                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                        @foreach($daftarRekan as $rekan)
                                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                            <div class="flex items-center">
                                                <img src="{{ $rekan->user->profile_picture ? asset('storage/' . $rekan->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($rekan->user->name ?? 'U').'&background=random&color=fff&size=32' }}" alt="{{ $rekan->user->name ?? '' }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                                <p class="font-semibold text-gray-700 text-sm">{{ $rekan->user->name }}</p>
                                            </div>
                                            <span class="px-2 py-0.5 text-xs font-semibold leading-tight rounded-full capitalize
                                                @switch($rekan->status)
                                                    @case('hadir') bg-green-100 text-green-800 @break
                                                    @case('sakit') bg-red-100 text-red-800 @break
                                                    @case('izin') bg-yellow-100 text-yellow-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch">
                                                {{ str_replace('_', ' ', $rekan->status) }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- MODAL UNTUK ABSEN KELUAR --}}
    @if ($absensiHariIni && is_null($absensiHariIni->jam_keluar) && $absensiHariIni->status == 'hadir')
    <div id="modal-absen-keluar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
            <form action="{{ route('absen.keluar', $absensiHariIni->id) }}" method="POST" enctype="multipart/form-data" id="form-absen-keluar">
                @csrf
                @method('PATCH')
                <input type="hidden" name="latitude_keluar" id="latitude-keluar">
                <input type="hidden" name="longitude_keluar" id="longitude-keluar">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar</h3>
                    <p class="text-gray-500 mt-1">Ambil foto selfie untuk konfirmasi absen keluar.</p>
                    <div class="mt-6">
                        <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Keluar <span class="text-red-500">*</span></label>
                        <div id="camera-container-keluar" class="relative aspect-square rounded-lg overflow-hidden bg-gray-900">
                            <video id="video-keluar" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay></video>
                            <canvas id="canvas-keluar" class="hidden"></canvas>
                            <div id="snap-ui-keluar" class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                <button type="button" id="snap-keluar" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <div id="preview-ui-keluar" class="absolute inset-0 hidden">
                                <img id="preview-image-keluar" src="" class="w-full h-full object-cover" alt="Pratinjau Foto"/>
                                <div class="absolute inset-0 flex items-end justify-center p-4 gap-3 bg-black bg-opacity-40">
                                    <button type="button" id="retake-btn-keluar" class="bg-red-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-sync-alt mr-1.5"></i>Ambil Ulang</button>
                                    <button type="button" id="use-photo-btn-keluar" class="bg-green-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-check mr-1.5"></i>Gunakan</button>
                                </div>
                            </div>
                        </div>
                        <div id="upload-label-keluar" class="hidden">
                            <input name="lampiran_keluar" id="lampiran-keluar" type="file" class="hidden" accept="image/*" />
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" id="btn-tutup-modal-keluar" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" id="submit-button-keluar" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400">Kirim Absen Keluar</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    {{-- MODAL UNTUK ABSEN LEMBUR --}}
    @if ($absensiHariIni && $absensiHariIni->jam_keluar && $absensiHariIni->status == 'hadir')
    <div id="modal-absen-lembur" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
            <form action="{{ route('absen.lembur.store') }}" method="POST" enctype="multipart/form-data" id="form-absen-lembur">
                @csrf
                <input type="hidden" name="latitude_masuk" id="latitude-lembur">
                <input type="hidden" name="longitude_masuk" id="longitude-lembur">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">Form Absen Lembur</h3>
                    <p class="text-gray-500 mt-1">Ambil foto selfie dan isi keterangan untuk memulai lembur.</p>
                    <div class="mt-6">
                        <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Lembur <span class="text-red-500">*</span></label>
                        <div id="camera-container-lembur" class="relative aspect-square rounded-lg overflow-hidden bg-gray-900">
                            <video id="video-lembur" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay></video>
                            <canvas id="canvas-lembur" class="hidden"></canvas>
                            <div id="snap-ui-lembur" class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                <button type="button" id="snap-lembur" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <div id="preview-ui-lembur" class="absolute inset-0 hidden">
                                <img id="preview-image-lembur" src="" class="w-full h-full object-cover" alt="Pratinjau Foto"/>
                                <div class="absolute inset-0 flex items-end justify-center p-4 gap-3 bg-black bg-opacity-40">
                                    <button type="button" id="retake-btn-lembur" class="bg-red-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-sync-alt mr-1.5"></i>Ambil Ulang</button>
                                    <button type="button" id="use-photo-btn-lembur" class="bg-green-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-check mr-1.5"></i>Gunakan</button>
                                </div>
                            </div>
                        </div>
                        <div id="upload-label-lembur" class="hidden">
                            <input name="lampiran_masuk" id="lampiran-lembur" type="file" class="hidden" accept="image/*" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="keterangan-lembur" class="block text-md font-medium text-gray-700 mb-2">Keterangan Lembur <span class="text-red-500">*</span></label>
                        <textarea id="keterangan-lembur" name="keterangan" rows="3" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Menyelesaikan laporan bulanan." required></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" id="btn-tutup-modal-lembur" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" id="submit-button-lembur" class="bg-purple-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-purple-700 disabled:bg-gray-400">Kirim Absen Lembur</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL UNTUK ABSEN KELUAR LEMBUR --}}
    @if ($lemburHariIni && is_null($lemburHariIni->jam_keluar_lembur))
    <div id="modal-keluar-lembur" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
            <form action="{{ route('absen.lembur.keluar', $lemburHariIni->id) }}" method="POST" enctype="multipart/form-data" id="form-keluar-lembur">
                @csrf
                @method('PATCH')
                <input type="hidden" name="latitude_keluar" id="latitude-keluar-lembur">
                <input type="hidden" name="longitude_keluar" id="longitude-keluar-lembur">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar Lembur</h3>
                    <p class="text-gray-500 mt-1">Ambil foto selfie untuk konfirmasi selesai lembur.</p>
                    <div class="mt-6">
                        <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Keluar Lembur <span class="text-red-500">*</span></label>
                        <div id="camera-container-keluar-lembur" class="relative aspect-square rounded-lg overflow-hidden bg-gray-900">
                            <video id="video-keluar-lembur" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay></video>
                            <canvas id="canvas-keluar-lembur" class="hidden"></canvas>
                            <div id="snap-ui-keluar-lembur" class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                <button type="button" id="snap-keluar-lembur" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <div id="preview-ui-keluar-lembur" class="absolute inset-0 hidden">
                                <img id="preview-image-keluar-lembur" src="" class="w-full h-full object-cover" alt="Pratinjau Foto"/>
                                <div class="absolute inset-0 flex items-end justify-center p-4 gap-3 bg-black bg-opacity-40">
                                    <button type="button" id="retake-btn-keluar-lembur" class="bg-red-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-sync-alt mr-1.5"></i>Ambil Ulang</button>
                                    <button type="button" id="use-photo-btn-keluar-lembur" class="bg-green-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs flex items-center"><i class="fas fa-check mr-1.5"></i>Gunakan</button>
                                </div>
                            </div>
                        </div>
                        <div id="upload-label-keluar-lembur" class="hidden">
                            <input name="lampiran_keluar" id="lampiran-keluar-lembur" type="file" class="hidden" accept="image/*" />
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" id="btn-tutup-modal-keluar-lembur" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" id="submit-button-keluar-lembur" class="bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 disabled:bg-gray-400">Kirim Absen Keluar Lembur</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const jamElement = document.getElementById('jam-realtime');
        if(jamElement) {
            function updateJam() {
                jamElement.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            }
            setInterval(updateJam, 1000);
            updateJam();
        }

        // =========================================================================
        // GENERIC CAMERA LOGIC
        // =========================================================================
        window.cameraInstances = {};
        function setupCameraLogic(prefix) {
            const cameraContainer = document.getElementById(`camera-container${prefix}`);
            if (!cameraContainer) return;

            const fileInput = document.getElementById(`lampiran${prefix}`);
            const video = document.getElementById(`video${prefix}`);
            const canvas = document.getElementById(`canvas${prefix}`);
            const snapUI = document.getElementById(`snap-ui${prefix}`);
            const snapButton = document.getElementById(`snap${prefix}`);
            const previewUI = document.getElementById(`preview-ui${prefix}`);
            const previewImage = document.getElementById(`preview-image${prefix}`);
            const retakeButton = document.getElementById(`retake-btn${prefix}`);
            const usePhotoButton = document.getElementById(`use-photo-btn${prefix}`);
            
            let stream;

            const startCamera = async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                    video.srcObject = stream;
                    video.onloadedmetadata = () => { snapButton.disabled = false; };
                    video.classList.remove('hidden');
                    snapUI.classList.remove('hidden');
                    previewUI.classList.add('hidden');
                    cameraContainer.classList.remove('hidden');
                } catch (err) {
                    alert('Tidak bisa mengakses kamera. Pastikan Anda memberikan izin pada browser.');
                    cameraContainer.classList.add('hidden');
                }
            };
            
            const stopCamera = () => {
                if (stream) { stream.getTracks().forEach(track => track.stop()); }
                snapButton.disabled = true;
            };

            snapButton.addEventListener("click", function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                previewImage.src = canvas.toDataURL('image/png');
                video.classList.add('hidden');
                snapUI.classList.add('hidden');
                previewUI.classList.remove('hidden');
            });
            
            retakeButton.addEventListener('click', function() {
                video.classList.remove('hidden');
                snapUI.classList.remove('hidden');
                previewUI.classList.add('hidden');
            });

            usePhotoButton.addEventListener('click', function() {
                previewUI.classList.add('hidden');
                canvas.toBlob(function(blob) {
                    const file = new File([blob], `selfie${prefix.replace('-', '_')}_${Date.now()}.png`, { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    stopCamera();
                    document.dispatchEvent(new CustomEvent(`photoReady${prefix}`, { detail: { isReady: true } }));
                }, 'image/png');
            });

            window.cameraInstances[prefix] = { startCamera, stopCamera };
        }
        
        setupCameraLogic('');
        setupCameraLogic('-keluar');
        setupCameraLogic('-keluar-unfinished');
        setupCameraLogic('-lembur');
        setupCameraLogic('-keluar-lembur');

        // =========================================================================
        // MAIN FORM LOGIC (ABSEN MASUK)
        // =========================================================================
        const formAbsen = document.getElementById('form-absen');
        if (formAbsen) {
            const hiddenStatusInput = document.getElementById('status');
            const submitButton = document.getElementById('submit-button');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const cameraContainer = document.getElementById('camera-container');
            const uploadLabel = document.getElementById('upload-label');
            let isLocationReady = false;
            let isPhotoReady = false;

            document.addEventListener('photoReady', e => {
                isPhotoReady = e.detail.isReady;
                const existingSuccessMsg = cameraContainer.parentNode.querySelector('.success-message');
                if (existingSuccessMsg) existingSuccessMsg.remove();
                
                if(isPhotoReady) {
                    const successMsg = document.createElement('div');
                    successMsg.className = 'success-message mt-2 text-center text-sm text-green-600 font-semibold p-2 bg-green-50 rounded-lg';
                    successMsg.innerHTML = `<i class="fas fa-check-circle"></i> Foto berhasil diambil.`;
                    cameraContainer.parentNode.insertBefore(successMsg, cameraContainer.nextSibling);
                }
                checkFormReadiness();
            });

            const checkFormReadiness = () => {
                if (hiddenStatusInput.value === 'hadir') {
                    if (isLocationReady && isPhotoReady) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Kirim Absensi';
                    } else {
                        submitButton.disabled = true;
                        let text = 'Mohon ';
                        if(!isPhotoReady) text += 'Ambil Foto';
                        if(!isLocationReady && !isPhotoReady) text += ' & ';
                        if(!isLocationReady) text += 'Izinkan Lokasi';
                        submitButton.textContent = text;
                    }
                } else {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Absensi';
                }
            };

            const getLocation = () => {
                isLocationReady = false;
                checkFormReadiness();
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        latitudeInput.value = position.coords.latitude;
                        longitudeInput.value = position.coords.longitude;
                        isLocationReady = true;
                        checkFormReadiness();
                    },
                    () => { alert('Tidak bisa mendapatkan lokasi. Pastikan GPS Anda aktif dan berikan izin pada browser.'); isLocationReady = false; checkFormReadiness(); },
                    { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
                );
            };

            const selectedStyles = {
                hadir: 'bg-emerald-500 text-white border-emerald-500',
                izin: 'bg-amber-500 text-white border-amber-500',
                sakit: 'bg-red-500 text-white border-red-500'
            };

            const setActiveButton = (status) => {
                document.querySelectorAll('.status-btn').forEach(btn => {
                    btn.classList.remove(...Object.values(selectedStyles).join(' ').split(' '));
                    btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
                });
                const activeButton = document.querySelector(`.status-btn[data-status="${status}"]`);
                if (activeButton) {
                    activeButton.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
                    activeButton.classList.add(...selectedStyles[status].split(' '));
                }
            };

            const toggleUiForStatus = (status) => {
                const keteranganWajibSpan = document.getElementById('keterangan-wajib');
                const existingSuccessMsg = cameraContainer.parentNode.querySelector('.success-message');
                if (existingSuccessMsg) existingSuccessMsg.remove();
                
                isPhotoReady = false;
                
                if (status === 'hadir') {
                    keteranganWajibSpan.classList.add('hidden');
                    uploadLabel.classList.add('hidden');
                    window.cameraInstances[''].startCamera();
                    getLocation();
                } else {
                    keteranganWajibSpan.classList.remove('hidden');
                    window.cameraInstances[''].stopCamera();
                    cameraContainer.classList.add('hidden');
                    uploadLabel.classList.remove('hidden');
                }
                checkFormReadiness();
            };
            
            document.getElementById('status-buttons').addEventListener('click', function(e) {
                if (e.target.matches('.status-btn')) {
                    const selectedStatus = e.target.dataset.status;
                    hiddenStatusInput.value = selectedStatus;
                    setActiveButton(selectedStatus);
                    toggleUiForStatus(selectedStatus);
                }
            });

            setActiveButton(hiddenStatusInput.value);
            toggleUiForStatus(hiddenStatusInput.value);
        }

        // =========================================================================
        // MODAL ACTIVATION LOGIC
        // =========================================================================
        function setupModalLogic(btnId, modalId, prefix) {
            const btn = document.getElementById(btnId);
            const modal = document.getElementById(modalId);
            if (!btn || !modal) return;

            const modalContent = modal.querySelector('.transform');
            const btnTutupModal = document.getElementById(`btn-tutup-modal${prefix}`);
            const submitBtn = document.getElementById(`submit-button${prefix}`);
            const latitudeInput = document.getElementById(`latitude${prefix}`);
            const longitudeInput = document.getElementById(`longitude${prefix}`);
            const keteranganInput = document.getElementById(`keterangan${prefix}`);
            
            let isLocationReady = false;
            let isPhotoReady = false;

            const checkReadiness = () => {
                const isKeteranganReady = keteranganInput ? keteranganInput.value.trim() !== '' : true;
                const allReady = isLocationReady && isPhotoReady && isKeteranganReady;
                submitBtn.disabled = !allReady;

                if (allReady) {
                    submitBtn.textContent = submitBtn.dataset.readyText || 'Kirim';
                } else {
                    // PERBAIKAN: Teks dinamis saat tombol disabled
                    if (keteranganInput && !isKeteranganReady) {
                        submitBtn.textContent = 'Mohon Isi Data Lengkap';
                    } else {
                        submitBtn.textContent = 'Mohon Ambil Foto & Lokasi';
                    }
                }
            };

            const getLocation = () => {
                isLocationReady = false;
                checkReadiness();
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        latitudeInput.value = pos.coords.latitude;
                        longitudeInput.value = pos.coords.longitude;
                        isLocationReady = true;
                        checkReadiness();
                    },
                    () => { alert('Gagal mendapatkan lokasi.'); isLocationReady = false; checkReadiness(); },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            };

            document.addEventListener(`photoReady${prefix}`, e => {
                isPhotoReady = e.detail.isReady;
                checkReadiness();
                const cameraContainer = document.getElementById(`camera-container${prefix}`);
                const existingSuccessMsg = cameraContainer.parentNode.querySelector('.success-message');
                if (existingSuccessMsg) existingSuccessMsg.remove();
                
                if(isPhotoReady) {
                    const successMsg = document.createElement('div');
                    successMsg.className = 'success-message mt-2 text-center text-sm text-green-600 font-semibold p-2 bg-green-50 rounded-lg';
                    successMsg.innerHTML = `<i class="fas fa-check-circle"></i> Foto berhasil diambil.`;
                    cameraContainer.parentNode.insertBefore(successMsg, cameraContainer.nextSibling);
                }
            });

            if (keteranganInput) {
                keteranganInput.addEventListener('input', checkReadiness);
            }

            const openModal = () => {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => modalContent.classList.remove('scale-95', 'opacity-0'), 10);
                window.cameraInstances[prefix].startCamera();
                getLocation();
                submitBtn.dataset.readyText = submitBtn.textContent;
            };

            const closeModal = () => {
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden'); modal.classList.remove('flex');
                    window.cameraInstances[prefix].stopCamera();
                    const cameraContainer = document.getElementById(`camera-container${prefix}`);
                    const existingSuccessMsg = cameraContainer.parentNode.querySelector('.success-message');
                    if (existingSuccessMsg) existingSuccessMsg.remove();
                    if(keteranganInput) keteranganInput.value = '';
                    isLocationReady = isPhotoReady = false;
                    checkReadiness();
                }, 200);
            };

            btn.addEventListener('click', openModal);
            btnTutupModal.addEventListener('click', closeModal);
        }

        setupModalLogic('btn-absen-keluar', 'modal-absen-keluar', '-keluar');
        setupModalLogic('btn-absen-keluar-unfinished', 'modal-absen-keluar-unfinished', '-keluar-unfinished');
        setupModalLogic('btn-absen-lembur', 'modal-absen-lembur', '-lembur');
        setupModalLogic('btn-absen-keluar-lembur', 'modal-keluar-lembur', '-keluar-lembur');
    });
    </script>
    @endpush
</x-layout-users>