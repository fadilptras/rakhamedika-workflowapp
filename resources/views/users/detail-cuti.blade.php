<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    {{-- Font Awesome untuk ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bg-gray-100 font-sans bg-gradient-to-br from-sky-50 to-blue-100 p-0 md:p-0 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm">
                
                {{-- Header Halaman --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-gray-200 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Pengajuan Cuti</h1>
                        <p class="text-sm text-gray-500 mt-1">Diajukan pada {{ $cuti->created_at->format('d F Y, H:i') }}</p>
                    </div>
                    <a href="{{ route('cuti') }}" class="mt-4 sm:mt-0 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Riwayat
                    </a>
                </div>

                <div class="space-y-6">
                    {{-- Status Pengajuan --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Status Pengajuan</h3>
                        @if ($cuti->status == 'diajukan')
                            <p class="flex items-center text-base font-medium text-yellow-600">
                                <i class="fas fa-clock fa-fw mr-3"></i>
                                <span>
                                Menunggu persetujuan dari: 
                                <span class="font-bold ml-1">{{ $approver?->name ?? 'Atasan belum ditentukan' }}</span>
                            </span>
                            </p>
                        @elseif($cuti->status == 'disetujui')
                            <p class="flex items-center text-base font-medium text-green-600">
                                <i class="fas fa-check-circle fa-fw mr-3"></i>
                                Pengajuan Anda telah disetujui.
                            </p>
                        @elseif($cuti->status == 'dibatalkan')
                             <p class="flex items-center text-base font-medium text-gray-600">
                                <i class="fas fa-ban fa-fw mr-3"></i>
                                Pengajuan ini telah Anda batalkan.
                            </p>
                        @else
                            <p class="flex items-center text-base font-medium text-red-600">
                                <i class="fas fa-times-circle fa-fw mr-3"></i>
                                Pengajuan Anda ditolak.
                            </p>
                        @endif
                    </div>

                    {{-- Informasi Detail --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                        {{-- Detail Cuti --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Detail Cuti</h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Jenis Cuti</dt>
                                    <dd class="font-semibold text-gray-900">{{ ucfirst($cuti->jenis_cuti) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Tanggal Mulai</dt>
                                    <dd class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d F Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Tanggal Selesai</dt>
                                    <dd class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d F Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Total Durasi</dt>
                                    <dd class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari</dd>
                                </div>
                            </dl>
                        </div>
                        {{-- Detail Pemohon --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Diajukan Oleh</h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Nama</dt>
                                    <dd class="font-semibold text-gray-900">{{ $cuti->user->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Divisi</dt>
                                    <dd class="font-semibold text-gray-900">{{ $cuti->user->divisi ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Jabatan</dt>
                                    <dd class="font-semibold text-gray-900">{{ $cuti->user->jabatan }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Alasan --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Alasan Pengajuan</h3>
                        <div class="mt-2 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border">
                            <p>{{ $cuti->alasan }}</p>
                        </div>
                    </div>
                    
                    {{-- Catatan Approval --}}
                    @if($cuti->catatan_approval)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Catatan dari Approver</h3>
                            {{-- Kotak catatan yang sudah dirapikan --}}
                            <div class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-6 text-sm text-gray-700">
                                <p>{{ $cuti->catatan_approval }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Tombol Aksi untuk Approver --}}
                    @can('update', $cuti)
                        @if ($cuti->status == 'diajukan')
                            <div class="pt-6 border-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tindakan Persetujuan</h3>
                                <form action="{{ route('cuti.updateStatus', $cuti) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                            {{-- INI BAGIAN YANG DIPERBAIKI --}}
                                            <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <button type="submit" name="status" value="disetujui" class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                                                <i class="fas fa-check mr-2"></i>Setujui
                                            </button>
                                            <button type="submit" name="status" value="ditolak" class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                                                <i class="fas fa-times mr-2"></i>Tolak
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endcan

                    {{-- Tombol Aksi untuk Karyawan (Pembatalan) --}}
                    @can('cancel', $cuti)
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tindakan Lain</h3>
                        <form action="{{ route('cuti.cancel', $cuti) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                                <i class="fas fa-ban mr-2"></i>Batalkan Pengajuan Cuti
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-layout-users>