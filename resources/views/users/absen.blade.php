<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-gray-50 p-4 md:p-8 min-h-screen">
        <div class="max-w-6xl mx-auto">

            @if ($absensiHariIni)
                {{-- TAMPILAN JIKA SUDAH ABSEN MASUK --}}
                <div class="bg-white p-8 rounded-xl shadow-sm text-center">
                    <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Anda Sudah Absen Hari Ini</h2>
                    <p class="text-gray-600 mt-2">
                        Status: <span class="font-semibold capitalize">{{ $absensiHariIni->status }}</span>
                        pada jam {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                    </p>

                    {{-- Kondisi untuk menampilkan info atau tombol absen keluar --}}
                    @if ($absensiHariIni->jam_keluar)
                        <p class="text-gray-600 mt-1">
                            Anda sudah absen keluar pada jam {{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }}.
                        </p>
                    @elseif ($absensiHariIni->status == 'hadir')
                        {{-- Tombol untuk memunculkan modal --}}
                        <button type="button" id="btn-absen-keluar" class="mt-4 inline-block bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition">Absen Keluar</button>
                    @endif

                    <a href="{{ route('dashboard') }}" class="mt-6 block text-blue-600 hover:underline">Kembali ke Dashboard</a>
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
                <form action="{{ route('absen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col lg:flex-row gap-8">
                        {{-- KOLOM KIRI: FORM ABSEN MASUK --}}
                        <div class="w-full lg:w-2/3 bg-white p-6 rounded-xl shadow-sm space-y-6">
                            @if (session('success'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p>{{ session('success') }}</p></div>
                            @endif
                            @if (session('error'))
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert"><p>{{ session('error') }}</p></div>
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
                                @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
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
                                        <textarea name="keterangan" id="keterangan" rows="5" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all @error('keterangan') border-red-500 @enderror" placeholder="Contoh: Ada keperluan keluarga.">{{ old('keterangan') }}</textarea>
                                        @error('keterangan') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                       <label for="lampiran" id="upload-label" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition @error('lampiran') border-red-500 @enderror">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                                <i id="upload-icon" class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                                <p id="upload-text" class="mt-2 text-sm text-gray-500"><span class="font-semibold">Upload Lampiran</span></p>
                                            </div>
                                            <input name="lampiran" id="lampiran" type="file" class="hidden" />
                                        </label>
                                        @error('lampiran') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">Kirim Absensi</button>
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

            // --- LOGIKA FORM ABSEN MASUK ---
            const statusButtonsContainer = document.getElementById('status-buttons');
            if (statusButtonsContainer) {
                const hiddenStatusInput = document.getElementById('status');
                const keteranganWajibSpan = document.getElementById('keterangan-wajib');
                const keteranganTextarea = document.getElementById('keterangan');
                const selectedStyles = {
                    hadir: 'bg-emerald-500 text-white border-emerald-500',
                    izin: 'bg-amber-500 text-white border-amber-500',
                    sakit: 'bg-red-500 text-white border-red-500'
                };

                function toggleKeteranganUI(status) {
                    if (status === 'hadir') {
                        keteranganWajibSpan.classList.add('hidden');
                        keteranganTextarea.disabled = true;
                        keteranganTextarea.value = '';
                        keteranganTextarea.placeholder = 'Keterangan tidak diperlukan untuk status Hadir.';
                        keteranganTextarea.classList.add('bg-gray-100');
                    } else {
                        keteranganWajibSpan.classList.remove('hidden');
                        keteranganTextarea.disabled = false;
                        keteranganTextarea.placeholder = 'Contoh: Ada keperluan keluarga.';
                        keteranganTextarea.classList.remove('bg-gray-100');
                    }
                }

                function setActiveButton(status) {
                    document.querySelectorAll('.status-btn').forEach(btn => btn.classList.remove(...Object.values(selectedStyles).join(' ').split(' ')));
                    const activeButton = document.querySelector(`.status-btn[data-status="${status}"]`);
                    if (activeButton) activeButton.classList.add(...selectedStyles[status].split(' '));
                }

                setActiveButton(hiddenStatusInput.value);
                toggleKeteranganUI(hiddenStatusInput.value);

                statusButtonsContainer.addEventListener('click', function(e) {
                    if (e.target.matches('.status-btn')) {
                        const selectedStatus = e.target.dataset.status;
                        hiddenStatusInput.value = selectedStatus;
                        setActiveButton(selectedStatus);
						toggleKeteranganUI(selectedStatus);
                    }
                });
            }

            // --- LOGIKA NOTIFIKASI UPLOAD LAMPIRAN ---
            const fileInput = document.getElementById('lampiran');
            if(fileInput) {
                const uploadLabel = document.getElementById('upload-label');
                const uploadIcon = document.getElementById('upload-icon');
                const uploadText = document.getElementById('upload-text');
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        uploadText.textContent = this.files[0].name;
                        uploadIcon.className = 'fas fa-check-circle text-3xl text-green-500';
                        uploadLabel.classList.remove('border-dashed', 'bg-gray-50', 'hover:bg-gray-100');
                        uploadLabel.classList.add('border-solid', 'border-green-500', 'bg-green-50');
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
                    setTimeout(() => {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                    }, 10);
                }

                function closeModal() {
                    modalContent.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 200); // Durasi transisi
                }

                if (btnAbsenKeluar) btnAbsenKeluar.addEventListener('click', openModal);
                if (btnTutupModal) btnTutupModal.addEventListener('click', closeModal);
            }
        });
    </script>
    @endpush
</x-layout-users>

