<x-layout-admin>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>

    <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700">
        {{-- Header --}}
        <div class="p-6 border-b border-zinc-700 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">Detail Pengajuan: {{ $pengajuanDana->judul_pengajuan }}</h2>
                <p class="text-sm text-zinc-400 mt-1">Diajukan oleh: {{ $pengajuanDana->user->name }}</p>
            </div>
            <a href="{{ route('admin.pengajuan_dana.index') }}" class="text-sm text-zinc-300 hover:text-white transition-colors duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Body --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
            
            {{-- Kolom Kiri: Detail Utama --}}
            <div class="md:col-span-2 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Pengajuan</label>
                    <p class="text-base text-zinc-200">{{ $pengajuanDana->created_at->translatedFormat('l, d F Y - H:i') }}</p>
                </div>

                {{-- Detail Rincian Dana --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Rincian Dana</label>
                    <div class="space-y-3 border border-zinc-700 rounded-lg p-4">
                        @forelse($pengajuanDana->rincian_dana as $rincian)
                        <div class="flex justify-between items-center text-zinc-300">
                            <span>- {{ $rincian['deskripsi'] ?? 'Item tidak ada deskripsi' }}</span>
                            {{-- Pastikan kode ini tidak diubah, karena sudah benar --}}
                            <span class="font-mono">Rp {{ number_format($rincian['jumlah'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @empty
                            <p class="text-zinc-400 text-sm">Rincian dana tidak tersedia.</p>
                        @endforelse
                        <div class="border-t border-zinc-600 pt-3 mt-3 flex justify-between items-center text-white font-bold">
                            <span>TOTAL</span>
                            <span class="font-mono text-amber-400">Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Informasi Bank --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Informasi Transfer</label>
                    <p class="text-base text-zinc-200">{{ $pengajuanDana->nama_bank }} - {{ $pengajuanDana->no_rekening }}</p>
                </div>
            </div>

            {{-- Kolom Kanan: Status & Approval --}}
            <div class="md:col-span-1 space-y-6 border-t md:border-t-0 md:border-l border-zinc-700 pt-6 md:pt-0 md:pl-8">
                {{-- Status Atasan --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Persetujuan Kepala Divisi</label>
                    @if ($pengajuanDana->status_atasan == 'disetujui')
                        <p class="flex items-center text-emerald-400"><i class="fas fa-check-circle mr-2"></i> Disetujui</p>
                    @elseif ($pengajuanDana->status_atasan == 'ditolak')
                        <p class="flex items-center text-red-400"><i class="fas fa-times-circle mr-2"></i> Ditolak</p>
                    @else
                        <p class="flex items-center text-yellow-400"><i class="fas fa-clock mr-2"></i> Menunggu Persetujuan</p>
                    @endif

                    @if($pengajuanDana->catatan_atasan)
                    <div class="mt-2 text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $pengajuanDana->catatan_atasan }}"</div>
                    @endif
                </div>

                {{-- Status Finance --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Persetujuan Finance</label>
                    
                    {{-- LOGIKA YANG DIPERBAIKI --}}
                    @if ($pengajuanDana->status_atasan != 'disetujui')
                        <p class="flex items-center text-zinc-500"><i class="fas fa-pause-circle mr-2"></i> Menunggu Keputusan Atasan</p>
                    @elseif ($pengajuanDana->status_finance == 'disetujui')
                        <p class="flex items-center text-emerald-400"><i class="fas fa-check-circle mr-2"></i> Disetujui</p>
                    @elseif ($pengajuanDana->status_finance == 'ditolak')
                        <p class="flex items-center text-red-400"><i class="fas fa-times-circle mr-2"></i> Ditolak</p>
                    @else
                        {{-- Ini akan menjadi default jika status_hrd adalah 'menunggu' atau null --}}
                        <p class="flex items-center text-yellow-400"><i class="fas fa-clock mr-2"></i> Menunggu Persetujuan</p>
                    @endif

                    @if($pengajuanDana->catatan_hrd)
                    <div class="mt-2 text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $pengajuanDana->catatan_hrd }}"</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout-admin>