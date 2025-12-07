<x-layout-admin>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>

    <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700">
        {{-- Header (Tidak Berubah) --}}
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
            
            {{-- KOLOM KIRI & TENGAH: DETAIL & DOKUMEN (Tidak Berubah) --}}
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
                     @if (empty($pengajuanDana->lampiran) && !$pengajuanDana->bukti_transfer && !$pengajuanDana->invoice)
                        <p class="text-sm text-zinc-500 bg-zinc-700/50 border border-zinc-600 rounded-lg p-4 text-center">Tidak ada dokumen yang dilampirkan.</p>
                     @endif
                </div>
            </div>

            {{-- =================================================================== --}}
            {{-- ============ KOLOM KANAN: ALUR PERSETUJUAN (PERBAIKAN) ============ --}}
            {{-- =================================================================== --}}
            <div class="lg:col-span-1 space-y-6 border-t lg:border-t-0 lg:border-l border-zinc-700 pt-6 lg:pt-0 lg:pl-8">
                <h3 class="text-lg font-semibold text-white mb-3">Alur Persetujuan</h3>

                {{-- TAHAP 1: APPROVER 1 --}}
                @if ($pengajuanDana->approver1) {{-- [FIX] Gunakan relasi approver1 --}}
                    @php
                        $status = $pengajuanDana->approver_1_status; // [FIX] Gunakan kolom status yg benar
                        $catatan = $pengajuanDana->approver_1_catatan;
                        $tanggal = $pengajuanDana->approver_1_approved_at;
                        $namaApprover = $pengajuanDana->approver1->name ?? 'Approver Dihapus';
                        
                        $statusClass = ''; $statusText = ''; $statusIcon = '';
                        switch ($status) {
                            case 'disetujui': $statusClass = 'text-emerald-400'; $statusIcon = 'fa-check-circle'; $statusText = 'Disetujui'; break;
                            case 'ditolak': $statusClass = 'text-red-400'; $statusIcon = 'fa-times-circle'; $statusText = 'Ditolak'; break;
                            case 'skipped': $statusClass = 'text-zinc-400'; $statusIcon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                            default: $statusClass = 'text-yellow-400'; $statusIcon = 'fa-clock'; $statusText = 'Menunggu'; break;
                        }
                        if ($pengajuanDana->status === 'dibatalkan') {
                            $statusClass = 'text-zinc-400'; $statusIcon = 'fa-ban'; $statusText = 'Dibatalkan';
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">
                            Approver 1: <span class="text-zinc-300">{{ $namaApprover }}</span>
                        </label>
                        <p class="flex items-center font-medium {{ $statusClass }}"><i class="fas {{ $statusIcon }} mr-2 w-4 text-center"></i> {{ $statusText }}</p>
                        
                        @if($tanggal || $catatan)
                        <div class="mt-2 space-y-1">
                            @if($tanggal)
                                <p class="text-xs text-zinc-400 flex items-center">
                                    <i class="fas fa-calendar-alt fa-fw mr-2 text-zinc-500"></i>
                                    <span>{{ $tanggal->translatedFormat('d M Y, H:i') }}</span>
                                </p>
                            @endif
                            @if($catatan)
                            <div class="text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatan }}"</div>
                            @endif
                        </div>
                        @endif
                    </div>
                @elseif ($pengajuanDana->approver_1_status === 'skipped') {{-- [FIX] Cek status skipped --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Approver 1</label>
                        <p class="flex items-center text-zinc-400"><i class="fas fa-minus-circle mr-2"></i> Dilewati</p>
                    </div>
                @endif


                {{-- TAHAP 2: APPROVER 2 --}}
                 @if ($pengajuanDana->approver2) {{-- [FIX] Gunakan relasi approver2 --}}
                    @php
                        $status = $pengajuanDana->approver_2_status; // [FIX] Gunakan kolom status yg benar
                        $catatan = $pengajuanDana->approver_2_catatan;
                        $tanggal = $pengajuanDana->approver_2_approved_at;
                        $namaApprover = $pengajuanDana->approver2->name ?? 'Approver Dihapus';
                        
                        $statusClass = ''; $statusText = ''; $statusIcon = '';
                        switch ($status) {
                            case 'disetujui': $statusClass = 'text-emerald-400'; $statusIcon = 'fa-check-circle'; $statusText = 'Disetujui'; break;
                            case 'ditolak': $statusClass = 'text-red-400'; $statusIcon = 'fa-times-circle'; $statusText = 'Ditolak'; break;
                            case 'skipped': $statusClass = 'text-zinc-400'; $statusIcon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                            default: $statusClass = 'text-yellow-400'; $statusIcon = 'fa-clock'; $statusText = 'Menunggu'; break;
                        }
                        if ($pengajuanDana->status === 'dibatalkan') {
                            $statusClass = 'text-zinc-400'; $statusIcon = 'fa-ban'; $statusText = 'Dibatalkan';
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">
                            Approver 2: <span class="text-zinc-300">{{ $namaApprover }}</span>
                        </label>
                         <p class="flex items-center font-medium {{ $statusClass }}"><i class="fas {{ $statusIcon }} mr-2 w-4 text-center"></i> {{ $statusText }}</p>
                        
                        @if($tanggal || $catatan)
                        <div class="mt-2 space-y-1">
                            @if($tanggal)
                                <p class="text-xs text-zinc-400 flex items-center">
                                    <i class="fas fa-calendar-alt fa-fw mr-2 text-zinc-500"></i>
                                    <span>{{ $tanggal->translatedFormat('d M Y, H:i') }}</span>
                                </p>
                            @endif
                            @if($catatan)
                            <div class="text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatan }}"</div>
                            @endif
                        </div>
                        @endif
                    </div>
                @elseif ($pengajuanDana->approver_2_status === 'skipped') {{-- [FIX] Cek status skipped --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Approver 2</label>
                        <p class="flex items-center text-zinc-400"><i class="fas fa-minus-circle mr-2"></i> Dilewati</p>
                    </div>
                @endif


                {{-- TAHAP 3: MANAGER KEUANGAN --}}
                @if ($pengajuanDana->user->managerKeuangan) {{-- [FIX] Cek dari relasi user --}}
                    @php
                        $status = $pengajuanDana->payment_status; // [FIX] Gunakan payment_status
                        $catatan = $pengajuanDana->catatan_finance;
                        $tanggal = $pengajuanDana->finance_processed_at; 
                        $namaApprover = $pengajuanDana->user->managerKeuangan->name ?? 'Finance Dihapus';
                        
                        $statusClass = ''; $statusText = ''; $statusIcon = '';
                        switch ($status) {
                            case 'selesai': $statusClass = 'text-emerald-400'; $statusIcon = 'fa-check-circle'; $statusText = 'Selesai (Dibayar)'; break;
                            case 'diproses': $statusClass = 'text-blue-400'; $statusIcon = 'fa-sync-alt'; $statusText = 'Sedang Diproses'; break;
                            case 'ditolak': $statusClass = 'text-red-400'; $statusIcon = 'fa-times-circle'; $statusText = 'Ditolak'; break;
                            case 'skipped': $statusClass = 'text-zinc-400'; $statusIcon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                            default: $statusClass = 'text-yellow-400'; $statusIcon = 'fa-clock'; $statusText = 'Menunggu'; break; // 'menunggu'
                        }
                        
                        if ($pengajuanDana->status === 'dibatalkan') {
                            $statusClass = 'text-zinc-400'; $statusIcon = 'fa-ban'; $statusText = 'Dibatalkan';
                        } 
                        elseif ($pengajuanDana->status === 'ditolak') {
                            $statusClass = 'text-zinc-400'; $statusIcon = 'fa-ban'; 
                            if ($pengajuanDana->approver_1_status === 'ditolak') {
                                $statusText = 'Dihentikan (Ditolak Appr 1)';
                            } elseif ($pengajuanDana->approver_2_status === 'ditolak') {
                                $statusText = 'Dihentikan (Ditolak Appr 2)';
                            } else {
                                $statusText = 'Dihentikan';
                            }
                        }

                        if ($status === 'selesai') {
                            $tanggal = $pengajuanDana->updated_at;
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">
                            Manager Keuangan: <span class="text-zinc-300">{{ $namaApprover }}</span>
                        </label>
                        <p class="flex items-center font-medium {{ $statusClass }}"><i class="fas {{ $statusIcon }} mr-2 w-4 text-center"></i> {{ $statusText }}</p>
                        
                        {{-- Hanya tampilkan detail finance jika status relevan --}}
                        @if(in_array($pengajuanDana->payment_status, ['diproses', 'selesai', 'ditolak']) && ($tanggal || $catatan))
                        <div class="mt-2 space-y-1">
                            @if($tanggal)
                                <p class="text-xs text-zinc-400 flex items-center">
                                    <i class="fas fa-calendar-alt fa-fw mr-2 text-zinc-500"></i>
                                    <span>{{ $tanggal->translatedFormat('d M Y, H:i') }}</span>
                                </p>
                            @endif
                            @if($catatan)
                            <div class="text-xs text-zinc-500 border-l-2 border-zinc-600 pl-2 italic">"{{ $catatan }}"</div>
                            @endif
                        </div>
                        @endif
                    </div>
                @elseif ($pengajuanDana->payment_status === 'skipped') {{-- [FIX] Cek status skipped --}}
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Manager Keuangan</label>
                        <p class="flex items-center text-zinc-400"><i class="fas fa-minus-circle mr-2"></i> Dilewati</p>
                    </div>
                @endif
            
            
                {{-- STATUS FINAL --}}
                <div class="border-t border-zinc-700 pt-4">
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Status Final Pengajuan</label>
                     @if ($pengajuanDana->status == 'selesai') {{-- [FIX] Ganti disetujui jadi selesai --}}
                        <p class="text-xl font-bold flex items-center text-emerald-400"><i class="fas fa-check-circle mr-2"></i> SELESAI</p>
                    @elseif ($pengajuanDana->status == 'ditolak')
                        <p class="text-xl font-bold flex items-center text-red-400"><i class="fas fa-times-circle mr-2"></i> DITOLAK</p>
                    @elseif ($pengajuanDana->status == 'proses_pembayaran' || $pengajuanDana->status == 'diproses_appr_2') {{-- [FIX] Tambah status --}}
                        <p class="text-xl font-bold flex items-center text-blue-400"><i class="fas fa-sync-alt fa-spin mr-2"></i> DIPROSES</p>
                    @elseif ($pengajuanDana->status == 'dibatalkan')
                        <p class="text-xl font-bold flex items-center text-zinc-400"><i class="fas fa-ban mr-2"></i> DIBATALKAN</p>
                    @else {{-- Status 'diajukan' --}}
                        <p class="text-xl font-bold flex items-center text-yellow-400"><i class="fas fa-hourglass-start mr-2"></i> DIAJUKAN</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout-admin>