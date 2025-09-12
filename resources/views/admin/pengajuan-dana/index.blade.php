<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-6">Rekap Pengajuan Dana</h1>

        <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
            <h3 class="text-lg font-semibold text-white mb-4">Filter Data</h3>
            <form action="{{ route('admin.pengajuan_dana.index') }}" method="GET">
                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-zinc-300">Status</label>
                        <select id="status" name="status" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-zinc-300">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="tanggal_akhir" class="block text-sm font-medium text-zinc-300">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
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

        <div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
            <table class="min-w-full text-sm text-left text-zinc-300">
                <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Pemohon</th>
                        <th class="px-4 py-3">Judul Pengajuan</th>
                        <th class="px-4 py-3">Total Dana</th>
                        <th class="px-4 py-3">Status Atasan</th>
                        <th class="px-4 py-3">Status HRD</th>
                        <th class="px-4 py-3">Status Direktur</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse ($pengajuanDanas as $pengajuan)
                        <tr class="hover:bg-zinc-700/30">
                            <td class="px-4 py-3">{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $pengajuan->user->name }}</td>
                            <td class="px-4 py-3">{{ $pengajuan->judul_pengajuan }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($pengajuan->total_dana, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                    @if ($pengajuan->status_atasan == 'menunggu') bg-yellow-400 text-yellow-900
                                    @elseif ($pengajuan->status_atasan == 'disetujui') bg-green-500 text-green-900
                                    @else bg-red-500 text-red-900 @endif">
                                    {{ ucfirst($pengajuan->status_atasan) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                    @if ($pengajuan->status_hrd == 'menunggu') bg-yellow-400 text-yellow-900
                                    @elseif ($pengajuan->status_hrd == 'disetujui') bg-green-500 text-green-900
                                    @else bg-red-500 text-red-900 @endif">
                                    {{ ucfirst($pengajuan->status_hrd) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                    @if ($pengajuan->status_direktur == 'menunggu') bg-yellow-400 text-yellow-900
                                    @elseif ($pengajuan->status_direktur == 'disetujui') bg-green-500 text-green-900
                                    @else bg-red-500 text-red-900 @endif">
                                    {{ ucfirst($pengajuan->status_direktur) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.pengajuan_dana.show', $pengajuan->id) }}" class="text-indigo-400 hover:underline text-sm font-medium">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-zinc-400" colspan="8">Tidak ada data pengajuan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $pengajuanDanas->links() }}
        </div>
    </div>
</x-layout-admin>