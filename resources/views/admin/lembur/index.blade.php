<x-layout-admin>
    <x-slot:title>Rekap Lembur Karyawan</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Rekap Lembur Karyawan</h1>
        <a href="#"
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-file-pdf mr-2"></i> Download PDF
        </a>
    </div>

    {{-- Filter --}}
    <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
        <form method="GET" action="#">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="filter_rentang" class="block text-sm font-medium text-zinc-300">Filter Waktu</label>
                    <select name="filter_rentang" id="filter_rentang" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="semua">Semua Waktu</option>
                        <option value="bulan_ini">Bulan Ini</option>
                        <option value="pilih_bulan">Pilih Bulan</option>
                    </select>
                </div>
                <div id="filter-bulanan" class="hidden flex items-end gap-4">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-zinc-300">Bulan</label>
                        <select name="bulan" id="bulan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="1">Januari</option>
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-zinc-300">Tahun</label>
                        <select name="tahun" id="tahun" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="2024">2024</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="divisi" class="block text-sm font-medium text-zinc-300">Divisi</label>
                    <select name="divisi" id="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Divisi</option>
                    </select>
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-zinc-300">Karyawan</label>
                    <select name="user_id" id="user_id" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Karyawan</option>
                    </select>
                </div>
                 <div class="flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="#" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Rekap Lembur --}}
    <div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
        <table class="min-w-full text-sm text-left text-zinc-300">
            <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                <tr>
                    <th class="px-4 py-3">Karyawan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Waktu Lembur</th>
                    <th class="px-4 py-3">Durasi</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Lampiran & Lokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse ($lemburRecords as $record)
                <tr class="hover:bg-zinc-700/30">
                    <td class="px-4 py-3 flex items-center gap-3">
                        <img src="{{ $record->user->profile_picture ? asset('storage/' . $record->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($record->user->name ?? 'U').'&background=4f46e5&color=e0e7ff' }}"
                             alt="{{ $record->user->name ?? '' }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-semibold text-white">{{ $record->user->name ?? 'User Dihapus' }}</p>
                            <p class="text-xs text-zinc-400">{{ $record->user->divisi ?? '-' }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($record->jam_masuk_lembur && $record->jam_keluar_lembur)
                            <span class="font-semibold text-white">{{ \Carbon\Carbon::parse($record->jam_masuk_lembur)->format('H:i') }} - {{ \Carbon\Carbon::parse($record->jam_keluar_lembur)->format('H:i') }}</span>
                        @else
                            <span class="font-semibold text-zinc-400">--:--</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if ($record->jam_masuk_lembur && $record->jam_keluar_lembur)
                            @php
                                $jamMasuk = \Carbon\Carbon::parse($record->jam_masuk_lembur);
                                $jamKeluar = \Carbon\Carbon::parse($record->jam_keluar_lembur);
                                $durasi = $jamMasuk->diffForHumans($jamKeluar, true, false, 2);
                            @endphp
                            <span class="font-semibold text-white">{{ $durasi }}</span>
                        @else
                            <span class="font-semibold text-zinc-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        {{ $record->keterangan ?? '-' }}
                    </td>
                    <td class="px-4 py-3 space-y-1">
                        @php
                            $hasLink = false;
                        @endphp
                        @if ($record->lampiran_masuk)
                            <a href="{{ asset('storage/' . $record->lampiran_masuk) }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lampiran Masuk
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif
                        @if ($record->lampiran_keluar)
                            <a href="{{ asset('storage/' . $record->lampiran_keluar) }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lampiran Keluar
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif
                        @if ($record->latitude_masuk && $record->longitude_masuk)
                            <a href="http://maps.google.com/maps?q={{ $record->latitude_masuk }},{{ $record->longitude_masuk }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                Lokasi Masuk
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif
                        @if ($record->latitude_keluar && $record->longitude_keluar)
                            <a href="http://maps.google.com/maps?q={{ $record->latitude_keluar }},{{ $record->longitude_keluar }}" target="_blank"
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
                    <td colspan="6" class="px-4 py-6 text-center text-zinc-400">
                        Tidak ada data lembur yang tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $lemburRecords->links() }}
    </div>

</x-layout-admin>