<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    {{-- Ikon (Font Awesome) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bg-gray-50 p-4 md:p-8 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- ======================= PERUBAHAN DI SINI ======================= --}}
            {{-- HEADER HALAMAN DAN TOMBOL-TOMBOL --}}
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                {{-- Tombol Kembali ke Dashboard yang lebih keren --}}
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-gray-200 rounded-lg shadow-sm transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                
                {{-- Tombol Pengajuan Baru --}}
                <a href="#form-pengajuan" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Ajukan Cuti Baru
                </a>
            </div>
            {{-- ===================== AKHIR PERUBAHAN ===================== --}}


            {{-- RIWAYAT PENGAJUAN CUTI --}}
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Riwayat Pengajuan Cuti</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi Cuti</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($cutiRequests as $cuti)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}
                                    <span class="text-gray-500">({{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($cuti->status == 'disetujui') bg-green-100 text-green-800
                                        @elseif($cuti->status == 'ditolak') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($cuti->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('cuti.show', $cuti) }}" class="text-blue-600 hover:underline">Lihat Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada riwayat pengajuan cuti.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FORMULIR PENGAJUAN CUTI --}}
            <div id="form-pengajuan" class="flex flex-col lg:flex-row gap-8">
                <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-2/3">
                    @csrf
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm space-y-7">
                        <h2 class="text-2xl font-bold text-gray-900">Formulir Pengajuan Cuti Tahunan</h2>

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
                        
                        {{-- Input Jenis Cuti (tersembunyi karena hanya ada satu) --}}
                        <input type="hidden" name="jenis_cuti" value="tahunan">

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

                        <div>
                             <label for="alasan" class="block text-md font-semibold text-gray-800 mb-3">Alasan Cuti</label>
                             <textarea id="alasan" name="alasan" rows="4" class="w-full p-3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('alasan') border-red-500 @enderror" placeholder="Jelaskan alasan Anda mengajukan cuti...">{{ old('alasan') }}</textarea>
                        </div>

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