<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="p-6">
        <x-back-button href="{{ route('admin.cuti.index') }}">Kembali ke Rekap Cuti</x-back-button>

        @if (session('success'))
            <div class="bg-green-500/10 border-l-4 border-green-500 text-green-400 p-4 rounded-md mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-500/10 border-l-4 border-red-500 text-red-400 p-4 rounded-md mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-zinc-800 rounded-lg shadow-lg p-6 md:p-8 text-zinc-300 border border-zinc-700">
            <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Detail Pengajuan Cuti</h2>
                {{-- Status Akhir yang Lebih Menonjol --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-400 text-left md:text-right">Status Akhir</label>
                    <span class="px-3 py-1.5 font-semibold leading-tight rounded-full text-sm capitalize
                        @if($cuti->status == 'disetujui' || $cuti->status == 'diterima') bg-green-500/10 text-green-400
                        @elseif($cuti->status == 'ditolak') bg-red-500/10 text-red-400
                        @else bg-yellow-500/10 text-yellow-400 @endif">
                        {{ $cuti->status }}
                    </span>
                </div>
            </div>
            
            {{-- INFORMASI UTAMA --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Nama Pemohon</label>
                    <p class="font-semibold text-white text-lg">{{ $cuti->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Jenis Cuti</label>
                    <p class="font-semibold text-white capitalize">{{ $cuti->jenis_cuti }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Pengajuan</label>
                    <p class="font-semibold text-white">{{ $cuti->created_at->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Mulai</label>
                    <p class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Tanggal Selesai</label>
                    <p class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d F Y') }}</p>
                </div>
                 <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Total Durasi</label>
                    <p class="font-semibold text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} hari</p>
                </div>
            </div>

            <div class="md:col-span-3 mt-6">
                <label class="block text-sm font-medium text-zinc-400 mb-2">Alasan Cuti</label>
                <div class="p-4 bg-zinc-700 rounded-lg prose prose-invert prose-sm max-w-none">
                    <p>{{ $cuti->alasan }}</p>
                </div>
            </div>

            @if ($cuti->lampiran)
                <div class="md:col-span-2 mt-6">
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Lampiran Dokumen</label>
                    <div class="p-4 bg-zinc-700 rounded-lg text-center">
                        <a href="{{ asset('storage/' . $cuti->lampiran) }}" target="_blank" class="font-semibold text-indigo-400 hover:underline">
                            <i class="fas fa-file-alt mr-2"></i>Lihat Berkas Lampiran
                        </a>
                    </div>
                </div>
            @endif
            
            <hr class="my-8 border-zinc-700">

            {{-- CATATAN PERSETUJUAN (VERSI SEDERHANA) --}}
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-white mb-3">Catatan dari Approver</h3>
                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-zinc-900 border border-zinc-700">
                        <p class="font-semibold text-white"></p>
                        <p class="text-sm text-zinc-400 mt-2 italic">"{{ $cuti->catatan_approval ?? 'Tidak ada catatan.' }}"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout-admin>