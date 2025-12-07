<x-layout-users>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>

    <div class="container mx-auto p-0 md:p-0">
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">

            @if(Auth::id() == $pengajuanDana->user_id)
                <a href="{{ route('pengajuan_dana.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors w-full sm:w-auto">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Rekap Pengajuan
                </a>
            @endif

            {{-- Tombol Unduh PDF --}}
            @if(Auth::id() == $pengajuanDana->user_id)
            <a href="{{ route('pengajuan_dana.download', $pengajuanDana) }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-100 rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors w-full sm:w-auto"
               title="Unduh sebagai PDF">
                <i class="fas fa-file-pdf text-xl"></i>
                <span>Cetak Pengajuan (.pdf)</span>
            </a>
            @endif
        </div>

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
                            case 'selesai': $statusClass = 'bg-green-100 text-green-700'; $statusText = 'Selesai'; break;
                            case 'ditolak': $statusClass = 'bg-red-100 text-red-700'; $statusText = 'Ditolak'; break;
                            case 'proses_pembayaran': $statusClass = 'bg-blue-100 text-blue-700'; $statusText = 'Proses Pembayaran'; break;
                            case 'diproses_appr_2': $statusClass = 'bg-blue-100 text-blue-700'; $statusText = 'Menunggu Approver 2'; break;
                            case 'dibatalkan': $statusClass = 'bg-slate-100 text-slate-600'; $statusText = 'Dibatalkan'; break;
                            default: $statusClass = 'bg-yellow-100 text-yellow-700'; $statusText = 'Menunggu Approver 1'; break; // diajukan
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

                    {{-- ====================================================== --}}
                    {{-- ==== AWAL BLOK YANG DIPERBARUI ==== --}}
                    {{-- ====================================================== --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Dokumen Terkait</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            
                            {{-- Lampiran --}}
                            @if ($pengajuanDana->lampiran && count($pengajuanDana->lampiran) > 0)
                                @foreach ($pengajuanDana->lampiran as $lampiran)
                                    <div class="relative"> 
                                        <a href="{{ asset('storage/' . $lampiran) }}" 
                                           class="block bg-slate-100 hover:bg-slate-200 p-3 pt-12 rounded-lg text-center text-slate-700 transition">
                                            
                                            <i class="fas fa-paperclip mb-2 text-xl text-slate-500"></i>
                                            <p class="text-sm font-semibold">Lampiran {{ $loop->iteration }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ basename($lampiran) }}</p>
                                        </a>
                                        
                                        <a href="{{ asset('storage/' . $lampiran) }}" 
                                           download="{{ basename($lampiran) }}"
                                           class="absolute top-3 right-3 w-8 h-8 flex items-center rounded-lg justify-center bg-slate-500 hover:bg-slate-600 text-white transition"
                                           title="Download Lampiran {{ $loop->iteration }}">
                                           <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            
                            {{-- Bukti Transfer --}}
                            @if($pengajuanDana->bukti_transfer)
                                <div class="relative">
                                    <a href="{{ asset('storage/' . $pengajuanDana->bukti_transfer) }}" 
                                       class="block bg-green-50 hover:bg-green-100 p-3 pt-12 rounded-lg text-center text-green-700 transition">
                                        <i class="fas fa-receipt mb-2 text-xl"></i>
                                        <p class="text-sm font-semibold">Bukti Transfer</p>
                                    </a>
                                    
                                    <a href="{{ asset('storage/' . $pengajuanDana->bukti_transfer) }}" 
                                       download="{{ basename($pengajuanDana->bukti_transfer) }}"
                                       class="absolute top-3 right-3 w-8 h-8 flex items-center rounded-lg justify-center bg-slate-500 hover:bg-slate-600 text-white transition"
                                       title="Download Bukti Transfer">
                                       <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endif
                            
                            {{-- Invoice --}}
                            @if($pengajuanDana->invoice)
                                <div class="relative">
                                    <a href="{{ asset('storage/' . $pengajuanDana->invoice) }}" 
                                       class="block bg-purple-50 hover:bg-purple-100 p-3 pt-12 rounded-lg text-center text-purple-700 transition">
                                        <i class="fas fa-file-invoice-dollar mb-2 text-xl"></i>
                                        <p class="text-sm font-semibold">Invoice Final</p>
                                    </a>
                                    
                                    <a href="{{ asset('storage/' . $pengajuanDana->invoice) }}" 
                                       download="{{ basename($pengajuanDana->invoice) }}"
                                       class="absolute top-3 right-3 p-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition"
                                       title="Download Invoice Final">
                                       <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                         {{-- Pesan jika tidak ada dokumen --}}
                         @if (empty($pengajuanDana->lampiran) && !$pengajuanDana->bukti_transfer && !$pengajuanDana->invoice)
                            <p class="text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">Tidak ada dokumen yang dilampirkan.</p>
                         @endif
                    </div>
                    {{-- ====================================================== --}}
                    {{-- ==== AKHIR BLOK YANG DIPERBARUI ==== --}}
                    {{-- ====================================================== --}}

                </div>

                {{-- Alur Persetujuan --}}
                <div class="lg:col-span-1 space-y-6 border-t lg:border-t-0 lg:border-l border-slate-200 pt-6 lg:pt-0 lg:pl-8">
                    <h3 class="text-lg font-semibold text-slate-800 mb-3">Alur Persetujuan</h3>

                    {{-- TAHAP 1: APPROVER 1 --}}
                    @if ($pengajuanDana->approver_1_id)
                        @php
                            $status = $pengajuanDana->approver_1_status;
                            $catatan = $pengajuanDana->approver_1_catatan;
                            $tanggal = $pengajuanDana->approver_1_approved_at;
                            $namaApprover = $pengajuanDana->approver1->name ?? 'Approver Dihapus';
                            
                            $statusClass = ''; $statusText = ''; $statusIcon = '';
                            switch ($status) {
                                case 'disetujui': $statusClass = 'text-green-600'; $statusIcon = 'fa-check-circle'; $statusText = 'Disetujui'; break;
                                case 'ditolak': $statusClass = 'text-red-600'; $statusIcon = 'fa-times-circle'; $statusText = 'Ditolak'; break;
                                case 'skipped': $statusClass = 'text-slate-500'; $statusIcon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                                default: $statusClass = 'text-yellow-600'; $statusIcon = 'fa-clock'; $statusText = 'Menunggu'; break;
                            }
                            if ($pengajuanDana->status === 'dibatalkan') {
                                $statusClass = 'text-slate-500'; $statusIcon = 'fa-ban'; $statusText = 'Dibatalkan';
                            }
                        @endphp
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Approver 1: <span class="text-slate-700 font-semibold">{{ $namaApprover }}</span></label>
                            <p class="flex items-center font-semibold {{ $statusClass }}"><i class="fas {{ $statusIcon }} mr-2 w-4 text-center"></i> {{ $statusText }}</p>
                            
                            @if($tanggal || $catatan)
                            <div class="mt-2 space-y-2">
                                @if($tanggal)
                                    <p class="text-xs text-slate-500 flex items-center">
                                        <i class="fas fa-calendar-alt fa-fw mr-2 text-slate-400"></i>
                                        <span>{{ $tanggal->translatedFormat('d F Y \p\u\k\u\l H:i') }}</span>
                                    </p>
                                @endif
                                @if($catatan)
                                <div class="text-xs text-slate-600 bg-slate-50 border-l-4 border-slate-200 pl-3 pr-2 py-2 italic">"{{ $catatan }}"</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif

                    {{-- TAHAP 2: APPROVER 2 --}}
                    @if ($pengajuanDana->approver_2_id)
                        @php
                            $status = $pengajuanDana->approver_2_status;
                            $catatan = $pengajuanDana->approver_2_catatan;
                            $tanggal = $pengajuanDana->approver_2_approved_at;
                            $namaApprover = $pengajuanDana->approver2->name ?? 'Approver Dihapus';
                            
                            $statusClass = ''; $statusText = ''; $statusIcon = '';
                            switch ($status) {
                                case 'disetujui': $statusClass = 'text-green-600'; $statusIcon = 'fa-check-circle'; $statusText = 'Disetujui'; break;
                                case 'ditolak': $statusClass = 'text-red-600'; $statusIcon = 'fa-times-circle'; $statusText = 'Ditolak'; break;
                                case 'skipped': $statusClass = 'text-slate-500'; $statusIcon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                                default: $statusClass = 'text-yellow-600'; $statusIcon = 'fa-clock'; $statusText = 'Menunggu'; break;
                            }
                            if ($pengajuanDana->status === 'dibatalkan') {
                                $statusClass = 'text-slate-500'; $statusIcon = 'fa-ban'; $statusText = 'Dibatalkan';
                            }
                        @endphp
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Approver 2: <span class="text-slate-700 font-semibold">{{ $namaApprover }}</span></label>
                            <p class="flex items-center font-semibold {{ $statusClass }}"><i class="fas {{ $statusIcon }} mr-2 w-4 text-center"></i> {{ $statusText }}</p>
                            
                            @if($tanggal || $catatan)
                            <div class="mt-2 space-y-2">
                                @if($tanggal)
                                    <p class="text-xs text-slate-500 flex items-center">
                                        <i class="fas fa-calendar-alt fa-fw mr-2 text-slate-400"></i>
                                        <span>{{ $tanggal->translatedFormat('d F Y \p\u\k\u\l H:i') }}</span>
                                    </p>
                                @endif
                                @if($catatan)
                                <div class="text-xs text-slate-600 bg-slate-50 border-l-4 border-slate-200 pl-3 pr-2 py-2 italic">"{{ $catatan }}"</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif
                    
                    {{-- TAHAP 3: MANAGER KEUANGAN --}}
                    @if ($pengajuanDana->user->managerKeuangan)
                        @php
                            $status = $pengajuanDana->payment_status; 
                            $catatan = $pengajuanDana->catatan_finance;
                            $tanggal = $pengajuanDana->finance_processed_at; 
                            $namaApprover = $pengajuanDana->user->managerKeuangan->name ?? 'Finance Dihapus';
                            
                            $statusClass = ''; $statusText = ''; $statusIcon = '';
                            switch ($status) {
                                case 'selesai': $statusClass = 'text-green-600'; $statusIcon = 'fa-check-circle'; $statusText = 'Selesai (Dibayar)'; break;
                                case 'diproses': $statusClass = 'text-blue-600'; $statusIcon = 'fa-sync-alt'; $statusText = 'Sedang Diproses'; break;
                                case 'ditolak': $statusClass = 'text-red-600'; $statusIcon = 'fa-times-circle'; $statusText = 'Ditolak'; break;
                                case 'skipped': $statusClass = 'text-slate-500'; $statusIcon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                                default: $statusClass = 'text-yellow-600'; $statusIcon = 'fa-clock'; $statusText = 'Menunggu'; break;
                            }
                            if ($pengajuanDana->status === 'dibatalkan') {
                                $statusClass = 'text-slate-500'; $statusIcon = 'fa-ban'; $statusText = 'Dibatalkan';
                            } elseif ($pengajuanDana->status === 'ditolak') {
                                $statusClass = 'text-slate-500'; $statusIcon = 'fa-ban'; 
                                // Tentukan siapa yang menolak
                                if ($pengajuanDana->approver_1_status === 'ditolak') {
                                    $statusText = 'Dihentikan (Ditolak Approver 1)';
                                } elseif ($pengajuanDana->approver_2_status === 'ditolak') {
                                    $statusText = 'Dihentikan (Ditolak Approver 2)';
                                } else {
                                    $statusText = 'Dihentikan'; // Fallback jika tidak jelas
                                }
                            }
                            if ($status === 'selesai') {
                                $tanggal = $pengajuanDana->updated_at;
                            }
                        @endphp
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Manager Keuangan: <span class="text-slate-700 font-semibold">{{ $namaApprover }}</span></label>
                            <p class="flex items-center font-semibold {{ $statusClass }}"><i class="fas {{ $statusIcon }} mr-2 w-4 text-center"></i> {{ $statusText }}</p>
                            
                            @if($tanggal || $catatan)
                            <div class="mt-2 space-y-2">
                                @if($tanggal)
                                    <p class="text-xs text-slate-500 flex items-center">
                                        <i class="fas fa-calendar-alt fa-fw mr-2 text-slate-400"></i>
                                        <span>{{ $tanggal->translatedFormat('d F Y \p\u\k\u\l H:i') }}</span>
                                    </p>
                                @endif
                                @if($catatan)
                                <div class="text-xs text-slate-600 bg-slate-50 border-l-4 border-slate-200 pl-3 pr-2 py-2 italic">"{{ $catatan }}"</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif
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

        {{-- Approve/Reject HANYA untuk Approver 1 & 2 --}}
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

        {{-- Tombol "Proses Pembayaran" HANYA untuk Manager Keuangan --}}
        @can('prosesPembayaran', $pengajuanDana)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Tindakan Pembayaran</h3>
                <p class="text-sm text-slate-500 mb-4">Tandai pengajuan ini sebagai "sedang diproses" sebelum mengunggah bukti transfer.</p>
                <form action="{{ route('pengajuan_dana.proses_pembayaran', $pengajuanDana) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="catatan-proses">Catatan Proses (Opsional)</label>
                    <textarea id="catatan-proses" name="catatan_proses" rows="3" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    <button type="submit" class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition"><i class="fas fa-sync-alt mr-2"></i>Tandai Sedang Diproses</button>
                </form>
            </div>
        @endcan

        {{-- Tombol "Upload Bukti Transfer" HANYA untuk Manager Keuangan --}}
        @can('uploadBuktiTransfer', $pengajuanDana)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Unggah Bukti Transfer</h3>
                <form action="{{ route('pengajuan_dana.upload_bukti_transfer', $pengajuanDana) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="bukti-transfer">Pilih File</Elabel>
                    <input type="file" name="bukti_transfer" id="bukti-transfer" class="w-full p-2 border border-slate-300 rounded-lg" required>
                    <button type="submit" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition"><i class="fas fa-upload mr-2"></i>Unggah Bukti</Tbutton>
                </form>
            </div>
        @endcan

    </div>
</x-layout-users>