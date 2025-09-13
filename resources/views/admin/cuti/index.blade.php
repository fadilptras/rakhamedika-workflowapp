<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full text-zinc-300">
            <thead class="bg-zinc-700 text-left text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">Karyawan</th>
                    <th class="px-5 py-3">Jenis Cuti</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Status Akhir</th>
                    <th class="px-5 py-3">Persetujuan Manajer</th>
                    <th class="px-5 py-3">Persetujuan HRD</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @php $user = Auth::user(); @endphp
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
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                            @if($cuti->status_manajer == 'disetujui') bg-green-500/10 text-green-400
                            @elseif($cuti->status_manajer == 'ditolak') bg-red-500/10 text-red-400
                            @else bg-yellow-500/10 text-yellow-400 @endif">
                            {{ ucfirst($cuti->status_manajer) }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                            @if($cuti->status_hrd == 'disetujui') bg-green-500/10 text-green-400
                            @elseif($cuti->status_hrd == 'ditolak') bg-red-500/10 text-red-400
                            @else bg-yellow-500/10 text-yellow-400 @endif">
                            {{ ucfirst($cuti->status_hrd) }}
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
                    <td colspan="7" class="text-center py-10 text-zinc-400">Belum ada pengajuan cuti.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout-admin>