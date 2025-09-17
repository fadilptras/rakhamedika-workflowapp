<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    <div class="p-6">
        <x-back-button href="{{ route('admin.cuti.index') }}">Kembali ke Pengajuan Cuti</x-back-button>
        
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                <p class="font-bold">Terjadi Kesalahan</p>
                <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
                </ul>
            </div>
        @endif
        
        <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8 text-zinc-300">
            {{-- Header Halaman --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-zinc-700/50 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">Detail Pengajuan Cuti</h1>
                    <p class="text-sm text-zinc-400 mt-1">Diajukan pada {{ \Carbon\Carbon::parse($cuti->created_at)->format('d F Y, H:i') }}</p>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Status Pengajuan --}}
                <div class="bg-zinc-700 rounded-lg p-4 border border-zinc-600">
                    <h3 class="text-lg font-semibold text-white mb-2">Status Pengajuan</h3>
                    @php
                        $statusText = '';
                        $statusIcon = '';
                        $statusColor = '';
                        switch($cuti->status) {
                            case 'diajukan':
                                $statusText = 'Pengajuan cuti sedang dalam proses.';
                                $statusIcon = 'fas fa-clock';
                                $statusColor = 'text-yellow-400';
                                break;
                            case 'diterima':
                                $statusText = 'Pengajuan cuti telah diterima.';
                                $statusIcon = 'fas fa-check-circle';
                                $statusColor = 'text-green-400';
                                break;
                            case 'ditolak':
                                $statusText = 'Pengajuan cuti telah ditolak.';
                                $statusIcon = 'fas fa-times-circle';
                                $statusColor = 'text-red-400';
                                break;
                            default:
                                $statusText = 'Status tidak diketahui.';
                                $statusIcon = 'fas fa-question-circle';
                                $statusColor = 'text-zinc-400';
                                break;
                        }
                    @endphp
                    <p class="flex items-center text-base font-medium {{ $statusColor }}">
                        <i class="{{ $statusIcon }} fa-fw mr-3"></i>
                        {{ $statusText }}
                    </p>
                </div>
                
                {{-- Informasi Detail --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-zinc-700/50">
                    {{-- Detail Cuti --}}
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Detail Cuti</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Jenis Cuti</dt>
                                <dd class="font-semibold text-white capitalize">{{ $cuti->jenis_cuti }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Tanggal Mulai</dt>
                                <dd class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d F Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Tanggal Selesai</dt>
                                <dd class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d F Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Total Durasi</dt>
                                <dd class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari</dd>
                            </div>
                        </dl>
                    </div>
                    {{-- Detail Pemohon --}}
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Diajukan Oleh</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Nama</dt>
                                <dd class="font-semibold text-white">{{ $cuti->user->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Divisi</dt>
                                <dd class="font-semibold text-white">{{ $cuti->user->divisi ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-400">Jabatan</dt>
                                <dd class="font-semibold text-white">{{ $cuti->user->jabatan }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Alasan --}}
                <div>
                    <h3 class="text-lg font-semibold text-white">Alasan Pengajuan</h3>
                    <div class="mt-2 text-sm text-zinc-300 bg-zinc-700 p-4 rounded-lg border border-zinc-600">
                        <p>{{ $cuti->alasan }}</p>
                    </div>
                </div>
                
                {{-- Catatan Admin --}}
                @if($cuti->catatan_approval)
                <div>
                    <h3 class="text-lg font-semibold text-white">Catatan Admin</h3>
                    <div class="mt-2 text-sm text-zinc-300 bg-zinc-700 p-4 rounded-lg border border-zinc-600">
                        <p>{{ $cuti->catatan_approval }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layout-admin>