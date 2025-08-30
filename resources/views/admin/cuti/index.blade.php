<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full text-zinc-300">
            <thead class="bg-zinc-700 text-left text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">Karyawan</th>
                    <th class="px-5 py-3">Jenis Cuti</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Status</th>
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
                            @if($cuti->status == 'disetujui') bg-green-500/10 text-green-400
                            @elseif($cuti->status == 'ditolak') bg-red-500/10 text-red-400
                            @else bg-yellow-500/10 text-yellow-400 @endif">
                            {{ ucfirst($cuti->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($cuti->status == 'diajukan')
                        <form action="{{ route('admin.cuti.update', $cuti) }}" method="POST" class="inline-flex gap-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" name="status" value="disetujui" class="text-green-400 hover:text-green-300 transition-colors">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </button>
                            <button type="submit" name="status" value="ditolak" class="text-red-500 hover:text-red-400 transition-colors">
                                <i class="fas fa-times-circle fa-lg"></i>
                            </button>
                        </form>
                        @else
                        -
                        @endif
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
</x-layout-admin>