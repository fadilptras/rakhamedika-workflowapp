<x-layout-admin>
<x-slot:title>Aktivitas</x-slot:title>

<div class="flex justify-between items-center mb-6 flex-wrap gap-4">
    <h1 class="text-2xl font-bold text-white">Absensi Harian Karyawan</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.absensi.index') }}"
           class="font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors duration-200
           @if(Route::is('admin.absensi.index')) bg-indigo-600 hover:bg-indigo-700 text-white @else bg-zinc-700 hover:bg-zinc-600 text-zinc-300 @endif">
            <i class="fas fa-chart-bar mr-2"></i> Absensi
        </a>
        <a href="{{ route('admin.lembur.index') }}"
           class="font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors duration-200
           @if(Route::is('admin.lembur.index')) bg-indigo-600 hover:bg-indigo-700 text-white @else bg-zinc-700 hover:bg-zinc-600 text-zinc-300 @endif">
            <i class="fas fa-clock mr-2"></i> Lembur
        </a>
        <a href="{{ route('admin.absensi.downloadPdfHarian', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-file-pdf mr-2"></i> Download PDF
        </a>
    </div>
</div>

<div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
    <form method="GET" action="{{ route('admin.absensi.index') }}" id="filter-form">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 items-end">
            
            <div>
                <label for="month" class="block text-sm font-medium text-zinc-300">Bulan</label>
                <select name="month" id="month" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @php
                        $currentMonth = request('month') ? intval(request('month')) : now()->month;
                    @endphp
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected($num == $currentMonth)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="year" class="block text-sm font-medium text-zinc-300">Tahun</label>
                <select name="year" id="year" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @php
                        $currentYear = request('year') ? intval(request('year')) : now()->year;
                    @endphp
                    @foreach($years as $yearItem)
                        <option value="{{ $yearItem }}" @selected($yearItem == $currentYear)>{{ $yearItem }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="divisi" class="block text-sm font-medium text-zinc-300">Divisi</label>
                <select name="divisi" id="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d }}" @selected(request('divisi') == $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-3 flex items-end justify-end gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.absensi.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="mb-4">
    <h2 class="text-white font-semibold">Pilih Tanggal: </h2>
</div>

<div class="mt-6 flex justify-start items-center flex-wrap">
    @for ($i = 1; $i <= $daysInMonth; $i++)
        <a href="{{ route('admin.absensi.index', array_merge(request()->except('day'), ['day' => $i])) }}"
           class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200
           @if ($i == $day) bg-indigo-600 text-white font-bold
           @else bg-zinc-700 text-zinc-300 hover:bg-zinc-600 @endif">
            {{ $i }}
        </a>
    @endfor
</div>

{{-- UPDATE: MENGHAPUS LOGIKA @if($isWeekend) AGAR TABEL SELALU MUNCUL --}}
<div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700 mt-6">
    <table class="min-w-full text-sm text-left text-zinc-300">
        <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
            <tr>
                <th class="px-4 py-3">Karyawan</th>
                <th class="px-4 py-3">Waktu Masuk</th>
                <th class="px-4 py-3">Waktu Keluar</th>
                <th class="px-4 py-3">Durasi Kerja</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Lembur</th>
                <th class="px-4 py-3">Keterangan</th>
                <th class="px-4 py-3">Lampiran & Lokasi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-700">
            @forelse ($absensi_harian as $record)
                @php
                    $statusBadgeColor = 'bg-gray-500/10 text-gray-400';
                    $statusText = $record->status;
                    $jamMasuk = $record->jam_masuk ? \Carbon\Carbon::parse($record->jam_masuk) : null;
                    
                    $durasiKerja = $record->durasi_teks ?? '-'; 

                    if ($record->status == 'hadir') {
                        $batasWaktuMasuk = \Carbon\Carbon::createFromTimeString('08:00:00', 'Asia/Jakarta');
                        $waktuMasukKaryawan = $jamMasuk ? \Carbon\Carbon::parse($jamMasuk, 'Asia/Jakarta') : null;

                        $isLate = $waktuMasukKaryawan && $waktuMasukKaryawan->gt($batasWaktuMasuk);
                        
                        if ($isLate) {
                            $statusText = 'Hadir (Terlambat)';
                            $statusBadgeColor = 'bg-green-500/10 text-green-400';
                        } else {
                            $statusText = 'Hadir';
                            $statusBadgeColor = 'bg-green-500/10 text-green-400';
                        }
                    } elseif ($record->status == 'sakit') {
                        $statusBadgeColor = 'bg-red-500/10 text-red-400';
                    } elseif ($record->status == 'izin') {
                        $statusBadgeColor = 'bg-amber-500/10 text-amber-400';
                    } elseif ($record->status == 'cuti') {
                        $statusBadgeColor = 'bg-purple-500/10 text-purple-400';
                    } elseif ($record->status == 'tidak hadir') {
                        $statusBadgeColor = 'bg-gray-500/10 text-gray-400';
                    }
                @endphp
                <tr class="hover:bg-zinc-700/30">
                    {{-- KOLOM KARYAWAN --}}
                    <td class="px-4 py-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border-2 border-zinc-600 shadow-md">
                            <img src="{{ isset($record->user->profile_picture) ? asset('storage/' . $record->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($record->user->name ?? 'U').'&background=4f46e5&color=e0e7ff' }}"
                                 alt="{{ $record->user->name ?? '' }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $record->user->name ?? 'User Dihapus' }}</p>
                            <p class="text-xs text-zinc-400">{{ $record->user->divisi ?? '-' }}</p>
                        </div>
                    </td>

                    {{-- KOLOM WAKTU MASUK (TANGGAL & JAM) --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col">
                            <span class="font-semibold text-white">
                                {{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('dddd, D MMM Y') }}
                            </span>
                            @if ($record->jam_masuk)
                                <span class="text-zinc-400 text-xs mt-1">
                                    {{ \Carbon\Carbon::parse($record->jam_masuk)->format('H:i') }} WIB
                                </span>
                            @else
                                <span class="text-zinc-500 text-xs mt-1">-</span>
                            @endif
                        </div>
                    </td>

                    {{-- KOLOM WAKTU KELUAR (TANGGAL & JAM) --}}
                    <td class="px-4 py-3">
                        @if ($record->jam_keluar)
                            <div class="flex flex-col">
                                {{-- Tampilkan Tanggal Keluar (Jika ada, kalau tidak pakai tanggal masuk) --}}
                                <span class="font-semibold text-white">
                                    {{ \Carbon\Carbon::parse($record->tanggal_keluar ?? $record->tanggal)->isoFormat('dddd, D MMM Y') }}
                                </span>
                                <span class="text-zinc-400 text-xs mt-1">
                                    {{ \Carbon\Carbon::parse($record->jam_keluar)->format('H:i') }} WIB
                                </span>
                            </div>
                        @else
                            <span class="font-semibold text-zinc-500">-</span>
                        @endif
                    </td>

                    {{-- KOLOM DURASI --}}
                    <td class="px-4 py-3">
                       <span class="font-semibold text-white">{{ $durasiKerja }}</span>
                    </td>

                    {{-- KOLOM STATUS --}}
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs text-center capitalize {{ $statusBadgeColor }}">
                            {{ $statusText }}
                        </span>
                    </td>

                    {{-- KOLOM LEMBUR --}}
                    <td class="px-4 py-3">
                        @if ($record->lembur)
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs text-center capitalize bg-purple-500/10 text-purple-400">
                                Ya
                            </span>
                        @else
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs text-center capitalize bg-gray-500/10 text-gray-400">
                                Tidak
                            </span>
                        @endif
                    </td>

                    {{-- KOLOM KETERANGAN --}}
                    <td class="px-4 py-3">
                        {{ $record->keterangan ?? '-' }}
                    </td>

                    {{-- KOLOM LAMPIRAN & LOKASI --}}
                    <td class="px-4 py-3 space-y-1">
                        @php
                            $hasLink = false;
                        @endphp

                        @if (isset($record->lampiran) && $record->lampiran)
                            <a href="{{ asset('storage/' . $record->lampiran) }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lampiran Masuk
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif
                        @if (isset($record->lampiran_keluar) && $record->lampiran_keluar)
                            <a href="{{ asset('storage/'. $record->lampiran_keluar) }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lampiran Keluar
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif
                        @if (isset($record->latitude) && $record->latitude && $record->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $record->latitude }},{{ $record->longitude }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lokasi Masuk
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif
                        @if (isset($record->latitude_keluar) && $record->latitude_keluar && $record->longitude_keluar)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $record->latitude_keluar }},{{ $record->longitude_keluar }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lokasi Keluar
                            </a>
                            @php $hasLink = true; @endphp
                        @endif

                        @if (!$hasLink)
                            <span>-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-zinc-400">
                        Tidak ada data absensi yang cocok dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const divisiSelect = document.getElementById('divisi');
        const form = document.getElementById('filter-form');

        divisiSelect.addEventListener('change', function() {
            form.submit();
        });
    });
</script>
@endpush

</x-layout-admin>