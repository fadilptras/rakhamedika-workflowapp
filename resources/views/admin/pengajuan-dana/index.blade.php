<x-layout-admin>
    <x-slot:title>Kelola Pengajuan Dana</x-slot:title>

    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-6">Manajemen Pengajuan Dana Karyawan</h1>

        {{-- Form Filter --}}
        <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
            <h3 class="text-lg font-semibold text-white mb-4">Filter Pengajuan Dana</h3>
            <form action="{{ route('admin.pengajuan_dana.index') }}" method="GET">
                <div class="flex flex-wrap items-end gap-4">
                    {{-- Filter Karyawan --}}
                    <div class="flex-1 min-w-[200px]">
                        <label for="karyawan_id" class="block text-sm font-medium text-zinc-300">Karyawan</label>
                        <select id="karyawan_id" name="karyawan_id" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Karyawan</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->id }}" @selected(request('karyawan_id') == $karyawan->id)>
                                    {{ $karyawan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Divisi --}}
                    <div class="flex-1 min-w-[180px]">
                        <label for="divisi" class="block text-sm font-medium text-zinc-300">Divisi</label>
                        <select id="divisi" name="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Divisi</option>
                             @foreach($divisiList as $item)
                                <option value="{{ $item->divisi }}" @selected(request('divisi') == $item->divisi)>
                                    {{ $item->divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Filter Tanggal Mulai --}}
                    <div class="flex-1 min-w-[150px]">
                        <label for="start_date" class="block text-sm font-medium text-zinc-300">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    {{-- Filter Tanggal Akhir --}}
                    <div class="flex-1 min-w-[150px]">
                        <label for="end_date" class="block text-sm font-medium text-zinc-300">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                        <a href="{{ route('admin.pengajuan_dana.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Pengajuan Dana --}}
        <div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
            <table class="min-w-full text-sm text-left text-zinc-300">
                <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                    <tr>
                        <th class="px-4 py-3">Karyawan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Keperluan</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse ($pengajuanDana as $item)
                        <tr class="hover:bg-zinc-700/30">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-white">{{ $item->user->name }}</div>
                                <div class="text-xs text-zinc-400">{{ $item->divisi ?? 'Divisi Kosong' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3">{{ Str::limit($item->judul_pengajuan, 40) }}</td>
                            <td class="px-4 py-3 font-mono text-white whitespace-nowrap">
                                Rp {{ number_format($item->total_dana, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusText = 'Menunggu Atasan';
                                    $statusClass = 'bg-yellow-500/10 text-yellow-400';

                                    if ($item->status_atasan === 'ditolak' || $item->status_finance === 'ditolak') {
                                        $statusText = 'Ditolak';
                                        $statusClass = 'bg-red-500/10 text-red-400';
                                    } elseif ($item->status_atasan === 'disetujui' && $item->status_finance === 'disetujui') {
                                        $statusText = 'Disetujui';
                                        $statusClass = 'bg-green-500/10 text-green-400';
                                    } elseif ($item->status_atasan === 'disetujui') {
                                        $statusText = 'Menunggu Finance';
                                        $statusClass = 'bg-indigo-500/10 text-indigo-400';
                                    }
                                @endphp
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs capitalize {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.pengajuan_dana.show', $item->id) }}" class="text-indigo-400 hover:underline text-sm font-medium">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-zinc-400">
                                <i class="fas fa-folder-open fa-2x mb-3"></i>
                                <p>Tidak ada data pengajuan dana yang cocok dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout-admin>