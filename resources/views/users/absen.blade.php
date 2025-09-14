<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-gray-50 p-4 md:p-8 min-h-screen">
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

            @if ($absensiHariIni)
                {{-- TAMPILAN JIKA PENGGUNA SUDAH ABSEN MASUK --}}
                <div class="flex flex-col lg:flex-row gap-8">
                    {{-- KOLOM KIRI: STATUS ABSENSI PENGGUNA --}}
                    <div class="w-full lg:w-2/3 bg-white p-6 md:p-8 rounded-xl shadow-sm">
                        <div class="flex flex-col md:flex-row items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Absensi Hari Ini</h2>
                                <p class="text-gray-600 mt-1">
                                    Status Kehadiran Anda: <span class="font-semibold capitalize">{{ $absensiHariIni->status }}</span>
                                </p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="mt-4 md:mt-0 text-blue-600 hover:underline font-semibold">Kembali ke Dashboard</a>
                        </div>
                        
                        <div class="mt-6 border-t pt-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- KOTAK JAM MASUK --}}
                                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                                    <div class="flex items-center text-emerald-800 mb-2">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        <p class="font-semibold">Absen Masuk</p>
                                    </div>
                                    <p class="text-3xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }} <span class="text-lg font-medium">WIB</span></p>
                                    @if($absensiHariIni->latitude && $absensiHariIni->longitude)
                                        <a href="http://maps.google.com/maps?q={{ $absensiHariIni->latitude }},{{ $absensiHariIni->longitude }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Lihat Lokasi
                                        </a>
                                    @endif
                                </div>

                                {{-- KOTAK JAM KELUAR --}}
                                <div class="bg-rose-50 p-4 rounded-lg border border-rose-200 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center text-rose-800 mb-2">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            <p class="font-semibold">Absen Keluar</p>
                                        </div>
                                        @if ($absensiHariIni->jam_keluar)
                                            <p class="text-3xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }} <span class="text-lg font-medium">WIB</span></p>
                                            @if($absensiHariIni->latitude_keluar && $absensiHariIni->longitude_keluar)
                                                <a href="http://maps.google.com/maps?q={{ $absensiHariIni->latitude_keluar }},{{ $absensiHariIni->longitude_keluar }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
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

                            {{-- KOTAK ABSEN LEMBUR --}}
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

                    {{-- KOLOM KANAN: REKAP DAN DAFTAR REKAN --}}
                    <div class="w-full lg:w-1/3 space-y-8">
                        {{-- REKAP BULANAN PENGGUNA --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Rekap Bulan Ini</h2>
                            <p class="text-center text-gray-500 text-sm mb-6">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                            <div class="space-y-4">
                                <div class="flex items-center bg-emerald-50 p-4 rounded-lg">
                                    <div class="flex-shrink-0 bg-emerald-100 text-emerald-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-check"></i></div>
                                    <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Hadir</p></div>
                                    <div class="text-lg font-bold text-emerald-600">{{ $rekapAbsen['hadir'] }}</div>
                                </div>
                                <div class="flex items-center bg-red-50 p-4 rounded-lg">
                                    <div class="flex-shrink-0 bg-red-100 text-red-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-medkit"></i></div>
                                    <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Sakit</p></div>
                                    <div class="text-lg font-bold text-red-600">{{ $rekapAbsen['sakit'] }}</div>
                                </div>
                                <div class="flex items-center bg-amber-50 p-4 rounded-lg">
                                    <div class="flex-shrink-0 bg-amber-100 text-amber-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-file-alt"></i></div>
                                    <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Izin</p></div>
                                    <div class="text-lg font-bold text-amber-600">{{ $rekapAbsen['izin'] }}</div>
                                </div>
                                <div class="flex items-center bg-purple-50 p-4 rounded-lg">
                                    <div class="flex-shrink-0 bg-purple-100 text-purple-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-plane-departure"></i></div>
                                    <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Cuti</p></div>
                                    <div class="text-lg font-bold text-purple-600">{{ $rekapAbsen['cuti'] }}</div>
                                </div>
                                <div class="flex items-center bg-rose-50 p-4 rounded-lg">
                                    <div class="flex-shrink-0 bg-rose-100 text-rose-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-clock"></i></div>
                                    <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Terlambat</p></div>
                                    <div class="text-lg font-bold text-rose-600">{{ $rekapAbsen['terlambat'] }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- DAFTAR SEMUA REKAN DIVISI --}}
                        @if(isset($daftarRekan) && count($daftarRekan) > 0)
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h2 class="text-xl font-bold text-gray-800 text-center mb-4">
                                Absensi Tim Divisi {{ Auth::user()->divisi }}
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

                {{-- MODAL UNTUK ABSEN KELUAR --}}
                @if ($absensiHariIni && is_null($absensiHariIni->jam_keluar) && $absensiHariIni->status == 'hadir')
                {{-- Data ID Absensi untuk JavaScript --}}
                <div id="absensi-data" data-id="{{ $absensiHariIni->id }}"></div>
                <div id="modal-absen-keluar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
                        <input type="hidden" name="latitude_keluar" id="latitude-keluar">
                        <input type="hidden" name="longitude_keluar" id="longitude-keluar">
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar</h3>
                            <p class="text-gray-500 mt-1">Ambil foto selfie untuk konfirmasi absen keluar.</p>
                            
                            <div class="mt-6">
                                <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Keluar <span class="text-red-500">*</span></label>
                                <div id="camera-container-keluar" class="relative aspect-video rounded-lg overflow-hidden bg-gray-900 hidden">
                                    <video id="video-keluar" class="w-full h-full object-cover" autoplay></video>
                                    <canvas id="canvas-keluar" class="hidden"></canvas>
                                    <div class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                        <button type="button" id="snap-keluar" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </div>
                                <label for="lampiran-keluar" id="upload-label-keluar" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[150px]">
                                    <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui-keluar">
                                        <i id="upload-icon-keluar" class="fas fa-camera text-3xl text-gray-400"></i>
                                        <p id="upload-text-keluar" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>
                                    </div>
                                    <input name="lampiran_keluar" id="lampiran-keluar" type="file" class="hidden" accept="image/*" />
                                </label>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                            <button type="button" id="btn-tutup-modal" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                            <button type="button" id="submit-button-keluar" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400">Kirim Absen Keluar</button>
                        </div>
                    </div>
                </div>
                @endif
            @else
                {{-- TAMPILAN JIKA BELUM ABSEN --}}

                {{-- ======================= PENAMBAHAN DI SINI ======================= --}}
                <div class="flex justify-end mb-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline font-semibold">Kembali ke Dashboard</a>
                </div>
                {{-- ===================== AKHIR PENAMBAHAN ===================== --}}
                
                <form action="{{ route('absen.store') }}" method="POST" enctype="multipart/form-data" id="form-absen">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <div class="flex flex-col lg:flex-row gap-8">
                        <div class="w-full lg:w-2/3 bg-white p-6 rounded-xl shadow-sm space-y-6">
                            @if ($errors->any())
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                                    <p class="font-bold">Terjadi Kesalahan</p>
                                    <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>- {{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif
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
                            <div>
                                <label class="block text-md font-medium text-gray-700 mb-3">Pilih Status Kehadiran</label>
                                <input type="hidden" name="status" id="status" value="hadir">
                                <div class="grid grid-cols-3 gap-3 mt-2" id="status-buttons">
                                    <button type="button" data-status="hadir" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Hadir</button>
                                    <button type="button" data-status="izin" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Izin</button>
                                    <button type="button" data-status="sakit" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Sakit</button>
                                </div>
                            </div>
                            <div>
                                <label for="keterangan" class="block text-md font-medium text-gray-700 mb-3">
                                    Keterangan & Lampiran <span id="keterangan-wajib" class="text-red-500 font-normal hidden">*</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div class="md:col-span-3">
                                        <textarea name="keterangan" id="keterangan" rows="5" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Ada keperluan keluarga.">{{ old('keterangan') }}</textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <div id="camera-container" class="relative aspect-video rounded-lg overflow-hidden bg-gray-900 hidden">
                                            <video id="video" class="w-full h-full object-cover" autoplay></video>
                                            <canvas id="canvas" class="hidden"></canvas>
                                            <div class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                                                <button type="button" id="snap" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <label for="lampiran" id="upload-label" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[150px]">
                                            <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui">
                                                <i id="upload-icon" class="fas fa-camera text-3xl text-gray-400"></i>
                                                <p id="upload-text" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>
                                            </div>
                                            <input name="lampiran" id="lampiran" type="file" class="hidden" accept="image/*,application/pdf" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" id="submit-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    Kirim Absensi
                                </button>
                            </div>
                        </div>

                        <div class="w-full lg:w-1/3 space-y-8">
                            <div class="bg-white p-6 rounded-xl shadow-sm">
                                <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Rekap Bulan Ini</h2>
                                <p class="text-center text-gray-500 text-sm mb-6">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                                <div class="space-y-4">
                                    <div class="flex items-center bg-emerald-50 p-4 rounded-lg">
                                        <div class="flex-shrink-0 bg-emerald-100 text-emerald-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-check"></i></div>
                                        <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Hadir</p></div>
                                        <div class="text-lg font-bold text-emerald-600">{{ $rekapAbsen['hadir'] }}</div>
                                    </div>
                                    <div class="flex items-center bg-red-50 p-4 rounded-lg">
                                        <div class="flex-shrink-0 bg-red-100 text-red-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-medkit"></i></div>
                                        <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Sakit</p></div>
                                        <div class="text-lg font-bold text-red-600">{{ $rekapAbsen['sakit'] }}</div>
                                    </div>
                                    <div class="flex items-center bg-amber-50 p-4 rounded-lg">
                                        <div class="flex-shrink-0 bg-amber-100 text-amber-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-file-alt"></i></div>
                                        <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Izin</p></div>
                                        <div class="text-lg font-bold text-amber-600">{{ $rekapAbsen['izin'] }}</div>
                                    </div>
                                    <div class="flex items-center bg-purple-50 p-4 rounded-lg">
                                        <div class="flex-shrink-0 bg-purple-100 text-purple-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-plane-departure"></i></div>
                                        <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Cuti</p></div>
                                        <div class="text-lg font-bold text-purple-600">{{ $rekapAbsen['cuti'] }}</div>
                                    </div>
                                    <div class="flex items-center bg-rose-50 p-4 rounded-lg">
                                        <div class="flex-shrink-0 bg-rose-100 text-rose-600 rounded-full h-10 w-10 flex items-center justify-center"><i class="fas fa-clock"></i></div>
                                        <div class="ml-4 flex-grow"><p class="font-semibold text-gray-700">Terlambat</p></div>
                                        <div class="text-lg font-bold text-rose-600">{{ $rekapAbsen['terlambat'] }}</div>
                                    </div>
                                </div>
                            </div>
                            @if(isset($daftarRekan) && count($daftarRekan) > 0)
                            <div class="bg-white p-6 rounded-xl shadow-sm">
                                <h2 class="text-xl font-bold text-gray-800 text-center mb-4">
                                    Absensi Tim Divisi {{ Auth::user()->divisi }}
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
                </form>
            @endif
        </div>
    </div>

    {{-- MODAL UNTUK ABSEN KELUAR --}}
    @if ($absensiHariIni && is_null($absensiHariIni->jam_keluar) && $absensiHariIni->status == 'hadir')
    <div id="absensi-data" data-id="{{ $absensiHariIni->id }}"></div>
    <div id="modal-absen-keluar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
            <input type="hidden" name="latitude_keluar" id="latitude-keluar">
            <input type="hidden" name="longitude_keluar" id="longitude-keluar">
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar</h3>
                <p class="text-gray-500 mt-1">Ambil foto selfie untuk konfirmasi absen keluar.</p>
                
                <div class="mt-6">
                    <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Keluar <span class="text-red-500">*</span></label>
                    <div id="camera-container-keluar" class="relative aspect-video rounded-lg overflow-hidden bg-gray-900 hidden">
                        <video id="video-keluar" class="w-full h-full object-cover" autoplay></video>
                        <canvas id="canvas-keluar" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                            <button type="button" id="snap-keluar" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <label for="lampiran-keluar" id="upload-label-keluar" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[150px]">
                        <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui-keluar">
                            <i id="upload-icon-keluar" class="fas fa-camera text-3xl text-gray-400"></i>
                            <p id="upload-text-keluar" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>
                        </div>
                        <input name="lampiran_keluar" id="lampiran-keluar" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                <button type="button" id="btn-tutup-modal" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="button" id="submit-button-keluar" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400">Kirim Absen Keluar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL UNTUK ABSEN LEMBUR --}}
    @if ($absensiHariIni && $absensiHariIni->jam_keluar && is_null($lemburHariIni))
    <div id="modal-absen-lembur" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
            <input type="hidden" name="latitude_lembur" id="latitude-lembur">
            <input type="hidden" name="longitude_lembur" id="longitude-lembur">
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800">Form Absen Lembur</h3>
                <p class="text-gray-500 mt-1">Ambil foto selfie dan isi keterangan untuk memulai lembur.</p>
                
                <div class="mt-6">
                    <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Lembur <span class="text-red-500">*</span></label>
                    <div id="camera-container-lembur" class="relative aspect-video rounded-lg overflow-hidden bg-gray-900 hidden">
                        <video id="video-lembur" class="w-full h-full object-cover" autoplay></video>
                        <canvas id="canvas-lembur" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                            <button type="button" id="snap-lembur" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <label for="lampiran-lembur" id="upload-label-lembur" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[150px]">
                        <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui-lembur">
                            <i id="upload-icon-lembur" class="fas fa-camera text-3xl text-gray-400"></i>
                            <p id="upload-text-lembur" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>
                        </div>
                        <input name="lampiran_masuk" id="lampiran-lembur" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>

                <div class="mt-4">
                    <label for="keterangan-lembur" class="block text-md font-medium text-gray-700 mb-2">Keterangan Lembur <span class="text-red-500">*</span></label>
                    <textarea id="keterangan-lembur" name="keterangan" rows="3" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Menyelesaikan laporan bulanan." required></textarea>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                <button type="button" id="btn-tutup-modal-lembur" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="button" id="submit-button-lembur" class="bg-purple-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-purple-700 disabled:bg-gray-400">Kirim Absen Lembur</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL UNTUK ABSEN KELUAR LEMBUR --}}
    @if ($lemburHariIni && is_null($lemburHariIni->jam_keluar_lembur))
    <div id="lembur-data" data-id="{{ $lemburHariIni->id }}"></div>
    <div id="modal-keluar-lembur" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
            <input type="hidden" name="latitude_keluar_lembur" id="latitude-keluar-lembur">
            <input type="hidden" name="longitude_keluar_lembur" id="longitude-keluar-lembur">
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar Lembur</h3>
                <p class="text-gray-500 mt-1">Ambil foto selfie untuk konfirmasi selesai lembur.</p>
                
                <div class="mt-6">
                    <label class="block text-md font-medium text-gray-700 mb-2">Foto Selfie Keluar Lembur <span class="text-red-500">*</span></label>
                    <div id="camera-container-keluar-lembur" class="relative aspect-video rounded-lg overflow-hidden bg-gray-900 hidden">
                        <video id="video-keluar-lembur" class="w-full h-full object-cover" autoplay></video>
                        <canvas id="canvas-keluar-lembur" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-end justify-center p-4 bg-black bg-opacity-25">
                            <button type="button" id="snap-keluar-lembur" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <label for="lampiran-keluar-lembur" id="upload-label-keluar-lembur" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[150px]">
                        <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui-keluar-lembur">
                            <i id="upload-icon-keluar-lembur" class="fas fa-camera text-3xl text-gray-400"></i>
                            <p id="upload-text-keluar-lembur" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>
                        </div>
                        <input name="lampiran_keluar" id="lampiran-keluar-lembur" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                <button type="button" id="btn-tutup-modal-keluar-lembur" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="button" id="submit-button-keluar-lembur" class="bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 disabled:bg-gray-400">Kirim Absen Keluar Lembur</button>
            </div>
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

        const formAbsen = document.getElementById('form-absen');
        if (formAbsen) {
            const hiddenStatusInput = document.getElementById('status');
            const uploadLabel = document.getElementById('upload-label');
            const fileInput = document.getElementById('lampiran');
            const cameraContainer = document.getElementById('camera-container');
            const uploadUI = document.getElementById('upload-ui');
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const snapButton = document.getElementById('snap');
            let stream;

            function setSuccessUI(fileName) {
                uploadUI.innerHTML = `
                    <div class="flex flex-col items-center justify-center text-center p-2 w-full">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                        <p class="mt-2 text-sm text-gray-700 font-semibold w-full truncate px-2" title="${fileName}">${fileName}</p>
                        <button type="button" id="change-photo-btn" class="mt-2 text-xs text-blue-600 hover:underline font-medium">Ganti</button>
                    </div>`;
                uploadLabel.classList.replace('border-dashed', 'border-solid');
                uploadLabel.classList.add('border-green-500', 'bg-green-50');
            }
            
            function resetUploadUI() {
                fileInput.value = '';
                uploadUI.innerHTML = `<i id="upload-icon" class="fas fa-camera text-3xl text-gray-400"></i><p id="upload-text" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>`;
                uploadLabel.classList.remove('border-solid', 'border-green-500', 'bg-green-50', 'hidden');
                uploadLabel.classList.add('border-dashed', 'border-gray-300');
            }

            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                    video.srcObject = stream;
                    video.onloadedmetadata = () => { snapButton.disabled = false; };
                } catch (err) {
                    alert('Tidak bisa mengakses kamera. Pastikan Anda memberikan izin pada browser.');
                    toggleUiForStatus('izin');
                    setActiveButton('izin');
                }
            }

            function stopCamera() {
                if (stream) { stream.getTracks().forEach(track => track.stop()); }
                snapButton.disabled = true;
            }

            function getLocation() {
                const submitButton = document.getElementById('submit-button');
                submitButton.disabled = true;
                submitButton.textContent = 'Mencari Lokasi...';
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            document.getElementById('latitude').value = position.coords.latitude;
                            document.getElementById('longitude').value = position.coords.longitude;
                            submitButton.disabled = false;
                            submitButton.textContent = 'Kirim Absensi';
                        },
                        () => {
                            alert('Tidak bisa mendapatkan lokasi. Pastikan GPS Anda aktif dan berikan izin pada browser.');
                            submitButton.disabled = false;
                            submitButton.textContent = 'Kirim Absensi';
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 20000,
                            maximumAge: 0
                        }
                    );
                } else {
                    alert('Browser Anda tidak mendukung Geolocation.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Absensi';
                }
            }

            const selectedStyles = {
                hadir: 'bg-emerald-500 text-white border-emerald-500',
                izin: 'bg-amber-500 text-white border-amber-500',
                sakit: 'bg-red-500 text-white border-red-500'
            };

            function toggleUiForStatus(status) {
                const keteranganWajibSpan = document.getElementById('keterangan-wajib');
                const keteranganTextarea = document.getElementById('keterangan');
                
                resetUploadUI();
                stopCamera();
                
                if (status === 'hadir') {
                    keteranganWajibSpan.classList.add('hidden');
                    keteranganTextarea.disabled = true;
                    keteranganTextarea.value = '';
                    keteranganTextarea.placeholder = 'Keterangan tidak diperlukan untuk status Hadir.';
                    keteranganTextarea.classList.add('bg-gray-100');
                    cameraContainer.classList.remove('hidden');
                    uploadLabel.classList.add('hidden');
                    startCamera();
                } else {
                    keteranganWajibSpan.classList.remove('hidden');
                    keteranganTextarea.disabled = false;
                    keteranganTextarea.placeholder = 'Contoh: Ada keperluan keluarga.';
                    keteranganTextarea.classList.remove('bg-gray-100');
                    cameraContainer.classList.add('hidden');
                    uploadLabel.classList.remove('hidden');
                }
                getLocation();
            }

            function setActiveButton(status) {
                document.querySelectorAll('.status-btn').forEach(btn => {
                    btn.classList.remove(...Object.values(selectedStyles).join(' ').split(' '));
                    btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
                });
                const activeButton = document.querySelector(`.status-btn[data-status="${status}"]`);
                if (activeButton) {
                    activeButton.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
                    activeButton.classList.add(...selectedStyles[status].split(' '));
                }
            }
            
            document.getElementById('status-buttons').addEventListener('click', function(e) {
                if (e.target.matches('.status-btn')) {
                    const selectedStatus = e.target.dataset.status;
                    hiddenStatusInput.value = selectedStatus;
                    setActiveButton(selectedStatus);
                    toggleUiForStatus(selectedStatus);
                }
            });

            snapButton.addEventListener("click", function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(function(blob) {
                    const file = new File([blob], "selfie_" + Date.now() + ".png", { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    
                    stopCamera();
                    cameraContainer.classList.add('hidden');
                    uploadLabel.classList.remove('hidden');
                    setSuccessUI(file.name);
                }, 'image/png');
            });

            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) setSuccessUI(this.files[0].name);
            });
            
            uploadLabel.addEventListener('click', function(e) {
                if (e.target.id === 'change-photo-btn') {
                    e.preventDefault();
                    e.stopPropagation();
                    cameraContainer.classList.remove('hidden');
                    uploadLabel.classList.add('hidden');
                    startCamera();
                } else {
                    if (hiddenStatusInput.value === 'hadir') {
                        e.preventDefault();
                        e.stopPropagation();
                        cameraContainer.classList.remove('hidden');
                        uploadLabel.classList.add('hidden');
                        startCamera();
                    }
                }
            });

            setActiveButton(hiddenStatusInput.value);
            toggleUiForStatus(hiddenStatusInput.value);
        }

        const btnAbsenKeluar = document.getElementById('btn-absen-keluar');
        const modal = document.getElementById('modal-absen-keluar');
        if (modal) {
            // --- Variabel untuk ID absensi --
            const absensiId = document.getElementById('absensi-data').dataset.id;

            const modalContent = modal.querySelector('.transform');
            const btnTutupModal = document.getElementById('btn-tutup-modal');
            const submitKeluarBtn = document.getElementById('submit-button-keluar');
            const latitudeKeluarInput = document.getElementById('latitude-keluar');
            const longitudeKeluarInput = document.getElementById('longitude-keluar');
            const cameraContainerKeluar = document.getElementById('camera-container-keluar');
            const uploadLabelKeluar = document.getElementById('upload-label-keluar');
            const fileInputKeluar = document.getElementById('lampiran-keluar');
            const videoKeluar = document.getElementById('video-keluar');
            const canvasKeluar = document.getElementById('canvas-keluar');
            const snapButtonKeluar = document.getElementById('snap-keluar');
            const uploadUiKeluar = document.getElementById('upload-ui-keluar');
            
            let streamKeluar;
            let isLocationReadyKeluar = false;
            let isPhotoReadyKeluar = false;

            function setSuccessUIKeluar(fileName) {
                uploadUiKeluar.innerHTML = `
                    <div class="flex flex-col items-center justify-center text-center p-2 w-full">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                        <p class="mt-2 text-sm text-gray-700 font-semibold w-full truncate px-2" title="${fileName}">${fileName}</p>
                        <button type="button" id="change-photo-keluar-btn" class="mt-2 text-xs text-blue-600 hover:underline font-medium">Ganti</button>
                    </div>`;
                uploadLabelKeluar.classList.replace('border-dashed', 'border-solid');
                uploadLabelKeluar.classList.add('border-green-500', 'bg-green-50');
            }

            function resetUploadUIKeluar() {
                fileInputKeluar.value = '';
                uploadUiKeluar.innerHTML = `<i id="upload-icon-keluar" class="fas fa-camera text-3xl text-gray-400"></i><p id="upload-text-keluar" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>`;
                uploadLabelKeluar.classList.remove('border-solid', 'border-green-500', 'bg-green-50', 'hidden');
                uploadLabelKeluar.classList.add('border-dashed', 'border-gray-300');
            }
            
            function checkFormReadinessKeluar() {
                if (isLocationReadyKeluar && isPhotoReadyKeluar) {
                    submitKeluarBtn.disabled = false;
                    submitKeluarBtn.textContent = 'Kirim Absen Keluar';
                } else {
                    submitKeluarBtn.disabled = true;
                    submitKeluarBtn.textContent = 'Mohon Ambil Foto & Lokasi';
                }
            }

            async function startCameraKeluar() {
                try {
                    streamKeluar = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                    videoKeluar.srcObject = streamKeluar;
                    videoKeluar.onloadedmetadata = () => { 
                        snapButtonKeluar.disabled = false; 
                    };
                    cameraContainerKeluar.classList.remove('hidden');
                    uploadLabelKeluar.classList.add('hidden');
                } catch (err) {
                    alert('Tidak bisa mengakses kamera untuk absen keluar. Pastikan izin telah diberikan.');
                    resetUploadUIKeluar();
                }
            }

            function stopCameraKeluar() {
                if (streamKeluar) { streamKeluar.getTracks().forEach(track => track.stop()); }
                snapButtonKeluar.disabled = true;
            }

            function getLocationKeluar() {
                submitKeluarBtn.textContent = 'Mencari Lokasi...';
                isLocationReadyKeluar = false;
                checkFormReadinessKeluar();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latitudeKeluarInput.value = position.coords.latitude;
                            longitudeKeluarInput.value = position.coords.longitude;
                            isLocationReadyKeluar = true;
                            checkFormReadinessKeluar();
                        },
                        () => {
                            alert('Gagal mendapatkan lokasi untuk absen keluar.');
                            isLocationReadyKeluar = false;
                            checkFormReadinessKeluar();
                        }, 
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                }
            }
            
            snapButtonKeluar.addEventListener("click", function() {
                canvasKeluar.width = videoKeluar.videoWidth;
                canvasKeluar.height = videoKeluar.videoHeight;
                canvasKeluar.getContext('2d').drawImage(videoKeluar, 0, 0, canvasKeluar.width, canvasKeluar.height);
                canvasKeluar.toBlob(function(blob) {
                    const file = new File([blob], "selfie_keluar_" + Date.now() + ".png", { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInputKeluar.files = dataTransfer.files;
                    
                    stopCameraKeluar();
                    cameraContainerKeluar.classList.add('hidden');
                    uploadLabelKeluar.classList.remove('hidden');
                    setSuccessUIKeluar(file.name);

                    isPhotoReadyKeluar = true;
                    checkFormReadinessKeluar();

                }, 'image/png');
            });

            uploadLabelKeluar.addEventListener('click', function(e) {
                if (e.target.id === 'change-photo-keluar-btn') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    isPhotoReadyKeluar = false;
                    checkFormReadinessKeluar();

                    cameraContainerKeluar.classList.remove('hidden');
                    uploadLabelKeluar.classList.add('hidden');
                    startCameraKeluar();
                } else if (fileInputKeluar.files.length === 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    cameraContainerKeluar.classList.remove('hidden');
                    uploadLabelKeluar.classList.add('hidden');
                    startCameraKeluar();
                }
            });

            submitKeluarBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData();
                formData.append('latitude_keluar', latitudeKeluarInput.value);
                formData.append('longitude_keluar', longitudeKeluarInput.value);
                if (fileInputKeluar.files.length > 0) {
                    formData.append('lampiran_keluar', fileInputKeluar.files[0]);
                }
                
                submitKeluarBtn.disabled = true;
                submitKeluarBtn.textContent = 'Memproses...';

                if (!absensiId) {
                     alert('ID absensi tidak ditemukan. Silakan refresh halaman.');
                     submitKeluarBtn.disabled = false;
                     submitKeluarBtn.textContent = 'Kirim Absen Keluar';
                     return;
                }
                const url = `/absen/keluar/${absensiId}`;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });

                    if (response.ok) {
                        const result = await response.json();
                        alert(result.success);
                        closeModal();
                        window.location.reload();
                    } else {
                        const errorText = await response.text();
                        console.error("Server Error Response:", errorText);
                        alert('Terjadi kesalahan dari server. Cek console browser (F12) untuk detail.');
                        submitKeluarBtn.disabled = false;
                        submitKeluarBtn.textContent = 'Kirim Absen Keluar';
                    }
                } catch (error) {
                    console.error("Fetch Error:", error);
                    alert('Terjadi kesalahan pada koneksi atau server. Cek console browser (F12) untuk detail.');
                    submitKeluarBtn.disabled = false;
                    submitKeluarBtn.textContent = 'Kirim Absen Keluar';
                }
            });

            btnTutupModal.addEventListener('click', function() {
                isLocationReadyKeluar = false;
                isPhotoReadyKeluar = false;
                resetUploadUIKeluar();
                checkFormReadinessKeluar();
                closeModal();
            });

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modalContent.classList.remove('scale-95', 'opacity-0'), 10);
                startCameraKeluar();
                getLocationKeluar();
            }

            function closeModal() {
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    stopCameraKeluar();
                }, 200);
            }

            if (btnAbsenKeluar) btnAbsenKeluar.addEventListener('click', openModal);
            if (btnTutupModal) btnTutupModal.addEventListener('click', closeModal);
        }

        // --- LOGIKA BARU UNTUK ABSENSI LEMBUR ---

        // Masuk Lembur
        const btnAbsenLembur = document.getElementById('btn-absen-lembur');
        const modalLembur = document.getElementById('modal-absen-lembur');
        if(modalLembur) {
            const modalContentLembur = modalLembur.querySelector('.transform');
            const btnTutupModalLembur = document.getElementById('btn-tutup-modal-lembur');
            const submitLemburBtn = document.getElementById('submit-button-lembur');
            const latitudeLemburInput = document.getElementById('latitude-lembur');
            const longitudeLemburInput = document.getElementById('longitude-lembur');
            const cameraContainerLembur = document.getElementById('camera-container-lembur');
            const uploadLabelLembur = document.getElementById('upload-label-lembur');
            const fileInputLembur = document.getElementById('lampiran-lembur');
            const videoLembur = document.getElementById('video-lembur');
            const canvasLembur = document.getElementById('canvas-lembur');
            const snapButtonLembur = document.getElementById('snap-lembur');
            const uploadUiLembur = document.getElementById('upload-ui-lembur');
            const keteranganLemburInput = document.getElementById('keterangan-lembur');
            
            let streamLembur;
            let isLocationReadyLembur = false;
            let isPhotoReadyLembur = false;

            function setSuccessUILembur(fileName) {
                uploadUiLembur.innerHTML = `<div class="flex flex-col items-center justify-center text-center p-2 w-full"><i class="fas fa-check-circle text-3xl text-green-500"></i><p class="mt-2 text-sm text-gray-700 font-semibold w-full truncate px-2" title="${fileName}">${fileName}</p><button type="button" id="change-photo-lembur-btn" class="mt-2 text-xs text-blue-600 hover:underline font-medium">Ganti</button></div>`;
                uploadLabelLembur.classList.replace('border-dashed', 'border-solid');
                uploadLabelLembur.classList.add('border-green-500', 'bg-green-50');
            }

            function resetUploadUILembur() {
                fileInputLembur.value = '';
                uploadUiLembur.innerHTML = `<i id="upload-icon-lembur" class="fas fa-camera text-3xl text-gray-400"></i><p id="upload-text-lembur" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>`;
                uploadLabelLembur.classList.remove('border-solid', 'border-green-500', 'bg-green-50', 'hidden');
                uploadLabelLembur.classList.add('border-dashed', 'border-gray-300');
            }
            
            function checkFormReadinessLembur() {
                if (isLocationReadyLembur && isPhotoReadyLembur && keteranganLemburInput.value.trim() !== '') {
                    submitLemburBtn.disabled = false;
                    submitLemburBtn.textContent = 'Kirim Absen Lembur';
                } else {
                    submitLemburBtn.disabled = true;
                    submitLemburBtn.textContent = 'Mohon Isi Data Lengkap';
                }
            }

            async function startCameraLembur() {
                try {
                    streamLembur = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                    videoLembur.srcObject = streamLembur;
                    videoLembur.onloadedmetadata = () => { snapButtonLembur.disabled = false; };
                    cameraContainerLembur.classList.remove('hidden');
                    uploadLabelLembur.classList.add('hidden');
                } catch (err) {
                    alert('Tidak bisa mengakses kamera untuk absen lembur.');
                    resetUploadUILembur();
                }
            }

            function stopCameraLembur() {
                if (streamLembur) { streamLembur.getTracks().forEach(track => track.stop()); }
                snapButtonLembur.disabled = true;
            }

            function getLocationLembur() {
                submitLemburBtn.textContent = 'Mencari Lokasi...';
                isLocationReadyLembur = false;
                checkFormReadinessLembur();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latitudeLemburInput.value = position.coords.latitude;
                            longitudeLemburInput.value = position.coords.longitude;
                            isLocationReadyLembur = true;
                            checkFormReadinessLembur();
                        },
                        () => {
                            alert('Gagal mendapatkan lokasi untuk absen lembur.');
                            isLocationReadyLembur = false;
                            checkFormReadinessLembur();
                        }, 
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                }
            }

            snapButtonLembur.addEventListener("click", function() {
                canvasLembur.width = videoLembur.videoWidth;
                canvasLembur.height = videoLembur.videoHeight;
                canvasLembur.getContext('2d').drawImage(videoLembur, 0, 0, canvasLembur.width, canvasLembur.height);
                canvasLembur.toBlob(function(blob) {
                    const file = new File([blob], "selfie_lembur_" + Date.now() + ".png", { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInputLembur.files = dataTransfer.files;
                    
                    stopCameraLembur();
                    cameraContainerLembur.classList.add('hidden');
                    uploadLabelLembur.classList.remove('hidden');
                    setSuccessUILembur(file.name);

                    isPhotoReadyLembur = true;
                    checkFormReadinessLembur();
                }, 'image/png');
            });

            keteranganLemburInput.addEventListener('input', checkFormReadinessLembur);

            uploadLabelLembur.addEventListener('click', function(e) {
                if (e.target.id === 'change-photo-lembur-btn') {
                    e.preventDefault();
                    e.stopPropagation();
                    isPhotoReadyLembur = false;
                    checkFormReadinessLembur();
                    cameraContainerLembur.classList.remove('hidden');
                    uploadLabelLembur.classList.add('hidden');
                    startCameraLembur();
                } else if (fileInputLembur.files.length === 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    cameraContainerLembur.classList.remove('hidden');
                    uploadLabelLembur.classList.add('hidden');
                    startCameraLembur();
                }
            });

            submitLemburBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData();
                formData.append('latitude_masuk', latitudeLemburInput.value);
                formData.append('longitude_masuk', longitudeLemburInput.value);
                formData.append('keterangan', keteranganLemburInput.value);
                if (fileInputLembur.files.length > 0) {
                    formData.append('lampiran_masuk', fileInputLembur.files[0]);
                }
                
                submitLemburBtn.disabled = true;
                submitLemburBtn.textContent = 'Memproses...';

                try {
                    const response = await fetch("{{ route('absen.lembur.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert(result.success);
                        closeModalLembur();
                        window.location.reload();
                    } else {
                        const errorMessages = Object.values(result.errors || {error: [result.error]}).flat().join('\n');
                        alert('Terjadi kesalahan:\n' + errorMessages);
                        submitLemburBtn.disabled = false;
                        submitLemburBtn.textContent = 'Kirim Absen Lembur';
                    }
                } catch (error) {
                    alert('Terjadi kesalahan pada koneksi atau server.');
                    submitLemburBtn.disabled = false;
                    submitLemburBtn.textContent = 'Kirim Absen Lembur';
                }
            });

            btnTutupModalLembur.addEventListener('click', function() {
                isLocationReadyLembur = false;
                isPhotoReadyLembur = false;
                resetUploadUILembur();
                keteranganLemburInput.value = '';
                checkFormReadinessLembur();
                closeModalLembur();
            });

            function openModalLembur() {
                modalLembur.classList.remove('hidden');
                modalLembur.classList.add('flex');
                setTimeout(() => modalContentLembur.classList.remove('scale-95', 'opacity-0'), 10);
                startCameraLembur();
                getLocationLembur();
            }

            function closeModalLembur() {
                modalContentLembur.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modalLembur.classList.add('hidden');
                    modalLembur.classList.remove('flex');
                    stopCameraLembur();
                }, 200);
            }

            if (btnAbsenLembur) btnAbsenLembur.addEventListener('click', openModalLembur);
            if (btnTutupModalLembur) btnTutupModalLembur.addEventListener('click', closeModalLembur);
        }

        // Keluar Lembur
        const btnKeluarLembur = document.getElementById('btn-absen-keluar-lembur');
        const modalKeluarLembur = document.getElementById('modal-keluar-lembur');
        if(modalKeluarLembur) {
            const lemburId = document.getElementById('lembur-data').dataset.id;

            const modalContentKeluarLembur = modalKeluarLembur.querySelector('.transform');
            const btnTutupModalKeluarLembur = document.getElementById('btn-tutup-modal-keluar-lembur');
            const submitKeluarLemburBtn = document.getElementById('submit-button-keluar-lembur');
            const latitudeKeluarLemburInput = document.getElementById('latitude-keluar-lembur');
            const longitudeKeluarLemburInput = document.getElementById('longitude-keluar-lembur');
            const cameraContainerKeluarLembur = document.getElementById('camera-container-keluar-lembur');
            const uploadLabelKeluarLembur = document.getElementById('upload-label-keluar-lembur');
            const fileInputKeluarLembur = document.getElementById('lampiran-keluar-lembur');
            const videoKeluarLembur = document.getElementById('video-keluar-lembur');
            const canvasKeluarLembur = document.getElementById('canvas-keluar-lembur');
            const snapButtonKeluarLembur = document.getElementById('snap-keluar-lembur');
            const uploadUiKeluarLembur = document.getElementById('upload-ui-keluar-lembur');
            
            let streamKeluarLembur;
            let isLocationReadyKeluarLembur = false;
            let isPhotoReadyKeluarLembur = false;

            function setSuccessUIKeluarLembur(fileName) {
                uploadUiKeluarLembur.innerHTML = `<div class="flex flex-col items-center justify-center text-center p-2 w-full"><i class="fas fa-check-circle text-3xl text-green-500"></i><p class="mt-2 text-sm text-gray-700 font-semibold w-full truncate px-2" title="${fileName}">${fileName}</p><button type="button" id="change-photo-keluar-lembur-btn" class="mt-2 text-xs text-blue-600 hover:underline font-medium">Ganti</button></div>`;
                uploadLabelKeluarLembur.classList.replace('border-dashed', 'border-solid');
                uploadLabelKeluarLembur.classList.add('border-green-500', 'bg-green-50');
            }

            function resetUploadUIKeluarLembur() {
                fileInputKeluarLembur.value = '';
                uploadUiKeluarLembur.innerHTML = `<i id="upload-icon-keluar-lembur" class="fas fa-camera text-3xl text-gray-400"></i><p id="upload-text-keluar-lembur" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Buka Kamera & Ambil Foto</span></p>`;
                uploadLabelKeluarLembur.classList.remove('border-solid', 'border-green-500', 'bg-green-50', 'hidden');
                uploadLabelKeluarLembur.classList.add('border-dashed', 'border-gray-300');
            }
            
            function checkFormReadinessKeluarLembur() {
                if (isLocationReadyKeluarLembur && isPhotoReadyKeluarLembur) {
                    submitKeluarLemburBtn.disabled = false;
                    submitKeluarLemburBtn.textContent = 'Kirim Absen Keluar Lembur';
                } else {
                    submitKeluarLemburBtn.disabled = true;
                    submitKeluarLemburBtn.textContent = 'Mohon Ambil Foto & Lokasi';
                }
            }

            async function startCameraKeluarLembur() {
                try {
                    streamKeluarLembur = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                    videoKeluarLembur.srcObject = streamKeluarLembur;
                    videoKeluarLembur.onloadedmetadata = () => { snapButtonKeluarLembur.disabled = false; };
                    cameraContainerKeluarLembur.classList.remove('hidden');
                    uploadLabelKeluarLembur.classList.add('hidden');
                } catch (err) {
                    alert('Tidak bisa mengakses kamera untuk absen keluar lembur.');
                    resetUploadUIKeluarLembur();
                }
            }

            function stopCameraKeluarLembur() {
                if (streamKeluarLembur) { streamKeluarLembur.getTracks().forEach(track => track.stop()); }
                snapButtonKeluarLembur.disabled = true;
            }

            function getLocationKeluarLembur() {
                submitKeluarLemburBtn.textContent = 'Mencari Lokasi...';
                isLocationReadyKeluarLembur = false;
                checkFormReadinessKeluarLembur();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latitudeKeluarLemburInput.value = position.coords.latitude;
                            longitudeKeluarLemburInput.value = position.coords.longitude;
                            isLocationReadyKeluarLembur = true;
                            checkFormReadinessKeluarLembur();
                        },
                        () => {
                            alert('Gagal mendapatkan lokasi untuk absen keluar lembur.');
                            isLocationReadyKeluarLembur = false;
                            checkFormReadinessKeluarLembur();
                        }, 
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                }
            }
            
            snapButtonKeluarLembur.addEventListener("click", function() {
                canvasKeluarLembur.width = videoKeluarLembur.videoWidth;
                canvasKeluarLembur.height = videoKeluarLembur.videoHeight;
                canvasKeluarLembur.getContext('2d').drawImage(videoKeluarLembur, 0, 0, canvasKeluarLembur.width, canvasKeluarLembur.height);
                canvasKeluarLembur.toBlob(function(blob) {
                    const file = new File([blob], "selfie_keluar_lembur_" + Date.now() + ".png", { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInputKeluarLembur.files = dataTransfer.files;
                    
                    stopCameraKeluarLembur();
                    cameraContainerKeluarLembur.classList.add('hidden');
                    uploadLabelKeluarLembur.classList.remove('hidden');
                    setSuccessUIKeluarLembur(file.name);

                    isPhotoReadyKeluarLembur = true;
                    checkFormReadinessKeluarLembur();
                }, 'image/png');
            });

            uploadLabelKeluarLembur.addEventListener('click', function(e) {
                if (e.target.id === 'change-photo-keluar-lembur-btn') {
                    e.preventDefault();
                    e.stopPropagation();
                    isPhotoReadyKeluarLembur = false;
                    checkFormReadinessKeluarLembur();
                    cameraContainerKeluarLembur.classList.remove('hidden');
                    uploadLabelKeluarLembur.classList.add('hidden');
                    startCameraKeluarLembur();
                } else if (fileInputKeluarLembur.files.length === 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    cameraContainerKeluarLembur.classList.remove('hidden');
                    uploadLabelKeluarLembur.classList.add('hidden');
                    startCameraKeluarLembur();
                }
            });

            submitKeluarLemburBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData();
                formData.append('_method', 'PATCH');
                formData.append('latitude_keluar', latitudeKeluarLemburInput.value);
                formData.append('longitude_keluar', longitudeKeluarLemburInput.value);
                if (fileInputKeluarLembur.files.length > 0) {
                    formData.append('lampiran_keluar', fileInputKeluarLembur.files[0]);
                }
                
                submitKeluarLemburBtn.disabled = true;
                submitKeluarLemburBtn.textContent = 'Memproses...';

                if (!lemburId) {
                     alert('ID lembur tidak ditemukan. Silakan refresh halaman.');
                     submitKeluarLemburBtn.disabled = false;
                     submitKeluarLemburBtn.textContent = 'Kirim Absen Keluar Lembur';
                     return;
                }
                const url = `/absen/lembur/keluar/${lemburId}`;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert(result.success);
                        closeModalKeluarLembur();
                        window.location.reload();
                    } else {
                        const errorMessages = Object.values(result.errors || {error: [result.error]}).flat().join('\n');
                        alert('Terjadi kesalahan:\n' + errorMessages);
                        submitKeluarLemburBtn.disabled = false;
                        submitKeluarLemburBtn.textContent = 'Kirim Absen Keluar Lembur';
                    }
                } catch (error) {
                    alert('Terjadi kesalahan pada koneksi atau server.');
                    submitKeluarLemburBtn.disabled = false;
                    submitKeluarLemburBtn.textContent = 'Kirim Absen Keluar Lembur';
                }
            });

            btnTutupModalKeluarLembur.addEventListener('click', function() {
                isLocationReadyKeluarLembur = false;
                isPhotoReadyKeluarLembur = false;
                resetUploadUIKeluarLembur();
                checkFormReadinessKeluarLembur();
                closeModalKeluarLembur();
            });

            function openModalKeluarLembur() {
                modalKeluarLembur.classList.remove('hidden');
                modalKeluarLembur.classList.add('flex');
                setTimeout(() => modalContentKeluarLembur.classList.remove('scale-95', 'opacity-0'), 10);
                startCameraKeluarLembur();
                getLocationKeluarLembur();
            }

            function closeModalKeluarLembur() {
                modalContentKeluarLembur.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modalKeluarLembur.classList.add('hidden');
                    modalKeluarLembur.classList.remove('flex');
                    stopCameraKeluarLembur();
                }, 200);
            }

            if (btnKeluarLembur) btnKeluarLembur.addEventListener('click', openModalKeluarLembur);
            if (btnTutupModalKeluarLembur) btnTutupModalKeluarLembur.addEventListener('click', closeModalKeluarLembur);
        }
    });
    </script>
    @endpush
</x-layout-users>