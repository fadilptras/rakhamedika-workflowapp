<x-layout-admin :title="'Detail Klien: ' . $client->nama_user">

    <div class="w-full max-w-7xl mx-auto px-0 py-0 relative">
        
        <div class="mb-6">
            <a href="{{ route('admin.crm.index') }}" class="inline-flex items-center text-zinc-400 hover:text-amber-500 font-semibold transition-colors text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Monitoring Sales
            </a>
        </div>

        {{-- SECTION 1: HERO PROFILE (Nama & Total Sales) --}}
        <div class="bg-gradient-to-r from-zinc-800 to-zinc-900 rounded-xl shadow-lg border border-zinc-700/50 p-6 mb-6 relative overflow-hidden group">
            {{-- Dekorasi Background --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="flex flex-col md:flex-row justify-between items-start gap-6 relative z-10">
                
                {{-- Kiri: Identitas Utama --}}
                <div class="flex-grow">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="bg-amber-500/10 text-amber-500 text-xs font-bold px-3 py-1 rounded-full border border-amber-500/20 uppercase tracking-wide">
                            <i class="fas fa-building mr-1"></i> {{ $client->nama_perusahaan }}
                        </span>
                        <span class="text-zinc-500 text-xs font-semibold flex items-center bg-zinc-800 px-3 py-1 rounded-full border border-zinc-700">
                            <i class="fas fa-user-tag mr-2 text-zinc-400"></i> Sales: <span class="text-zinc-300 ml-1">{{ $client->user->name ?? 'Deleted User' }}</span>
                        </span>
                    </div>

                    <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2">
                        {{ $client->nama_user }}
                    </h1>
                    
                    <p class="text-zinc-400 text-sm flex items-center gap-4">
                        <span class="flex items-center"><i class="fas fa-map-pin mr-2 text-red-500"></i> {{ $client->area ?? 'Area Belum diset' }}</span>
                        <span class="text-zinc-600">|</span>
                        <span class="flex items-center"><i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Join: {{ $client->created_at->format('d M Y') }}</span>
                    </p>

                    {{-- TOMBOL AKSI (Edit & Hapus) --}}
                    <div class="flex items-center gap-3 mt-6">
                        <button onclick="toggleModal('editClientModal')" class="flex items-center text-xs font-bold text-amber-400 bg-amber-900/20 hover:bg-amber-900/40 px-4 py-2 rounded-lg border border-amber-800/50 transition">
                            <i class="fas fa-edit mr-2"></i> Edit Data Lengkap
                        </button>
                        <form action="{{ route('admin.crm.client.destroy', $client->id) }}" method="POST" onsubmit="return confirm('PERINGATAN ADMIN: Menghapus klien ini akan menghapus semua riwayat transaksi secara permanen. Lanjutkan?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex items-center text-xs font-bold text-red-400 bg-red-900/20 hover:bg-red-900/40 px-4 py-2 rounded-lg border border-red-800/50 transition">
                                <i class="fas fa-trash-alt mr-2"></i> Hapus Klien
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Kanan: Big Number Statistik --}}
                @php
                    $total_gross = $client->interactions->where('jenis_transaksi', 'IN')->sum(function($i){
                        return $i->nilai_sales > 0 ? $i->nilai_sales : $i->nilai_kontribusi;
                    });
                @endphp
                <div class="bg-zinc-950/50 p-5 rounded-xl border border-zinc-800 text-right min-w-[250px]">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Sales (Gross)</p>
                    <div class="text-3xl font-extrabold text-emerald-500">
                        <span class="text-sm text-zinc-600 mr-1">Rp</span>{{ number_format($total_gross, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-[10px] text-zinc-500">
                        *Akumulasi seluruh transaksi masuk
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: DATA LENGKAP KLIEN (GRID 3 KOLOM) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            {{-- KARTU 1: KONTAK & ALAMAT --}}
            <div class="bg-zinc-800 rounded-xl border border-zinc-700/50 p-5 shadow-lg flex flex-col h-full">
                <div class="mb-4 pb-3 border-b border-zinc-700 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-zinc-300 uppercase tracking-wide">
                        <i class="fas fa-address-book mr-2 text-blue-500"></i> Kontak
                    </h4>
                </div>
                <div class="space-y-4 flex-grow">
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">Email</p>
                        <p class="text-zinc-200 text-sm font-medium">{{ $client->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">No. Telepon / WA</p>
                        <p class="text-zinc-200 text-sm font-medium flex items-center">
                            {{ $client->no_telpon ?? '-' }}
                            @if($client->no_telpon)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $client->no_telpon)) }}" target="_blank" class="ml-2 text-green-500 hover:text-green-400"><i class="fab fa-whatsapp"></i></a>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">Alamat Lengkap</p>
                        <p class="text-zinc-300 text-sm leading-relaxed">{{ $client->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: DATA INSTANSI --}}
            <div class="bg-zinc-800 rounded-xl border border-zinc-700/50 p-5 shadow-lg flex flex-col h-full">
                <div class="mb-4 pb-3 border-b border-zinc-700 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-zinc-300 uppercase tracking-wide">
                        <i class="fas fa-hospital mr-2 text-amber-500"></i> Data Instansi
                    </h4>
                </div>
                <div class="space-y-4 flex-grow">
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">PIC Klien (Internal)</p>
                        <p class="text-zinc-200 text-sm font-medium">{{ $client->pic ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">Tanggal Berdiri</p>
                        <p class="text-zinc-200 text-sm font-medium">
                            @if($client->tanggal_berdiri)
                                {{ \Carbon\Carbon::parse($client->tanggal_berdiri)->format('d F Y') }}
                                <span class="text-[10px] text-zinc-500 ml-1">({{ \Carbon\Carbon::parse($client->tanggal_berdiri)->age }} Tahun)</span>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">Wilayah / Area</p>
                        <span class="bg-zinc-700 text-zinc-300 text-xs px-2 py-1 rounded inline-block">
                            {{ $client->area ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- KARTU 3: INFORMASI KEUANGAN --}}
            <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-xl border border-zinc-700/50 p-5 shadow-lg flex flex-col h-full relative overflow-hidden">
                {{-- Efek Kartu Kredit --}}
                <div class="absolute -right-6 -bottom-6 text-9xl text-white/5 rotate-12 pointer-events-none">
                    <i class="fas fa-wallet"></i>
                </div>

                <div class="mb-4 pb-3 border-b border-zinc-700 flex items-center justify-between relative z-10">
                    <h4 class="text-sm font-bold text-emerald-400 uppercase tracking-wide">
                        <i class="fas fa-coins mr-2"></i> Keuangan
                    </h4>
                    <i class="fas fa-wifi text-zinc-600 rotate-90"></i>
                </div>
                <div class="space-y-5 relative z-10">
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">Bank & No. Rekening</p>
                        <p class="text-white text-lg font-bold tracking-wide">{{ $client->bank ?? 'BANK' }}</p>
                        <p class="text-zinc-300 text-sm font-mono tracking-wider">{{ $client->no_rekening ?? '---- ---- ----' }}</p>
                    </div>
                    <div class="pt-2 border-t border-zinc-700/50">
                        <p class="text-[10px] text-zinc-500 uppercase font-bold mb-0.5">Saldo Awal</p>
                        <p class="text-emerald-400 text-xl font-bold font-mono">
                            <span class="text-sm text-emerald-700 mr-1">Rp</span>{{ number_format($client->saldo_awal ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: TABS NAVIGATION (DIPISAH KIRI & KANAN) --}}
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            
            {{-- BAGIAN KIRI: MONITORING DATA --}}
            <div class="bg-zinc-900 p-1 rounded-lg inline-flex border border-zinc-700 w-full md:w-auto justify-center md:justify-start">
                <button id="btn-history" onclick="switchTab('history')" class="px-6 py-2 rounded-md text-sm font-bold bg-zinc-700 text-amber-500 shadow-sm transition-all flex-1 md:flex-none">
                    <i class="fas fa-history mr-2"></i> Riwayat
                </button>
                <button id="btn-recap" onclick="switchTab('recap')" class="px-6 py-2 rounded-md text-sm font-bold text-zinc-500 hover:text-zinc-300 transition-all flex-1 md:flex-none">
                    <i class="fas fa-chart-bar mr-2"></i> Rekap
                </button>
            </div>

            {{-- BAGIAN KANAN: INPUT TRANSAKSI --}}
            <div class="bg-zinc-900 p-1 rounded-lg inline-flex border border-zinc-700 w-full md:w-auto justify-center md:justify-end gap-1">
                {{-- Tombol Sales (IN) --}}
                <button id="btn-sales" onclick="switchTab('sales')" class="px-5 py-2 rounded-md text-sm font-bold text-zinc-500 hover:text-emerald-400 transition-all flex-1 md:flex-none">
                    <i class="fas fa-plus-circle mr-2"></i> Sales (In)
                </button>
                {{-- Tombol Support (OUT) --}}
                <button id="btn-support" onclick="switchTab('support')" class="px-5 py-2 rounded-md text-sm font-bold text-zinc-500 hover:text-red-400 transition-all flex-1 md:flex-none">
                    <i class="fas fa-hand-holding-usd mr-2"></i> Usage (Out)
                </button>
            </div>

        </div>

        {{-- SECTION 4: CONTENT AREA --}}
        
        {{-- [TAB 1] TAB FORM SALES (IN) --}}
        <div id="section-sales" class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700/50 overflow-hidden relative hidden">
            <div class="bg-emerald-900/20 px-6 py-4 border-b border-emerald-900/50 flex justify-between items-center">
                <h3 class="font-bold text-emerald-400 text-lg flex items-center">
                    <span class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                        <i class="fas fa-plus"></i>
                    </span>
                    Input Penjualan (Admin)
                </h3>
            </div>
            <div class="p-6 md:p-8">
                <form action="{{ route('admin.crm.interaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_interaksi" class="w-full bg-zinc-900 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500 px-4 py-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Nama Produk / Layanan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_produk" class="w-full bg-zinc-900 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500 px-4 py-2.5" placeholder="Contoh: Kassa Lipat" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-zinc-500 mb-1">Catatan Tambahan</label>
                                <textarea name="catatan" rows="3" class="w-full bg-zinc-900 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500 px-4 py-2.5"></textarea>
                            </div>
                        </div>
                        <div class="space-y-5 bg-zinc-900/50 p-6 rounded-xl border border-zinc-700">
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Nilai Sales (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-zinc-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="nilai_sales" class="w-full bg-zinc-800 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500 pl-10 px-4 py-2.5 font-mono text-lg" placeholder="0" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Komisi (%) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" name="komisi" step="0.1" max="100" class="w-full bg-zinc-800 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500 px-4 py-2.5 font-mono" placeholder="10" required>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                      <span class="text-zinc-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform active:scale-95 flex justify-center items-center gap-2">
                                    <i class="fas fa-save"></i> Simpan Data Sales
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- [TAB 2] TAB FORM SUPPORT (OUT) --}}
        <div id="section-support" class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700/50 overflow-hidden relative hidden">
            <div class="bg-red-900/20 px-6 py-4 border-b border-red-900/50 flex justify-between items-center">
                <h3 class="font-bold text-red-400 text-lg flex items-center">
                    <span class="w-8 h-8 bg-red-600 text-white rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    Pengeluaran Support (Admin)
                </h3>
            </div>
            <div class="p-6 md:p-8">
                <form action="{{ route('admin.crm.interaction.support') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_interaksi" class="w-full bg-zinc-900 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Keperluan Support <span class="text-red-500">*</span></label>
                                <input type="text" name="keperluan" class="w-full bg-zinc-900 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2.5" placeholder="Contoh: Transport" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-zinc-500 mb-1">Catatan Detail</label>
                                <textarea name="catatan" rows="3" class="w-full bg-zinc-900 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2.5"></textarea>
                            </div>
                        </div>
                        <div class="space-y-5 bg-zinc-900/50 p-6 rounded-xl border border-zinc-700">
                            <div>
                                <label class="block text-sm font-bold text-zinc-400 mb-1">Nominal Keluar (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-zinc-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="nominal" class="w-full bg-zinc-800 border-zinc-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 pl-10 px-4 py-2.5 font-mono text-lg" placeholder="0" required>
                                </div>
                                <p class="text-xs text-red-400 mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> Mengurangi saldo kontribusi</p>
                            </div>
                            <div class="pt-10">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform active:scale-95 flex justify-center items-center gap-2">
                                    <i class="fas fa-minus-circle"></i> Simpan Pengeluaran
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- [TAB 3] RIWAYAT TRANSAKSI --}}
        <div id="section-history" class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700/50 overflow-hidden">
             <div class="px-6 py-4 border-b border-zinc-700 bg-zinc-800 flex justify-between items-center">
                <h3 class="font-bold text-zinc-200">Data Transaksi</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left min-w-[900px] text-zinc-300">
                    <thead class="bg-zinc-900/50 text-zinc-400 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Item / Keterangan</th>
                            <th class="px-6 py-3 text-right">Sales (In)</th>
                            <th class="px-6 py-3 text-center">Komisi</th>
                            <th class="px-6 py-3 text-right text-blue-400">Value (In x Komisi)</th>
                            <th class="px-6 py-3 text-right text-red-400">Usage (Out)</th>
                            <th class="px-6 py-3 text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700">
                        @forelse ($interactions as $item)
                        @php
                            $isOut = $item->jenis_transaksi == 'OUT';
                            $rate = $item->komisi ?? 0;
                            if(!$isOut && !$rate && preg_match('/\[Rate:([\d\.]+)\]/', $item->catatan, $m)) { $rate = $m[1]; }
                            $displayNote = trim(preg_replace('/\[Rate:[\d\.]+\]/', '', $item->catatan));
                            $gross = ($item->nilai_sales > 0) ? $item->nilai_sales : $item->nilai_kontribusi;
                            $valueNet = $isOut ? 0 : ($gross * ($rate/100));
                        @endphp
                        <tr class="hover:bg-zinc-700/30 transition">
                            <td class="px-6 py-3 font-medium text-zinc-400">
                                {{ \Carbon\Carbon::parse($item->tanggal_interaksi)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3">
                                <div class="font-bold {{ $isOut ? 'text-red-400' : 'text-zinc-200' }}">
                                    {{ $item->nama_produk }}
                                </div>
                                <div class="text-xs text-zinc-500 italic">{{ $displayNote }}</div>
                            </td>
                            <td class="px-6 py-3 text-right font-mono text-emerald-500">
                                {{ !$isOut ? number_format($gross, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if(!$isOut && $rate > 0)
                                    <span class="bg-zinc-700 text-zinc-300 text-[10px] font-bold px-2 py-0.5 rounded border border-zinc-600">{{ $rate }}%</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right font-bold font-mono text-blue-400">
                                {{ !$isOut ? number_format($valueNet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold font-mono text-red-400">
                                {{ $isOut ? number_format($item->nilai_kontribusi, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <form action="{{ route('admin.crm.interaction.destroy', $item->id) }}" method="POST" onsubmit="return confirm('ADMIN: Hapus transaksi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-zinc-600 hover:text-red-500 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-6 text-zinc-500">Data kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700">
                {{ $interactions->links() }}
            </div>
        </div>

        {{-- [TAB 4] TAB REKAPITULASI (Dengan Tombol Export) --}}
        <div id="section-recap" class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700/50 overflow-hidden hidden">
            <div class="px-6 py-4 border-b border-zinc-700 flex flex-col md:flex-row justify-between items-center gap-4 bg-zinc-800">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-zinc-200">Rekapitulasi Tahun {{ $year }}</h3>
                    {{-- TOMBOL EXPORT ADMIN --}}
                    <a href="{{ route('admin.crm.client.export', ['client' => $client->id, 'year' => $year]) }}" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-3 rounded shadow-sm border border-emerald-800 transition flex items-center">
                        <i class="fas fa-file-excel mr-1.5"></i> Export Excel
                    </a>
                </div>

                <form action="{{ route('admin.crm.show', $client->id) }}" method="GET">
                    <select name="year" onchange="this.form.submit()" class="text-sm bg-zinc-900 border-zinc-700 rounded-md shadow-sm focus:border-amber-500 text-zinc-300 py-1.5">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left min-w-[900px] text-zinc-300">
                    <thead class="bg-zinc-900/50 text-zinc-400 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Bulan</th>
                            <th class="px-6 py-3 text-right">Sales (In)</th>
                            <th class="px-6 py-3 text-center">Komisi</th>
                            <th class="px-6 py-3 text-right text-blue-400 bg-blue-900/10">Value (Net)</th>
                            <th class="px-6 py-3 text-right text-red-400">Usage (Out)</th>
                            <th class="px-6 py-3 text-right text-zinc-200 border-l border-zinc-700">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700">
                        @foreach ($recap as $r)
                        <tr class="hover:bg-zinc-700/30 transition">
                            <td class="px-6 py-3 font-bold text-zinc-300">{{ $r['month_name'] }}</td>
                            <td class="px-6 py-3 text-right font-mono text-emerald-500">
                                {{ $r['gross_in'] > 0 ? number_format($r['gross_in'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-3 text-center text-xs text-zinc-500">
                                {{ $r['komisi_text'] }}
                            </td>
                            <td class="px-6 py-3 text-right font-mono font-bold bg-blue-900/10 text-blue-400">
                                {{ $r['net_value'] > 0 ? number_format($r['net_value'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-3 text-right font-mono text-red-400">
                                {{ $r['out'] > 0 ? number_format($r['out'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-3 text-right font-mono font-bold text-zinc-200 border-l border-zinc-700">
                                {{ number_format($r['saldo'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-zinc-900 font-bold border-t-2 border-zinc-700 text-zinc-100">
                        <tr>
                            <td class="px-6 py-4 uppercase tracking-wider">Total Akhir</td>
                            <td class="px-6 py-4 text-right text-emerald-500">{{ number_format($yearlyTotals['gross_in'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">-</td>
                            <td class="px-6 py-4 text-right text-blue-400 bg-blue-900/20">{{ number_format($yearlyTotals['net_value'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-red-400">{{ number_format($yearlyTotals['out'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right border-l border-zinc-600">{{ number_format($yearlyTotals['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT DATA KLIEN (Versi Admin Dark Mode) --}}
    <div id="editClientModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden transition-opacity duration-300">
        <div class="bg-zinc-900 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto border border-zinc-700">
            
            {{-- Modal Header --}}
            <div class="bg-zinc-800 px-6 py-4 flex justify-between items-center border-b border-zinc-700 sticky top-0 z-10">
                <h3 class="text-white font-bold text-lg flex items-center">
                    <i class="fas fa-user-edit mr-2 text-amber-500"></i> Edit Data Klien (Admin)
                </h3>
                <button onclick="toggleModal('editClientModal')" class="text-zinc-400 hover:text-white transition focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-6">
                <form action="{{ route('admin.crm.client.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Nama Klien / Dokter</label>
                            <input type="text" name="nama_user" value="{{ old('nama_user', $client->nama_user) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500" required>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Nama Instansi / RS</label>
                            <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $client->nama_perusahaan) }}" class="w-full px-4 py-2 bg-zinc-800 border-zinc-600 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500" required>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Area</label>
                            <input type="text" name="area" value="{{ old('area', $client->area) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Tanggal Berdiri</label>
                            <input type="date" name="tanggal_berdiri" value="{{ old('tanggal_berdiri', $client->tanggal_berdiri) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pt-4 border-t border-zinc-700">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Telepon / WA</label>
                            <input type="text" name="no_telpon" value="{{ old('no_telpon', $client->no_telpon) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Alamat</label>
                            <textarea name="alamat" rows="2" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('alamat', $client->alamat) }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 pt-4 border-t border-zinc-700">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Bank</label>
                            <input type="text" name="bank" value="{{ old('bank', $client->bank) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1">No. Rekening</label>
                            <input type="text" name="no_rekening" value="{{ old('no_rekening', $client->no_rekening) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1">Saldo Awal (Rp)</label>
                            <input type="number" name="saldo_awal" value="{{ old('saldo_awal', $client->saldo_awal) }}" class="w-full bg-zinc-800 border-zinc-600 px-4 py-2 rounded text-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-700">
                        <button type="button" onclick="toggleModal('editClientModal')" class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white font-bold rounded text-sm transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded shadow text-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; 
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; 
            }
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editClientModal');
            if (event.target == modal) {
                toggleModal('editClientModal');
            }
        }

        function switchTab(tabName) {
            // 1. Sembunyikan Semua Section
            const sections = ['history', 'recap', 'sales', 'support'];
            sections.forEach(sec => {
                const el = document.getElementById('section-' + sec);
                if(el) el.classList.add('hidden');
            });

            // 2. Reset Style Semua Tombol (Kembali ke Tampilan Default/Mati)
            // Style Default untuk Tombol Monitoring (Kiri)
            const defaultStyleLeft = "px-6 py-2 rounded-md text-sm font-bold text-zinc-500 hover:text-zinc-300 transition-all flex-1 md:flex-none";
            document.getElementById('btn-history').className = defaultStyleLeft;
            document.getElementById('btn-recap').className   = defaultStyleLeft;

            // Style Default untuk Tombol Input (Kanan)
            const defaultStyleRight = "px-5 py-2 rounded-md text-sm font-bold text-zinc-500 hover:text-white transition-all flex-1 md:flex-none";
            document.getElementById('btn-sales').className   = defaultStyleRight.replace('hover:text-white', 'hover:text-emerald-400');
            document.getElementById('btn-support').className = defaultStyleRight.replace('hover:text-white', 'hover:text-red-400');

            // 3. Aktifkan Section yang Dipilih
            const targetSection = document.getElementById('section-' + tabName);
            if(targetSection) targetSection.classList.remove('hidden');

            // 4. Berikan Style Aktif pada Tombol yang Dipilih
            const activeBtn = document.getElementById('btn-' + tabName);
            
            if (tabName === 'history' || tabName === 'recap') {
                // Style Aktif Kelompok Kiri (Amber)
                activeBtn.className = "px-6 py-2 rounded-md text-sm font-bold bg-zinc-700 text-amber-500 shadow-sm transition-all flex-1 md:flex-none";
            } else if (tabName === 'sales') {
                // Style Aktif Sales (Emerald)
                activeBtn.className = "px-5 py-2 rounded-md text-sm font-bold bg-emerald-900/30 text-emerald-400 border border-emerald-800 shadow-sm transition-all flex-1 md:flex-none";
            } else if (tabName === 'support') {
                // Style Aktif Support (Red)
                activeBtn.className = "px-5 py-2 rounded-md text-sm font-bold bg-red-900/30 text-red-400 border border-red-800 shadow-sm transition-all flex-1 md:flex-none";
            }
        }
        
        // Default Tab: History agar langsung lihat data
        document.addEventListener("DOMContentLoaded", () => switchTab('history'));
    </script>
</x-layout-admin>