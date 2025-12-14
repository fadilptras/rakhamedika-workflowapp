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

            {{-- KOLOM KANAN: ALUR PERSETUJUAN (2 TAHAP: ATASAN -> GUDANG) --}}
            <div class="lg:col-span-1 space-y-6 border-t lg:border-t-0 lg:border-l border-zinc-700 pt-6 lg:pt-0 lg:pl-8">
                <h3 class="text-lg font-semibold text-white mb-3">Alur Persetujuan</h3>

                {{-- TAHAP 1: ATASAN --}}
                @php
                    $statusAtasan = $pengajuanBarang->status_atasan;
                    $catatanAtasan = $pengajuanBarang->catatan_atasan;
                    $tglAtasan = $pengajuanBarang->atasan_approved_at;
                    $namaAtasan = $pengajuanBarang->approverAtasan->name ?? $pengajuanBarang->user->name; // Fallback jika data lama

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
                        Atasan Langsung: <span class="text-zinc-300">{{ $namaAtasan }}</span>
                    </label>
                    <p class="flex items-center font-medium {{ $clsAtasan }}">
                        <i class="fas {{ $iconAtasan }} mr-2 w-4 text-center"></i> {{ ucfirst($statusAtasan) }}
                    </p>
                    
                    @if($tglAtasan || $catatanAtasan)
                    <div class="mt-2 space-y-1">
                        @if($tglAtasan)
                            <p class="text-xs text-zinc-400 flex items-center">
                                <i class="fas fa-calendar-alt fa-fw mr-2 text-zinc-500"></i>
                                <span>{{ \Carbon\Carbon::parse($tglAtasan)->translatedFormat('d M Y, H:i') }}</span>
                            </p>
                        @endif
                        @if($catatanAtasan)
                            <div class="text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatanAtasan }}"</div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- TAHAP 2: GUDANG --}}
                @php
                    $statusGudang = $pengajuanBarang->status_gudang;
                    $catatanGudang = $pengajuanBarang->catatan_gudang;
                    $tglGudang = $pengajuanBarang->gudang_approved_at;
                    $namaGudang = $pengajuanBarang->approverGudang->name ?? 'Admin Gudang';

                    $clsGudang = match($statusGudang) {
                        'disetujui' => 'text-emerald-400',
                        'ditolak' => 'text-red-400',
                        'skipped' => 'text-zinc-400',
                        default => 'text-yellow-400',
                    };
                    $iconGudang = match($statusGudang) {
                        'disetujui' => 'fa-check-circle',
                        'ditolak' => 'fa-times-circle',
                        'skipped' => 'fa-minus-circle',
                        default => 'fa-clock',
                    };
                @endphp
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">
                        Gudang/Logistik: <span class="text-zinc-300">{{ $namaGudang }}</span>
                    </label>
                    <p class="flex items-center font-medium {{ $clsGudang }}">
                        <i class="fas {{ $iconGudang }} mr-2 w-4 text-center"></i> {{ ucfirst($statusGudang) }}
                    </p>
                    
                    @if($tglGudang || $catatanGudang)
                    <div class="mt-2 space-y-1">
                        @if($tglGudang)
                            <p class="text-xs text-zinc-400 flex items-center">
                                <i class="fas fa-calendar-alt fa-fw mr-2 text-zinc-500"></i>
                                <span>{{ \Carbon\Carbon::parse($tglGudang)->translatedFormat('d M Y, H:i') }}</span>
                            </p>
                        @endif
                        @if($catatanGudang)
                            <div class="text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatanGudang }}"</div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- STATUS FINAL --}}
                <div class="border-t border-zinc-700 pt-4">
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Status Final</label>
                    @if ($pengajuanBarang->status == 'selesai')
                        <p class="text-xl font-bold flex items-center text-emerald-400"><i class="fas fa-check-circle mr-2"></i> SELESAI</p>
                    @elseif ($pengajuanBarang->status == 'ditolak')
                        <p class="text-xl font-bold flex items-center text-red-400"><i class="fas fa-times-circle mr-2"></i> DITOLAK</p>
                    @elseif ($pengajuanBarang->status == 'diproses')
                        <p class="text-xl font-bold flex items-center text-blue-400"><i class="fas fa-sync-alt fa-spin mr-2"></i> DIPROSES</p>
                    @elseif ($pengajuanBarang->status == 'dibatalkan')
                        <p class="text-xl font-bold flex items-center text-zinc-400"><i class="fas fa-ban mr-2"></i> DIBATALKAN</p>
                    @else
                        <p class="text-xl font-bold flex items-center text-yellow-400"><i class="fas fa-hourglass-start mr-2"></i> DIAJUKAN</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout-admin>