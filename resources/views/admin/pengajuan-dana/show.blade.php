<x-layout-admin>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>

    <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700">
        {{-- Header --}}
        <div class="p-6 border-b border-zinc-700 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">Detail Pengajuan: {{ $pengajuanDana->judul_pengajuan }}</h2>
                <p class="text-sm text-zinc-400 mt-1">
                    Diajukan oleh: 
                    <span class="font-medium text-zinc-300">{{ $pengajuanDana->user->name }}</span> 
                    - Divisi {{ $pengajuanDana->user->divisi }}
                </p>
            </div>
            <a href="{{ route('admin.pengajuan_dana.index') }}" class="text-sm text-zinc-300 hover:text-white transition-colors duration-200 flex items-center">
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
                            <p class="text-zinc-200">{{ $pengajuanDana->created_at->translatedFormat('l, d F Y - H:i') }}</p>
                        </div>
                        <div>
                            <label class="block font-medium text-zinc-400">Informasi Transfer</label>
                            <p class="text-zinc-200">{{ $pengajuanDana->nama_bank }} - {{ $pengajuanDana->no_rekening }}</p>
                        </div>
                    </div>
                </div>

                {{-- RINCIAN DANA --}}
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 border-b border-zinc-700 pb-2">Rincian Dana</h3>
                    <div class="space-y-3 border border-zinc-700 rounded-lg p-4">
                        @forelse($pengajuanDana->rincian_dana as $rincian)
                        <div class="flex justify-between items-center text-zinc-300">
                            <span>- {{ $rincian['deskripsi'] ?? 'Item tidak ada deskripsi' }}</span>
                            <span class="font-mono">Rp {{ number_format($rincian['jumlah'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @empty
                            <p class="text-zinc-400 text-sm">Rincian dana tidak tersedia.</p>
                        @endforelse
                        <div class="border-t border-zinc-600 pt-3 mt-3 flex justify-between items-center text-white font-bold">
                            <span>TOTAL</span>
                            <span class="font-mono text-xl text-amber-400">Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- DOKUMEN TERKAIT --}}
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 border-b border-zinc-700 pb-2">Dokumen Terkait</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- ================= KODE BARU ================= --}}
                        @if ($pengajuanDana->lampiran && count($pengajuanDana->lampiran) > 0)
                            @foreach ($pengajuanDana->lampiran as $lampiran)
                                <a href="{{ asset('storage/' . $lampiran) }}" target="_blank" class="bg-zinc-700 hover:bg-zinc-600 p-3 rounded-lg text-center text-zinc-300 transition">
                                    <i class="fas fa-paperclip mb-2 text-xl"></i>
                                    <p class="text-sm font-semibold">Lampiran {{ $loop->iteration }}</p>
                                    <p class="text-xs text-zinc-400 truncate">{{ basename($lampiran) }}</p>
                                </a>
                            @endforeach
                        @endif
                        @if($pengajuanDana->bukti_transfer)
                        <a href="{{ asset('storage/' . $pengajuanDana->bukti_transfer) }}" target="_blank" class="bg-zinc-700 hover:bg-zinc-600 p-3 rounded-lg text-center text-zinc-300 transition">
                            <i class="fas fa-receipt mb-2 text-xl"></i><p class="text-sm font-semibold">Bukti Transfer</p>
                        </a>
                        @endif
                        @if($pengajuanDana->invoice)
                        <a href="{{ asset('storage/' . $pengajuanDana->invoice) }}" target="_blank" class="bg-zinc-700 hover:bg-zinc-600 p-3 rounded-lg text-center text-zinc-300 transition">
                            <i class="fas fa-file-invoice-dollar mb-2 text-xl"></i><p class="text-sm font-semibold">Invoice Final</p>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: ALUR PERSETUJUAN --}}
            {{-- KOLOM KANAN: ALUR PERSETUJUAN (KODE BARU) --}}
            <div class="lg:col-span-1 space-y-6 border-t lg:border-t-0 lg:border-l border-zinc-700 pt-6 lg:pt-0 lg:pl-8">
                <h3 class="text-lg font-semibold text-white mb-3">Alur Persetujuan</h3>
            
                {{-- TAHAP 1: ATASAN ATAU DIREKTUR (LOGIKA BARU) --}}
                @php
                    $title_atasan = ''; $catatan_atasan = ''; $status_atasan_class = ''; $status_atasan_text = ''; $status_atasan_icon = '';
            
                    if ($pengajuanDana->status === 'dibatalkan') {
                        $status_atasan_text = 'Dibatalkan'; $status_atasan_class = 'text-zinc-400'; $status_atasan_icon = 'fa-ban';
                        $title_atasan = $pengajuanDana->user->is_kepala_divisi ? 'Persetujuan Direktur' : 'Persetujuan Atasan';
                    } else {
                        if ($pengajuanDana->user->is_kepala_divisi) {
                            $status = $pengajuanDana->status_direktur; $title_atasan = 'Persetujuan Direktur'; $catatan_atasan = $pengajuanDana->catatan_direktur;
                        } else {
                            $status = $pengajuanDana->status_atasan; $title_atasan = 'Persetujuan Atasan'; $catatan_atasan = $pengajuanDana->catatan_atasan;
                        }
            
                        switch ($status) {
                            case 'disetujui': $status_atasan_class = 'text-emerald-400'; $status_atasan_icon = 'fa-check-circle'; $status_atasan_text = 'Disetujui'; break;
                            case 'ditolak': $status_atasan_class = 'text-red-400'; $status_atasan_icon = 'fa-times-circle'; $status_atasan_text = 'Ditolak'; break;
                            case 'skipped': $status_atasan_class = 'text-zinc-400'; $status_atasan_icon = 'fa-minus-circle'; $status_atasan_text = 'Dilewati'; break;
                            default: $status_atasan_class = 'text-yellow-400'; $status_atasan_icon = 'fa-clock'; $status_atasan_text = 'Menunggu'; break;
                        }
                    }
                @endphp
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">{{ $title_atasan }}</label>
                    <p class="flex items-center {{ $status_atasan_class }}"><i class="fas {{ $status_atasan_icon }} mr-2"></i> {{ $status_atasan_text }}</p>
                    @if($catatan_atasan)
                    <div class="mt-2 text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatan_atasan }}"</div>
                    @endif
                </div>
            
                {{-- TAHAP 2: FINANCE (LOGIKA BARU) --}}
                @php
                    $catatan_finance = $pengajuanDana->catatan_finance; $status_finance_class = ''; $status_finance_text = ''; $status_finance_icon = '';
            
                    if ($pengajuanDana->status === 'dibatalkan') {
                        $status_finance_text = 'Dibatalkan'; $status_finance_class = 'text-zinc-400'; $status_finance_icon = 'fa-ban';
                    } else {
                        $status = $pengajuanDana->status_finance;
                        switch ($status) {
                            case 'disetujui': $status_finance_class = 'text-emerald-400'; $status_finance_icon = 'fa-check-circle'; $status_finance_text = 'Disetujui'; break;
                            case 'ditolak': $status_finance_class = 'text-red-400'; $status_finance_icon = 'fa-times-circle'; $status_finance_text = 'Ditolak'; break;
                            case 'skipped': $status_finance_class = 'text-zinc-400'; $status_finance_icon = 'fa-minus-circle'; $status_finance_text = 'Dilewati'; break;
                            default: $status_finance_class = 'text-yellow-400'; $status_finance_icon = 'fa-clock'; $status_finance_text = 'Menunggu'; break;
                        }
                    }
                @endphp
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Persetujuan Finance</label>
                    <p class="flex items-center {{ $status_finance_class }}"><i class="fas {{ $status_finance_icon }} mr-2"></i> {{ $status_finance_text }}</p>
                    @if($catatan_finance)
                    <div class="mt-2 text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatan_finance }}"</div>
                    @endif
                </div>
            
            
                {{-- STATUS FINAL (DITAMBAH STATUS DIBATALKAN)--}}
                <div class="border-t border-zinc-700 pt-4">
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Status Final Pengajuan</label>
                     @if ($pengajuanDana->status == 'disetujui')
                        <p class="text-xl font-bold flex items-center text-emerald-400"><i class="fas fa-check-circle mr-2"></i> DISETUJUI</p>
                    @elseif ($pengajuanDana->status == 'ditolak')
                        <p class="text-xl font-bold flex items-center text-red-400"><i class="fas fa-times-circle mr-2"></i> DITOLAK</p>
                    @elseif ($pengajuanDana->status == 'diproses')
                        <p class="text-xl font-bold flex items-center text-blue-400"><i class="fas fa-sync-alt fa-spin mr-2"></i> DIPROSES</p>
                    @elseif ($pengajuanDana->status == 'dibatalkan')
                        <p class="text-xl font-bold flex items-center text-zinc-400"><i class="fas fa-ban mr-2"></i> DIBATALKAN</p>
                    @else
                        <p class="text-xl font-bold flex items-center text-yellow-400"><i class="fas fa-hourglass-start mr-2"></i> DIAJUKAN</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout-admin>