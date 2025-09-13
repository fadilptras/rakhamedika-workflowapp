<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    <div class="p-6">
        <x-back-button href="{{ route('admin.cuti.index') }}">Kembali ke Pengajuan Cuti</x-back-button>
        
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8 text-zinc-300">
            <h2 class="text-xl font-bold text-white mb-6">Detail Pengajuan Cuti</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Nama Pemohon</label>
                    <p class="font-semibold text-white">{{ $cuti->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Jenis Cuti</label>
                    <p class="font-semibold text-white capitalize">{{ $cuti->jenis_cuti }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Pengajuan</label>
                    <p class="font-semibold text-white">{{ $cuti->created_at->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Durasi</label>
                    <p class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Mulai</label>
                    <p class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Selesai</label>
                    <p class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d F Y') }}</p>
                </div>
            </div>

            <div class="md:col-span-2 mt-6">
                <label class="block text-sm font-medium text-zinc-400 mb-2">
                    Alasan Cuti
                </label>
                <div class="p-4 bg-zinc-700 rounded-lg">
                    <p class="text-zinc-300">{{ $cuti->alasan }}</p>
                </div>
            </div>

            @if ($cuti->lampiran)
                <div class="md:col-span-2 mt-6">
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Lampiran Dokumen</label>
                    <div class="p-4 bg-zinc-700 rounded-lg text-center">
                        <a href="{{ asset('storage/' . $cuti->lampiran) }}" target="_blank" class="mt-2 inline-block text-indigo-400 hover:underline">
                            Lihat Berkas Lampiran
                        </a>
                    </div>
                </div>
            @endif
            
            <hr class="my-8 border-zinc-700">

            <div class="bg-zinc-800 rounded-lg shadow p-6 mb-8 border border-zinc-700">
                <h2 class="text-xl font-bold text-white mb-6">Status Persetujuan</h2>
                <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                    <div class="flex flex-col items-center w-full md:w-1/3 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-600 text-white font-bold shadow-md">
                            1
                        </div>
                        <p class="mt-2 text-sm font-semibold text-white">Diajukan</p>
                        <p class="text-xs text-zinc-400">{{ $cuti->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-green-400 font-medium">✔ Selesai</p>
                    </div>

                    <div class="h-4 w-1 md:h-1 md:w-full {{ $cuti->status_manajer != 'diajukan' ? 'bg-indigo-600' : 'bg-zinc-700' }}"></div>

                    <div class="flex flex-col items-center w-full md:w-1/3 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $cuti->status_manajer == 'disetujui' ? 'bg-indigo-600' : 'bg-zinc-700' }} text-white font-bold shadow-md">
                            2
                        </div>
                        <p class="mt-2 text-sm font-semibold text-white">Disetujui Manajer</p>
                        @if ($cuti->status_manajer == 'disetujui')
                            <p class="text-xs text-green-400 font-medium">✔ Selesai</p>
                        @elseif ($cuti->status_manajer == 'ditolak')
                            <p class="text-xs text-red-400 font-medium">❌ Ditolak</p>
                        @else
                            <p class="text-xs text-zinc-400">Menunggu...</p>
                        @endif
                    </div>

                    <div class="h-4 w-1 md:h-1 md:w-full {{ $cuti->status_hrd != 'diajukan' ? 'bg-indigo-600' : 'bg-zinc-700' }}"></div>

                    <div class="flex flex-col items-center w-full md:w-1/3 text-center">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $cuti->status_hrd == 'disetujui' ? 'bg-indigo-600' : 'bg-zinc-700' }} text-white font-bold shadow-md">
                            3
                        </div>
                        <p class="mt-2 text-sm font-semibold text-white">Disetujui HRD</p>
                        @if ($cuti->status_hrd == 'disetujui')
                            <p class="text-xs text-green-400 font-medium">✔ Selesai</p>
                        @elseif ($cuti->status_hrd == 'ditolak')
                            <p class="text-xs text-red-400 font-medium">❌ Ditolak</p>
                        @else
                            <p class="text-xs text-zinc-400">Menunggu...</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-white mb-3">Catatan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-zinc-800 text-sm text-zinc-300">
                        <thead class="bg-zinc-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-zinc-300">Dari</th>
                                <th class="px-4 py-2 text-left font-medium text-zinc-300">Catatan</th>
                                <th class="px-4 py-2 text-left font-medium text-zinc-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-700">
                            <tr class="border-t">
                                <td class="px-4 py-2">Manajer</td>
                                <td class="px-4 py-2">{{ $cuti->catatan_manajer ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                        @if ($cuti->status_manajer == 'diajukan') bg-yellow-400/10 text-yellow-400
                                        @elseif ($cuti->status_manajer == 'disetujui') bg-green-500/10 text-green-400
                                        @else bg-red-500/10 text-red-400 @endif">
                                        {{ ucfirst($cuti->status_manajer) }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-t">
                                <td class="px-4 py-2">HRD</td>
                                <td class="px-4 py-2">{{ $cuti->catatan_hrd ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                        @if ($cuti->status_hrd == 'diajukan') bg-yellow-400/10 text-yellow-400
                                        @elseif ($cuti->status_hrd == 'disetujui') bg-green-500/10 text-green-400
                                        @else bg-red-500/10 text-red-400 @endif">
                                        {{ ucfirst($cuti->status_hrd) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @php
                $userJabatan = strtolower(Auth::user()->jabatan);
            @endphp
            @if ($userJabatan === 'manajer' && $cuti->status_manajer === 'diajukan')
                <div class="bg-zinc-800 rounded-lg shadow p-6 mt-8 border border-zinc-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Tindakan Persetujuan Manajer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label for="catatan-setuju" class="block text-sm font-medium text-zinc-400 mb-2">Catatan (Opsional)</label>
                            <textarea id="catatan-setuju" name="catatan_persetujuan" rows="3" class="w-full p-3 border border-zinc-600 rounded-lg bg-zinc-700 text-white focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Tambahkan catatan persetujuan..."></textarea>
                            <input type="hidden" name="status" value="disetujui">
                            <button type="submit" class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                                Setujui Pengajuan
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label for="catatan-tolak" class="block text-sm font-medium text-zinc-400 mb-2">Catatan Penolakan (Wajib)</label>
                            <textarea id="catatan-tolak" name="catatan_penolakan" rows="3" class="w-full p-3 border border-zinc-600 rounded-lg bg-zinc-700 text-white focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
                            <input type="hidden" name="status" value="ditolak">
                            <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                                Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @elseif ($userJabatan === 'hrd' && $cuti->status_manajer === 'disetujui' && $cuti->status_hrd === 'diajukan')
                <div class="bg-zinc-800 rounded-lg shadow p-6 mt-8 border border-zinc-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Tindakan Persetujuan HRD</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label for="catatan-setuju" class="block text-sm font-medium text-zinc-400 mb-2">Catatan (Opsional)</label>
                            <textarea id="catatan-setuju" name="catatan_persetujuan" rows="3" class="w-full p-3 border border-zinc-600 rounded-lg bg-zinc-700 text-white focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Tambahkan catatan persetujuan..."></textarea>
                            <input type="hidden" name="status" value="disetujui">
                            <button type="submit" class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                                Setujui Pengajuan
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label for="catatan-tolak" class="block text-sm font-medium text-zinc-400 mb-2">Catatan Penolakan (Wajib)</label>
                            <textarea id="catatan-tolak" name="catatan_penolakan" rows="3" class="w-full p-3 border border-zinc-600 rounded-lg bg-zinc-700 text-white focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
                            <input type="hidden" name="status" value="ditolak">
                            <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                                Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout-admin>