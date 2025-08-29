<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
    </div>

    <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
        <form method="GET" action="{{ route('admin.absensi.index') }}" id="filter-form">
            <div class="flex flex-wrap items-end gap-4">
                
                {{-- Filter Utama: Rentang Waktu --}}
                <div>
                    <label for="filter_rentang" class="block text-sm font-medium text-zinc-300">Filter Berdasarkan</label>
                    <select name="filter_rentang" id="filter_rentang" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="semua" @selected(request('filter_rentang') == 'semua' || !request()->has('filter_rentang'))>Semua Waktu</option>
                        <option value="harian" @selected(request('tanggal'))>Harian</option>
                        <option value="minggu_ini" @selected(request('filter_rentang') == 'minggu_ini')>Minggu Ini</option>
                        <option value="bulan_ini" @selected(request('filter_rentang') == 'bulan_ini')>Bulan Ini</option>
                        <option value="pilih_bulan" @selected(request('bulan') && request('tahun'))>Pilih Bulan</option>
                    </select>
                </div>

                {{-- Filter Spesifik Tanggal (Harian) --}}
                <div id="filter-harian" class="hidden">
                    <label for="tanggal" class="block text-sm font-medium text-zinc-300">Pilih Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Filter Spesifik Bulan & Tahun --}}
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

                {{-- Filter Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-zinc-300">Status</label>
                    <select name="status" id="status" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua</option>
                        <option value="hadir" @selected(request('status') == 'hadir')>Hadir</option>
                        <option value="sakit" @selected(request('status') == 'sakit')>Sakit</option>
                        <option value="izin" @selected(request('status') == 'izin')>Izin</option>
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden border border-zinc-700">
        {{-- ... Isi tabel dari <thead> sampai </tbody> sama persis seperti sebelumnya ... --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-zinc-300">
                <thead class="bg-zinc-700 text-left text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Karyawan</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Jam Masuk</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3">Keterangan</th>
                        <th class="px-5 py-3 text-center">Lampiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse ($absensiRecords as $record)
                    <tr class="hover:bg-zinc-700/50">
                        <td class="px-5 py-4">
                             <div class="flex items-center">
                                <img src="{{ $record->user->profile_picture ? asset('storage/' . $record->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($record->user->name ?? 'U').'&background=4f46e5&color=e0e7ff' }}"
                                     alt="{{ $record->user->name ?? '' }}" class="w-10 h-10 rounded-full object-cover mr-4">
                                <div>
                                    <p class="font-semibold text-white">{{ $record->user->name ?? 'User Dihapus' }}</p>
                                    <p class="text-sm text-zinc-400">{{ $record->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                           {{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                        </td>
                        <td class="px-5 py-4 text-sm">{{ $record->jam_masuk }}</td>
                        <td class="px-5 py-4 text-center">
                            @if ($record->status == 'hadir')
                                <span class="px-2 py-1 font-semibold leading-tight text-green-400 bg-green-500/10 rounded-full text-xs">
                                    Hadir
                                </span>
                            @elseif ($record->status == 'sakit')
                                <span class="px-2 py-1 font-semibold leading-tight text-red-400 bg-red-500/10 rounded-full text-xs">
                                    Sakit
                                </span>
                            @else
                                <span class="px-2 py-1 font-semibold leading-tight text-amber-400 bg-amber-500/10 rounded-full text-xs">
                                    Izin
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm">{{ $record->keterangan ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            @if ($record->lampiran)
                                <a href="{{ asset('storage/' . $record->lampiran) }}" target="_blank" 
                                   class="text-indigo-400 hover:text-indigo-300 underline font-semibold text-sm">
                                    Lihat File
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-zinc-400">
                            Tidak ada data absensi yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-8">
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
                
                // Sembunyikan semua filter spesifik terlebih dahulu
                harianFilter.classList.add('hidden');
                bulananFilter.classList.add('hidden');

                // Nonaktifkan input agar tidak terkirim saat form disubmit
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
            
            // Jalankan fungsi saat halaman dimuat untuk menyesuaikan dengan state filter saat ini
            toggleFilters();
        });
    </script>
    @endpush
</x-layout-admin>