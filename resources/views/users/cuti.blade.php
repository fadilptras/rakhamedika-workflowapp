<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    {{-- Ikon (Font Awesome) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- CSS Khusus untuk Radio Button --}}
    <style>
        .custom-radio-label {
            transition: all 0.2s ease-in-out;
        }
        .custom-radio:checked + .custom-radio-label {
            border-color: #3b82f6; /* blue-500 */
            background-color: #eff6ff; /* blue-50 */
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .custom-radio:checked + .custom-radio-label .radio-circle {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .custom-radio:checked + .custom-radio-label .radio-circle::after {
            transform: scale(1);
        }
        .radio-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db; /* gray-300 */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
        }
        .radio-circle::after {
            content: '';
            width: 10px;
            height: 10px;
            background-color: white;
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.2s ease-in-out;
        }
    </style>

    <div class="bg-gray-50 p-4 md:p-8 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- FORM UTAMA --}}
                <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-2/3">
                    @csrf
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm space-y-7">
                        <h2 class="text-2xl font-bold text-gray-900">Detail Pengajuan Cuti</h2>

                        {{-- Menampilkan pesan error validasi global --}}
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

                        {{-- JENIS CUTI --}}
                        <div>
                            <label class="block text-md font-semibold text-gray-800 mb-3">Jenis Cuti</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <input type="radio" name="jenis_cuti" id="cuti_tahunan" value="tahunan" class="hidden custom-radio" {{ old('jenis_cuti', 'tahunan') == 'tahunan' ? 'checked' : '' }}>
                                    <label for="cuti_tahunan" class="custom-radio-label flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer">
                                        <i class="fas fa-plane-departure text-xl text-sky-500 mr-4"></i>
                                        <span class="font-semibold text-gray-800 flex-1">Cuti Tahunan</span>
                                        <div class="radio-circle"></div>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" name="jenis_cuti" id="cuti_sakit" value="sakit" class="hidden custom-radio" {{ old('jenis_cuti') == 'sakit' ? 'checked' : '' }}>
                                    <label for="cuti_sakit" class="custom-radio-label flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer">
                                        <i class="fas fa-briefcase-medical text-xl text-red-500 mr-4"></i>
                                        <span class="font-semibold text-gray-800 flex-1">Cuti Sakit</span>
                                        <div class="radio-circle"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        {{-- PILIH TANGGAL --}}
                        <div>
                            <label class="block text-md font-semibold text-gray-800 mb-3">Pilih Tanggal</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('tanggal_mulai') border-red-500 @enderror">
                                </div>
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-600 mb-1">Tanggal Selesai</label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('tanggal_selesai') border-red-500 @enderror">
                                </div>
                            </div>
                        </div>

                        {{-- ALASAN CUTI --}}
                        <div>
                             <label for="alasan" class="block text-md font-semibold text-gray-800 mb-3">Alasan Cuti</label>
                             <textarea id="alasan" name="alasan" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('alasan') border-red-500 @enderror" placeholder="Jelaskan alasan Anda mengajukan cuti...">{{ old('alasan') }}</textarea>
                        </div>

                        {{-- LAMPIRAN --}}
                        <div>
                            <label class="block text-md font-semibold text-gray-800 mb-3">Lampiran <span class="text-sm text-gray-500 font-normal">(jika ada)</span></label>
                            <label for="lampiran" id="upload-label" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition @error('lampiran') border-red-500 @enderror">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center" id="upload-default-view">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                    <p class="mt-2 text-sm text-gray-500"><span class="font-semibold">Klik atau seret file</span></p>
                                    <p class="text-xs text-gray-500">Contoh: Surat dokter (PDF, JPG)</p>
                                </div>
                                <div class="hidden flex-col items-center justify-center text-center" id="upload-success-view">
                                     <i class="fas fa-check-circle text-3xl text-green-500"></i>
                                     <p class="mt-2 text-sm text-gray-700 font-semibold" id="filename"></p>
                                </div>
                                <input id="lampiran" name="lampiran" type="file" class="hidden" />
                            </label>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Ajukan Cuti Sekarang
                            </button>
                        </div>
                    </div>
                </form>

                {{-- SIDEBAR KANAN --}}
                <div class="w-full lg:w-1/3 space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Sisa Cuti Anda</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-1">
                                    <span class="text-gray-700">Tahunan</span>
                                    <span class="text-gray-500">{{ $sisaCuti['tahunan'] ?? 0 }} / {{ $totalCuti['tahunan'] ?? 12 }} hari</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    @php
                                        $persenTahunan = (($totalCuti['tahunan'] ?? 12) > 0) ? (($sisaCuti['tahunan'] ?? 0) / ($totalCuti['tahunan'] ?? 12)) * 100 : 0;
                                    @endphp
                                    <div class="bg-sky-500 h-2.5 rounded-full" style="width: {{ $persenTahunan }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-1">
                                    <span class="text-gray-700">Sakit</span>
                                    <span class="text-gray-500">{{ $sisaCuti['sakit'] ?? 0 }} / {{ $totalCuti['sakit'] ?? 5 }} hari</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                     @php
                                        $persenSakit = (($totalCuti['sakit'] ?? 5) > 0) ? (($sisaCuti['sakit'] ?? 0) / ($totalCuti['sakit'] ?? 5)) * 100 : 0;
                                    @endphp
                                    <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $persenSakit }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pengajuan</h3>
                        <div class="border border-dashed border-gray-300 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Total Hari</span>
                                <span id="total-hari" class="font-bold text-lg text-gray-800">- Hari</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Dari</span>
                                <span id="ringkasan-mulai" class="font-semibold text-gray-700">-</span>
                            </div>
                             <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Sampai</span>
                                <span id="ringkasan-selesai" class="font-semibold text-gray-700">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                         <h3 class="text-lg font-bold text-gray-800 mb-4">Alur Persetujuan</h3>
                         <ul class="space-y-4">
                             <li class="flex items-center gap-4">
                                 <div class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full font-bold">1</div>
                                 <span class="font-semibold text-gray-700">Manager</span>
                                 <span class="ml-auto text-xs bg-gray-200 text-gray-600 font-medium px-2 py-1 rounded-full">Menunggu</span>
                             </li>
                              <li class="flex items-center gap-4 opacity-50">
                                 <div class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-500 rounded-full font-bold">2</div>
                                 <span class="font-semibold text-gray-500">HR Department</span>
                                 <span class="ml-auto text-xs bg-gray-200 text-gray-600 font-medium px-2 py-1 rounded-full">Terkunci</span>
                             </li>
                         </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tglMulai = document.getElementById('tanggal_mulai');
        const tglSelesai = document.getElementById('tanggal_selesai');
        const totalHariElem = document.getElementById('total-hari');
        const ringkasanMulaiElem = document.getElementById('ringkasan-mulai');
        const ringkasanSelesaiElem = document.getElementById('ringkasan-selesai');
        const lampiranInput = document.getElementById('lampiran');
        const uploadLabel = document.getElementById('upload-label');
        const uploadDefaultView = document.getElementById('upload-default-view');
        const uploadSuccessView = document.getElementById('upload-success-view');
        const filenameElem = document.getElementById('filename');

        function formatTanggal(tanggalStr) {
            if (!tanggalStr) return '-';
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            const date = new Date(tanggalStr + 'T00:00:00');
            return date.toLocaleDateString('id-ID', options);
        }

        function hitungDurasi() {
            const startDate = tglMulai.value;
            const endDate = tglSelesai.value;

            ringkasanMulaiElem.textContent = formatTanggal(startDate);
            ringkasanSelesaiElem.textContent = formatTanggal(endDate);

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                if (end < start) {
                    totalHariElem.textContent = 'Invalid';
                    totalHariElem.classList.add('text-red-500');
                    return;
                }
                
                totalHariElem.classList.remove('text-red-500');
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                totalHariElem.textContent = `${diffDays} Hari`;
            } else {
                totalHariElem.textContent = '- Hari';
            }
        }

        function handleFileUpload() {
            if (lampiranInput.files && lampiranInput.files.length > 0) {
                const file = lampiranInput.files[0];
                filenameElem.textContent = file.name;
                uploadDefaultView.classList.add('hidden');
                uploadSuccessView.classList.remove('hidden');
                uploadLabel.classList.remove('border-dashed', 'border-gray-300');
                uploadLabel.classList.add('border-solid', 'border-green-500', 'bg-green-50');
            }
        }

        tglMulai.addEventListener('change', hitungDurasi);
        tglSelesai.addEventListener('change', hitungDurasi);
        lampiranInput.addEventListener('change', handleFileUpload);

        // Panggil fungsi sekali saat load untuk mengisi ringkasan jika ada old-value
        hitungDurasi();
    });
    </script>
    @endpush
</x-layout-users>