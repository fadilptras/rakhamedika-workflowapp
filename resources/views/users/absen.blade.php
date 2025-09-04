<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-gray-50 p-4 md:p-8 min-h-screen">
        <div class="max-w-6xl mx-auto">

            @if ($absensiHariIni)
                {{-- TAMPILAN JIKA SUDAH ABSEN --}}
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Anda Sudah Absen Hari Ini</h2>
                            <p class="text-gray-600 mt-1">
                                Status Kehadiran: <span class="font-semibold capitalize">{{ $absensiHariIni->status }}</span>
                            </p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="mt-4 md:mt-0 text-blue-600 hover:underline font-semibold">Kembali ke Dashboard</a>
                    </div>

                    {{-- Timeline Absensi --}}
                    <div class="mt-6 border-t pt-6">
                        <h3 class="font-bold text-lg text-gray-700 mb-4">Riwayat Absensi Hari Ini</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-full h-10 w-10 flex items-center justify-center text-white">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="font-semibold text-gray-800">Absen Masuk</p>
                                    <p class="text-sm text-gray-500">Jam: {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }} WIB</p>
                                </div>
                            </div>
                            
                            @if ($absensiHariIni->jam_keluar)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-red-500 rounded-full h-10 w-10 flex items-center justify-center text-white">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-semibold text-gray-800">Absen Keluar</p>
                                        <p class="text-sm text-gray-500">Jam: {{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }} WIB</p>
                                    </div>
                                </div>
                            @elseif($absensiHariIni->status == 'hadir')
                                <div class="flex items-center">
                                     <div class="flex-shrink-0 bg-gray-300 rounded-full h-10 w-10 flex items-center justify-center text-gray-600">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-semibold text-gray-500">Belum Absen Keluar</p>
                                        <button type="button" id="btn-absen-keluar" class="text-sm text-blue-600 hover:underline">Klik di sini untuk absen keluar</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- MODAL UNTUK ABSEN KELUAR --}}
                @if (is_null($absensiHariIni->jam_keluar) && $absensiHariIni->status == 'hadir')
                <div id="modal-absen-keluar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
                        <form action="{{ route('absen.keluar', $absensiHariIni) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800">Form Absen Keluar</h3>
                                <p class="text-gray-500 mt-1">Isi keterangan pekerjaan Anda hari ini sebelum pulang.</p>
                                <div class="mt-6">
                                    <label for="keterangan_keluar" class="block text-md font-medium text-gray-700 mb-2">Keterangan Pulang <span class="text-red-500">*</span></label>
                                    <textarea name="keterangan_keluar" id="keterangan_keluar" rows="5" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('keterangan_keluar') border-red-500 @enderror" placeholder="Contoh: Menyelesaikan laporan penjualan bulan Agustus." required>{{ old('keterangan_keluar') }}</textarea>
                                    @error('keterangan_keluar') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                                <button type="button" id="btn-tutup-modal" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Batal</button>
                                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700">Kirim Absen Keluar</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif


            @else
                {{-- TAMPILAN JIKA BELUM ABSEN --}}
                <form action="{{ route('absen.store') }}" method="POST" enctype="multipart/form-data" id="form-absen">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="flex flex-col lg:flex-row gap-8">
                        {{-- KOLOM KIRI: FORM ABSEN MASUK --}}
                        <div class="w-full lg:w-2/3 bg-white p-6 rounded-xl shadow-sm space-y-6">
                            @if (session('success'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p>{{ session('success') }}</p></div>
                            @endif
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
                                    Keterangan & Lampiran <span id="keterangan-wajib" class="text-red-500 font-normal hidden">* (wajib diisi salah satu)</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div class="md:col-span-3">
                                        <textarea name="keterangan" id="keterangan" rows="5" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Ada keperluan keluarga.">{{ old('keterangan') }}</textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <div id="camera-container" class="hidden relative aspect-video rounded-lg overflow-hidden">
                                            <video id="video" class="w-full h-full object-cover" autoplay></video>
                                            <canvas id="canvas" class="hidden"></canvas>
                                            
                                            <div class="absolute inset-0 flex items-end justify-center p-4">
                                                <button type="button" id="snap" class="bg-blue-600 text-white rounded-full h-12 w-12 flex items-center justify-center text-xl border-4 border-white shadow-lg disabled:bg-gray-400" disabled>
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                       <label for="lampiran" id="upload-label" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition min-h-[150px]">
                                            <div class="flex flex-col items-center justify-center text-center p-2" id="upload-ui">
                                                <i id="upload-icon" class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                                <p id="upload-text" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Upload Lampiran</span></p>
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

                        {{-- KOLOM KANAN: REKAP BULANAN --}}
                        <div class="w-full lg:w-1/3 bg-white p-6 rounded-xl shadow-sm">
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
                             </div>
                         </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- LOGIKA JAM REALTIME ---
        const jamElement = document.getElementById('jam-realtime');
        if(jamElement) {
            function updateJam() {
                const now = new Date();
                const jam = String(now.getHours()).padStart(2, '0');
                const menit = String(now.getMinutes()).padStart(2, '0');
                const detik = String(now.getSeconds()).padStart(2, '0');
                jamElement.textContent = `${jam}:${menit}:${detik} WIB`;
            }
            setInterval(updateJam, 1000);
            updateJam();
        }

        const formAbsen = document.getElementById('form-absen');
        if (formAbsen) {
            // --- UI Elements ---
            const hiddenStatusInput = document.getElementById('status');
            const keteranganWajibSpan = document.getElementById('keterangan-wajib');
            const keteranganTextarea = document.getElementById('keterangan');
            const uploadLabel = document.getElementById('upload-label');
            const fileInput = document.getElementById('lampiran');
            const submitButton = document.getElementById('submit-button');
            const cameraContainer = document.getElementById('camera-container');
            const uploadUI = document.getElementById('upload-ui');

            // --- Camera Elements ---
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const snapButton = document.getElementById('snap');
            
            // --- Geolocation Elements ---
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            let stream;

            // --- FUNCTIONS ---
            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                    video.srcObject = stream;
                    video.onloadedmetadata = () => { snapButton.disabled = false; };
                    getLocation();
                } catch (err) {
                    console.error("Error accessing camera: ", err);
                    alert('Tidak bisa mengakses kamera. Pastikan Anda memberikan izin pada browser.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Absensi';
                }
            }

            function stopCamera() {
                if (stream) { stream.getTracks().forEach(track => track.stop()); }
                snapButton.disabled = true;
                cameraContainer.classList.add('hidden');
                uploadLabel.classList.remove('hidden');
            }

            function getLocation() {
                submitButton.disabled = true;
                submitButton.textContent = 'Mencari Lokasi...';
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latitudeInput.value = position.coords.latitude;
                            longitudeInput.value = position.coords.longitude;
                            submitButton.disabled = false;
                            submitButton.textContent = 'Kirim Absensi';
                        },
                        () => {
                            alert('Tidak bisa mendapatkan lokasi. Pastikan GPS Anda aktif dan berikan izin pada browser.');
                            submitButton.disabled = false;
                            submitButton.textContent = 'Kirim Absensi';
                        },
                        { timeout: 10000, enableHighAccuracy: true }
                    );
                } else {
                    alert("Geolocation tidak didukung oleh browser ini.");
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Absensi';
                }
            }
            
            function setSuccessUI(fileName) {
                uploadUI.innerHTML = `
                    <div class="flex flex-col items-center justify-center text-center p-2 w-full">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                        <p class="mt-2 text-sm text-gray-700 font-semibold w-full truncate px-2" title="${fileName}">${fileName}</p>
                        <button type="button" id="change-photo-btn" class="mt-2 text-xs text-blue-600 hover:underline font-medium">Ganti Foto/File</button>
                    </div>`;
                uploadLabel.classList.replace('border-dashed', 'border-solid');
                uploadLabel.classList.add('border-green-500', 'bg-green-50');
            }
            
            function resetUploadUI() {
                fileInput.value = '';
                uploadUI.innerHTML = `<i id="upload-icon" class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i><p id="upload-text" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Upload Lampiran</span></p>`;
                uploadLabel.classList.remove('border-solid', 'border-green-500', 'bg-green-50');
                uploadLabel.classList.add('border-dashed', 'border-gray-300');
            }

            const selectedStyles = {
                hadir: 'bg-emerald-500 text-white border-emerald-500',
                izin: 'bg-amber-500 text-white border-amber-500',
                sakit: 'bg-red-500 text-white border-red-500'
            };

            function toggleUiForStatus(status) {
                resetUploadUI();
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
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Absensi';
                    stopCamera();
                }
            }

            function setActiveButton(status) {
                document.querySelectorAll('.status-btn').forEach(btn => btn.classList.remove(...Object.values(selectedStyles).join(' ').split(' ')));
                const activeButton = document.querySelector(`.status-btn[data-status="${status}"]`);
                if (activeButton) activeButton.classList.add(...selectedStyles[status].split(' '));
            }
            
            // --- EVENT LISTENERS ---
            setActiveButton(hiddenStatusInput.value);
            toggleUiForStatus(hiddenStatusInput.value);

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
                    const file = new File([blob], "absensi_" + Date.now() + ".png", { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    setSuccessUI(file.name);
                    stopCamera();
                }, 'image/png');
            });

            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                     setSuccessUI(this.files[0].name);
                }
            });

            uploadLabel.addEventListener('click', function(e) {
                // Hanya jalankan jika tombol ganti foto yang di-klik
                if (e.target.id === 'change-photo-btn') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (hiddenStatusInput.value === 'hadir') {
                        // Jika status hadir, buka lagi kamera
                        toggleUiForStatus('hadir');
                    } else {
                        // Jika status lain, reset UI agar bisa upload file lagi
                        resetUploadUI();
                    }
                }
            });
        }

        // --- LOGIKA MODAL ABSEN KELUAR ---
        const btnAbsenKeluar = document.getElementById('btn-absen-keluar');
        const modal = document.getElementById('modal-absen-keluar');
        if (modal) {
            const modalContent = modal.querySelector('.transform');
            const btnTutupModal = document.getElementById('btn-tutup-modal');
            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modalContent.classList.remove('scale-95', 'opacity-0'), 10);
            }
            function closeModal() {
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 200);
            }
            if (btnAbsenKeluar) btnAbsenKeluar.addEventListener('click', openModal);
            if (btnTutupModal) btnTutupModal.addEventListener('click', closeModal);
        }
    });
    </script>
    @endpush
</x-layout-users>