<x-layout-users>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>

    <div class="container mx-auto p-0 md:p-0">
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
            {{-- Tombol Kembali (Gaya Biru) --}}
            @if(Auth::id() == $pengajuanDana->user_id)
                <a href="{{ route('pengajuan_dana.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors w-full sm:w-auto">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Rekap Pengajuan
                </a>
            @endif

            {{-- Tombol Unduh PDF (Posisi Baru) --}}
            @if(Auth::id() == $pengajuanDana->user_id)
            <a href="{{ route('pengajuan_dana.download', $pengajuanDana) }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-100 rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors w-full sm:w-auto"
               title="Unduh sebagai PDF">
                <i class="fas fa-file-pdf text-xl"></i>
                <span>Cetak Pengajuan (.pdf)</span>
            </a>
            @endif
        </div>

        {{-- KARTU UTAMA --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200">
            {{-- Header --}}
            <div class="p-6 md:p-8 border-b border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Detail Pengajuan: {{ $pengajuanDana->judul_pengajuan }}</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                        <span class="inline-block bg-indigo-100 text-indigo-800 font-bold px-3 py-1 rounded text-xs">
                            No Pengajuan: {{ str_pad($pengajuanDana->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                        <span>
                            Diajukan oleh:
                            <span class="font-medium text-slate-700">{{ $pengajuanDana->user->name }}</span>
                            - Divisi {{ $pengajuanDana->user->divisi }}
                        </span>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="flex items-center gap-3 mt-4 sm:mt-0">
                    @php
                        $statusClass = '';
                        $statusText = '';
                        switch ($pengajuanDana->status) {
                            case 'disetujui': $statusClass = 'bg-green-100 text-green-700'; $statusText = 'Disetujui'; break;
                            case 'ditolak': $statusClass = 'bg-red-100 text-red-700'; $statusText = 'Ditolak'; break;
                            case 'diproses': $statusClass = 'bg-blue-100 text-blue-700'; $statusText = 'Diproses'; break;
                            case 'dibatalkan': $statusClass = 'bg-slate-100 text-slate-600'; $statusText = 'Dibatalkan'; break;
                            default: $statusClass = 'bg-yellow-100 text-yellow-700'; $statusText = 'Diajukan'; break;
                        }
                    @endphp
                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-3 gap-x-8 gap-y-6">

                {{-- KOLOM KIRI & TENGAH: DETAIL & DOKUMEN --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- DETAIL UTAMA --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Informasi Pengajuan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block font-medium text-slate-500">Tanggal Pengajuan</label>
                                <p class="text-slate-700 font-medium">{{ $pengajuanDana->created_at->translatedFormat('l, d F Y - H:i') }}</p>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-500">Informasi Transfer</label>
                                <p class="text-slate-700 font-medium">{{ $pengajuanDana->nama_bank }} - {{ $pengajuanDana->no_rekening }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- RINCIAN DANA --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Rincian Dana</h3>
                        <div class="space-y-3 border border-slate-200 rounded-lg p-4 bg-slate-50">
                            @forelse($pengajuanDana->rincian_dana as $rincian)
                            <div class="flex justify-between items-center text-slate-700">
                                <span>- {{ $rincian['deskripsi'] ?? 'Item tidak ada deskripsi' }}</span>
                                <span class="font-mono">Rp {{ number_format($rincian['jumlah'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @empty
                                <p class="text-slate-500 text-sm">Rincian dana tidak tersedia.</p>
                            @endforelse
                            <div class="border-t border-slate-300 pt-3 mt-3 flex justify-between items-center text-slate-800 font-bold">
                                <span>TOTAL</span>
                                <span class="font-mono text-xl text-indigo-600">Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- DOKUMEN TERKAIT --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Dokumen Terkait</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @if ($pengajuanDana->lampiran && count($pengajuanDana->lampiran) > 0)
                                @foreach ($pengajuanDana->lampiran as $lampiran)
                                    <a href="{{ asset('storage/' . $lampiran) }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 p-3 rounded-lg text-center text-slate-700 transition">
                                        <i class="fas fa-paperclip mb-2 text-xl text-slate-500"></i>
                                        <p class="text-sm font-semibold">Lampiran {{ $loop->iteration }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ basename($lampiran) }}</p>
                                    </a>
                                @endforeach
                            @endif
                            @if($pengajuanDana->bukti_transfer)
                            <a href="{{ asset('storage/' . $pengajuanDana->bukti_transfer) }}" target="_blank" class="bg-green-50 hover:bg-green-100 p-3 rounded-lg text-center text-green-700 transition">
                                <i class="fas fa-receipt mb-2 text-xl"></i><p class="text-sm font-semibold">Bukti Transfer</p>
                            </a>
                            @endif
                            @if($pengajuanDana->invoice)
                            <a href="{{ asset('storage/' . $pengajuanDana->invoice) }}" target="_blank" class="bg-purple-50 hover:bg-purple-100 p-3 rounded-lg text-center text-purple-700 transition">
                                <i class="fas fa-file-invoice-dollar mb-2 text-xl"></i><p class="text-sm font-semibold">Invoice Final</p>
                            </a>
                            @endif
                        </div>
                         @if (!$pengajuanDana->lampiran && !$pengajuanDana->bukti_transfer && !$pengajuanDana->invoice)
                            <p class="text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">Tidak ada dokumen yang dilampirkan.</p>
                         @endif
                    </div>
                </div>

                {{-- KOLOM KANAN: ALUR PERSETUJUAN --}}
                <div class="lg:col-span-1 space-y-6 border-t lg:border-t-0 lg:border-l border-slate-200 pt-6 lg:pt-0 lg:pl-8">
                    <h3 class="text-lg font-semibold text-slate-800 mb-3">Alur Persetujuan</h3>

                    {{-- TAHAP 1: ATASAN ATAU DIREKTUR --}}
                    @php
                        $title_atasan = ''; $catatan_atasan = ''; $status_atasan_class = ''; $status_atasan_text = ''; $status_atasan_icon = '';
                        $tanggal_atasan = null;

                        if ($pengajuanDana->status === 'dibatalkan') {
                            $status_atasan_text = 'Dibatalkan'; $status_atasan_class = 'text-slate-500'; $status_atasan_icon = 'fa-ban';
                            $title_atasan = $pengajuanDana->user->is_kepala_divisi ? 'Persetujuan Direktur' : 'Persetujuan Atasan';
                        } else {
                            if ($pengajuanDana->user->is_kepala_divisi) {
                                $status = $pengajuanDana->status_direktur;
                                $title_atasan = 'Persetujuan Direktur';
                                $catatan_atasan = $pengajuanDana->catatan_direktur;
                                $tanggal_atasan = $pengajuanDana->direktur_approved_at;
                                if ($status != 'menunggu' && $pengajuanDana->direkturApprover) {
                                    $title_atasan .= ' (' . $pengajuanDana->direkturApprover->name . ')';
                                }
                            } else {
                                $status = $pengajuanDana->status_atasan;
                                $title_atasan = 'Persetujuan Atasan';
                                $catatan_atasan = $pengajuanDana->catatan_atasan;
                                $tanggal_atasan = $pengajuanDana->atasan_approved_at;
                                if ($status != 'menunggu' && $pengajuanDana->atasanApprover) {
                                    $title_atasan .= ' (' . $pengajuanDana->atasanApprover->name . ')';
                                }
                            }

                            switch ($status) {
                                case 'disetujui': $status_atasan_class = 'text-green-600'; $status_atasan_icon = 'fa-check-circle'; $status_atasan_text = 'Disetujui'; break;
                                case 'ditolak': $status_atasan_class = 'text-red-600'; $status_atasan_icon = 'fa-times-circle'; $status_atasan_text = 'Ditolak'; break;
                                case 'skipped': $status_atasan_class = 'text-slate-500'; $status_atasan_icon = 'fa-minus-circle'; $status_atasan_text = 'Dilewati'; break;
                                default: $status_atasan_class = 'text-yellow-600'; $status_atasan_icon = 'fa-clock'; $status_atasan_text = 'Menunggu'; break;
                            }
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">{{ $title_atasan }}</label>
                        <p class="flex items-center font-semibold {{ $status_atasan_class }}"><i class="fas {{ $status_atasan_icon }} mr-2 w-4 text-center"></i> {{ $status_atasan_text }}</p>
                        
                        {{-- =================== BAGIAN YANG DIPERBAIKI =================== --}}
                        @if($tanggal_atasan || $catatan_atasan)
                        <div class="mt-2 space-y-2"> {{-- Padding kiri (pl-7) dihapus, margin atas (mt-2) ditambahkan --}}
                            @if($tanggal_atasan)
                                <p class="text-xs text-slate-500 flex items-center">
                                    <i class="fas fa-calendar-alt fa-fw mr-2 text-slate-400"></i>
                                    <span>{{ \Carbon\Carbon::parse($tanggal_atasan)->translatedFormat('d F Y \p\u\k\u\l H:i') }}</span>
                                </p>
                            @endif
                            @if($catatan_atasan)
                            <div class="text-xs text-slate-600 bg-slate-50 border-l-4 border-slate-200 pl-3 pr-2 py-2 italic">"{{ $catatan_atasan }}"</div>
                            @endif
                        </div>
                        @endif
                        {{-- ============================================================= --}}
                    </div>

                    {{-- TAHAP 2: FINANCE --}}
                    @php
                        $title_finance = 'Persetujuan Finance';
                        $catatan_finance = $pengajuanDana->catatan_finance;
                        $status_finance_class = ''; $status_finance_text = ''; $status_finance_icon = '';
                        $tanggal_finance = $pengajuanDana->finance_approved_at;

                        if ($pengajuanDana->status === 'dibatalkan') {
                            $status_finance_text = 'Dibatalkan'; $status_finance_class = 'text-slate-500'; $status_finance_icon = 'fa-ban';
                        } else {
                            $status = $pengajuanDana->status_finance;
                            if ($status != 'menunggu' && $pengajuanDana->financeApprover) {
                                $title_finance .= ' (' . $pengajuanDana->financeApprover->name . ')';
                            }

                            switch ($status) {
                                case 'disetujui': $status_finance_class = 'text-green-600'; $status_finance_icon = 'fa-check-circle'; $status_finance_text = 'Disetujui'; break;
                                case 'ditolak': $status_finance_class = 'text-red-600'; $status_finance_icon = 'fa-times-circle'; $status_finance_text = 'Ditolak'; break;
                                case 'skipped': $status_finance_class = 'text-slate-500'; $status_finance_icon = 'fa-minus-circle'; $status_finance_text = 'Dilewati'; break;
                                default: $status_finance_class = 'text-yellow-600'; $status_finance_icon = 'fa-clock'; $status_finance_text = 'Menunggu'; break;
                            }
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">{{ $title_finance }}</label>
                        <p class="flex items-center font-semibold {{ $status_finance_class }}"><i class="fas {{ $status_finance_icon }} mr-2 w-4 text-center"></i> {{ $status_finance_text }}</p>
                        
                        {{-- =================== BAGIAN YANG DIPERBAIKI =================== --}}
                        @if($tanggal_finance || $catatan_finance)
                        <div class="mt-2 space-y-2"> {{-- Padding kiri (pl-7) dihapus, margin atas (mt-2) ditambahkan --}}
                            @if($tanggal_finance)
                                <p class="text-xs text-slate-500 flex items-center">
                                    <i class="fas fa-calendar-alt fa-fw mr-2 text-slate-400"></i>
                                    <span>{{ \Carbon\Carbon::parse($tanggal_finance)->translatedFormat('d F Y \p\u\k\u\l H:i') }}</span>
                                </p>
                            @endif
                            @if($catatan_finance)
                            <div class="text-xs text-slate-600 bg-slate-50 border-l-4 border-slate-200 pl-3 pr-2 py-2 italic">"{{ $catatan_finance }}"</div>
                            @endif
                        </div>
                        @endif
                        {{-- ============================================================= --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN TINDAKAN --}}
        @can('cancel', $pengajuanDana)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
                <h3 class="text-xl font-bold text-slate-800 mb-2">Tindakan Lain</h3>
                <p class="text-sm text-slate-500 mb-4">Anda dapat membatalkan pengajuan ini selama statusnya masih "Diajukan" atau "Diproses".</p>
                <form action="{{ route('pengajuan_dana.cancel', $pengajuanDana) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan dana ini? Tindakan ini tidak dapat diurungkan.');">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition ease-in-out duration-150">
                        <i class="fas fa-times-circle mr-2"></i>Batalkan Pengajuan
                    </button>
                </form>
            </div>
        @endcan

        @can('approve', $pengajuanDana)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Tindakan Persetujuan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <form action="{{ route('pengajuan_dana.approve', $pengajuanDana) }}" method="POST">
                        @csrf
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="catatan-setuju">Catatan Persetujuan (Opsional)</label>
                        <textarea id="catatan-setuju" name="catatan_persetujuan" rows="3" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                        <button type="submit" class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition"><i class="fas fa-check mr-2"></i>Setujui</button>
                    </form>
                    <form action="{{ route('pengajuan_dana.reject', $pengajuanDana) }}" method="POST">
                        @csrf
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="catatan-tolak">Catatan Penolakan (Wajib)</label>
                        <textarea id="catatan-tolak" name="catatan_penolakan" rows="3" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500" required></textarea>
                        <button type="submit" class="mt-3 w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition"><i class="fas fa-times mr-2"></i>Tolak</button>
                    </form>
                </div>
            </div>
        @endcan

        @can('uploadBuktiTransfer', $pengajuanDana)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Unggah Bukti Transfer</h3>
                <form action="{{ route('pengajuan_dana.upload_bukti_transfer', $pengajuanDana) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="bukti-transfer">Pilih File</label>
                    <input type="file" name="bukti_transfer" id="bukti-transfer" class="w-full p-2 border border-slate-300 rounded-lg" required>
                    <button type="submit" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition"><i class="fas fa-upload mr-2"></i>Unggah Bukti</button>
                </form>
            </div>
        @endcan

        @can('uploadFinalInvoice', $pengajuanDana)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Unggah Invoice / Nota Pembelian</h3>
                <p class="text-sm text-slate-500 mb-4">Silakan unggah invoice atau nota sebagai bukti penggunaan dana.</p>
                <form action="{{ route('pengajuan_dana.upload_final_invoice', $pengajuanDana) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="invoice">Pilih File Invoice</label>
                    <input type="file" name="invoice" id="invoice" class="w-full p-2 border border-slate-300 rounded-lg" required>
                    <button type="submit" class="mt-4 w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition"><i class="fas fa-upload mr-2"></i>Unggah Invoice</button>
                </form>
            </div>
        @endcan
    </div>
</x-layout-users>