<x-layout-admin>
    <x-slot:title>Kelola Lokasi Absen</x-slot:title>

    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-6">Manajemen Lokasi Absensi Kantor</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Terjadi Kesalahan:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-zinc-800 rounded-lg shadow-lg border border-zinc-700 p-6 mb-8">
            <h2 class="text-xl font-bold text-white mb-4">Tambahkan Lokasi Baru</h2>
            <form action="{{ route('admin.lokasi.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="lokasi_url" class="block text-sm font-medium text-zinc-300">Link Google Maps</label>
                        <input type="text" id="lokasi_url" name="lokasi_url" value="{{ old('lokasi_url') }}"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" 
                               placeholder="Contoh: https://maps.app.goo.gl/..." required>
                        <p id="url_feedback" class="text-xs text-zinc-400 mt-1">Salin link dari Google Maps, pastikan pin lokasi sudah benar.</p>
                    </div>
                    <div>
                        <label for="nama_lokasi" class="block text-sm font-medium text-zinc-300">Nama Lokasi</label>
                        <input type="text" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi') }}"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div>
                        <label for="radius_meter" class="block text-sm font-medium text-zinc-300">Radius Absen (meter)</label>
                        <input type="number" id="radius_meter" name="radius_meter" value="{{ old('radius_meter', 50) }}"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    {{-- Input tersembunyi untuk menyimpan latitude dan longitude --}}
                    <input type="hidden" id="latitude" name="latitude" required>
                    <input type="hidden" id="longitude" name="longitude" required>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" id="simpan_btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform duration-200 hover:scale-105" disabled>
                        Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
        
        {{-- Tabel Lokasi --}}
        <div class="bg-zinc-800 rounded-lg shadow-lg border border-zinc-700 overflow-hidden">
            <table class="min-w-full text-sm text-left text-zinc-300">
                <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                    <tr>
                        <th class="px-4 py-3">Nama Lokasi</th>
                        <th class="px-4 py-3">Koordinat</th>
                        <th class="px-4 py-3">Radius</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse ($lokasi as $l)
                        <tr class="hover:bg-zinc-700/30">
                            <td class="px-4 py-3">{{ $l->nama }}</td>
                            <td class="px-4 py-3">{{ $l->latitude }}, {{ $l->longitude }}</td>
                            <td class="px-4 py-3">{{ $l->radius }} m</td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" class="edit-btn text-amber-400 hover:text-amber-300 underline font-medium mr-4"
                                    data-id="{{ $l->id }}"
                                    data-nama="{{ $l->nama }}"
                                    data-latitude="{{ $l->latitude }}"
                                    data-longitude="{{ $l->longitude }}"
                                    data-radius="{{ $l->radius }}">
                                    Edit
                                </button>
                                <form action="{{ route('admin.lokasi.destroy', $l->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus lokasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 underline font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-zinc-400">Belum ada lokasi yang diatur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Modal Edit Lokasi --}}
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-zinc-800 rounded-lg w-full max-w-lg p-6 shadow-lg border border-zinc-700">
            <h2 class="text-xl font-bold mb-6 text-white">Edit Lokasi Absen</h2>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit-nama_lokasi" class="block text-sm font-medium text-zinc-300">Nama Lokasi</label>
                        <input type="text" id="edit-nama_lokasi" name="nama_lokasi"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div>
                        <label for="edit-latitude" class="block text-sm font-medium text-zinc-300">Latitude</label>
                        <input type="text" id="edit-latitude" name="latitude"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div>
                        <label for="edit-longitude" class="block text-sm font-medium text-zinc-300">Longitude</label>
                        <input type="text" id="edit-longitude" name="longitude"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                    <div>
                        <label for="edit-radius_meter" class="block text-sm font-medium text-zinc-300">Radius Absen (meter)</label>
                        <input type="number" id="edit-radius_meter" name="radius_meter"
                               class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white" required>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="close-edit-modal" class="bg-zinc-600 hover:bg-zinc-500 text-white px-4 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Update Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</x-layout-admin>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk form tambah lokasi
        const urlInput = document.getElementById('lokasi_url');
        const latInput = document.getElementById('latitude');
        const longInput = document.getElementById('longitude');
        const simpanBtn = document.getElementById('simpan_btn');
        const feedbackElem = document.getElementById('url_feedback');

        function parseGoogleMapsUrl(url) {
            let match;
            // Regex untuk format URL panjang dengan @lat,long
            let regex1 = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
            match = url.match(regex1);
            if (match) {
                return { latitude: match[1], longitude: match[2] };
            }
            // Regex untuk format URL dengan parameter !3d dan !4d
            let regex2 = /!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/;
            match = url.match(regex2);
            if (match) {
                return { latitude: match[1], longitude: match[2] };
            }
            return null;
        }

        function handleUrlInput() {
            const url = urlInput.value.trim();
            const coordinates = parseGoogleMapsUrl(url);

            if (coordinates) {
                latInput.value = coordinates.latitude;
                longInput.value = coordinates.longitude;
                simpanBtn.disabled = false;
                simpanBtn.textContent = 'Simpan Lokasi';
                feedbackElem.textContent = `Link valid. Koordinat: ${coordinates.latitude}, ${coordinates.longitude}`;
                feedbackElem.classList.remove('text-red-400');
                feedbackElem.classList.add('text-green-400');
            } else {
                latInput.value = '';
                longInput.value = '';
                simpanBtn.disabled = true;
                simpanBtn.textContent = 'Link Tidak Valid';
                feedbackElem.textContent = 'Link tidak valid atau tidak mengandung koordinat. Mohon tempelkan link dari Google Maps yang benar.';
                feedbackElem.classList.remove('text-green-400');
                feedbackElem.classList.add('text-red-400');
            }
        }

        urlInput.addEventListener('input', handleUrlInput);
        handleUrlInput();

        // Logika untuk modal edit dan tombol edit
        const editModal = document.getElementById('edit-modal');
        const editForm = document.getElementById('edit-form');
        const editNama = document.getElementById('edit-nama_lokasi');
        const editLat = document.getElementById('edit-latitude');
        const editLong = document.getElementById('edit-longitude');
        const editRadius = document.getElementById('edit-radius_meter');
        const closeEditModalBtn = document.getElementById('close-edit-modal');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const latitude = this.dataset.latitude;
                const longitude = this.dataset.longitude;
                const radius = this.dataset.radius;

                editForm.action = `/admin/lokasi/${id}`;
                editNama.value = nama;
                editLat.value = latitude;
                editLong.value = longitude;
                editRadius.value = radius;

                editModal.classList.remove('hidden');
            });
        });

        closeEditModalBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });
    });
</script>
@endpush
