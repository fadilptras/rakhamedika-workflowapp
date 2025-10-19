<x-layout-admin>
    <x-slot:title>Kelola Pengajuan Dana</x-slot:title>

    <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700">
        <div class="p-6 border-b border-zinc-700">
            <h2 class="text-xl font-bold text-white">Rekap Pengajuan Dana Karyawan</h2>
            <p class="text-sm text-zinc-400 mt-1">Pantau semua riwayat pengajuan dana yang masuk.</p>
        </div>
        
        {{-- FORM FILTER --}}
        {{-- FORM FILTER --}}
        <div class="p-6">
            <form method="GET" action="{{ route('admin.pengajuan_dana.index') }}">
                {{-- Baris Pertama: Semua Form Input --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    
                    <div class="lg:col-span-2">
                        <label for="karyawan_id" class="block text-sm font-medium text-zinc-400 mb-1">Nama Karyawan</label>
                        <select name="karyawan_id" id="karyawan_id" class="w-full bg-zinc-700 border-zinc-600 rounded-lg text-white px-3 py-2">
                            <option value="">Semua Karyawan</option>
                            @foreach ($karyawanList as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ request('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="divisi" class="block text-sm font-medium text-zinc-400 mb-1">Divisi</label>
                        <select name="divisi" id="divisi" class="w-full bg-zinc-700 border-zinc-600 rounded-lg text-white px-3 py-2">
                            <option value="">Semua Divisi</option>
                            @foreach ($divisiList as $item)
                                <option value="{{ $item->divisi }}" {{ request('divisi') == $item->divisi ? 'selected' : '' }}>
                                    {{ $item->divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-zinc-400 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full bg-zinc-700 border-zinc-600 rounded-lg text-white px-3 py-2">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-zinc-400 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full bg-zinc-700 border-zinc-600 rounded-lg text-white px-3 py-2">
                    </div>
                </div>

                {{-- Baris Kedua: Tombol --}}
                <div class="mt-4">
                    <div class="flex items-center gap-2">
                        {{-- Class disamakan agar tinggi tombol konsisten --}}
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2 px-4 rounded-lg">Filter</button>
                        <a href="{{ route('admin.pengajuan_dana.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white text-sm font-semibold py-2 px-4 rounded-lg text-center">Reset</a>
                        <button type="submit" formaction="{{ route('admin.pengajuan_dana.downloadRekapPdf') }}" formmethod="GET" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center justify-center gap-2" title="Download Rekap PDF">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <span>Cetak Rekap Pengajuan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-zinc-300">
                <thead class="text-xs text-zinc-400 uppercase bg-zinc-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Nama Karyawan</th>
                        <th scope="col" class="px-6 py-3">Judul Pengajuan</th>
                        <th scope="col" class="px-6 py-3">Total Dana</th>
                        <th scope="col" class="px-6 py-3">Status Final</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuanDana as $pengajuan)
                    <tr class="bg-zinc-800 border-b border-zinc-700 hover:bg-zinc-700/50">
                        <td class="px-6 py-4">{{ $pengajuan->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-medium text-white">{{ $pengajuan->user->name }}</td>
                        <td class="px-6 py-4">{{ $pengajuan->judul_pengajuan }}</td>
                        <td class="px-6 py-4 font-mono">Rp {{ number_format($pengajuan->total_dana, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if ($pengajuan->status == 'disetujui')
                                <span class="font-bold bg-emerald-500/10 text-emerald-400 px-2 py-1 rounded-full text-xs">Disetujui</span>
                            @elseif ($pengajuan->status == 'dibatalkan')
                                <span class="font-bold bg-zinc-500/10 text-zinc-400 px-2 py-1 rounded-full text-xs">Dibatalkan</span>
                            @elseif ($pengajuan->status == 'ditolak')
                                <span class="font-bold bg-red-500/10 text-red-400 px-2 py-1 rounded-full text-xs">Ditolak</span>
                            @elseif ($pengajuan->status == 'diproses')
                                <span class="font-bold bg-blue-500/10 text-blue-400 px-2 py-1 rounded-full text-xs">Diproses</span>
                            @else
                                <span class="font-bold bg-yellow-500/10 text-yellow-400 px-2 py-1 rounded-full text-xs">Diajukan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-4">
                                {{-- Tombol Lihat Detail (Ikon diganti) --}}
                                <a href="{{ route('admin.pengajuan_dana.show', $pengajuan) }}" class="text-zinc-400 hover:text-amber-400" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2-2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                {{-- Tombol Download PDF --}}
                                <a href="{{ route('admin.pengajuan_dana.downloadPdf', $pengajuan) }}" class="text-zinc-400 hover:text-blue-400" title="Download PDF">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-500">
                            Tidak ada data untuk ditampilkan. Coba sesuaikan filter Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout-admin>