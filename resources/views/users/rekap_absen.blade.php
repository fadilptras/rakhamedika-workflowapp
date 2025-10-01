<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Chart.js untuk Diagram --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="p-0 md:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header dan Tombol Kembali --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Riwayat Absensi Saya</h2>
                    <p class="text-gray-500">Lihat kembali catatan kehadiran Anda per periode.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline font-semibold">Kembali ke Dashboard</a>
            </div>

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

            {{-- Kartu Rekap dan Chart --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {{-- Kiri: Chart --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-bold text-gray-900 mb-4 text-xl text-center">Diagram Kehadiran</h3>
                    <div class="relative h-72">
                        <canvas id="rekapAbsensiChart"></canvas>
                    </div>
                </div>
                {{-- Kanan: Detail Angka --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-bold text-gray-900 mb-4 text-xl text-center">Ringkasan Bulan Ini</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-center">
                            <p class="text-sm font-semibold text-emerald-800">Hadir</p>
                            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $rekap['hadir'] }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                            <p class="text-sm font-semibold text-red-800">Sakit</p>
                            <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $rekap['sakit'] }}</p>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-center">
                            <p class="text-sm font-semibold text-amber-800">Izin</p>
                            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $rekap['izin'] }}</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg text-center">
                            <p class="text-sm font-semibold text-purple-800">Cuti</p>
                            <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ $rekap['cuti'] }}</p>
                        </div>
                        <div class="bg-gray-100 border border-gray-200 p-4 rounded-lg text-center">
                            <p class="text-sm font-semibold text-gray-800">Alpa</p>
                            <p class="text-3xl font-extrabold text-gray-600 mt-1">{{ $rekap['alpa'] }}</p>
                        </div>
                         <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg text-center">
                            <p class="text-sm font-semibold text-orange-800">Terlambat</p>
                            <p class="text-xl font-bold text-orange-600 mt-2">{{ $rekap['terlambat_formatted'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Detail Absensi Harian --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-bold text-gray-900 mb-4 text-xl">Detail Kehadiran Harian</h3>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($detailHarian as $item)
                                @php
                                    // ======================= PERBAIKAN UTAMA DI SINI =======================
                                    
                                    // 1. Perbaikan Logika Total Jam Kerja Lintas Hari
                                    $totalJam = '-';
                                    if ($item->jam_masuk && $item->jam_keluar) {
                                        // Gabungkan tanggal asli dengan jam
                                        $waktuMasuk = \Carbon\Carbon::parse($item->tanggal->toDateString() . ' ' . $item->jam_masuk);
                                        $waktuKeluar = \Carbon\Carbon::parse($item->tanggal->toDateString() . ' ' . $item->jam_keluar);

                                        // Jika jam keluar lebih kecil dari jam masuk, tambahkan 1 hari
                                        if ($waktuKeluar->lt($waktuMasuk)) {
                                            $waktuKeluar->addDay();
                                        }

                                        // Hitung durasi dalam format yang lebih baik
                                        $totalMenit = $waktuKeluar->diffInMinutes($waktuMasuk);
                                        $jam = floor($totalMenit / 60);
                                        $menit = $totalMenit % 60;
                                        $totalJam = "$jam jam $menit mnt";
                                    }

                                    // 2. Perbaikan Logika Pengecekan Terlambat
                                    $isLate = false;
                                    if ($item->status == 'hadir' && $item->jam_masuk) {
                                         $waktuMasukKaryawan = \Carbon\Carbon::parse($item->jam_masuk, 'Asia/Jakarta');
                                         $batasWaktuMasuk = \Carbon\Carbon::createFromTimeString('08:00:00', 'Asia/Jakarta');
                                         $isLate = $waktuMasukKaryawan->gt($batasWaktuMasuk);
                                    }
                                    
                                    $keteranganText = $item->keterangan ?: '-';

                                    // Logika untuk badge status (tidak berubah)
                                    $statusBadge = '';
                                    switch($item->status) {
                                        case 'hadir': $statusBadge = '<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize bg-green-100 text-green-800">Hadir</span>'; break;
                                        case 'sakit': $statusBadge = '<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize bg-red-100 text-red-800">Sakit</span>'; break;
                                        case 'izin': $statusBadge = '<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize bg-yellow-100 text-yellow-800">Izin</span>'; break;
                                        case 'cuti': $statusBadge = '<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize bg-purple-100 text-purple-800">Cuti</span>'; break;
                                        case 'alpa': $statusBadge = '<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize bg-gray-200 text-gray-800">Alpa</span>'; break;
                                        default: $statusBadge = '<span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize bg-gray-100 text-gray-500">-</span>'; break;
                                    }
                                    // ======================= AKHIR PERBAIKAN =======================
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $item->is_weekend ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 text-sm whitespace-nowrap {{ $item->is_weekend ? 'text-gray-400' : 'text-gray-700' }}">{{ $item->tanggal->translatedFormat('l, d F Y') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        {!! $statusBadge !!}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center font-mono {{ $item->is_weekend ? 'text-gray-400' : 'text-gray-700' }}">{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i:s') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-center font-mono {{ $item->is_weekend ? 'text-gray-400' : 'text-gray-700' }}">{{ $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar)->format('H:i:s') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-center font-mono {{ $item->is_weekend ? 'text-gray-400' : 'text-gray-700' }}">{{ $totalJam }}</td>
                                    <td class="py-3 px-4 text-sm {{ $item->is_weekend ? 'text-gray-400' : 'text-gray-700' }}">
                                        @if($isLate)
                                            <span class="text-red-600 font-semibold">(Terlambat)</span>
                                        @endif
                                        {{ $keteranganText }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center text-gray-500">
                                        <i class="fas fa-box-open text-4xl mb-3"></i>
                                        <p class="font-semibold">Tidak ada data absensi untuk periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('rekapAbsensiChart');
                const rekapData = @json($rekap);

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Sakit', 'Izin', 'Cuti', 'Alpa'],
                        datasets: [{
                            label: 'Hari',
                            data: [
                                rekapData.hadir,
                                rekapData.sakit,
                                rekapData.izin,
                                rekapData.cuti,
                                rekapData.alpa,
                            ],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(239, 68, 68, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(139, 92, 246, 0.7)',
                                'rgba(107, 114, 128, 0.7)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(139, 92, 246, 1)',
                                'rgba(107, 114, 128, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 20,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) { label += ': '; }
                                        if (context.parsed !== null) { label += context.parsed + ' hari'; }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-layout-users>