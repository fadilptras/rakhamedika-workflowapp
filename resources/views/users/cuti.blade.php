<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Ikon (Font Awesome) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bg-gray-100 p-0 md:p-0 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="flex items-right">
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                </a>
            </div>

            {{-- Mengubah container utama menjadi grid untuk semua elemen --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- 1. Kartu Sisa Cuti (Urutan 1 di Mobile, Urutan 2 di Desktop) --}}
                <div class="order-1 lg:order-2 lg:col-span-2 bg-gradient-to-r from-blue-700 to-blue-600 text-white p-6 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-200">Sisa Cuti Tahunan</p>
                            <p class="text-4xl font-extrabold tracking-tight">
                                {{-- Hapus ['tahunan'] dan fallback-nya bisa disederhanakan --}}
                                {{ $sisaCuti ?? 0 }} <span class="text-2xl font-semibold text-indigo-300">/ {{ $totalCuti ?? 0 }} Hari</span>
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="fas fa-chart-pie text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- 2. Form Pengajuan Cuti (Urutan 2 di Mobile, Urutan 1 di Desktop) --}}
                <div class="order-2 lg:order-1 lg:col-span-3 lg:row-span-2 bg-white p-6 md:p-8 rounded-2xl shadow-xl border border-gray-200 flex flex-col">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Ajukan Cuti Baru</h3>
                    <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-grow space-y-6">
                        @csrf
                        <input type="hidden" name="jenis_cuti" value="tahunan">

                        <div class="flex-grow space-y-6">
                            {{-- Input Tanggal --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_mulai') border-red-500 @enderror">
                                </div>
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-600 mb-1">Tanggal Selesai</label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_selesai') border-red-500 @enderror">
                                </div>
                            </div>
                            
                            {{-- Input Alasan --}}
                            <div>
                                <label for="alasan" class="block text-sm font-medium text-gray-600 mb-1">Alasan</label>
                                <textarea id="alasan" name="alasan" rows="4" class="w-full p-3 bg-gray-100 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alasan') border-red-500 @enderror" placeholder="Jelaskan alasan Anda mengajukan cuti...">{{ old('alasan') }}</textarea>
                            </div>
                        </div>

                        {{-- Tombol Kirim --}}
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-800 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 transform hover:-translate-y-1">
                                <i class="fas fa-paper-plane"></i>
                                Kirim Pengajuan
                            </button>
                        </div>

                        @if ($errors->any())
                            <div class="!mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md text-sm" role="alert">
                                <p class="font-bold">Terjadi Kesalahan</p>
                                <ul class="list-disc list-inside ml-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>

                {{-- 3. Kartu Ringkasan (Urutan 3 di Mobile, Urutan 3 di Desktop) --}}
                <div class="order-3 lg:order-3 lg:col-span-2 bg-white p-6 rounded-2xl shadow-xl border border-gray-200 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan</h3>
                    <div class="space-y-4 flex-grow flex flex-col justify-center">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-day fa-lg text-gray-400"></i>
                                <p class="ml-3 text-sm font-medium text-gray-600">Mulai Cuti</p>
                            </div>
                            <p id="ringkasan-mulai" class="font-bold text-sm text-gray-800">-</p>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check fa-lg text-gray-400"></i>
                                <p class="ml-3 text-sm font-medium text-gray-600">Selesai Cuti</p>
                            </div>
                            <p id="ringkasan-selesai" class="font-bold text-sm text-gray-800">-</p>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-hourglass-half fa-lg text-gray-400"></i>
                                <p class="ml-3 text-sm font-medium text-gray-600">Total Durasi</p>
                            </div>
                            <p id="total-hari" class="font-bold text-sm text-gray-800">- Hari</p>
                        </div>
                    </div>
                </div>

                {{-- 4. Riwayat Pengajuan Cuti (Urutan 4 di Mobile & Desktop) --}}
                <div class="order-4 lg:col-span-5 bg-white p-6 md:p-8 rounded-2xl shadow-xl border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Riwayat Pengajuan</h2>
                    <div class="space-y-4">
                        @forelse ($cutiRequests as $cuti)
                        <a href="{{ route('cuti.show', $cuti) }}" class="block p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-300">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full
                                        @if($cuti->status == 'disetujui') bg-green-100 text-green-600
                                        @elseif($cuti->status == 'ditolak') bg-red-100 text-red-600
                                        @elseif($cuti->status == 'dibatalkan') bg-gray-100 text-gray-500
                                        @else bg-yellow-100 text-yellow-600 @endif">
                                        <i class="fas 
                                            @if($cuti->status == 'disetujui') fa-check
                                            @elseif($cuti->status == 'ditolak') fa-times
                                            @elseif($cuti->status == 'dibatalkan') fa-ban
                                            @else fa-clock @endif"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Durasi: {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari • Diajukan: {{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 sm:mt-0">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                        @if($cuti->status == 'disetujui') bg-green-100 text-green-800
                                        @elseif($cuti->status == 'ditolak') bg-red-100 text-red-800
                                        @elseif($cuti->status == 'dibatalkan') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($cuti->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-10 border-2 border-dashed rounded-xl">
                            <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">Belum ada riwayat pengajuan cuti.</p>
                        </div>
                        @endforelse
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

        function formatTanggal(tanggalStr) {
            if (!tanggalStr) return '-';
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            // Menambahkan 'T00:00:00' untuk menghindari masalah timezone
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
                // Perhitungan hari yang inklusif
                const diffTime = end.getTime() - start.getTime();
                const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

                totalHariElem.textContent = `${diffDays} Hari`;
            } else {
                totalHariElem.textContent = '- Hari';
            }
        }

        tglMulai.addEventListener('change', hitungDurasi);
        tglSelesai.addEventListener('change', hitungDurasi);

        // Panggil fungsi saat halaman dimuat untuk menampilkan nilai awal jika ada
        hitungDurasi();
    });
    </script>
    @endpush
</x-layout-users>