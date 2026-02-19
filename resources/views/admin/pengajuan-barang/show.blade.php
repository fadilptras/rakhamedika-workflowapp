<x-layout-admin>
    <x-slot:title>Detail Pengajuan Barang</x-slot:title>

    <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700">
        {{-- Header --}}
        <div class="p-6 border-b border-zinc-700 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">Detail: {{ $pengajuanBarang->judul_pengajuan }}</h2>
                <p class="text-sm text-zinc-400 mt-1">
                    Diajukan oleh: <span class="font-medium text-zinc-300">{{ $pengajuanBarang->user->name }}</span> 
                    - Divisi {{ $pengajuanBarang->user->divisi }}
                </p>
            </div>
            <a href="{{ route('admin.pengajuan_barang.index') }}" class="text-sm text-zinc-300 hover:text-white transition-colors duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Body --}}
        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-x-8 gap-y-6">
            
            {{-- KOLOM KIRI & TENGAH: DETAIL & DOKUMEN --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- DETAIL UTAMA --}}
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 border-b border-zinc-700 pb-2">Informasi Pengajuan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block font-medium text-zinc-400">Tanggal Pengajuan</label>
                            <p class="text-zinc-200">{{ $pengajuanBarang->created_at->translatedFormat('l, d F Y - H:i') }}</p>
                        </div>
                        <div>
                            <label class="block font-medium text-zinc-400">Status Terkini</label>
                            <p class="text-zinc-200 capitalize">{{ $pengajuanBarang->status }}</p>
                        </div>
                    </div>
                </div>

                {{-- RINCIAN BARANG --}}
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 border-b border-zinc-700 pb-2">Rincian Barang</h3>
                    <div class="relative overflow-x-auto border border-zinc-700 rounded-lg">
                        <table class="w-full text-sm text-left text-zinc-300">
                            <thead class="text-xs text-zinc-400 uppercase bg-zinc-700/50">
                                <tr>
                                    <th class="px-4 py-2 w-10 text-center">No</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                    <th class="px-4 py-2 w-32 text-center">Satuan</th>
                                    <th class="px-4 py-2 w-24 text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuanBarang->rincian_barang as $index => $item)
                                <tr class="border-b border-zinc-700 last:border-0">
                                    <td class="px-4 py-2 text-center text-zinc-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 text-white font-medium">{{ $item['deskripsi'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item['satuan'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center font-bold bg-zinc-700/30">{{ $item['jumlah'] ?? 0 }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-zinc-500">Tidak ada rincian barang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- DOKUMEN TERKAIT --}}
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 border-b border-zinc-700 pb-2">Dokumen Pendukung</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @if ($pengajuanBarang->lampiran && count($pengajuanBarang->lampiran) > 0)
                            @foreach ($pengajuanBarang->lampiran as $lampiran)
                                <a href="{{ asset('storage/' . $lampiran) }}" target="_blank" class="bg-zinc-700 hover:bg-zinc-600 p-3 rounded-lg text-center text-zinc-300 transition">
                                    <i class="fas fa-paperclip mb-2 text-xl"></i>
                                    <p class="text-sm font-semibold">Lampiran {{ $loop->iteration }}</p>
                                    <p class="text-xs text-zinc-400 truncate">{{ basename($lampiran) }}</p>
                                </a>
                            @endforeach
                        @else
                            <p class="text-sm text-zinc-500 bg-zinc-700/50 border border-zinc-600 rounded-lg p-4 text-center w-full col-span-3">Tidak ada dokumen yang dilampirkan.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Ganti bagian Alur Persetujuan (Kolom Kanan) dengan ini --}}

            <div class="lg:col-span-1 space-y-6 border-t lg:border-t-0 lg:border-l border-zinc-700 pt-6 lg:pt-0 lg:pl-8">
                <h3 class="text-lg font-semibold text-white mb-3">Alur Persetujuan</h3>

                {{-- TAHAP 1: ATASAN --}}
                @php
                    $statusAtasan = $pengajuanBarang->status_appr_1;
                    $clsAtasan = match($statusAtasan) {
                        'disetujui' => 'text-emerald-400',
                        'ditolak' => 'text-red-400',
                        'skipped' => 'text-zinc-400',
                        default => 'text-yellow-400',
                    };
                    $iconAtasan = match($statusAtasan) {
                        'disetujui' => 'fa-check-circle',
                        'ditolak' => 'fa-times-circle',
                        'skipped' => 'fa-minus-circle',
                        default => 'fa-clock',
                    };
                @endphp
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">
                        Atasan Langsung: <span class="text-zinc-300">{{ $pengajuanBarang->approverAtasan->name ?? $pengajuanBarang->user->name }}</span>
                    </label>
                    <p class="flex items-center font-medium {{ $clsAtasan }}">
                        <i class="fas {{ $iconAtasan }} mr-2 w-4 text-center"></i> {{ ucfirst($statusAtasan ?? 'Menunggu') }}
                    </p>
                    @if($pengajuanBarang->tanggal_approve_1)
                        <p class="text-xs text-zinc-500 mt-1"><i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($pengajuanBarang->tanggal_approve_1)->translatedFormat('d M Y, H:i') }}</p>
                    @endif
                </div>

                {{-- TAHAP 2: GUDANG/DIREKTUR --}}
                @php
                    $statusGudang = $pengajuanBarang->status_appr_2;
                    $clsGudang = match($statusGudang) {
                        'disetujui' => 'text-emerald-400',
                        'ditolak' => 'text-red-400',
                        'skipped' => 'text-zinc-400',
                        default => 'text-yellow-400',
                    };
                @endphp
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">
                        Gudang/Logistik: <span class="text-zinc-300">{{ $pengajuanBarang->approverGudang->name ?? 'Admin Gudang' }}</span>
                    </label>
                    <p class="flex items-center font-medium {{ $clsGudang }}">
                        <i class="fas {{ match($statusGudang){'disetujui'=>'fa-check-circle','ditolak'=>'fa-times-circle','skipped'=>'fa-minus-circle',default=>'fa-clock'} }} mr-2 w-4 text-center"></i> 
                        {{ ucfirst($statusGudang ?? 'Menunggu') }}
                    </p>
                    @if($pengajuanBarang->tanggal_approve_2)
                        <p class="text-xs text-zinc-500 mt-1"><i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($pengajuanBarang->tanggal_approve_2)->translatedFormat('d M Y, H:i') }}</p>
                    @endif
                </div>

                {{-- TAHAP 3: MANAJER KEUANGAN (BARU) --}}
                @php
                    $statusKeuangan = $pengajuanBarang->status_appr_3;
                    $clsKeuangan = match($statusKeuangan) {
                        'disetujui' => 'text-emerald-400',
                        'ditolak' => 'text-red-400',
                        'skipped' => 'text-zinc-400',
                        default => 'text-yellow-400',
                    };
                @endphp
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">
                        Manajer Keuangan: <span class="text-zinc-300">{{ $pengajuanBarang->approver3->name ?? 'Belum Ditentukan' }}</span>
                    </label>
                    <p class="flex items-center font-medium {{ $clsKeuangan }}">
                        <i class="fas {{ match($statusKeuangan){'disetujui'=>'fa-check-circle','ditolak'=>'fa-times-circle','skipped'=>'fa-minus-circle',default=>'fa-clock'} }} mr-2 w-4 text-center"></i> 
                        {{ ucfirst($statusKeuangan ?? 'Menunggu') }}
                    </p>
                    @if($pengajuanBarang->tanggal_approved_3)
                        <p class="text-xs text-zinc-500 mt-1"><i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($pengajuanBarang->tanggal_approved_3)->translatedFormat('d M Y, H:i') }}</p>
                    @endif
                </div>

                {{-- STATUS FINAL --}}
                <div class="border-t border-zinc-700 pt-4">
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Status Final</label>
                    @php
                        $finalMatch = match($pengajuanBarang->status) {
                            'selesai' => ['text-emerald-400', 'fa-check-circle', 'SELESAI'],
                            'ditolak' => ['text-red-400', 'fa-times-circle', 'DITOLAK'],
                            'dibatalkan' => ['text-zinc-400', 'fa-ban', 'DIBATALKAN'],
                            'diproses' => ['text-blue-400', 'fa-sync-alt fa-spin', 'DIPROSES'],
                            default => ['text-yellow-400', 'fa-hourglass-start', 'DIAJUKAN'],
                        };
                    @endphp
                    <p class="text-xl font-bold flex items-center {{ $finalMatch[0] }}">
                        <i class="fas {{ $finalMatch[1] }} mr-2"></i> {{ $finalMatch[2] }}
                    </p>
                </div>
            </div>
            
    </div>
</x-layout-admin>