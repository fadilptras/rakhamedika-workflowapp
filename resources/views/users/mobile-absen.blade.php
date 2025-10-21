<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Container Utama dengan Padding Minimal di Mobile --}}
    <div class="bg-gray-100 p-3 min-h-screen">
        <div class="max-w-lg mx-auto">
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-md mb-3 text-sm" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                 <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md mb-3 text-sm" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md mb-3 text-sm" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

            @if ($isWeekend)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-4" role="alert">
                    <p class="font-bold">Akhir Pekan</p>
                    <p>Hari ini adalah hari libur.</p>
                </div>
            @endif

            {{-- TAMPILAN JIKA PENGGUNA SUDAH ABSEN --}}
            @if ($absensiHariIni)
                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Absensi Hari Ini</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                Status: <span class="font-semibold capitalize">{{ $absensiHariIni->status }}</span>
                            </p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="text-xs text-blue-600 font-semibold">Dashboard</a>
                    </div>
                    <div class="mt-4 border-t pt-4">
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Absen Masuk --}}
                            <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                                <p class="font-semibold text-emerald-800 text-sm">Masuk</p>
                                <p class="text-2xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</p>
                                @if($absensiHariIni->latitude && $absensiHariIni->longitude)
                                    <a href="https://maps.google.com/?q={{ $absensiHariIni->latitude }},{{ $absensiHariIni->longitude }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Lihat Lokasi</a>
                                @endif
                            </div>
                            {{-- Absen Keluar --}}
                            <div class="bg-rose-50 p-3 rounded-lg border border-rose-200 flex flex-col justify-between">
                                <div>
                                    <p class="font-semibold text-rose-800 text-sm">Keluar</p>
                                    @if ($absensiHariIni->jam_keluar)
                                        <p class="text-2xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }}</p>
                                        @if($absensiHariIni->latitude_keluar && $absensiHariIni->longitude_keluar)
                                            <a href="https://maps.google.com/?q={{ $absensiHariIni->latitude_keluar }},{{ $absensiHariIni->longitude_keluar }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Lihat Lokasi</a>
                                        @endif
                                    @else
                                        <p class="text-2xl font-bold text-gray-400">--:--</p>
                                    @endif
                                </div>
                                @if (is_null($absensiHariIni->jam_keluar) && $absensiHariIni->status == 'hadir')
                                    <button type="button" id="btn-absen-keluar" class="w-full mt-2 bg-red-600 text-white font-bold py-2 px-3 rounded-lg hover:bg-red-700 transition text-xs">
                                        Absen Keluar
                                    </button>
                                @endif
                            </div>
                        </div>
                         {{-- Logic Lembur --}}
                        @if ($absensiHariIni->jam_keluar && $absensiHariIni->status == 'hadir')
                            @if (is_null($lemburHariIni))
                                <button type="button" id="btn-absen-lembur" class="w-full mt-4 bg-purple-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-purple-700 transition text-sm">
                                    Absen Lembur
                                </button>
                            @elseif (is_null($lemburHariIni->jam_keluar_lembur))
                                <button type="button" id="btn-absen-keluar-lembur" class="w-full mt-4 bg-red-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-red-700 transition text-sm">
                                    Selesaikan Lembur
                                </button>
                            @else
                                 <div class="mt-4 p-3 rounded-lg bg-green-100 text-green-700 font-semibold text-center text-sm">
                                     Lembur hari ini selesai.
                                 </div>
                            @endif
                        @endif
                    </div>
                </div>

            {{-- TAMPILAN JIKA ADA ABSEN KEMARIN YANG BELUM SELESAI --}}
            @elseif (isset($unfinishedAbsensi) && $unfinishedAbsensi)
                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800">Absen Belum Selesai</h2>
                    <p class="text-sm text-gray-600 mt-1">Anda belum absen keluar pada tanggal <strong>{{ \Carbon\Carbon::parse($unfinishedAbsensi->tanggal)->translatedFormat('j F Y') }}</strong>.</p>
                    <div class="mt-4 border-t pt-4">
                        <div class="bg-yellow-50 p-3 rounded-lg text-center mb-3">
                            <p class="font-semibold text-yellow-800 text-sm">Masuk (Kemarin)</p>
                            <p class="text-2xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($unfinishedAbsensi->jam_masuk)->format('H:i') }}</p>
                        </div>
                        <button type="button" id="btn-absen-keluar-unfinished" data-id="{{ $unfinishedAbsensi->id }}" class="w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700 transition">
                            Selesaikan Absen Keluar
                        </button>
                    </div>
                </div>

            {{-- TAMPILAN JIKA BELUM ABSEN --}}
            @else
                <form action="{{ route('absen.store') }}" method="POST" enctype="multipart/form-data" id="form-absen">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="status" id="status" value="hadir">
                    <input name="lampiran" id="lampiran" type="file" class="hidden" accept="image/*,application/pdf"/>

                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        {{-- KAMERA --}}
                        <div id="camera-section">
                            <div id="camera-container" class="relative w-full aspect-[4/5] bg-gray-900 flex items-center justify-center text-white">
                                <video id="video" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay playsinline></video>
                                <canvas id="canvas" class="hidden"></canvas>
                                <div id="snap-ui" class="absolute inset-0 flex items-end justify-center p-4 bg-gradient-to-t from-black/50 to-transparent">
                                    <button type="button" id="snap" class="bg-white text-blue-600 rounded-full h-16 w-16 flex items-center justify-center text-2xl border-4 border-blue-300 shadow-lg disabled:bg-gray-400" disabled>
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                                <div id="preview-ui" class="absolute inset-0 hidden">
                                    <img id="preview-image" src="" class="w-full h-full object-cover" alt="Pratinjau"/>
                                    <div class="absolute inset-0 flex items-end justify-center p-4 gap-4 bg-black/60">
                                        <button type="button" id="retake-btn" class="bg-white/90 text-red-600 font-semibold py-2 px-4 rounded-full text-sm">Ambil Ulang</button>
                                        <button type="button" id="use-photo-btn" class="bg-blue-600 text-white font-semibold py-2 px-5 rounded-full text-sm">Gunakan Foto</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KONTEN FORM --}}
                        <div class="p-4">
                            <div class="grid grid-cols-3 gap-2 bg-gray-200 p-1 rounded-full" id="status-buttons">
                                <button type="button" data-status="hadir" class="status-btn text-sm font-semibold py-2 rounded-full transition-all">Hadir</button>
                                <button type="button" data-status="izin" class="status-btn text-sm font-semibold py-2 rounded-full transition-all">Izin</button>
                                <button type="button" data-status="sakit" class="status-btn text-sm font-semibold py-2 rounded-full transition-all">Sakit</button>
                            </div>

                            <div id="form-content" class="mt-4">
                                {{-- KONTEN UNTUK IZIN & SAKIT --}}
                                <div id="izin-sakit-content" class="hidden space-y-4">
                                    <div>
                                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                                            Keterangan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="keterangan" id="keterangan" rows="3" class="w-full text-sm p-2 border-gray-300 rounded-lg shadow-sm" placeholder="Jelaskan alasan Anda..."></textarea>
                                    </div>
                                    <label for="lampiran" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center text-center p-2">
                                            <i class="fas fa-paperclip text-2xl text-gray-400"></i>
                                            <p id="upload-text" class="mt-2 text-xs text-gray-500">Sertakan Surat Dokter / Bukti Lain</p>
                                        </div>
                                    </label>
                                </div>
                                {{-- KONTEN UNTUK HADIR --}}
                                <div id="hadir-content">
                                    <div class="text-center">
                                        <p class="font-bold text-2xl text-gray-800" id="jam-realtime">--:--:--</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL SUBMIT STICKY --}}
                    <div class="fixed bottom-0 left-0 right-0 p-3 bg-white/80 backdrop-blur-sm border-t border-gray-200 z-10">
                        <div class="max-w-lg mx-auto">
                            <button type="submit" id="submit-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-all disabled:bg-gray-400 disabled:shadow-none">
                                Kirim Absensi
                            </button>
                        </div>
                    </div>
                </form>
            @endif

            {{-- KARTU REKAP & TIM --}}
            <div class="space-y-4 mt-4 @if(!$absensiHariIni && !isset($unfinishedAbsensi)) pb-24 @endif">
                {{-- REKAP --}}
                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <h2 class="text-lg font-bold text-gray-800 text-center mb-3">Rekap Bulan Ini</h2>
                    <div class="grid grid-cols-3 gap-2">
                         @foreach(['hadir', 'sakit', 'izin'] as $status)
                            @php $color = ['hadir' => 'green', 'sakit' => 'red', 'izin' => 'yellow'][$status]; @endphp
                            <div class="bg-{{$color}}-50 p-2 rounded-lg text-center">
                                <p class="font-semibold text-xs text-{{$color}}-700 capitalize">{{$status}}</p>
                                <div class="text-lg font-bold text-{{$color}}-700">{{ $rekapAbsen[$status] ?? 0 }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                 {{-- TIM --}}
                 @if(isset($daftarRekan) && count($daftarRekan) > 0)
                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <h2 class="text-lg font-bold text-gray-800 text-center mb-3">Absensi Tim</h2>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                            @foreach($daftarRekan as $rekan)
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded-lg">
                                <div class="flex items-center truncate">
                                    <img src="{{ $rekan->user->profile_picture ? asset('storage/' . $rekan->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($rekan->user->name ?? 'U').'&background=random&color=fff&size=32' }}" alt="{{ $rekan->user->name ?? '' }}" class="w-8 h-8 rounded-full object-cover mr-3 flex-shrink-0">
                                    <p class="font-semibold text-gray-700 text-sm truncate">{{ $rekan->user->name }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full capitalize flex-shrink-0
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
    </div>
    
    {{-- ========================================================================= --}}
    {{-- KODE SEMUA MODAL DITEMPEL LANGSUNG DI SINI --}}
    {{-- ========================================================================= --}}

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
                            <video id="video-keluar" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay playsinline></video>
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
                            <video id="video-lembur" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay playsinline></video>
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
                            <video id="video-keluar-lembur" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay playsinline></video>
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
    
    {{-- MODAL UNTUK ABSEN KELUAR YANG BELUM SELESAI --}}
    @if (isset($unfinishedAbsensi) && $unfinishedAbsensi)
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
                            <video id="video-keluar-unfinished" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay playsinline></video>
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
    @endif

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- FUNGSI JAM REALTIME ---
        const jamElement = document.getElementById('jam-realtime');
        if(jamElement) {
            const updateJam = () => jamElement.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            setInterval(updateJam, 1000);
            updateJam();
        }

        // =========================================================================
        // LOGIKA GENERIC UNTUK KAMERA & MODAL
        // =========================================================================
        window.cameraInstances = {};
        function setupCameraLogic(prefix) {
            // ... (Fungsi setupCameraLogic dari file absen.blade.php Anda)
        }
        function setupModalLogic(btnId, modalId, prefix) {
            // ... (Fungsi setupModalLogic dari file absen.blade.php Anda)
        }
        
        // Inisialisasi semua kamera
        setupCameraLogic('');
        setupCameraLogic('-keluar');
        setupCameraLogic('-keluar-unfinished');
        setupCameraLogic('-lembur');
        setupCameraLogic('-keluar-lembur');

        // Inisialisasi semua modal
        setupModalLogic('btn-absen-keluar', 'modal-absen-keluar', '-keluar');
        setupModalLogic('btn-absen-keluar-unfinished', 'modal-absen-keluar-unfinished', '-keluar-unfinished');
        setupModalLogic('btn-absen-lembur', 'modal-absen-lembur', '-lembur');
        setupModalLogic('btn-absen-keluar-lembur', 'modal-keluar-lembur', '-keluar-lembur');


        // =========================================================================
        // LOGIKA UTAMA FORM ABSENSI MASUK (MOBILE VIEW)
        // =========================================================================
        const formAbsen = document.getElementById('form-absen');
        if (formAbsen) {
            const hiddenStatusInput = document.getElementById('status');
            const submitButton = document.getElementById('submit-button');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const cameraSection = document.getElementById('camera-section');
            const izinSakitContent = document.getElementById('izin-sakit-content');
            const hadirContent = document.getElementById('hadir-content');
            const keteranganInput = document.getElementById('keterangan');
            const lampiranInput = document.getElementById('lampiran');
            const uploadText = document.getElementById('upload-text');

            let isLocationReady = false;
            let isPhotoReady = false;

            document.addEventListener('photoReady', e => {
                if (e.detail.isReady) { isPhotoReady = true; }
                checkFormReadiness();
            });
            
            lampiranInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    uploadText.textContent = this.files[0].name;
                    uploadText.classList.add('text-blue-600', 'font-semibold');
                } else {
                    uploadText.textContent = 'Sertakan Surat Dokter / Bukti Lain';
                    uploadText.classList.remove('text-blue-600', 'font-semibold');
                }
                checkFormReadiness();
            });
            
            keteranganInput.addEventListener('input', checkFormReadiness);

            const checkFormReadiness = () => {
                const status = hiddenStatusInput.value;
                if (status === 'hadir') {
                    submitButton.disabled = !(isLocationReady && isPhotoReady);
                } else {
                    const isKeteranganFilled = keteranganInput.value.trim() !== '';
                    const isLampiranFilled = lampiranInput.files.length > 0;
                    submitButton.disabled = !(isKeteranganFilled || isLampiranFilled);
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
                    () => { alert('Gagal dapat lokasi. Izinkan akses lokasi pada browser Anda.'); isLocationReady = false; checkFormReadiness(); },
                    { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
                );
            };

            const setActiveButton = (status) => {
                document.querySelectorAll('.status-btn').forEach(btn => btn.classList.remove('bg-blue-600', 'text-white', 'shadow'));
                const activeButton = document.querySelector(`.status-btn[data-status="${status}"]`);
                if (activeButton) activeButton.classList.add('bg-blue-600', 'text-white', 'shadow');
            };

            const toggleUiForStatus = (status) => {
                isPhotoReady = false;
                lampiranInput.value = '';
                keteranganInput.value = '';
                uploadText.textContent = 'Sertakan Surat Dokter / Bukti Lain';
                uploadText.classList.remove('text-blue-600', 'font-semibold');

                if (status === 'hadir') {
                    cameraSection.style.display = 'block';
                    hadirContent.style.display = 'block';
                    izinSakitContent.style.display = 'none';
                    if (window.cameraInstances['']) window.cameraInstances[''].startCamera();
                    getLocation();
                } else {
                    if (window.cameraInstances['']) window.cameraInstances[''].stopCamera();
                    cameraSection.style.display = 'none';
                    hadirContent.style.display = 'none';
                    izinSakitContent.style.display = 'block';
                }
                checkFormReadiness();
            };

            document.getElementById('status-buttons').addEventListener('click', function(e) {
                const targetButton = e.target.closest('.status-btn');
                if (targetButton) {
                    const selectedStatus = targetButton.dataset.status;
                    hiddenStatusInput.value = selectedStatus;
                    setActiveButton(selectedStatus);
                    toggleUiForStatus(selectedStatus);
                }
            });

            // INISIALISASI SAAT HALAMAN DIBUKA
            setActiveButton('hadir');
            toggleUiForStatus('hadir');
        }
    });
    </script>
    @endpush
</x-layout-users>