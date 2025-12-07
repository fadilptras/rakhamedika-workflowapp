<x-layout-users :title="'Detail Klien & Sales'">

    {{-- Container Utama Responsif --}}
    <div class="w-full max-w-7xl mx-auto px-0 sm:px-0 lg:px-0 py-0">
        
        {{-- Tombol Kembali --}}
        <div class="mb-6">
            <a href="{{ route('crm.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Data Sales
            </a>
        </div>

        {{-- SECTION 1: KARTU INFORMASI KLIEN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                
                {{-- Kiri: Identitas --}}
                <div class="flex-grow w-full">
                    <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded mb-2 uppercase tracking-wide">
                        {{ $client->nama_perusahaan }}
                    </span>
                    {{-- Judul responsif --}}
                    <h2 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-4 break-words">
                        {{ $client->nama_user }}
                    </h2>
                    
                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs md:text-sm font-medium bg-gray-100 text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i> {{ $client->area ?? '-' }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs md:text-sm font-medium bg-gray-100 text-gray-600">
                            <i class="fas fa-user-tie mr-2 text-gray-400"></i> PIC: {{ $client->pic ?? '-' }}
                        </span>
                    </div>

                    <div class="mt-4 text-sm text-gray-500 space-y-1">
                        @if($client->no_telpon)
                            <div class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center"></i> {{ $client->no_telpon }}</div>
                        @endif
                        @if($client->email)
                            <div class="flex items-center gap-2 break-all"><i class="fas fa-envelope w-4 text-center"></i> {{ $client->email }}</div>
                        @endif
                    </div>
                </div>

                {{-- Kanan: Total Sales & Aksi --}}
                <div class="w-full md:w-auto text-left md:text-right flex flex-col md:items-end justify-between h-full gap-6 border-t md:border-t-0 border-gray-100 pt-4 md:pt-0">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Sales Contribution</p>
                        <div class="flex items-baseline md:justify-end">
                            <span class="text-gray-400 text-lg font-bold mr-1">Rp</span>
                            <span class="text-3xl md:text-4xl font-extrabold text-emerald-500">
                                {{ number_format($client->total_kontribusi, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if($client->user_id == Auth::id())
                    <div>
                        <form action="{{ route('crm.client.destroy', $client->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus klien ini akan menghapus SEMUA riwayat transaksi penjualan mereka secara permanen. Lanjutkan?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold flex items-center transition-colors">
                                <i class="fas fa-trash-alt mr-2"></i> Hapus Klien
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- SECTION 2: MENU NAVIGASI (GRID RESPONSIVE) --}}
        <div class="mb-8">
            <div class="bg-gray-100 p-1.5 rounded-xl grid grid-cols-2 md:inline-flex gap-1 shadow-inner">
                
                {{-- 1. Tombol Sales --}}
                <button id="btn-sales" onclick="switchTab('sales')"
                    class="px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none bg-white text-blue-700 shadow-sm">
                    <i class="fas fa-plus-circle"></i> Sales (In)
                </button>
                
                {{-- 2. Tombol Support --}}
                <button id="btn-support" onclick="switchTab('support')"
                    class="px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none text-gray-500 hover:text-gray-700">
                    <i class="fas fa-hand-holding-usd"></i> Usage (Out)
                </button>

                {{-- 3. Tombol History --}}
                <button id="btn-history" onclick="switchTab('history')"
                    class="px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none text-gray-500 hover:text-gray-700">
                    <i class="fas fa-history"></i> Riwayat
                </button>

                {{-- 4. Tombol Rekap --}}
                <button id="btn-recap" onclick="switchTab('recap')"
                    class="px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none text-gray-500 hover:text-gray-700">
                    <i class="fas fa-calendar-check"></i> Rekap Sales
                </button>
            </div>
        </div>

        {{-- SECTION 3: CONTENT CONTAINER --}}
        
        {{-- A. FORM SALES --}}
        <div id="section-sales" class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                <h3 class="font-bold text-blue-900 text-lg flex items-center">
                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                        <i class="fas fa-plus"></i>
                    </span>
                    Input Penjualan
                </h3>
            </div>
            <div class="p-6 md:p-8">
                <form action="{{ route('crm.interaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_interaksi" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk / Layanan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_produk" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5" placeholder="Contoh: Kassa Lipat" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Catatan Tambahan</label>
                                <textarea name="catatan" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5"></textarea>
                            </div>
                        </div>
                        <div class="space-y-5 bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nilai Sales (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="nilai_sales" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-10 px-4 py-2.5 font-mono text-lg" placeholder="0" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Komisi (%) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" name="komisi" step="0.1" max="100" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 font-mono" placeholder="10" required>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                      <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform active:scale-95">
                                    <i class="fas fa-save mr-2"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- B. FORM SUPPORT --}}
        <div id="section-support" class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-bold text-red-900 text-lg flex items-center">
                    <span class="w-8 h-8 bg-red-600 text-white rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    Pengeluaran Support
                </h3>
            </div>
            <div class="p-6 md:p-8">
                <form action="{{ route('crm.interaction.support') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_interaksi" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Keperluan Support <span class="text-red-500">*</span></label>
                                <input type="text" name="keperluan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2.5" placeholder="Contoh: Transport" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Catatan Detail</label>
                                <textarea name="catatan" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2.5"></textarea>
                            </div>
                        </div>
                        <div class="space-y-5 bg-red-50 p-6 rounded-xl border border-red-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nominal Keluar (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="nominal" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 pl-10 px-4 py-2.5 font-mono text-lg" placeholder="0" required>
                                </div>
                                <p class="text-xs text-red-500 mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> Mengurangi saldo kontribusi</p>
                            </div>
                            <div class="pt-10">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform active:scale-95">
                                    <i class="fas fa-minus-circle mr-2"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- C. TABLE RIWAYAT (TAB KE-3) [UPDATED] --}}
        <div id="section-history" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <i class="fas fa-history text-gray-400"></i> Riwayat Transaksi
                </h3>
                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full border border-gray-200">
                    {{ $interactions->total() }} Record
                </span>
            </div>
            
            {{-- Wrapper Scroll Horizontal untuk Mobile --}}
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm text-left min-w-[800px]"> {{-- Lebarkan min-w agar kolom tidak sempit --}}
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-4">Tanggal</th>
                            <th class="px-4 py-4">Keterangan</th>
                            <th class="px-4 py-4 text-right">Nilai Sales</th>
                            <th class="px-4 py-4 text-center">Komisi (%)</th>
                            <th class="px-4 py-4">Catatan</th>
                            <th class="px-4 py-4 text-right">Saldo (Net)</th>
                            <th class="px-4 py-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($interactions as $item)
                        <tr class="{{ $item->jenis_transaksi == 'OUT' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-blue-50' }} transition duration-150">
                            
                            {{-- 1. Tanggal --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($item->tanggal_interaksi)->format('d M Y') }}</div>
                            </td>

                            {{-- 2. Keterangan --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-bold {{ $item->jenis_transaksi == 'OUT' ? 'text-red-800' : 'text-blue-900' }} text-base">
                                    {{ $item->nama_produk }}
                                </div>
                            </td>

                            {{-- 3. Nilai Sales (Hanya muncul jika Transaksi IN) --}}
                            <td class="px-4 py-4 text-right whitespace-nowrap font-mono text-gray-600">
                                @if($item->jenis_transaksi == 'IN')
                                    Rp {{ number_format($item->nilai_sales ?? ($item->nilai_kontribusi / ($item->komisi/100 > 0 ? $item->komisi/100 : 0.1)), 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>

                            {{-- 4. Komisi (Hanya muncul jika Transaksi IN) --}}
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($item->jenis_transaksi == 'IN')
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                        {{ $item->komisi ?? '10' }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- 5. Catatan --}}
                            <td class="px-4 py-4 text-gray-500 italic max-w-xs truncate">{{ $item->catatan ?? '-' }}</td>

                            {{-- 6. Saldo (Net) --}}
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                @if($item->jenis_transaksi == 'OUT')
                                    <span class="font-mono font-bold text-red-600 text-base">( {{ number_format($item->nilai_kontribusi, 0, ',', '.') }} )</span>
                                @else
                                    <span class="font-mono font-bold text-green-600 text-base">+ {{ number_format($item->nilai_kontribusi, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            {{-- 7. Aksi --}}
                            <td class="px-4 py-4 text-center">
                                <form action="{{ route('crm.interaction.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-red-600 hover:text-white hover:border-red-600 transition flex items-center justify-center shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">Belum ada riwayat transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($interactions->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $interactions->links() }}
            </div>
            @endif
        </div>

        {{-- D. TABLE REKAP TAHUNAN (TAB KE-4) --}}
        <div id="section-recap" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-600"></i> Rekap {{ $year }}
                </h3>
                
                {{-- Filter Tahun --}}
                <form action="{{ route('crm.show', $client->id) }}" method="GET">
                    <select name="year" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            {{-- Wrapper Scroll Horizontal untuk Mobile --}}
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm text-left min-w-[800px]">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Bulan</th>
                            <th class="px-4 py-3 text-center text-gray-500">% KS</th>
                            <th class="px-4 py-3 text-right text-emerald-600">Sales (In)</th>
                            <th class="px-4 py-3 text-right text-red-600">Usage (Out)</th>
                            <th class="px-4 py-3 text-right text-blue-700 bg-blue-50">Net Value</th>
                            <th class="px-4 py-3 text-right text-gray-800">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($recap as $r)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 font-bold text-gray-700 whitespace-nowrap">{{ $r['month_name'] }}</td>
                            
                            {{-- % Komisi --}}
                            <td class="px-4 py-3 text-center font-mono text-gray-500 text-xs">
                                @if($r['komisi'] !== '-')
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">{{ $r['komisi'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            
                            {{-- Pemasukan --}}
                            <td class="px-4 py-3 text-right font-mono text-emerald-600 whitespace-nowrap">
                                {{ $r['in'] > 0 ? number_format($r['in'], 0, ',', '.') : '-' }}
                            </td>
                            
                            {{-- Pengeluaran --}}
                            <td class="px-4 py-3 text-right font-mono text-red-600 whitespace-nowrap">
                                {{ $r['out'] > 0 ? number_format($r['out'], 0, ',', '.') : '-' }}
                            </td>
                            
                            {{-- Net --}}
                            <td class="px-4 py-3 text-right font-mono font-bold bg-blue-50 whitespace-nowrap {{ $r['net'] < 0 ? 'text-red-600' : 'text-blue-800' }}">
                                {{ number_format($r['net'], 0, ',', '.') }}
                            </td>

                            {{-- Saldo --}}
                            <td class="px-4 py-3 text-right font-mono font-bold text-gray-800 whitespace-nowrap">
                                {{ number_format($r['saldo'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold text-gray-800 border-t-2 border-gray-200">
                        <tr>
                            <td class="px-4 py-4 uppercase tracking-wider">Total</td>
                            <td class="px-4 py-4 text-center">-</td> 
                            <td class="px-4 py-4 text-right text-emerald-700 whitespace-nowrap">{{ number_format($yearlyTotals['in'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-red-700 whitespace-nowrap">{{ number_format($yearlyTotals['out'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-blue-900 bg-blue-100 whitespace-nowrap">{{ number_format($yearlyTotals['net'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        function switchTab(tabName) {
            const sections = {
                'sales': document.getElementById('section-sales'),
                'support': document.getElementById('section-support'),
                'history': document.getElementById('section-history'),
                'recap': document.getElementById('section-recap')
            };

            const buttons = {
                'sales': document.getElementById('btn-sales'),
                'support': document.getElementById('btn-support'),
                'history': document.getElementById('btn-history'),
                'recap': document.getElementById('btn-recap')
            };

            // 1. Reset Semua
            for (const key in sections) {
                if (sections[key]) sections[key].classList.add('hidden');
                
                if (buttons[key]) {
                    // Reset class ke default (gray text)
                    buttons[key].className = "px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none text-gray-500 hover:text-gray-700";
                }
            }

            // 2. Aktifkan
            if (sections[tabName]) sections[tabName].classList.remove('hidden');

            if (buttons[tabName]) {
                let activeColor = 'text-blue-700'; 
                if(tabName === 'support') activeColor = 'text-red-700';
                if(tabName === 'history') activeColor = 'text-gray-800';
                if(tabName === 'recap')   activeColor = 'text-blue-900';

                // Tambahkan class aktif (bg-white, shadow, colored text)
                buttons[tabName].className = `px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none bg-white shadow-sm ${activeColor}`;
            }

            // Simpan state tab di localStorage
            localStorage.setItem('activeTab', tabName);
        }

        // Auto restore tab saat reload
        document.addEventListener("DOMContentLoaded", function() {
            const lastTab = localStorage.getItem('activeTab') || 'sales';
            switchTab(lastTab);
        });
    </script>
</x-layout-users>