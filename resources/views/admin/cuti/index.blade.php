<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- HEADER DAN NOTIFIKASI --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- FILTER FORM --}}
    <div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
        <form method="GET" action="{{ route('admin.cuti.index') }}" class="flex flex-wrap items-end gap-4">
            {{-- Filter Karyawan --}}
            <div>
                <label for="user_id" class="block text-sm font-medium text-zinc-300">Karyawan</label>
                <select name="user_id" id="user_id" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Karyawan</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Akhir --}}
            <div>
                <label for="status" class="block text-sm font-medium text-zinc-300">Status Akhir</label>
                <select name="status" id="status" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="diterima" @selected(request('status') == 'diterima')>Diterima</option>
                    <option value="ditolak" @selected(request('status') == 'ditolak')>Ditolak</option>
                    <option value="diajukan" @selected(request('status') == 'diajukan')>Menunggu Persetujuan</option>
                </select>
            </div>

            {{-- Filter Jenis Cuti --}}
            <div>
                <label for="jenis_cuti" class="block text-sm font-medium text-zinc-300">Jenis Cuti</label>
                <select name="jenis_cuti" id="jenis_cuti" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Jenis</option>
                    <option value="tahunan" @selected(request('jenis_cuti') == 'tahunan')>Tahunan</option>
                </select>
            </div>
            
            {{-- Filter Tanggal Mulai --}}
            <div>
                <label for="tanggal_mulai" class="block text-sm font-medium text-zinc-300">Dari Tanggal</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Filter Tanggal Selesai --}}
            <div>
                <label for="tanggal_selesai" class="block text-sm font-medium text-zinc-300">Sampai Tanggal</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
             <div class="flex items-end gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.cuti.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- TABEL DATA PENGAJUAN CUTI --}}
    <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full text-zinc-300">
            <thead class="bg-zinc-700 text-left text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">Karyawan</th>
                    <th class="px-5 py-3">Jenis Cuti</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Status Akhir</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse($cutiRequests as $cuti)
                <tr class="hover:bg-zinc-700/50">
                    <td class="px-5 py-4">{{ $cuti->user->name }}</td>
                    <td class="px-5 py-4 capitalize">{{ $cuti->jenis_cuti }}</td>
                    <td class="px-5 py-4">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                            @if($cuti->status == 'diterima') bg-green-500/10 text-green-400
                            @elseif($cuti->status == 'ditolak') bg-red-500/10 text-red-400
                            @else bg-yellow-500/10 text-yellow-400 @endif">
                            {{ ucfirst($cuti->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <a href="{{ route('admin.cuti.show', $cuti) }}" class="text-indigo-400 hover:underline text-sm font-medium">
                            Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-zinc-400">Belum ada pengajuan cuti.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINASI --}}
    <div class="mt-6">
        {{ $cutiRequests->links() }}
    </div>

</x-layout-admin>