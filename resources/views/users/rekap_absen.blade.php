<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="p-4 md:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Rekap Absensi - {{ \Carbon\Carbon::create()->month($bulanDipilih)->translatedFormat('F') }} {{ $tahunDipilih }}
                    </h2>

                    {{-- Form Filter --}}
                    <form method="GET" action="{{ route('rekap_absen.index') }}" class="flex items-center gap-2">
                        <select name="bulan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            @foreach($daftarBulan as $num => $nama)
                                <option value="{{ $num }}" {{ $num == $bulanDipilih ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            @foreach($daftarTahun as $tahun)
                                <option value="{{ $tahun }}" {{ $tahun == $tahunDipilih ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg flex items-center">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                    </form>
                </div>


                {{-- Kotak Rekap --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-emerald-600">{{ $rekap['hadir'] }}</p>
                        <p class="text-sm font-semibold text-emerald-800 mt-1">Hadir</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-red-600">{{ $rekap['sakit'] }}</p>
                        <p class="text-sm font-semibold text-red-800 mt-1">Sakit</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-amber-600">{{ $rekap['izin'] }}</p>
                        <p class="text-sm font-semibold text-amber-800 mt-1">Izin</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-purple-600">{{ $rekap['cuti'] }}</p>
                        <p class="text-sm font-semibold text-purple-800 mt-1">Cuti</p>
                    </div>
                </div>

                {{-- Tabel Detail Absensi --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($absensi as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                                    <td class="py-3 px-4 text-center">
                                         <span class="px-2.5 py-1 font-semibold leading-tight rounded-full text-xs capitalize
                                            @if($item->status == 'hadir') bg-green-100 text-green-800
                                            @elseif($item->status == 'sakit') bg-red-100 text-red-800
                                            @elseif($item->status == 'izin') bg-yellow-100 text-yellow-800
                                            @elseif($item->status == 'cuti') bg-purple-100 text-purple-800
                                            @endif">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $item->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-gray-500">
                                        <i class="fas fa-box-open text-3xl mb-2"></i>
                                        <p>Tidak ada data absensi untuk periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-layout-users>
