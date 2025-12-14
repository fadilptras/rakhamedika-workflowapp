<x-layout-admin :title="'Monitoring Sales'">
    
    {{-- Header & Filter (TIDAK BERUBAH) --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Monitoring Sales & Klien</h1>
            <p class="text-zinc-400 text-sm">Rekapitulasi seluruh aktivitas sales tim Rakha Medika.</p>
        </div>
        
        <div class="w-full md:w-auto">
            <form action="{{ route('admin.crm.index') }}" method="GET" class="flex items-center gap-2">
                 <select name="user_id" onchange="this.form.submit()" class="bg-zinc-900 border border-zinc-700 text-zinc-300 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5">
                    <option value="">-- Semua Sales --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $filterUser == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card 1 --}}
        <div class="bg-zinc-800 p-6 rounded-xl shadow-lg border border-zinc-700/50 flex flex-col justify-between h-full">
            <div>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Klien Aktif</p>
                <h3 class="text-3xl font-extrabold text-white">{{ $clients->total() }} <span class="text-sm font-medium text-zinc-500">Perusahaan</span></h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-amber-500 font-bold">
                <i class="fas fa-users mr-1"></i> Data Terupdate
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-zinc-800 p-6 rounded-xl shadow-lg border border-zinc-700/50 flex flex-col justify-between h-full">
            <div>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Nilai Sales (Gross)</p>
                <h3 class="text-3xl font-extrabold text-emerald-500 truncate">
                    <span class="text-lg text-zinc-500 mr-1">Rp</span>{{ number_format($totalOmset, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-emerald-500 font-bold">
                <i class="fas fa-chart-line mr-1"></i> Akumulasi Transaksi
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-zinc-800 p-6 rounded-xl shadow-lg border border-zinc-700/50 flex flex-col justify-between h-full">
            <div>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Saldo (Net)</p>
                <h3 class="text-3xl font-extrabold text-blue-400 truncate">
                    <span class="text-lg text-zinc-500 mr-1">Rp</span>{{ number_format($totalNet, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-blue-400 font-bold">
                <i class="fas fa-wallet mr-1"></i> Pendapatan Bersih - Usage
            </div>
        </div>
    </div>

    {{-- Tabel Monitoring --}}
    <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700/50 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-700 bg-zinc-800/50 flex justify-between items-center">
            <h3 class="font-bold text-zinc-200">Daftar Klien & Sales</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap text-zinc-300">
                <thead class="bg-zinc-900/50 text-zinc-400 uppercase text-xs font-bold tracking-wider border-b border-zinc-700">
                    <tr>
                        <th class="px-6 py-4">Nama Perusahaan / Klien</th>
                        <th class="px-6 py-4">Sales (PIC)</th>
                        <th class="px-6 py-4 text-right">Nilai Sales (Gross)</th>
                        <th class="px-6 py-4 text-right">Saldo (Net)</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse($clients as $client)
                    @php
                        // --- PERBAIKAN LOGIKA DISINI ---
                        $row_sales = 0;
                        $row_net_val = 0;
                        $row_usage = 0;

                        foreach($client->interactions as $i) {
                            if($i->jenis_transaksi == 'IN') {
                                // 1. Tentukan Gross (Ambil nilai_sales jika ada, jika 0 ambil nilai_kontribusi)
                                $gross = ($i->nilai_sales > 0) ? $i->nilai_sales : $i->nilai_kontribusi;
                                
                                $row_sales += $gross;

                                // 2. Parse Rate
                                $r = $i->komisi ?? 0;
                                if(!$r && preg_match('/\[Rate:([\d\.]+)\]/', $i->catatan, $m)) $r = floatval($m[1]);
                                
                                // 3. Hitung Value (Pakai variable $gross yang sudah benar)
                                $row_net_val += $gross * ($r/100);
                            } else {
                                $row_usage += $i->nilai_kontribusi;
                            }
                        }
                        $row_saldo = $row_net_val - $row_usage;
                    @endphp

                    <tr class="hover:bg-zinc-700/30 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="font-bold text-white text-base">{{ $client->nama_user }}</div>
                            <div class="text-xs text-zinc-400 font-medium">{{ $client->nama_perusahaan }}</div>
                            <div class="text-xs text-zinc-500 mt-1 flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 w-3 text-center"></i> {{ $client->area ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-700 flex items-center justify-center text-amber-500 font-bold text-xs border border-zinc-600">
                                    {{ substr($client->user->name ?? '?', 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-zinc-300 text-xs">{{ $client->user->name ?? 'Deleted User' }}</div>
                                    <div class="text-[10px] text-zinc-500 uppercase tracking-wide">Sales Representative</div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Nilai Sales Gross --}}
                        <td class="px-6 py-4 text-right font-mono text-emerald-500 font-semibold">
                            Rp {{ number_format($row_sales, 0, ',', '.') }}
                        </td>

                        {{-- Saldo Net --}}
                        <td class="px-6 py-4 text-right font-mono font-bold bg-blue-900/10 rounded {{ $row_saldo < 0 ? 'text-red-400' : 'text-blue-400' }}">
                            Rp {{ number_format($row_saldo, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.crm.show', $client->id) }}" class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-900 font-medium rounded-lg text-xs px-3 py-2 transition shadow-sm">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-zinc-500 bg-zinc-800/50">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl mb-3 text-zinc-600"></i>
                                <p>Belum ada data klien yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($clients->hasPages())
        <div class="bg-zinc-800 px-6 py-4 border-t border-zinc-700">
            {{ $clients->withQueryString()->links() }} 
        </div>
        @endif
    </div>

</x-layout-admin>