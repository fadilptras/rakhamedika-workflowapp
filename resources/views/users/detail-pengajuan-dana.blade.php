<x-layout-users>
    <x-slot:title>Detail Pengajuan Dana</x-slot:title>
    
    <div class="flex-1 overflow-auto">
        <div class="container mx-auto p-4 md:p-6">
            <x-back-button href="{{ route('pengajuan_dana.index') }}">Kembali ke Rekap Pengajuan</x-back-button>
            <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Detail Pengajuan Dana</h2>
                
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                {{-- Bagian Detail Pengajuan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8">
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

                {{-- Bagian Rincian Dana --}}
                <div class="mt-6">
                    <label class="block text-gray-700 font-medium mb-2">Rincian Penggunaan Dana</label>
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

                {{-- Bagian Lampiran --}}
                @if ($pengajuanDana->lampiran)
                    <div class="mt-6">
                        <label class="block text-gray-700 font-medium mb-2">Lampiran Dokumen</label>
                        <div class="p-4 bg-gray-100 rounded-lg text-center">
                            <a href="{{ asset('storage/' . $pengajuanDana->lampiran) }}" target="_blank" class="mt-2 inline-block text-blue-600 hover:underline">
                                Lihat Berkas Pengajuan
                            </a>
                        </div>
                    </div>
                @endif
                
                <hr class="my-8">

                {{-- Tampilan Timeline Status --}}
                <div class="bg-white rounded-lg p-4 md:p-6 mt-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pengajuan</h2>
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-x-4 md:space-y-0">
                        <div class="flex flex-col items-center w-full md:w-1/3 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow-md">1</div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Diajukan</p>
                            <p class="text-xs text-gray-500">{{ $pengajuanDana->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                        </div>
                        <div class="hidden md:block h-1 w-full {{ $pengajuanDana->status_atasan != 'menunggu' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        <div class="flex flex-col items-center w-full md:w-1/3 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $pengajuanDana->status_atasan == 'disetujui' ? 'bg-blue-600' : ($pengajuanDana->status_atasan == 'ditolak' ? 'bg-red-500' : 'bg-gray-300') }} text-white font-bold shadow-md">2</div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Kepala Divisi</p>
                            @if ($pengajuanDana->status_atasan == 'disetujui')
                                <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                            @elseif ($pengajuanDana->status_atasan == 'ditolak')
                                <p class="text-xs text-red-600 font-medium">❌ Ditolak</p>
                            @else
                                <p class="text-xs text-gray-500">Menunggu...</p>
                            @endif
                        </div>
                        <div class="hidden md:block h-1 w-full {{ $pengajuanDana->status_finance != 'menunggu' && $pengajuanDana->status_atasan == 'disetujui' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        <div class="flex flex-col items-center w-full md:w-1/3 text-center">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full {{ $pengajuanDana->status_finance == 'disetujui' ? 'bg-blue-600' : ($pengajuanDana->status_finance == 'ditolak' ? 'bg-red-500' : 'bg-gray-300') }} text-white font-bold shadow-md">3</div>
                            <p class="mt-2 text-sm font-semibold text-gray-800">Disetujui Kepala Finance</p>
                            @if ($pengajuanDana->status_finance == 'disetujui')
                                <p class="text-xs text-green-600 font-medium">✔ Selesai</p>
                            @elseif ($pengajuanDana->status_finance == 'ditolak')
                                <p class="text-xs text-red-600 font-medium">❌ Ditolak</p>
                            @else
                                <p class="text-xs text-gray-500">Menunggu...</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tampilan Catatan --}}
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
                                    <td class="px-4 py-2">Kepala Divisi</td>
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
                                    <td class="px-4 py-2">Kepala Finance</td>
                                    <td class="px-4 py-2">{{ $pengajuanDana->catatan_finance ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                            @if ($pengajuanDana->status_finance == 'menunggu' || $pengajuanDana->status_finance == null) bg-gray-100 text-gray-600
                                            @elseif ($pengajuanDana->status_finance == 'disetujui') bg-green-100 text-green-600
                                            @else bg-red-100 text-red-600 @endif">
                                            {{ ucfirst($pengajuanDana->status_finance ?? 'menunggu') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Logika untuk Menampilkan Tombol Persetujuan --}}
                @can('approve', $pengajuanDana)
                    <div class="bg-white rounded-lg shadow p-4 md:p-6 mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tindakan Persetujuan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <form action="{{ route('pengajuan_dana.approve', $pengajuanDana->id) }}" method="POST">
                                @csrf
                                <label for="catatan-setuju" class="block text-gray-700 font-medium mb-2">Catatan (Opsional)</label>
                                <textarea id="catatan-setuju" name="catatan_persetujuan" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Tambahkan catatan persetujuan..."></textarea>
                                <button type="submit" class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                                    Setujui Pengajuan
                                </button>
                            </form>
                            
                            <form action="{{ route('pengajuan_dana.reject', $pengajuanDana->id) }}" method="POST">
                                @csrf
                                <label for="catatan-tolak" class="block text-gray-700 font-medium mb-2">Catatan Penolakan (Wajib)</label>
                                <textarea id="catatan-tolak" name="catatan_penolakan" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
                                <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                                    Tolak Pengajuan
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-layout-users>