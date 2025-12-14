<x-layout-users>
    <x-slot:title>Detail Pengajuan Barang</x-slot:title>

    <div class="bg-gradient-to-br from-sky-50 to-blue-100 min-h-screen p-0 md:p-8">
        <div class="container mx-auto max-w-6xl">

            {{-- 1. NAVIGASI (BACK & DOWNLOAD) --}}
            {{-- HANYA TAMPIL JIKA YANG LOGIN ADALAH PEMBUAT PENGAJUAN --}}
            @if(Auth::id() == $pengajuanBarang->user_id)
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
                {{-- Tombol Kembali --}}
                <a href="{{ route('pengajuan_barang.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-[#001A6E] rounded-xl shadow-lg hover:bg-[#0B1D51] hover:shadow-xl transition-all duration-300 w-full sm:w-auto">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>

                {{-- Tombol Download PDF --}}
                <a href="{{ route('pengajuan_barang.download', $pengajuanBarang) }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl shadow-lg hover:bg-red-700 hover:shadow-xl transition-all duration-300 w-full sm:w-auto"
                   title="Unduh sebagai PDF">
                    <i class="fas fa-file-pdf"></i>
                    <span>Cetak PDF</span>
                </a>
            </div>
            @endif

            {{-- 2. HEADER UTAMA --}}
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-6 md:p-8 bg-[#001A6E] text-white">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-5">
                            <div class="h-14 w-14 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-box text-3xl text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight">{{ $pengajuanBarang->judul_pengajuan }}</h1>
                                <p class="text-slate-300 text-sm mt-1">
                                    Diajukan tanggal {{ $pengajuanBarang->created_at->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- Status Badge Global --}}
                        <div>
                            @php
                                $statusClass = '';
                                $statusText = '';
                                switch ($pengajuanBarang->status) {
                                    case 'selesai': $statusClass = 'bg-green-500 text-white shadow-green-500/30'; $statusText = 'Selesai'; break;
                                    case 'ditolak': $statusClass = 'bg-red-500 text-white shadow-red-500/30'; $statusText = 'Ditolak'; break;
                                    case 'diproses': $statusClass = 'bg-blue-500 text-white shadow-blue-500/30'; $statusText = 'Diproses Gudang'; break;
                                    case 'dibatalkan': $statusClass = 'bg-slate-500 text-white shadow-slate-500/30'; $statusText = 'Dibatalkan'; break;
                                    default: $statusClass = 'bg-yellow-500 text-white shadow-yellow-500/30'; $statusText = 'Menunggu Atasan'; break;
                                }
                            @endphp
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold shadow-lg {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. GRID CONTENT (INFO & TIMELINE) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                {{-- KOLOM KIRI: INFO PEMOHON --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden h-full">
                        <div class="px-5 py-4 border-b border-slate-100 bg-[#D0E1FD]">
                            <h2 class="text-base font-bold text-slate-800 flex items-center">
                                <i class="fas fa-user-circle text-slate-700 mr-2 text-lg"></i> Informasi Pemohon
                            </h2>
                        </div>
                        <div class="p-5 text-sm space-y-3">
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span class="text-slate-500">Nama</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $pengajuanBarang->user->name }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span class="text-slate-500">Divisi</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $pengajuanBarang->user->divisi }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span class="text-slate-500">Jabatan</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $pengajuanBarang->user->jabatan ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">ID Pengajuan</span>
                                <span class="font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded text-xs">
                                    #{{ str_pad($pengajuanBarang->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: TIMELINE (CARD STYLE BLOCKS) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden h-full flex flex-col">
                        <div class="px-5 py-3 border-b border-slate-100 bg-[#D0E1FD]">
                            <h2 class="text-sm font-bold text-slate-800 flex items-center">
                                <i class="fas fa-history text-slate-700 mr-2 text-lg"></i> Timeline Persetujuan
                            </h2>
                        </div>

                        {{-- Isi Timeline --}}
                        <div class="p-5 space-y-3 flex-1 overflow-y-auto">
                            
                            {{-- 1. APPROVER 1 (ATASAN) --}}
                            @php
                                $s1 = $pengajuanBarang->status_atasan;
                                $c1 = $pengajuanBarang->catatan_atasan;
                                $t1 = $pengajuanBarang->atasan_approved_at ?? null; 
                                
                                $theme1 = match($s1) {
                                    'disetujui' => ['border' => 'border-l-green-500', 'bg' => 'bg-slate-50', 'badge' => 'bg-green-100 text-green-700'],
                                    'ditolak' => ['border' => 'border-l-red-500', 'bg' => 'bg-red-50', 'badge' => 'bg-red-100 text-red-700'],
                                    'skipped' => ['border' => 'border-l-slate-400', 'bg' => 'bg-slate-50', 'badge' => 'bg-slate-200 text-slate-600'],
                                    default => ['border' => 'border-l-yellow-400', 'bg' => 'bg-yellow-50', 'badge' => 'bg-yellow-100 text-yellow-700'],
                                };
                            @endphp
                            <div class="rounded border border-slate-200 p-3 {{ $theme1['bg'] }} {{ $theme1['border'] }} border-l-[3px]">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahap 1</span>
                                            {{-- TANGGAL APPROVAL ATASAN --}}
                                            @if($s1 == 'disetujui' && $t1)
                                                <span class="text-[10px] text-slate-400">• {{ \Carbon\Carbon::parse($t1)->translatedFormat('d M, H:i') }}</span>
                                            @elseif($s1 == 'skipped')
                                                <span class="text-[10px] text-slate-400">• (Dilewati)</span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 leading-tight mt-0.5">Atasan Langsung</h4>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $theme1['badge'] }}">
                                        {{ ucfirst($s1) }}
                                    </span>
                                </div>
                                @if($c1)
                                    <div class="mt-2 bg-white p-2 rounded border border-slate-200 text-xs text-slate-600 italic relative flex items-start gap-2">
                                        <i class="fas fa-quote-left text-slate-300 text-[10px] mt-0.5"></i>
                                        <span>{{ $c1 }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- 2. APPROVER 2 (GUDANG/LOGISTIK) --}}
                            @php
                                // MENGGUNAKAN KOLOM BARU (GUDANG)
                                $s2 = $pengajuanBarang->status_gudang; 
                                $c2 = $pengajuanBarang->catatan_gudang;
                                $t2 = $pengajuanBarang->gudang_approved_at ?? null;

                                $theme2 = match($s2) {
                                    'disetujui' => ['border' => 'border-l-green-500', 'bg' => 'bg-slate-50', 'badge' => 'bg-green-100 text-green-700'],
                                    'ditolak' => ['border' => 'border-l-red-500', 'bg' => 'bg-red-50', 'badge' => 'bg-red-100 text-red-700'],
                                    'skipped' => ['border' => 'border-l-slate-400', 'bg' => 'bg-slate-50', 'badge' => 'bg-slate-200 text-slate-600'],
                                    default => ['border' => 'border-l-yellow-400', 'bg' => 'bg-yellow-50', 'badge' => 'bg-yellow-100 text-yellow-700'],
                                };
                            @endphp
                            <div class="rounded border border-slate-200 p-3 {{ $theme2['bg'] }} {{ $theme2['border'] }} border-l-[3px]">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahap 2</span>
                                            {{-- TANGGAL APPROVAL GUDANG --}}
                                            @if($s2 == 'disetujui' && $t2)
                                                <span class="text-[10px] text-slate-400">• {{ \Carbon\Carbon::parse($t2)->translatedFormat('d M, H:i') }}</span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 leading-tight mt-0.5">Bagian Gudang/Logistik</h4>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $theme2['badge'] }}">
                                        {{ ucfirst($s2) }}
                                    </span>
                                </div>
                                @if($c2)
                                    <div class="mt-2 bg-white p-2 rounded border border-slate-200 text-xs text-slate-600 italic relative flex items-start gap-2">
                                        <i class="fas fa-quote-left text-slate-300 text-[10px] mt-0.5"></i>
                                        <span>{{ $c2 }}</span>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. RINCIAN BARANG (TABEL) --}}
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-slate-100 bg-[#D0E1FD]">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center">
                        <i class="fas fa-list-ul text-slate-700 mr-2 text-lg"></i> Rincian Barang
                    </h2>
                </div>
                <div class="p-6">
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider font-semibold">
                                <tr>
                                    <th class="px-6 py-4 text-center w-16">No</th>
                                    <th class="px-6 py-4 text-left">Deskripsi Barang</th>
                                    <th class="px-6 py-4 text-center w-32">Satuan</th>
                                    <th class="px-6 py-4 text-center w-32">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($pengajuanBarang->rincian_barang as $index => $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 text-slate-800 font-medium">{{ $item['deskripsi'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center text-slate-600 bg-slate-50">
                                        {{ $item['satuan'] ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-md font-bold font-mono">
                                            {{ $item['jumlah'] ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">Data rincian tidak tersedia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-blue-50/50 border-t border-blue-100">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-blue-900">TOTAL ITEM BARANG</td>
                                    <td class="px-6 py-4 text-center font-bold font-mono text-blue-700 text-lg">
                                        {{ count($pengajuanBarang->rincian_barang ?? []) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 5. DOKUMEN & BUKTI --}}
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-slate-100 bg-[#D0E1FD]">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center">
                        <i class="fas fa-paperclip text-slate-700 mr-2 text-lg"></i> Dokumen Pendukung
                    </h2>
                </div>
                <div class="p-6">
                    @if(empty($pengajuanBarang->lampiran))
                        <div class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                            <i class="fas fa-file-excel text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500">Tidak ada dokumen yang dilampirkan.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($pengajuanBarang->lampiran as $lampiran)
                            <div class="relative group">
                                <a href="{{ asset('storage/' . $lampiran) }}" target="_blank"
                                   class="flex flex-col items-center p-6 bg-slate-50 border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-all text-center h-full">
                                    <i class="fas fa-file-alt text-3xl text-blue-500 mb-3 group-hover:scale-110 transition-transform"></i>
                                    <p class="text-sm font-semibold text-slate-700 truncate w-full">{{ basename($lampiran) }}</p>
                                    <p class="text-xs text-slate-500 mt-1">Lampiran {{ $loop->iteration }}</p>
                                </a>
                                <a href="{{ asset('storage/' . $lampiran) }}" download
                                   class="absolute top-2 right-2 p-1.5 bg-slate-200 text-slate-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"
                                   title="Download">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- 6. AREA TINDAKAN / ACTION --}}
            <div class="space-y-6 mt-8 pb-12">
                
                @php
                    $user = Auth::user();
                    
                    // --- LOGIC MANUAL (Agar form muncul tanpa perlu setting Policies Laravel dulu) ---
                    
                    // 1. Cek User = Atasan?
                    // Harus Kepala Divisi, Divisi Sama, dan Status masih Menunggu
                    $isAtasanApprove = $user->is_kepala_divisi 
                                       && $user->divisi === $pengajuanBarang->divisi 
                                       && $pengajuanBarang->status_atasan === 'menunggu';

                    // 2. Cek User = Gudang?
                    // Jabatan ada kata 'Gudang', Atasan sudah OK (atau skipped), dan Status Gudang masih Menunggu
                    $isGudangApprove = str_contains(strtolower($user->jabatan ?? ''), 'gudang') 
                                       && ($pengajuanBarang->status_atasan === 'disetujui' || $pengajuanBarang->status_atasan === 'skipped')
                                       && $pengajuanBarang->status_gudang === 'menunggu'; // Pakai kolom baru

                    // 3. Admin bypass
                    $isAdmin = $user->role === 'admin';
                @endphp

                {{-- TAMPILKAN FORM JIKA USER BERHAK --}}
                @if($isAtasanApprove || $isGudangApprove || $isAdmin)
                <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 border-t-4 border-t-blue-500">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fas fa-gavel text-slate-800 mr-2"></i> Tindakan Persetujuan
                    </h3>
                    
                    {{-- Info user login sebagai apa --}}
                    @if($isAtasanApprove)
                        <div class="mb-4 bg-blue-50 text-blue-800 px-4 py-2 rounded-lg text-sm font-semibold border border-blue-100 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            <span>Anda login sebagai <span class="font-bold">Kepala Divisi</span>. Silakan verifikasi pengajuan ini.</span>
                        </div>
                    @elseif($isGudangApprove)
                         <div class="mb-4 bg-blue-50 text-blue-800 px-4 py-2 rounded-lg text-sm font-semibold border border-blue-100 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            <span>Anda login sebagai <span class="font-bold">Gudang/Logistik</span>. Silakan verifikasi ketersediaan barang.</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- FORM SETUJU --}}
                        <form action="{{ route('pengajuan_barang.approve', $pengajuanBarang) }}" method="POST" class="bg-green-50 p-6 rounded-2xl border border-green-100">
                            @csrf
                            <label class="block text-sm font-bold text-green-800 mb-2">
                                Catatan Persetujuan <span class="font-normal text-green-600">(Opsional)</span>
                            </label>
                            <textarea name="catatan_persetujuan" rows="3" 
                                class="w-full p-3 border border-green-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white text-sm"
                                placeholder="Tulis catatan jika perlu..."></textarea>
                            
                            <button type="submit" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md shadow-green-200 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i> Setujui Pengajuan
                            </button>
                        </form>
                        
                        {{-- FORM TOLAK --}}
                        <form action="{{ route('pengajuan_barang.reject', $pengajuanBarang) }}" method="POST" class="bg-red-50 p-6 rounded-2xl border border-red-100">
                            @csrf
                            <label class="block text-sm font-bold text-red-800 mb-2">
                                Catatan Penolakan <span class="text-red-600">*</span>
                            </label>
                            <textarea name="catatan_penolakan" rows="3" 
                                class="w-full p-3 border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white text-sm"
                                placeholder="Alasan penolakan..." required></textarea>
                            
                            <button type="submit" class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md shadow-red-200 flex items-center justify-center gap-2">
                                <i class="fas fa-times-circle"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- TOMBOL BATALKAN PENGAJUAN (Khusus Pemilik) --}}
                @php
                    $isOwner = Auth::id() == $pengajuanBarang->user_id;
                    $canCancel = $isOwner && ($pengajuanBarang->status == 'diajukan' || $pengajuanBarang->status_atasan == 'menunggu');
                @endphp

                @if($canCancel)
                <div class="bg-red-50 rounded-2xl border border-red-100 p-6 md:p-8 text-center mt-6">
                    <h3 class="text-lg font-bold text-red-800 mb-2">Batalkan Pengajuan?</h3>
                    <p class="text-sm text-red-600 mb-4">Tindakan ini tidak dapat diurungkan. Pengajuan akan ditandai sebagai dibatalkan.</p>
                    <form action="{{ route('pengajuan_barang.cancel', $pengajuanBarang) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?');">
                        @csrf
                        <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-600 hover:text-white font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Batalkan Pengajuan
                        </button>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-layout-users>