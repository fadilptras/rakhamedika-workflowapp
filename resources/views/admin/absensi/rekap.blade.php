<x-layout-admin>
    <x-slot:title>Rekap Absensi Bulanan</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Rekap Absensi Bulanan</h1>
        {{-- Tombol Download PDF --}}
        <a href="{{ route('admin.absensi.rekap.downloadPdf', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <i class="fas fa-file-pdf mr-2"></i> Download PDF
        </a>
    </div>

    {{-- Filter --}}
    <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
        <form method="GET" action="{{ route('admin.absensi.rekap') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-zinc-300">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-zinc-300">Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="divisi" class="block text-sm font-medium text-zinc-300">Divisi</label>
                <select name="divisi" id="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d }}" {{ $divisi == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.absensi.rekap') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Rekap Absensi --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow-lg border border-zinc-300">
        <table class="text-sm text-left text-zinc-800 border-collapse">
            <thead class="bg-zinc-200 text-xs uppercase font-semibold text-zinc-700">
                <tr class="border-b border-zinc-300">
                    <th class="px-4 py-3 bg-zinc-200 w-48 border-r border-zinc-300">No. & Karyawan</th>
                    <th class="px-4 py-3 text-center border-r border-zinc-300" colspan="{{ $allDates->count() }}">Bulan {{ \Carbon\Carbon::parse($startDate)->isoFormat('MMMM YYYY') }}</th>
                    <th class="px-4 py-3 text-center border-r border-zinc-300" colspan="6">Rekap Kehadiran</th>
                    <th class="px-4 py-3">Waktu Terlambat</th>
                </tr>
                <tr class="border-b border-zinc-300">
                    <th class="px-4 py-3 bg-zinc-200 border-r border-zinc-300"></th>
                    @foreach($allDates as $date)
                        @php
                            $isWeekend = $date->isWeekend();
                            $textColor = $isWeekend ? 'text-gray-500' : '';
                        @endphp
                        <th class="px-2 py-2 text-center border-r border-zinc-300 {{ $textColor }}">{{ $date->day }}</th>
                    @endforeach
                    <th class="px-2 py-2 text-center text-green-600 font-bold border-r border-zinc-300">H</th>
                    <th class="px-2 py-2 text-center text-red-600 font-bold border-r border-zinc-300">S</th>
                    <th class="px-2 py-2 text-center text-orange-600 font-bold border-r border-zinc-300">I</th>
                    <th class="px-2 py-2 text-center text-blue-600 font-bold border-r border-zinc-300">C</th>
                    <th class="px-2 py-2 text-center text-gray-600 font-bold border-r border-zinc-300">A</th>
                    <th class="px-2 py-2 text-center text-purple-600 font-bold border-r border-zinc-300">L</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-300">
                @forelse ($rekapData as $index => $data)
                <tr class="hover:bg-zinc-50">
                    <td class="px-4 py-3 bg-white hover:bg-zinc-50 border-r border-zinc-300">
                        <p class="font-semibold text-zinc-800">{{ $index + 1 }}. {{ $data['user']->name ?? 'User Dihapus' }}</p>
                        <p class="text-xs text-zinc-500">{{ $data['user']->jabatan ?? '-' }}</p>
                    </td>
                    @foreach($allDates as $date)
                        @php
                            $isWeekend = $date->isWeekend();
                            $statusString = $data['daily'][$date->toDateString()] ?? '-';
                            $hasLembur = str_contains($statusString, 'L');
                            $mainStatus = trim(str_replace('L', '', $statusString));
                            
                            // Tentukan warna badge berdasarkan status
                            $colorClass = 'text-gray-600';
                            if ($isWeekend) {
                                $colorClass = 'text-gray-400';
                                $mainStatus = '-'; // Pastikan status di hari weekend adalah '-'
                            } else {
                                switch ($mainStatus) {
                                    case 'H': $colorClass = 'text-green-600'; break;
                                    case 'S': $colorClass = 'text-red-600'; break;
                                    case 'I': $colorClass = 'text-orange-600'; break;
                                    case 'C': $colorClass = 'text-blue-600'; break;
                                    case 'A': $colorClass = 'text-gray-800'; break;
                                    case '-': $colorClass = 'text-zinc-400'; break;
                                }
                            }
                        @endphp
                        <td class="px-2 py-2 text-center font-bold border-r border-zinc-300 {{ $isWeekend ? 'bg-gray-100' : '' }}">
                            <span class="{{ $colorClass }}">{{ $mainStatus }}</span>
                            @if ($hasLembur)
                                <span class="text-purple-600">L</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="px-2 py-2 text-center font-bold text-green-600 border-r border-zinc-300">{{ $data['summary']['H'] }}</td>
                    <td class="px-2 py-2 text-center font-bold text-red-600 border-r border-zinc-300">{{ $data['summary']['S'] }}</td>
                    <td class="px-2 py-2 text-center font-bold text-orange-600 border-r border-zinc-300">{{ $data['summary']['I'] }}</td>
                    <td class="px-2 py-2 text-center font-bold text-blue-600 border-r border-zinc-300">{{ $data['summary']['C'] }}</td>
                    <td class="px-2 py-2 text-center font-bold text-gray-800 border-r border-zinc-300">{{ $data['summary']['A'] }}</td>
                    <td class="px-2 py-2 text-center font-bold text-purple-600 border-r border-zinc-300">{{ $data['summary']['L'] }}</td>
                    <td class="px-4 py-3 font-semibold text-red-600">{{ $data['summary']['terlambat_formatted'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $allDates->count() + 7 }}" class="px-4 py-6 text-center text-zinc-500">
                        Tidak ada data absensi yang cocok dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout-admin>