<x-layout-users>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>
    
    <div class="flex-1 overflow-auto">
        <div class="container mx-auto p-6">
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Detail Pengajuan Dana</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Nama Pemohon</label>
                        <p class="font-semibold text-gray-900">{{ $pengajuanDana->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Judul Pengajuan</label>
                        <p class="font-semibold text-gray-900">{{ $pengajuanDana->judul_pengajuan }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Divisi</label>
                        <p class="font-semibold text-gray-900">{{ $pengajuanDana->divisi }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Tanggal Pengajuan</label>
                        <p class="font-semibold text-gray-900">{{ $pengajuanDana->created_at->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Bank</label>
                        <p class="font-semibold text-gray-900">{{ $pengajuanDana->nama_bank }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">No. Rekening</label>
                        <p class="font-semibold text-gray-900">{{ $pengajuanDana->no_rekening }}</p>
                    </div>
                </div>

                <div class="md:col-span-2 mt-6">
                    <label class="block text-gray-700 font-medium mb-2">
                        Rincian Penggunaan Dana
                    </label>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 w-2/3">Deskripsi Pengeluaran</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 w-1/3">Dana yang Dibutuhkan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuanDana->rincian_dana as $rincian)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $rincian['deskripsi'] }}</td>
                                        <td class="px-4 py-2">Rp {{ number_format($rincian['jumlah'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 text-right">Total:</td>
                                    <td class="px-4 py-2 font-bold text-gray-800">
                                        Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if ($pengajuanDana->lampiran)
                    <div class="md:col-span-2 mt-6">
                        <label class="block text-gray-700 font-medium mb-2">Lampiran Dokumen</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            <a href="{{ asset('storage/' . $pengajuanDana->lampiran) }}" target="_blank" class="mt-2 inline-block text-blue-600 hover:underline">
                                Lihat Berkas Pengajuan
                            </a>
                        </div>
                    </div>
                @endif
                
                <hr class="my-8">

                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pengajuan</h2>
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <div class="flex flex-col items-center w-full md:w-1/4 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">
                                1
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Diajukan</p>
                            <p class="text-xs text-gray-500">{{ $pengajuanDana->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                        </div>

                        <div class="h-4 w-1 md:h-1 md:w-full {{ $pengajuanDana->status_atasan != 'menunggu' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>

                        <div class="flex flex-col items-center w-full md:w-1/4 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $pengajuanDana->status_atasan == 'disetujui' ? 'bg-blue-600' : 'bg-gray-300' }} text-white font-bold shadow-md">
                                2
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Atasan</p>
                            @if ($pengajuanDana->status_atasan == 'disetujui')
                                <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                            @elseif ($pengajuanDana->status_atasan == 'ditolak')
                                <p class="text-xs text-red-600 font-medium">❌ Ditolak</p>
                            @else
                                <p class="text-xs text-gray-500">Menunggu...</p>
                            @endif
                        </div>

                        <div class="h-4 w-1 md:h-1 md:w-full {{ $pengajuanDana->status_hrd != 'menunggu' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>

                        <div class="flex flex-col items-center w-full md:w-1/4 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $pengajuanDana->status_hrd == 'disetujui' ? 'bg-blue-600' : 'bg-gray-300' }} text-white font-bold shadow-md">
                                3
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui HRD</p>
                            @if ($pengajuanDana->status_hrd == 'disetujui')
                                <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                            @elseif ($pengajuanDana->status_hrd == 'ditolak')
                                <p class="text-xs text-red-600 font-medium">❌ Ditolak</p>
                            @else
                                <p class="text-xs text-gray-500">Menunggu...</p>
                            @endif
                        </div>

                        <div class="h-4 w-1 md:h-1 md:w-full {{ $pengajuanDana->status_direktur != 'menunggu' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>

                        <div class="flex flex-col items-center w-full md:w-1/4 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $pengajuanDana->status_direktur == 'disetujui' ? 'bg-blue-600' : 'bg-gray-300' }} text-white font-bold shadow-md">
                                4
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Direktur</p>
                            @if ($pengajuanDana->status_direktur == 'disetujui')
                                <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                            @elseif ($pengajuanDana->status_direktur == 'ditolak')
                                <p class="text-xs text-red-600 font-medium">❌ Ditolak</p>
                            @else
                                <p class="text-xs text-gray-500">Menunggu...</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Catatan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Dari</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Catatan</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t">
                                    <td class="px-4 py-2">Atasan</td>
                                    <td class="px-4 py-2">{{ $pengajuanDana->catatan_atasan ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                            @if ($pengajuanDana->status_atasan == 'menunggu') bg-gray-100 text-gray-600
                                            @elseif ($pengajuanDana->status_atasan == 'disetujui') bg-green-100 text-green-600
                                            @else bg-red-100 text-red-600 @endif">
                                            {{ ucfirst($pengajuanDana->status_atasan) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2">HRD</td>
                                    <td class="px-4 py-2">{{ $pengajuanDana->catatan_hrd ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                            @if ($pengajuanDana->status_hrd == 'menunggu') bg-gray-100 text-gray-600
                                            @elseif ($pengajuanDana->status_hrd == 'disetujui') bg-green-100 text-green-600
                                            @else bg-red-100 text-red-600 @endif">
                                            {{ ucfirst($pengajuanDana->status_hrd) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2">Direktur</td>
                                    <td class="px-4 py-2">{{ $pengajuanDana->catatan_direktur ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                            @if ($pengajuanDana->status_direktur == 'menunggu') bg-gray-100 text-gray-600
                                            @elseif ($pengajuanDana->status_direktur == 'disetujui') bg-green-100 text-green-600
                                            @else bg-red-100 text-red-600 @endif">
                                            {{ ucfirst($pengajuanDana->status_direktur) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Bukti Transfer & Invoice</h2>
                @php
                    $isApproved = ($pengajuanDana->status_atasan == 'disetujui' && $pengajuanDana->status_hrd == 'disetujui' && $pengajuanDana->status_direktur == 'disetujui');
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Surat Pengajuan</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            @if ($isApproved)
                                <p class="text-gray-500 italic">Pengajuan telah disetujui</p>
                                <a href="{{ asset('storage/' . $pengajuanDana->lampiran) }}" target="_blank" class="mt-2 inline-block text-blue-600 hover:underline">
                                    Lihat Berkas Pengajuan
                                </a>
                            @else
                                <p class="text-gray-500 italic">Menunggu semua persetujuan.</p>
                                <span class="mt-2 inline-block text-gray-400 cursor-not-allowed">
                                    Lihat Berkas Pengajuan
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Bukti Transfer</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            @if ($isApproved)
                                <p class="text-gray-500 italic">Transfer dana sudah dilakukan.</p>
                                <a href="#" class="mt-2 inline-block text-blue-600 hover:underline">
                                    Lihat Bukti Transfer
                                </a>
                            @else
                                <p class="text-gray-500 italic">Menunggu semua persetujuan.</p>
                                <span class="mt-2 inline-block text-gray-400 cursor-not-allowed">
                                    Lihat Bukti Transfer
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Invoice</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            @if ($isApproved)
                                <p class="text-gray-500 italic">Invoice telah dibuat dan siap diunduh.</p>
                                <a href="#" class="mt-2 inline-block text-blue-600 hover:underline">
                                    Lihat Invoice
                                </a>
                            @else
                                <p class="text-gray-500 italic">Menunggu semua persetujuan.</p>
                                <span class="mt-2 inline-block text-gray-400 cursor-not-allowed">
                                    Lihat Invoice
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout-users>