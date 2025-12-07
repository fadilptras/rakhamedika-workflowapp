<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Library Chart & Axios --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <div class="p-0 md:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">

            {{-- 1. HEADER & TOMBOL KEMBALI --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Riwayat Absensi Saya</h2>
                    <p class="text-gray-500">Lihat kembali catatan kehadiran dan aktivitas Anda.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline font-semibold mt-2 md:mt-0">Kembali ke Dashboard</a>
            </div>

            {{-- 2. FORM FILTER (Tetap Seperti Semula) --}}
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

            {{-- 3. CHART & RINGKASAN ANGKA (Tetap Seperti Semula) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {{-- Chart --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-bold text-gray-900 mb-4 text-xl text-center">Diagram Kehadiran</h3>
                    <div class="relative h-72">
                        <canvas id="rekapAbsensiChart"></canvas>
                    </div>
                </div>
                {{-- Angka Ringkasan --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-bold text-gray-900 mb-4 text-xl text-center">Ringkasan Bulan Ini</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-center">
                            <p class="text-xs font-semibold text-emerald-800">Hadir</p>
                            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $rekap['hadir'] }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                            <p class="text-xs font-semibold text-red-800">Sakit</p>
                            <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $rekap['sakit'] }}</p>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-center">
                            <p class="text-xs font-semibold text-amber-800">Izin</p>
                            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $rekap['izin'] }}</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg text-center">
                            <p class="text-xs font-semibold text-purple-800">Cuti</p>
                            <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ $rekap['cuti'] }}</p>
                        </div>
                        <div class="bg-gray-100 border border-gray-200 p-4 rounded-lg text-center">
                            <p class="text-xs font-semibold text-gray-800">Alpa</p>
                            <p class="text-2xl font-extrabold text-gray-600 mt-1">{{ $rekap['alpa'] }}</p>
                        </div>
                        <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-lg text-center">
                            <p class="text-xs font-semibold text-indigo-800">Lembur</p>
                            <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $rekap['lembur'] }}</p>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg text-center col-span-3">
                            <p class="text-xs font-semibold text-orange-800">Total Terlambat</p>
                            <p class="text-xl font-bold text-orange-600 mt-1">{{ $rekap['terlambat_formatted'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. BAGIAN UTAMA YANG BARU: TABEL RESPONSIF --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg">Detail Harian</h3>
                </div>

                {{-- OPSI A: TAMPILAN DESKTOP (TABEL BIASA) --}}
                {{-- 'hidden md:block' artinya sembunyi di HP, muncul di layar medium ke atas --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aktivitas</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($detailHarian as $item)
                                @php
                                    $isLate = false;
                                    if ($item->status == 'hadir' && $item->jam_masuk) {
                                         $waktuMasuk = \Carbon\Carbon::parse($item->jam_masuk, 'Asia/Jakarta');
                                         $batas = \Carbon\Carbon::createFromTimeString('08:00:00', 'Asia/Jakarta');
                                         $isLate = $waktuMasuk->gt($batas);
                                    }
                                    
                                    // Logika Warna Badge Inline
                                    $badgeClass = 'bg-gray-100 text-gray-600';
                                    switch(strtolower($item->status)) {
                                        case 'hadir': $badgeClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200'; break;
                                        case 'sakit': $badgeClass = 'bg-rose-100 text-rose-700 border border-rose-200'; break;
                                        case 'izin':  $badgeClass = 'bg-amber-100 text-amber-700 border border-amber-200'; break;
                                        case 'cuti':  $badgeClass = 'bg-purple-100 text-purple-700 border border-purple-200'; break;
                                        case 'lembur':$badgeClass = 'bg-indigo-100 text-indigo-700 border border-indigo-200'; break;
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition {{ $item->is_weekend ? 'bg-gray-50/50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->tanggal->format('d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->tanggal->translatedFormat('F Y') }}</div>
                                        <div class="text-xs font-semibold {{ $item->is_weekend ? 'text-red-400' : 'text-blue-500' }}">{{ $item->tanggal->translatedFormat('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide inline-flex items-center justify-center {{ $badgeClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">
                                            <span class="text-green-600 font-mono">{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '--:--' }}</span>
                                            <span class="text-gray-400 mx-1">-</span>
                                            <span class="text-red-600 font-mono">{{ $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') : '--:--' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($item->jumlah_aktivitas > 0)
                                            <button onclick="openModalAktivitas('{{ $item->tanggal->toDateString() }}')" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200 transition">
                                                <i class="fas fa-eye mr-1.5"></i> {{ $item->jumlah_aktivitas }} Catatan
                                            </button>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700 truncate max-w-xs">
                                            @if($isLate)
                                                <span class="text-red-600 font-bold text-xs mr-1">[Terlambat]</span>
                                            @endif
                                            {{ $item->keterangan ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Tidak ada data absensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- OPSI B: TAMPILAN MOBILE (KARTU/LIST) --}}
                {{-- 'block md:hidden' artinya muncul di HP, sembunyi di Desktop --}}
                <div class="block md:hidden bg-gray-50 p-3 space-y-3">
                    @forelse($detailHarian as $item)
                        @php
                            // Logika PHP sama (dicopy agar scope aman di mobile loop)
                            $isLate = false;
                            if ($item->status == 'hadir' && $item->jam_masuk) {
                                $waktuMasuk = \Carbon\Carbon::parse($item->jam_masuk, 'Asia/Jakarta');
                                $batas = \Carbon\Carbon::createFromTimeString('08:00:00', 'Asia/Jakarta');
                                $isLate = $waktuMasuk->gt($batas);
                            }

                            // Warna Border Kiri Kartu
                            $borderColor = 'border-gray-300';
                            switch(strtolower($item->status)) {
                                case 'hadir': $borderColor = 'border-emerald-500'; break;
                                case 'sakit': $borderColor = 'border-rose-500'; break;
                                case 'izin':  $borderColor = 'border-amber-500'; break;
                                case 'lembur':$borderColor = 'border-indigo-500'; break;
                            }
                        @endphp
                        
                        {{-- KARTU ITEM --}}
                        <div class="bg-white rounded-lg shadow-sm border-l-4 {{ $borderColor }} p-4 relative">
                            {{-- Baris 1: Tanggal & Status --}}
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $item->tanggal->translatedFormat('d F Y') }}
                                    </p>
                                    <p class="text-xs font-semibold {{ $item->is_weekend ? 'text-red-500' : 'text-blue-500' }}">
                                        {{ $item->tanggal->translatedFormat('l') }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600">
                                    {{ $item->status }}
                                </span>
                            </div>

                            {{-- Baris 2: Jam Masuk & Keluar (Grid 2 Kolom) --}}
                            <div class="grid grid-cols-2 gap-2 mb-3 bg-gray-50 rounded p-2 border border-gray-100">
                                <div class="text-center">
                                    <span class="block text-[10px] uppercase text-gray-400 font-bold">Masuk</span>
                                    <span class="block text-sm font-mono font-bold text-gray-700">
                                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '--:--' }}
                                    </span>
                                </div>
                                <div class="text-center border-l border-gray-200">
                                    <span class="block text-[10px] uppercase text-gray-400 font-bold">Keluar</span>
                                    <span class="block text-sm font-mono font-bold text-gray-700">
                                        {{ $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') : '--:--' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Baris 3: Keterangan (Jika ada) --}}
                            @if($item->keterangan || $isLate)
                                <div class="mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded border border-gray-100 italic">
                                    @if($isLate) <span class="text-red-600 font-bold not-italic">[Terlambat]</span> @endif
                                    {{ $item->keterangan ?? 'Tidak ada keterangan.' }}
                                </div>
                            @endif

                            {{-- Baris 4: Tombol Aktivitas (Full Width) --}}
                            @if($item->jumlah_aktivitas > 0)
                                <button onclick="openModalAktivitas('{{ $item->tanggal->toDateString() }}')" class="w-full text-center text-xs font-bold text-blue-600 border border-blue-200 bg-blue-50 hover:bg-blue-100 py-2 rounded transition">
                                    <i class="fas fa-clipboard-list mr-1"></i> Lihat {{ $item->jumlah_aktivitas }} Aktivitas
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-2 text-gray-300"></i>
                            <p>Tidak ada data absensi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL AKTIVITAS (Logic Javascript) --}}
    <div id="modalAktivitas" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Overlay Gelap --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeModalAktivitas()"></div>

        {{-- Panel Modal --}}
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                {{-- Header Modal --}}
                <div class="bg-blue-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">Detail Aktivitas</h3>
                    <button onclick="closeModalAktivitas()" class="text-blue-100 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="bg-blue-50 px-4 py-2 border-b border-blue-100">
                    <p class="text-sm text-blue-800" id="modal-date">Memuat tanggal...</p>
                </div>

                {{-- Body Modal --}}
                <div class="px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto bg-gray-50">
                    <div id="loading-aktivitas" class="hidden text-center py-6">
                        <i class="fas fa-circle-notch fa-spin text-2xl text-blue-500"></i>
                        <p class="text-xs text-gray-500 mt-2">Memuat data...</p>
                    </div>
                    <div id="list-aktivitas" class="space-y-3">
                        </div>
                </div>

                {{-- Footer Modal --}}
                <div class="bg-gray-100 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="closeModalAktivitas()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // --- CHART LOGIC ---
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('rekapAbsensiChart');
                if (ctx) {
                    const rekapData = @json($rekap);
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Hadir', 'Sakit', 'Izin', 'Cuti', 'Alpa', 'Lembur'],
                            datasets: [{
                                data: [rekapData.hadir, rekapData.sakit, rekapData.izin, rekapData.cuti, rekapData.alpa, rekapData.lembur],
                                backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#6B7280', '#4F46E5'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            });

            // --- MODAL LOGIC ---
            const modal = document.getElementById('modalAktivitas');
            const listContainer = document.getElementById('list-aktivitas');
            const loadingSpinner = document.getElementById('loading-aktivitas');
            const modalDate = document.getElementById('modal-date');

            function openModalAktivitas(date) {
                modal.classList.remove('hidden');
                listContainer.innerHTML = ''; 
                loadingSpinner.classList.remove('hidden');
                
                // Format Tanggal
                const d = new Date(date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                modalDate.textContent = d.toLocaleDateString('id-ID', options);

                axios.get('/aktivitas/json', {
                    params: { start: date, user_id: '{{ auth()->id() }}' }
                })
                .then(function (response) {
                    loadingSpinner.classList.add('hidden');
                    const data = response.data;

                    if (data.length === 0) {
                        listContainer.innerHTML = '<div class="text-center py-4 text-gray-400 text-sm">Tidak ada catatan aktivitas.</div>';
                    } else {
                        data.forEach(item => {
                            const props = item.extendedProps;
                            const photoHtml = props.photo_url 
                                ? `<div class="mt-2"><img src="${props.photo_url}" class="w-full h-32 object-cover rounded-lg border border-gray-200"></div>` 
                                : '';
                            
                            // Tampilan Item Aktivitas di Modal
                            const html = `
                                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex justify-between items-center mb-1">
                                        <h4 class="font-bold text-gray-800 text-sm">${item.title}</h4>
                                        <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-500 font-mono">
                                            ${new Date(item.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">${props.keterangan}</p>
                                    ${photoHtml}
                                </div>
                            `;
                            listContainer.innerHTML += html;
                        });
                    }
                })
                .catch(function (error) {
                    loadingSpinner.classList.add('hidden');
                    listContainer.innerHTML = '<p class="text-center text-red-500 text-sm">Gagal memuat data.</p>';
                });
            }

            function closeModalAktivitas() {
                modal.classList.add('hidden');
            }
        </script>
    @endpush
</x-layout-users>