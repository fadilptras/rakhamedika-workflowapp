    <x-layout-admin>
        <x-slot:title>{{ $title }}</x-slot:title>

        <div class="p-6">
            <h1 class="text-2xl font-bold text-white mb-6">Manajemen Pengajuan Cuti Karyawan</h1>

            {{-- Form Filter --}}
            <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
                <h3 class="text-lg font-semibold text-white mb-4">Filter Data Cuti</h3>
                <form action="{{ route('admin.cuti.index') }}" method="GET">
                    <div class="flex flex-wrap items-end gap-4">
                        {{-- Filter Karyawan --}}
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-zinc-300">Karyawan</label>
                            <select name="user_id" id="user_id" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Semua Karyawan</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Filter Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-zinc-300">Status</label>
                            <select id="status" name="status" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Semua Status</option>
                                <option value="diajukan" @selected(request('status') == 'diajukan')>Diajukan</option>
                                <option value="disetujui" @selected(request('status') == 'disetujui')>Disetujui</option>
                                <option value="ditolak" @selected(request('status') == 'ditolak')>Ditolak</option>
                            </select>
                        </div>
                        {{-- Filter Tanggal --}}
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-zinc-300">Dari Tanggal</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-medium text-zinc-300">Sampai Tanggal</label>
                            <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        {{-- Tombol Aksi --}}
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                            <a href="{{ route('admin.cuti.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel Rekap Cuti --}}
            <div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
                <table class="min-w-full text-sm text-left text-zinc-300">
                    <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                        <tr>
                            <th class="px-4 py-3">Karyawan</th>
                            <th class="px-4 py-3">Jenis Cuti</th>
                            <th class="px-4 py-3">Tanggal & Durasi</th>
                            <th class="px-4 py-3">Status Akhir</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700">
                        @forelse($cutiRequests as $cuti)
                        <tr class="hover:bg-zinc-700/30">
                            <td class="px-4 py-3">
                                {{-- INI BAGIAN YANG DIUBAH --}}
                                <div class="font-semibold text-white">{{ $cuti->user->name }}</div>
                                <div class="text-xs text-zinc-400">{{ $cuti->user->jabatan ?? 'Jabatan tidak diatur' }}</div>
                            </td>
                            <td class="px-4 py-3 capitalize">{{ $cuti->jenis_cuti }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</div>
                                <div class="text-xs text-zinc-400">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs capitalize
                                    @if($cuti->status == 'disetujui') bg-green-500/10 text-green-400
                                    @elseif($cuti->status == 'ditolak') bg-red-500/10 text-red-400
                                    @else bg-yellow-500/10 text-yellow-400 @endif">
                                    {{ $cuti->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.cuti.show', $cuti) }}" class="text-indigo-400 hover:underline text-sm font-medium">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-zinc-400">
                                Tidak ada data pengajuan cuti yang cocok dengan filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $cutiRequests->links() }}
            </div>
        </div>
    </x-layout-admin>