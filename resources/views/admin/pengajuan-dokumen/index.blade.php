<x-layout-admin>
    <x-slot:title>Kelola Pengajuan Dokumen</x-slot:title>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-6">Manajemen Pengajuan Dokumen Karyawan</h1>
        <div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
            <table class="min-w-full text-sm text-left text-zinc-300">
                <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                    <tr>
                        <th class="px-4 py-3">Karyawan</th>
                        <th class="px-4 py-3">Jenis Dokumen</th>
                        <th class="px-4 py-3">Tanggal Pengajuan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse($pengajuanDokumens as $dokumen)
                    <tr class="hover:bg-zinc-700/30">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-semibold text-white">{{ $dokumen->user->name }}</div>
                            <div class="text-xs text-zinc-400">{{ $dokumen->user->divisi ?? 'Divisi Kosong' }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $dokumen->jenis_dokumen }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $dokumen->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs capitalize
                                @if($dokumen->status == 'diajukan') bg-yellow-500/10 text-yellow-400
                                @elseif($dokumen->status == 'diproses') bg-blue-500/10 text-blue-400
                                @elseif($dokumen->status == 'selesai') bg-green-500/10 text-green-400
                                @elseif($dokumen->status == 'ditolak') bg-red-500/10 text-red-400
                                @endif">
                                {{ $dokumen->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.pengajuan-dokumen.show', $dokumen) }}" class="text-indigo-400 hover:underline text-sm font-medium">
                                Lihat & Proses
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-zinc-400">
                            <i class="fas fa-folder-open fa-2x mb-3"></i>
                            <p>Tidak ada pengajuan dokumen yang masuk.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout-admin>