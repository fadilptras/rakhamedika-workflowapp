<x-layout-admin>
    <x-slot:title>Pengaturan Lokasi Absen</x-slot:title>

    {{-- CSS untuk Peta LeafletJS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map {
            height: 450px;
            border-radius: 0.5rem;
            z-index: 10; /* Pastikan peta tidak tertutup elemen lain */
        }
    </style>

    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-6">Pengaturan Lokasi Absensi Kantor</h1>

        {{-- Notifikasi Sukses atau Error --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-bold">Terjadi Kesalahan:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-zinc-800 rounded-lg shadow-lg border border-zinc-700 p-6">
            <form action="{{ route('admin.lokasi.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Kolom Kiri: Form Input --}}
                    <div class="flex flex-col gap-y-4">
                        <div>
                            <label for="nama_lokasi" class="block text-sm font-medium text-zinc-300 mb-1">Nama Lokasi</label>
                            <input type="text" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}"
                                   class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" placeholder="Contoh: Kantor Pusat" required>
                        </div>

                        <div>
                            <label for="lokasi_url" class="block text-sm font-medium text-zinc-300 mb-1">Link Google Maps</label>
                            <input type="text" id="lokasi_url" name="lokasi_url"
                                   class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" 
                                   placeholder="Tempel link dari Google Maps di sini">
                            <p id="url_feedback" class="text-xs text-zinc-400 mt-2">Tempel link dari Google Maps untuk mengisi Latitude & Longitude secara otomatis.</p>
                        </div>

                        <div>
                            <label for="latitude" class="block text-sm font-medium text-zinc-300 mb-1">Latitude</label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $lokasi->latitude) }}"
                                   class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-3 py-2 text-zinc-400" placeholder="-6.1753924" required readonly>
                        </div>
                        
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-zinc-300 mb-1">Longitude</label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $lokasi->longitude) }}"
                                   class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-3 py-2 text-zinc-400" placeholder="106.8271528" required readonly>
                        </div>

                        <div>
                            <label for="radius_meter" class="block text-sm font-medium text-zinc-300 mb-1">Radius Absen (meter)</label>
                            <input type="number" id="radius_meter" name="radius_meter" value="{{ old('radius_meter', $lokasi->radius_meter ?? 50) }}"
                                   class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" min="10" required>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Peta --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Visualisasi Lokasi & Radius</label>
                        <div id="map"></div>
                         <p class="text-xs text-zinc-400 mt-2">Anda bisa klik/geser pin di peta untuk menentukan lokasi.</p>
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="flex justify-end mt-6 border-t border-zinc-700 pt-6">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-transform duration-200 hover:scale-105">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- JS untuk Peta LeafletJS & Logika Form --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen-elemen form
            const latInput = document.getElementById('latitude');
            const lonInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius_meter');
            const urlInput = document.getElementById('lokasi_url');
            const feedbackElem = document.getElementById('url_feedback');

            // Tentukan koordinat awal (dari database atau default Jakarta)
            const initialLat = parseFloat(latInput.value) || -6.175392;
            const initialLon = parseFloat(lonInput.value) || 106.827152;
            const initialRadius = parseInt(radiusInput.value) || 50;

            // Inisialisasi peta
            const map = L.map('map').setView([initialLat, initialLon], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Buat penanda (marker) yang bisa digeser
            let marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);
            
            // Buat lingkaran radius
            let circle = L.circle([initialLat, initialLon], {
                color: '#3b82f6',
                fillColor: '#60a5fa',
                fillOpacity: 0.3,
                radius: initialRadius
            }).addTo(map);

            // --- FUNGSI-FUNGSI ---
            function updateFormAndMap(lat, lon, rad) {
                // Update nilai input
                latInput.value = lat.toFixed(6);
                lonInput.value = lon.toFixed(6);
                
                // Update posisi di peta
                const latLng = [lat, lon];
                marker.setLatLng(latLng);
                circle.setLatLng(latLng);
                circle.setRadius(rad);
                map.panTo(latLng);
            }
            
            function parseGoogleMapsUrl(url) {
                const patterns = [
                    /@(-?\d+\.\d+),(-?\d+\.\d+)/,      // Format @lat,lng
                    /!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/, // Format !3d lat !4d lng
                    /1d(-?\d+\.\d+)!2d(-?\d+\.\d+)/     // Format 1d lat !2d lng dari embed
                ];
                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match) {
                        return { latitude: parseFloat(match[1]), longitude: parseFloat(match[2]) };
                    }
                }
                return null;
            }

            // --- EVENT LISTENERS ---
            // Saat marker selesai digeser
            marker.on('dragend', function(e) {
                const { lat, lng } = marker.getLatLng();
                updateFormAndMap(lat, lng, parseInt(radiusInput.value));
            });

            // Saat peta diklik
            map.on('click', function(e) {
                const { lat, lng } = e.latlng;
                updateFormAndMap(lat, lng, parseInt(radiusInput.value));
            });

            // Saat nilai radius diubah
            radiusInput.addEventListener('input', function() {
                const newRadius = parseInt(this.value, 10) || 0;
                circle.setRadius(newRadius);
            });
            
            // Saat input URL Google Maps diisi
            urlInput.addEventListener('input', function() {
                const coords = parseGoogleMapsUrl(this.value);
                if (coords) {
                    updateFormAndMap(coords.latitude, coords.longitude, parseInt(radiusInput.value));
                    feedbackElem.textContent = 'Koordinat berhasil diekstrak!';
                    feedbackElem.className = 'text-xs text-green-400 mt-2';
                } else {
                    feedbackElem.textContent = 'Link tidak valid atau format tidak dikenali.';
                    feedbackElem.className = 'text-xs text-red-400 mt-2';
                }
            });
        });
    </script>
    @endpush
</x-layout-admin>