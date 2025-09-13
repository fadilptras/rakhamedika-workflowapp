<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>

        {{-- tombol download --}}
        <a href="{{ route('admin.absensi.pdf', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-file-pdf mr-2"></i> Download PDF
        </a>
    </div>

    {{-- filter --}}
    <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
        <form method="GET" action="{{ route('admin.absensi.index') }}" id="filter-form">
            <div class="flex flex-wrap items-end gap-4">
                
                <div>
                    <label for="filter_rentang" class="block text-sm font-medium text-zinc-300">Filter Waktu</label>
                    <select name="filter_rentang" id="filter_rentang" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="semua" @selected(request('filter_rentang') == 'semua' || !request()->has('filter_rentang'))>Semua Waktu</option>
                        <option value="harian" @selected(request('tanggal'))>Harian</option>
                        <option value="minggu_ini" @selected(request('filter_rentang') == 'minggu_ini')>Minggu Ini</option>
                        <option value="bulan_ini" @selected(request('filter_rentang') == 'bulan_ini')>Bulan Ini</option>
                        <option value="pilih_bulan" @selected(request('bulan') && request('tahun'))>Pilih Bulan</option>
                    </select>
                </div>

                {{-- Filter Tanggal (Harian) --}}
                <div id="filter-harian" class="hidden">
                    <label for="tanggal" class="block text-sm font-medium text-zinc-300">Pilih Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Filter Bulan & Tahun --}}
                <div id="filter-bulanan" class="hidden flex items-end gap-4">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-zinc-300">Bulan</label>
                        <select name="bulan" id="bulan" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" @selected(request('bulan') == $num)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-zinc-300">Tahun</label>
                        <select name="tahun" id="tahun" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected(request('tahun') == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Filter Divisi --}}
                <div>
                    <label for="divisi" class="block text-sm font-medium text-zinc-300">Divisi</label>
                    <select name="divisi" id="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $divisi)
                            <option value="{{ $divisi }}" @selected(request('divisi') == $divisi)>{{ $divisi }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- filter Karyawan --}}
                <div>
                    <label for="user_id" class="block text-sm font-medium text-zinc-300">Karyawan</label>
                    <select name="user_id" id="user_id" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}" data-divisi="{{ $user->divisi }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-zinc-300">Status</label>
                    <select name="status" id="status" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="hadir" @selected(request('status') == 'hadir')>Hadir</option>
                        <option value="sakit" @selected(request('status') == 'sakit')>Sakit</option>
                        <option value="izin" @selected(request('status') == 'izin')>Izin</option>
                        <option value="cuti" @selected(request('status') == 'cuti')>Cuti</option>
                        <option value="terlambat" @selected(request('status') == 'terlambat')>Terlambat</option>
                        <option value="tidak_hadir" @selected(request('status') == 'tidak_hadir')>Tidak Hadir</option>
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                 <div class="flex items-end gap-2">
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

    {{-- Total Jam Terlambat --}}
    @if(isset($totalLate) && $totalLate !== '0 detik')
    <div class="mb-6 p-4 bg-yellow-500/10 text-yellow-400 rounded-lg border border-yellow-500/30 font-semibold">
        Total Waktu Terlambat: <span class="font-bold">{{ $totalLate }}</span>
    </div>
    @endif


    {{-- Daftar Absensi (Format Tabel Lurus) --}}
    <div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
        <table class="min-w-full text-sm text-left text-zinc-300">
            <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                <tr>
                    <th class="px-4 py-3">Karyawan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Jam Masuk / Keluar</th>
                    <th class="px-4 py-3">Jam Telat</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Lampiran & Lokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse ($absensiRecords as $record)
                    @php
                        // Logika untuk menampilkan status dan badge
                        $statusBadgeColor = 'bg-gray-500/10 text-gray-400';
                        $statusText = 'Belum Absen';

                        if ($record->status == 'hadir') {
                             if (property_exists($record, 'isLate') && $record->isLate) {
                                 $statusText = 'Terlambat';
                                 $statusBadgeColor = 'bg-red-500/10 text-red-400';
                             } else {
                                 $statusText = 'Hadir';
                                 $statusBadgeColor = 'bg-green-500/10 text-green-400';
                             }
                        } elseif ($record->status == 'sakit') {
                            $statusText = 'Sakit';
                            $statusBadgeColor = 'bg-red-500/10 text-red-400';
                        } elseif ($record->status == 'izin') {
                            $statusText = 'Izin';
                            $statusBadgeColor = 'bg-amber-500/10 text-amber-400';
                        } elseif ($record->status == 'cuti') {
                            $statusText = 'Cuti';
                            $statusBadgeColor = 'bg-purple-500/10 text-purple-400';
                        } elseif ($record->status == 'tidak_hadir') {
                             $statusText = 'Tidak Hadir';
                             $statusBadgeColor = 'bg-red-500/10 text-red-400';
                        }

                        // Logika jam telat
                        $jamTelat = '-';
                        if ($statusText === 'Terlambat') {
                           $jamMasuk = \Carbon\Carbon::parse($record->tanggal . ' ' . $record->jam_masuk);
                           $jamMulaiKerja = \Carbon\Carbon::parse($record->tanggal . ' ' . $standardWorkHour);
                           $telatMenit = $jamMasuk->diffInMinutes($jamMulaiKerja);
                           $jamTelat = \Carbon\CarbonInterval::minutes($telatMenit)->cascade()->forHumans(['short' => true]);
                        }
                    @endphp
                    <tr class="hover:bg-zinc-700/30">
                        {{-- Karyawan --}}
                        <td class="px-4 py-3 flex items-center gap-3">
                            <img src="{{ isset($record->user->profile_picture) ? asset('storage/' . $record->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($record->user->name ?? 'U').'&background=4f46e5&color=e0e7ff' }}"
                                 alt="{{ $record->user->name ?? '' }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-semibold text-white">{{ $record->user->name ?? 'User Dihapus' }}</p>
                                <p class="text-xs text-zinc-400">{{ $record->user->divisi ?? '-' }}</p>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                        </td>

                        {{-- Jam Masuk / Keluar --}}
                        <td class="px-4 py-3">
                            @if ($record->jam_masuk)
                                <span class="font-semibold text-white">{{ $record->jam_masuk }}</span>
                            @else
                                <span class="font-semibold text-zinc-400">-</span>
                            @endif
                            @if (isset($record->jam_keluar) && $record->jam_keluar)
                                <br>
                                <span class="text-zinc-400 text-xs">Keluar:</span>
                                <span class="font-semibold text-white">{{ $record->jam_keluar }}</span>
                            @endif
                        </td>

                        {{-- Jam Telat --}}
                        <td class="px-4 py-3">
                            <span class="font-semibold text-red-400">{{ $jamTelat }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs text-center capitalize {{ $statusBadgeColor }}">
                                {{ $statusText }}
                            </span>
                        </td>

                        {{-- Keterangan --}}
                        <td class="px-4 py-3">
                            {{ $record->keterangan ?? '-' }}
                            @if (isset($record->keterangan_keluar) && $record->keterangan_keluar)
                                <br><span class="text-xs text-zinc-400">Keluar:</span>
                                <span>{{ $record->keterangan_keluar }}</span>
                            @endif
                        </td>

                        {{-- Lampiran & Lokasi --}}
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
                                <a href="{{ asset('storage/' . $record->lampiran_keluar) }}" target="_blank"
                                   class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                    Lampiran Keluar
                                </a><br>
                                @php $hasLink = true; @endphp
                            @endif
                            @if (isset($record->latitude) && $record->latitude && $record->longitude)
                                <a href="https://maps.google.com/?q={{ $record->latitude }},{{ $record->longitude }}" target="_blank"
                                   class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                    Lokasi Masuk
                                </a><br>
                                @php $hasLink = true; @endphp
                            @endif
                            @if (isset($record->latitude_keluar) && $record->latitude_keluar && $record->longitude_keluar)
                                <a href="https://maps.google.com/?q={{ $record->latitude_keluar }},{{ $record->longitude_keluar }}" target="_blank"
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
                        <td colspan="7" class="px-4 py-6 text-center text-zinc-400">
                            Tidak ada data absensi yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINASI --}}
    <div class="mt-6">
        {{ $absensiRecords->links() }}
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rentangFilter = document.getElementById('filter_rentang');
            const harianFilter = document.getElementById('filter-harian');
            const bulananFilter = document.getElementById('filter-bulanan');
            const tanggalInput = document.getElementById('tanggal');
            const bulanInput = document.getElementById('bulan');
            const tahunInput = document.getElementById('tahun');

            function toggleFilters() {
                const selected = rentangFilter.value;

                harianFilter.classList.add('hidden');
                bulananFilter.classList.add('hidden');

                tanggalInput.disabled = true;
                bulanInput.disabled = true;
                tahunInput.disabled = true;

                if (selected === 'harian') {
                    harianFilter.classList.remove('hidden');
                    tanggalInput.disabled = false;
                } else if (selected === 'pilih_bulan') {
                    bulananFilter.classList.remove('hidden');
                    bulanInput.disabled = false;
                    tahunInput.disabled = false;
                }
            }

            rentangFilter.addEventListener('change', toggleFilters);
            toggleFilters();

            // Filter karyawan berdasarkan divisi
            const divisiSelect = document.getElementById('divisi');
            const userSelect = document.getElementById('user_id');
            const userOptions = Array.from(userSelect.options);

            function filterKaryawan() {
                const selectedDivisi = divisiSelect.value;
                const currentSelectedUser = userSelect.value;

                userSelect.innerHTML = '';

                userOptions.forEach(option => {
                    if (option.value === '') {
                        userSelect.add(option.cloneNode(true));
                    }
                    else if (selectedDivisi === '' || option.dataset.divisi === selectedDivisi) {
                        userSelect.add(option.cloneNode(true));
                    }
                });

                userSelect.value = currentSelectedUser;
            }

            divisiSelect.addEventListener('change', filterKaryawan);
            filterKaryawan();
        });
    </script>
    @endpush
</x-layout-admin>