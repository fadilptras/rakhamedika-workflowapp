<x-layout-users>
    <x-slot:title>Detail Pengajuan Barang</x-slot:title>

    <div class="bg-gradient-to-br from-sky-50 to-blue-100 min-h-screen p-0 md:p-8">
        <div class="container mx-auto max-w-6xl">

            {{-- 1. NAVIGASI --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
                <a href="{{ route('pengajuan_barang.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-[#001A6E] rounded-xl shadow-lg hover:bg-[#0B1D51] hover:shadow-xl transition-all duration-300 w-full sm:w-auto">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>

                @if(Auth::id() == $pengajuanBarang->user_id || Auth::user()->role === 'admin')
                <a href="{{ route('pengajuan_barang.download', $pengajuanBarang) }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl shadow-lg hover:bg-red-700 hover:shadow-xl transition-all duration-300 w-full sm:w-auto">
                    <i class="fas fa-file-pdf"></i>
                    <span>Cetak PDF</span>
                </a>
                @endif
            </div>

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
                        
                        <div>
                            @php
                                $statusClass = match($pengajuanBarang->status) {
                                    'selesai' => 'bg-green-500 shadow-green-500/30',
                                    'ditolak' => 'bg-red-500 shadow-red-500/30',
                                    'diproses' => 'bg-blue-500 shadow-blue-500/30',
                                    'dibatalkan' => 'bg-slate-500 shadow-slate-500/30',
                                    default => 'bg-yellow-500 shadow-yellow-500/30',
                                };
                            @endphp
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold shadow-lg text-white {{ $statusClass }}">
                                {{ ucfirst($pengajuanBarang->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. INFO & TIMELINE --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden h-full">
                        <div class="px-5 py-4 border-b border-slate-100 bg-[#D0E1FD]">
                            <h2 class="text-base font-bold text-slate-800 flex items-center">
                                <i class="fas fa-user-circle text-slate-700 mr-2 text-lg"></i> Informasi Pemohon
                            </h2>
                        </div>
                        <div class="p-5 text-sm space-y-3">
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span class="text-slate-500">Nama</span>
                                <span class="font-semibold text-slate-800">{{ $pengajuanBarang->user->name }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span class="text-slate-500">Divisi</span>
                                <span class="font-semibold text-slate-800">{{ $pengajuanBarang->divisi }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">ID Pengajuan</span>
                                <span class="font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded text-xs">
                                    #BRG-{{ str_pad($pengajuanBarang->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden h-full">
                        <div class="px-5 py-3 border-b border-slate-100 bg-[#D0E1FD]">
                            <h2 class="text-sm font-bold text-slate-800 flex items-center">
                                <i class="fas fa-history text-slate-700 mr-2 text-lg"></i> Timeline Persetujuan
                            </h2>
                        </div>

                        <div class="p-5 space-y-3">
                            {{-- TAHAP 1 --}}
                            <div class="rounded border border-slate-200 p-3 bg-slate-50 border-l-[3px] {{ $pengajuanBarang->status_appr_1 == 'disetujui' ? 'border-l-green-500' : 'border-l-yellow-400' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahap 1: Approver 1</span>
                                        <h4 class="text-sm font-bold text-slate-800 leading-tight">{{ $pengajuanBarang->approver1->name ?? 'Belum Diatur' }}</h4>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $pengajuanBarang->status_appr_1 == 'disetujui' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($pengajuanBarang->status_appr_1) }}
                                    </span>
                                </div>
                                @if($pengajuanBarang->catatan_approver_1)
                                    <p class="mt-2 text-xs text-slate-600 italic">"{{ $pengajuanBarang->catatan_approver_1 }}"</p>
                                @endif
                            </div>

                            {{-- TAHAP 2 --}}
                            <div class="rounded border border-slate-200 p-3 bg-slate-50 border-l-[3px] {{ $pengajuanBarang->status_appr_2 == 'disetujui' ? 'border-l-green-500' : 'border-l-yellow-400' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahap 2: Approver 2</span>
                                        <h4 class="text-sm font-bold text-slate-800 leading-tight">{{ $pengajuanBarang->approver2->name ?? 'Belum Diatur' }}</h4>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $pengajuanBarang->status_appr_2 == 'disetujui' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($pengajuanBarang->status_appr_2) }}
                                    </span>
                                </div>
                                @if($pengajuanBarang->catatan_approver_2)
                                    <p class="mt-2 text-xs text-slate-600 italic">"{{ $pengajuanBarang->catatan_approver_2 }}"</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RINCIAN BARANG --}}
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-slate-100 bg-[#D0E1FD]">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center">
                        <i class="fas fa-list-ul text-slate-700 mr-2 text-lg"></i> Rincian Barang
                    </h2>
                </div>
                <div class="p-6">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left">Deskripsi Barang</th>
                                <th class="px-6 py-4 text-center">Satuan</th>
                                <th class="px-6 py-4 text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pengajuanBarang->rincian_barang ?? [] as $item)
                            <tr>
                                <td class="px-6 py-4 text-slate-800 font-medium">{{ $item['nama_barang'] ?? $item['deskripsi'] }}</td>
                                <td class="px-6 py-4 text-center text-slate-600">{{ $item['satuan'] }}</td>
                                <td class="px-6 py-4 text-center font-bold text-blue-700">{{ $item['jumlah'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- AREA TINDAKAN --}}
            @php
                $user = Auth::user();
                $showForm = ($user->id == $pengajuanBarang->approver_1_id && $pengajuanBarang->status_appr_1 == 'menunggu') ||
                            ($user->id == $pengajuanBarang->approver_2_id && $pengajuanBarang->status_appr_1 == 'disetujui' && $pengajuanBarang->status_appr_2 == 'menunggu');
            @endphp

            @if($showForm)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8 border-t-4 border-t-blue-500 mb-12">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                    <i class="fas fa-gavel text-slate-800 mr-2"></i> Tindakan Persetujuan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Form Setuju --}}
                    <form action="{{ route('pengajuan_barang.updateStatus', $pengajuanBarang) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-green-50 p-6 rounded-2xl border border-green-100 text-center h-full flex flex-col">
                            <label class="block text-sm font-bold text-green-800 mb-2">Setujui Pengajuan</label>
                            <textarea name="alasan" rows="3" class="w-full p-3 border border-green-200 rounded-xl mb-3 text-sm focus:ring-green-500 focus:border-green-500" placeholder="Catatan persetujuan (opsional)..."></textarea>
                            <input type="hidden" name="status" value="disetujui">
                            <button type="submit" class="mt-auto w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition shadow-md">Setujui Sekarang</button>
                        </div>
                    </form>

                    {{-- Form Tolak --}}
                    <form action="{{ route('pengajuan_barang.updateStatus', $pengajuanBarang) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-red-50 p-6 rounded-2xl border border-red-100 text-center h-full flex flex-col">
                            <label class="block text-sm font-bold text-red-800 mb-2">Tolak Pengajuan <span class="text-red-500">*</span></label>
                            <textarea name="alasan" rows="3" class="w-full p-3 border border-red-200 rounded-xl mb-3 text-sm focus:ring-red-500 focus:border-red-500" placeholder="Wajib isi alasan penolakan..." required></textarea>
                            <input type="hidden" name="status" value="ditolak">
                            <button type="submit" class="mt-auto w-full bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition shadow-md">Tolak Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-layout-users>