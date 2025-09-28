<x-layout-users>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>

    <div class="container mx-auto p-4 md:p-6">
        <x-back-button href="{{ route('pengajuan_dana.index') }}">Kembali ke Rekap Pengajuan</x-back-button>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-4">
            {{-- ... (bagian atas tidak ada perubahan) ... --}}
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Detail Pengajuan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                <div class="bg-slate-50 p-4 rounded-lg">
                    <label class="block text-slate-500">Nama Pemohon</label>
                    <p class="font-semibold text-slate-800">{{ $pengajuanDana->user->name }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <label class="block text-slate-500">Divisi</label>
                    <p class="font-semibold text-slate-800">{{ $pengajuanDana->divisi }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <label class="block text-slate-500">Tanggal Pengajuan</label>
                    <p class="font-semibold text-slate-800">{{ $pengajuanDana->created_at->format('d F Y') }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <label class="block text-slate-500">Bank & No. Rekening</label>
                    <p class="font-semibold text-slate-800">{{ $pengajuanDana->nama_bank }} - {{ $pengajuanDana->no_rekening }}</p>
                </div>
                <div class="lg:col-span-2 bg-slate-50 p-4 rounded-lg">
                    <label class="block text-slate-500">Judul Pengajuan</label>
                    <p class="font-semibold text-slate-800">{{ $pengajuanDana->judul_pengajuan }}</p>
                </div>
            </div>
            <div class="mt-8">
                <h3 class="font-bold text-slate-800 mb-2">Rincian Penggunaan Dana</h3>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100"><tr class="text-left"><th class="p-3 font-semibold text-slate-600 w-2/3">Deskripsi</th><th class="p-3 font-semibold text-slate-600 w-1/3">Jumlah</th></tr></thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($pengajuanDana->rincian_dana as $rincian)
                                <tr><td class="p-3">{{ $rincian['deskripsi'] }}</td><td class="p-3">Rp {{ number_format($rincian['jumlah'], 0, ',', '.') }}</td></tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100">
                            <tr>
                                <td class="p-3 font-bold text-slate-800 text-right">TOTAL</td>
                                <td class="p-3 font-bold text-slate-900">Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- --- PERUBAHAN DI SINI --- --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                @if ($pengajuanDana->lampiran)
                <div>
                    <h3 class="font-bold text-slate-800 mb-2">Lampiran Pengajuan</h3>
                    <a href="{{ asset('storage/' . $pengajuanDana->lampiran) }}" target="_blank" class="flex items-center gap-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 p-3 rounded-lg transition text-blue-700 font-semibold">
                        <i class="fas fa-file-alt text-xl"></i><span>Lihat Lampiran</span>
                    </a>
                </div>
                @endif
                @if ($pengajuanDana->bukti_transfer)
                <div>
                    <h3 class="font-bold text-slate-800 mb-2">Bukti Transfer</h3>
                    <a href="{{ asset('storage/' . $pengajuanDana->bukti_transfer) }}" target="_blank" class="flex items-center gap-3 bg-green-50 hover:bg-green-100 border border-green-200 p-3 rounded-lg transition text-green-700 font-semibold">
                        <i class="fas fa-receipt text-xl"></i><span>Lihat Bukti Transfer</span>
                    </a>
                </div>
                @endif
                {{-- --- TAMBAHAN BARU DI SINI --- --}}
                @if ($pengajuanDana->invoice)
                <div>
                    <h3 class="font-bold text-slate-800 mb-2">Invoice Final</h3>
                    <a href="{{ asset('storage/' . $pengajuanDana->invoice) }}" target="_blank" class="flex items-center gap-3 bg-purple-50 hover:bg-purple-100 border border-purple-200 p-3 rounded-lg transition text-purple-700 font-semibold">
                        <i class="fas fa-file-invoice-dollar text-xl"></i><span>Lihat Invoice</span>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-8 mt-8">
             {{-- ... (bagian timeline status tidak ada perubahan) ... --}}
            <h2 class="text-2xl font-bold text-slate-800 mb-8">Alur Persetujuan</h2>
            <div class="space-y-8">
                @php
                    if ($pengajuanDana->user->is_kepala_divisi) {
                        $status = $pengajuanDana->status_direktur; $title = 'Persetujuan Direktur'; $catatan = $pengajuanDana->catatan_direktur;
                    } else {
                        $status = $pengajuanDana->status_atasan; $title = 'Persetujuan Atasan'; $catatan = $pengajuanDana->catatan_atasan;
                    }
                    $bgColor = 'bg-yellow-100 text-yellow-600'; $textColor = 'text-yellow-700'; $icon = 'fa-clock'; $statusText = ucfirst($status);
                    switch ($status) {
                        case 'disetujui': $bgColor = 'bg-green-100 text-green-600'; $textColor = 'text-green-700'; $icon = 'fa-check-circle'; break;
                        case 'ditolak': $bgColor = 'bg-red-100 text-red-600'; $textColor = 'text-red-700'; $icon = 'fa-times-circle'; break;
                        case 'skipped': $bgColor = 'bg-slate-100 text-slate-400'; $textColor = 'text-slate-500'; $icon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                    }
                @endphp
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center {{ $bgColor }}"><i class="fas {{ $icon }} text-xl"></i></div>
                    <div class="flex-grow pt-1">
                        <h4 class="font-bold text-slate-800 text-lg">{{ $title }}</h4>
                        <p class="text-sm text-slate-500">Status: <span class="font-bold {{ $textColor }}">{{ $statusText }}</span></p>
                        @if($catatan)<div class="mt-2 text-sm text-slate-700 bg-slate-50 border-l-4 border-slate-200 p-3 rounded-r-lg"><p class="italic">"{{ $catatan }}"</p></div>@endif
                    </div>
                </div>
                @php
                    $status = $pengajuanDana->status_finance; $title = 'Persetujuan Finance'; $catatan = $pengajuanDana->catatan_finance;
                    $bgColor = 'bg-yellow-100 text-yellow-600'; $textColor = 'text-yellow-700'; $icon = 'fa-clock'; $statusText = ucfirst($status);
                    switch ($status) {
                        case 'disetujui': $bgColor = 'bg-green-100 text-green-600'; $textColor = 'text-green-700'; $icon = 'fa-check-circle'; break;
                        case 'ditolak': $bgColor = 'bg-red-100 text-red-600'; $textColor = 'text-red-700'; $icon = 'fa-times-circle'; break;
                        case 'skipped': $bgColor = 'bg-slate-100 text-slate-400'; $textColor = 'text-slate-500'; $icon = 'fa-minus-circle'; $statusText = 'Dilewati'; break;
                    }
                @endphp
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center {{ $bgColor }}"><i class="fas {{ $icon }} text-xl"></i></div>
                    <div class="flex-grow pt-1">
                        <h4 class="font-bold text-slate-800 text-lg">{{ $title }}</h4>
                        <p class="text-sm text-slate-500">Status: <span class="font-bold {{ $textColor }}">{{ $statusText }}</span></p>
                        @if($catatan)<div class="mt-2 text-sm text-slate-700 bg-slate-50 border-l-4 border-slate-200 p-3 rounded-r-lg"><p class="italic">"{{ $catatan }}"</p></div>@endif
                    </div>
                </div>
            </div>
        </div>
        
        @can('approve', $pengajuanDana)
            {{-- ... (bagian form persetujuan tidak ada perubahan) ... --}}
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

        {{-- --- PERUBAHAN DI SINI (FORM BUKTI TRANSFER) --- --}}
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

        {{-- --- TAMBAHAN BARU DI SINI (FORM INVOICE) --- --}}
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