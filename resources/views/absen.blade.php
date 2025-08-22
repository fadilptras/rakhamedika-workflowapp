<x-layout-dash>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-gray-50 p-4 md:p-8 min-h-screen">
        <div class="max-w-6xl mx-auto">

            {{-- JIKA USER SUDAH ABSEN HARI INI, TAMPILKAN INI --}}
            @if ($absensiHariIni)
                <div class="bg-white p-8 rounded-xl shadow-sm text-center">
                    <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Anda Sudah Absen Hari Ini</h2>
                    <p class="text-gray-600 mt-2">
                        Status: <span class="font-semibold capitalize">{{ $absensiHariIni->status }}</span>
                        pada jam {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                    </p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition">Kembali ke Dashboard</a>
                </div>
            @else
            {{-- JIKA BELUM ABSEN, TAMPILKAN FORM INI --}}
            <form action="{{ route('absen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col lg:flex-row gap-8">

                    <div class="w-full lg:w-2/3 bg-white p-6 rounded-xl shadow-sm space-y-6">

                        {{-- Menampilkan pesan sukses atau error dari controller --}}
                        @if (session('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif
                        @if (session('error'))
                             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                                 <p>{{ session('error') }}</p>
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
                            {{-- Input tersembunyi untuk menyimpan status yang dipilih --}}
                            <input type="hidden" name="status" id="status" value="hadir">
                            @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            <div class="grid grid-cols-3 gap-3 mt-2" id="status-buttons">
                                <button type="button" data-status="hadir" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Hadir</button>
                                <button type="button" data-status="izin" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Izin</button>
                                <button type="button" data-status="sakit" class="status-btn border font-semibold py-3 rounded-lg transition-all duration-200">Sakit</button>
                            </div>
                        </div>

                        <div>
                            <label for="keterangan" class="block text-md font-medium text-gray-700 mb-3">Keterangan & Lampiran (Wajib jika Izin/Sakit)</label>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div class="md:col-span-3">
                                    <textarea name="keterangan" id="keterangan" rows="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror" placeholder="Contoh: Ada keperluan keluarga.">{{ old('keterangan') }}</textarea>
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

                    <div class="w-full lg:w-1/3 bg-white p-6 rounded-xl shadow-sm">
                        <h2 class="text-xl font-bold text-gray-800 text-center mb-4">Rekap Kehadiran</h2>
                        <div class="text-center text-gray-500 text-sm">(Fitur rekap akan datang)</div>
                        <div class="mt-4 h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-4xl text-gray-300"></i>
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
            // Logika untuk jam realtime
            const jamElement = document.getElementById('jam-realtime');
            function updateJam() {
                const now = new Date();
                const jam = String(now.getHours()).padStart(2, '0');
                const menit = String(now.getMinutes()).padStart(2, '0');
                const detik = String(now.getSeconds()).padStart(2, '0');
                if(jamElement) {
                    jamElement.textContent = `${jam}:${menit}:${detik} WIB`;
                }
            }
            setInterval(updateJam, 1000);
            updateJam();

            // Logika untuk tombol status
            const statusButtonsContainer = document.getElementById('status-buttons');
            const hiddenStatusInput = document.getElementById('status');
            const selectedStyles = {
                hadir: 'bg-emerald-500 text-white border-emerald-500',
                izin: 'bg-amber-500 text-white border-amber-500',
                sakit: 'bg-red-500 text-white border-red-500'
            };
            function setActiveButton(status) {
                document.querySelectorAll('.status-btn').forEach(btn => {
                    btn.classList.remove(...Object.values(selectedStyles).join(' ').split(' '));
                });
                const activeButton = document.querySelector(`.status-btn[data-status="${status}"]`);
                if (activeButton) {
                    activeButton.classList.add(...selectedStyles[status].split(' '));
                }
            }
            setActiveButton(hiddenStatusInput.value);
            if(statusButtonsContainer) {
                statusButtonsContainer.addEventListener('click', function(e) {
                    if (e.target.matches('.status-btn')) {
                        const selectedStatus = e.target.dataset.status;
                        hiddenStatusInput.value = selectedStatus;
                        setActiveButton(selectedStatus);
                    }
                });
            }

            // [KODE BARU] LOGIKA NOTIFIKASI UPLOAD LAMPIRAN
            const fileInput = document.getElementById('lampiran');
            const uploadLabel = document.getElementById('upload-label');
            const uploadIcon = document.getElementById('upload-icon');
            const uploadText = document.getElementById('upload-text');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    // Cek apakah ada file yang dipilih
                    if (this.files && this.files.length > 0) {
                        const fileName = this.files[0].name;
                        // 1. Ubah teks untuk menampilkan nama file
                        uploadText.textContent = fileName;
                        // 2. Ubah ikon menjadi centang dan beri warna hijau
                        uploadIcon.className = 'fas fa-check-circle text-3xl text-green-500';
                        // 3. Ubah style kotak (border dan background) menjadi hijau
                        uploadLabel.classList.remove('border-dashed', 'bg-gray-50', 'hover:bg-gray-100');
                        uploadLabel.classList.add('border-solid', 'border-green-500', 'bg-green-50');
                    }
                });
            }
        });
    </script>
    @endpush
</x-layout-dash>