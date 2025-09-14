<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- CSS untuk Peta & Styling Tambahan --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map-masuk, #map-keluar { height: 250px; border-radius: 0.5rem; z-index: 1000; }
        .modal { transition: opacity 0.3s ease; }
    </style>

    <div class="p-4 md:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            
            {{-- ======================= PERUBAHAN DI SINI ======================= --}}
            {{-- Header dan Tombol Kembali --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Riwayat Absensi Saya</h2>
                    <p class="text-gray-500">Lihat kembali catatan kehadiran Anda per periode.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline font-semibold">Kembali ke Dashboard</a>
            </div>
            {{-- ===================== AKHIR PERUBAHAN ===================== --}}

            
            {{-- Form Filter --}}
            <div class="bg-white p-4 rounded-xl shadow-md mb-6">
                <form method="GET" action="{{ route('rekap_absen.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select name="bulan" id="bulan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                @foreach($daftarBulan as $num => $nama)
                                    <option value="{{ $num }}" {{ $num == $bulanDipilih ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select name="tahun" id="tahun" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                @foreach($daftarTahun as $tahun)
                                    <option value="{{ $tahun }}" {{ $tahun == $tahunDipilih ? 'selected' : '' }}>{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1 flex gap-2">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg flex items-center justify-center">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Kartu Rekap --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-center flex flex-col items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-500 text-3xl mb-2"></i>
                    <p class="text-3xl font-extrabold text-emerald-600">{{ $rekap['hadir'] }}</p>
                    <p class="text-sm font-semibold text-emerald-800 mt-1">Hadir</p>
                </div>
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center flex flex-col items-center justify-center">
                    <i class="fas fa-notes-medical text-red-500 text-3xl mb-2"></i>
                    <p class="text-3xl font-extrabold text-red-600">{{ $rekap['sakit'] }}</p>
                    <p class="text-sm font-semibold text-red-800 mt-1">Sakit</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-center flex flex-col items-center justify-center">
                    <i class="fas fa-file-alt text-amber-500 text-3xl mb-2"></i>
                    <p class="text-3xl font-extrabold text-amber-600">{{ $rekap['izin'] }}</p>
                    <p class="text-sm font-semibold text-amber-800 mt-1">Izin</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg text-center flex flex-col items-center justify-center">
                    <i class="fas fa-plane-departure text-purple-500 text-3xl mb-2"></i>
                    <p class="text-3xl font-extrabold text-purple-600">{{ $rekap['cuti'] }}</p>
                    <p class="text-sm font-semibold text-purple-800 mt-1">Cuti</p>
                </div>
            </div>

            {{-- Tabel Detail Absensi (Sama seperti sebelumnya, tidak perlu diubah) --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                         <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Masuk</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Keluar</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Jam</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($absensi as $item)
                                @php
                                    $jamMasuk = $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk) : null;
                                    $jamKeluar = $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar) : null;
                                    $totalJam = $jamMasuk && $jamKeluar ? $jamMasuk->diff($jamKeluar)->format('%H:%I:%S') : '-';
                                    $keteranganTerlambat = $item->status == 'hadir' && $jamMasuk && $jamMasuk->format('H:i:s') > '08:00:00'
                                        ? '<span class="text-red-600 font-semibold">(Terlambat)</span> ' . ($item->keterangan ?: '')
                                        : ($item->keterangan ?: '-');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize
                                            @if($item->status == 'hadir') bg-green-100 text-green-800
                                            @elseif($item->status == 'sakit') bg-red-100 text-red-800
                                            @elseif($item->status == 'izin') bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center font-mono">{{ $item->jam_masuk ? $jamMasuk->format('H:i:s') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center font-mono">{{ $item->jam_keluar ? $jamKeluar->format('H:i:s') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center font-mono">{{ $totalJam }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{!! $keteranganTerlambat !!}</td>
                                    <td class="py-3 px-4 text-center">
                                        <button class="text-indigo-600 hover:text-indigo-800 font-semibold" onclick="showDetailModal({{ json_encode($item) }})">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center text-gray-500">
                                        <i class="fas fa-box-open text-4xl mb-3"></i>
                                        <p class="font-semibold">Tidak ada data absensi</p>
                                        <p class="text-sm">Silakan pilih periode lain.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="detailModal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4 opacity-0" onclick="hideDetailModal()">
        <div class="bg-white rounded-lg w-full max-w-2xl shadow-xl transform transition-transform duration-300 scale-95" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-xl font-bold text-gray-800">Detail Absensi - <span id="modal-tanggal"></span></h3>
                <button class="text-gray-500 hover:text-gray-800" onclick="hideDetailModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">Informasi Umum</h4>
                        <dl class="text-sm">
                            <dt class="font-semibold text-gray-500">Status</dt>
                            <dd class="mb-2" id="modal-status"></dd>
                            <dt class="font-semibold text-gray-500">Keterangan Masuk</dt>
                            <dd class="mb-2" id="modal-keterangan-masuk"></dd>
                            <dt class="font-semibold text-gray-500">Keterangan Keluar</dt>
                            <dd class="mb-2" id="modal-keterangan-keluar"></dd>
                        </dl>
                        <h4 class="font-bold text-gray-700 mt-4 mb-2">Lampiran</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1">Masuk/Izin/Sakit</p>
                                <img id="modal-lampiran-masuk" src="" class="w-full h-auto rounded-lg object-cover cursor-pointer hidden" onclick="showImage(this.src)">
                                <p id="no-lampiran-masuk" class="text-xs text-gray-400">Tidak ada lampiran.</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1">Keluar</p>
                                <img id="modal-lampiran-keluar" src="" class="w-full h-auto rounded-lg object-cover cursor-pointer hidden" onclick="showImage(this.src)">
                                <p id="no-lampiran-keluar" class="text-xs text-gray-400">Tidak ada lampiran.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">Rekam Lokasi</h4>
                        <div id="map-masuk-container" class="hidden">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Lokasi Masuk</p>
                            <div id="map-masuk"></div>
                        </div>
                        <div id="map-keluar-container" class="mt-4 hidden">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Lokasi Keluar</p>
                            <div id="map-keluar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="image-viewer" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center hidden z-[100]" onclick="this.classList.add('hidden')">
        <img src="" class="max-w-[90vw] max-h-[90vh] rounded-lg">
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @push('scripts')
    <script>
        const modal = document.getElementById('detailModal');
        let mapMasuk, mapKeluar;
        let markerMasuk, markerKeluar;

        function initializeMap(containerId) {
            return L.map(containerId, { zoomControl: false }).setView([-6.200000, 106.816666], 15);
        }

        function showDetailModal(data) {
            document.getElementById('modal-tanggal').textContent = new Date(data.tanggal).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            let statusBadge = `<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize
                ${data.status === 'hadir' ? 'bg-green-100 text-green-800' : ''}
                ${data.status === 'sakit' ? 'bg-red-100 text-red-800' : ''}
                ${data.status === 'izin' ? 'bg-yellow-100 text-yellow-800' : ''}
            ">${data.status}</span>`;
            document.getElementById('modal-status').innerHTML = statusBadge;

            document.getElementById('modal-keterangan-masuk').textContent = data.keterangan || '-';
            document.getElementById('modal-keterangan-keluar').textContent = data.keterangan_keluar || '-';
            
            const lampiranMasukImg = document.getElementById('modal-lampiran-masuk');
            const noLampiranMasuk = document.getElementById('no-lampiran-masuk');
            if (data.lampiran) {
                lampiranMasukImg.src = `/storage/${data.lampiran}`;
                lampiranMasukImg.style.display = 'block';
                noLampiranMasuk.style.display = 'none';
            } else {
                lampiranMasukImg.style.display = 'none';
                noLampiranMasuk.style.display = 'block';
            }

            const lampiranKeluarImg = document.getElementById('modal-lampiran-keluar');
            const noLampiranKeluar = document.getElementById('no-lampiran-keluar');
             if (data.lampiran_keluar) {
                lampiranKeluarImg.src = `/storage/${data.lampiran_keluar}`;
                lampiranKeluarImg.style.display = 'block';
                noLampiranKeluar.style.display = 'none';
            } else {
                lampiranKeluarImg.style.display = 'none';
                noLampiranKeluar.style.display = 'block';
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
                
                const mapMasukContainer = document.getElementById('map-masuk-container');
                if (data.latitude && data.longitude) {
                    mapMasukContainer.style.display = 'block';
                    if (!mapMasuk) {
                        mapMasuk = initializeMap('map-masuk');
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapMasuk);
                    }
                    const latLngMasuk = [data.latitude, data.longitude];
                    mapMasuk.setView(latLngMasuk, 16);
                    if (markerMasuk) markerMasuk.setLatLng(latLngMasuk);
                    else markerMasuk = L.marker(latLngMasuk).addTo(mapMasuk);
                    setTimeout(() => mapMasuk.invalidateSize(), 200);
                } else {
                    mapMasukContainer.style.display = 'none';
                }

                const mapKeluarContainer = document.getElementById('map-keluar-container');
                if (data.latitude_keluar && data.longitude_keluar) {
                    mapKeluarContainer.style.display = 'block';
                    if (!mapKeluar) {
                        mapKeluar = initializeMap('map-keluar');
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapKeluar);
                    }
                    const latLngKeluar = [data.latitude_keluar, data.longitude_keluar];
                    mapKeluar.setView(latLngKeluar, 16);
                    if (markerKeluar) markerKeluar.setLatLng(latLngKeluar);
                    else markerKeluar = L.marker(latLngKeluar).addTo(mapKeluar);
                    setTimeout(() => mapKeluar.invalidateSize(), 200);
                } else {
                    mapKeluarContainer.style.display = 'none';
                }
            }, 50);
        }

        function hideDetailModal() {
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function showImage(src) {
            const viewer = document.getElementById('image-viewer');
            viewer.querySelector('img').src = src;
            viewer.classList.remove('hidden');
        }
    </script>
    @endpush
</x-layout-users>