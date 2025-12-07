<x-layout-users :title="'Matrix Sales Tahunan (' . $year . ')'">

    {{-- CONTAINER UTAMA: Full Width & Height biar kayak aplikasi --}}
    <div class="flex flex-col h-[calc(100vh-100px)]">

        {{-- BAGIAN ATAS: Judul & Filter (Dibuat Compact) --}}
        <div class="flex justify-between items-center px-6 py-4 bg-white border-b border-gray-300 shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('crm.index') }}" class="text-gray-500 hover:text-blue-600 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-table text-emerald-600"></i> Laporan Sales Tahunan
                    </h1>
                </div>
            </div>

            {{-- Filter Tahun (Model Pagination Excel) --}}
            <div class="flex items-center bg-white border border-gray-300 rounded shadow-sm">
                <a href="{{ route('crm.matrix', ['year' => $year - 1]) }}" class="px-3 py-1.5 hover:bg-gray-100 text-gray-600 border-r border-gray-300">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <span class="px-4 py-1.5 font-mono font-bold text-gray-800 bg-gray-50">
                    {{ $year }}
                </span>
                <a href="{{ route('crm.matrix', ['year' => $year + 1]) }}" class="px-3 py-1.5 hover:bg-gray-100 text-gray-600 border-l border-gray-300">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        {{-- AREA TABEL (Excel View) --}}
        {{-- overflow-auto penting agar scrollbar muncul di sini, bukan di body --}}
        <div class="flex-1 overflow-auto bg-gray-100 relative w-full">
            <table class="w-full border-collapse text-sm text-left bg-white">
                
                {{-- HEADER (Sticky Top) --}}
                <thead class="bg-gray-100 text-gray-700 font-bold sticky top-0 z-30 shadow-sm">
                    <tr>
                        {{-- Pojok Kiri Atas (Sticky 2 Arah: Kiri & Atas) --}}
                        <th class="px-4 py-2 border border-gray-300 bg-gray-200 sticky left-0 z-40 w-64 min-w-[250px] text-xs uppercase tracking-wider">
                            Nama Klien / Perusahaan
                        </th>
                        
                        {{-- Kolom Bulan --}}
                        @foreach($months as $monthNum => $monthName)
                            <th class="px-2 py-2 border border-gray-300 text-center min-w-[100px] bg-gray-100 text-xs uppercase">
                                {{ substr($monthName, 0, 3) }}
                            </th>
                        @endforeach

                        {{-- Kolom Total (Sticky Kanan - Opsional, tapi kita taruh biasa dulu biar ringan) --}}
                        <th class="px-4 py-2 border border-gray-300 bg-emerald-50 text-emerald-800 text-right min-w-[120px] text-xs uppercase">
                            Total {{ $year }}
                        </th>
                    </tr>
                </thead>

                {{-- BODY DATA --}}
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse ($clients as $index => $client)
                        @php
                            $clientTotalYear = $client->interactions->sum('nilai_kontribusi');
                            // Zebra Striping: Genap/Ganjil
                            $rowClass = $index % 2 == 0 ? 'bg-white' : 'bg-gray-50';
                        @endphp
                        <tr class="{{ $rowClass }} hover:bg-blue-50 transition-colors duration-75 group">
                            
                            {{-- Nama Klien (Sticky Left) --}}
                            <td class="px-4 py-2 border border-gray-300 {{ $rowClass }} group-hover:bg-blue-50 sticky left-0 z-20 align-middle">
                                <div class="font-bold text-gray-800 text-sm truncate w-56" title="{{ $client->nama_user }}">
                                    {{ $client->nama_user }}
                                </div>
                                <div class="text-[10px] text-gray-500 truncate w-56" title="{{ $client->nama_perusahaan }}">
                                    {{ $client->nama_perusahaan }}
                                </div>
                            </td>

                            {{-- Data Bulanan --}}
                            @foreach($months as $monthNum => $monthName)
                                @php
                                    $monthlySales = $client->interactions->filter(function($i) use ($monthNum) {
                                        return \Carbon\Carbon::parse($i->tanggal_interaksi)->month == $monthNum;
                                    })->sum('nilai_kontribusi');
                                @endphp
                                
                                <td class="px-2 py-2 border border-gray-300 text-right font-mono text-xs align-middle {{ $monthlySales > 0 ? 'text-gray-900 font-medium' : 'text-gray-300' }}">
                                    {{ $monthlySales > 0 ? number_format($monthlySales, 0, ',', '.') : '-' }}
                                </td>
                            @endforeach

                            {{-- Total Tahunan Klien --}}
                            <td class="px-4 py-2 border border-gray-300 bg-emerald-50 text-right font-mono font-bold text-emerald-700 align-middle">
                                {{ number_format($clientTotalYear, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="p-8 text-center text-gray-400 bg-white">
                                <i class="fas fa-database mb-2 text-2xl"></i>
                                <p>Tidak ada data penjualan di tahun {{ $year }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- FOOTER GRAND TOTAL (Sticky Bottom) --}}
                <tfoot class="sticky bottom-0 z-30 shadow-[0_-2px_10px_rgba(0,0,0,0.1)]">
                    <tr class="bg-gray-800 text-white font-bold text-xs">
                        {{-- Label Grand Total (Sticky Left & Bottom) --}}
                        <td class="px-4 py-3 border border-gray-600 bg-gray-800 sticky left-0 z-40 text-right uppercase tracking-wider">
                            GRAND TOTAL
                        </td>
                        
                        {{-- Total Per Bulan --}}
                        @foreach($monthlyTotals as $total)
                            <td class="px-2 py-3 border border-gray-600 text-right font-mono">
                                {{ $total > 0 ? number_format($total, 0, ',', '.') : '-' }}
                            </td>
                        @endforeach

                        {{-- Grand Total Pojok Kanan Bawah --}}
                        <td class="px-4 py-3 border border-gray-600 bg-emerald-600 text-white text-right font-mono text-sm">
                            {{ number_format($grandTotalYear, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    {{-- CSS Tambahan Khusus Halaman Ini --}}
    @push('styles')
    <style>
        /* Mempercantik Scrollbar agar mirip Excel/App modern */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }
    </style>
    @endpush

</x-layout-users>