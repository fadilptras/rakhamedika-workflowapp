<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    {{-- Font Awesome untuk ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bg-gray-100 font-sans bg-gradient-to-br from-sky-50 to-blue-100 p-0 md:p-0 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm">
                
                {{-- HEADER HALAMAN --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-gray-200 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Pengajuan Cuti</h1>
                        <p class="text-sm text-gray-500 mt-1">Diajukan pada {{ $cuti->created_at->format('d F Y, H:i') }}</p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                        <a href="{{ route('cuti.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>

                        <a href="{{ route('cuti.download', $cuti) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                            <i class="fas fa-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Status Pengajuan (Logic Updated) --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Status Pengajuan</h3>
                        @if ($cuti->status == 'diajukan')
                            <p class="flex items-center text-base font-medium text-yellow-600">
                                <i class="fas fa-clock fa-fw mr-3"></i>
                                <span>
                                    Menunggu persetujuan dari: 
                                    <span class="font-bold ml-1">
                                        @if($cuti->status_approver_1 == 'menunggu')
                                            {{ $approver1->name ?? 'Atasan 1' }}
                                        @else
                                            {{ $approver2->name ?? 'Atasan 2' }}
                                        @endif
                                    </span>
                                </span>
                            </p>
                        @elseif($cuti->status == 'disetujui')
                            <p class="flex items-center text-base font-medium text-green-600">
                                <i class="fas fa-check-circle fa-fw mr-3"></i>
                                Pengajuan telah disetujui.
                            </p>
                        @elseif($cuti->status == 'dibatalkan')
                            <p class="flex items-center text-base font-medium text-gray-600">
                                <i class="fas fa-ban fa-fw mr-3"></i>
                                <span>Pengajuan ini telah dibatalkan oleh <strong>{{ $cuti->user->name }}</strong>.</span>
                            </p>
                        @else
                            <p class="flex items-center text-base font-medium text-red-600">
                                <i class="fas fa-times-circle fa-fw mr-3"></i>
                                Pengajuan ditolak.
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
                                    <dd class="font-semibold text-gray-900">
                                        {{-- Menghitung hari kerja secara dinamis atau dari controller --}}
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} Hari
                                    </dd>
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
                            <h3 class="text-lg font-semibold text-gray-800">Catatan dari Atasan</h3>
                            <div class="mt-2 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-gray-700">
                                <p>{{ $cuti->catatan_approval }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- FORM TINDAKAN APPROVER (Logic Updated) --}}
                    @php
                        $userLoggedIn = Auth::user();
                        $pemohon = $cuti->user;
                        $showForm = false;

                        if ($cuti->status == 'diajukan') {
                            // Cek giliran Atasan 1
                            if ($pemohon->approver_cuti_1_id == $userLoggedIn->id && $cuti->status_approver_1 == 'menunggu') {
                                $showForm = true;
                            } 
                            // Cek giliran Atasan 2 (Muncul jika Atasan 1 sudah Beres/Skipped)
                            elseif ($pemohon->approver_cuti_2_id == $userLoggedIn->id && $cuti->status_approver_2 == 'menunggu') {
                                if ($cuti->status_approver_1 == 'disetujui' || $cuti->status_approver_1 == 'skipped') {
                                    $showForm = true;
                                }
                            }
                        }
                    @endphp

                    @if ($showForm)
                        <div class="pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tindakan Persetujuan</h3>
                            <form action="{{ route('cuti.updateStatus', $cuti) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <div>
                                        <label for="catatan_approval" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                        <textarea name="catatan_approval" id="catatan_approval" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2" placeholder="Berikan alasan..."></textarea>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <button type="submit" name="status" value="disetujui" class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-all">
                                            <i class="fas fa-check mr-2"></i> Setujui
                                        </button>
                                        <button type="submit" name="status" value="ditolak" class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-all">
                                            <i class="fas fa-times mr-2"></i> Tolak
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Tombol Pembatalan (Untuk Pemilik) --}}
                    @if(Auth::id() == $cuti->user_id && $cuti->status == 'diajukan')
                    <div class="pt-6 border-t border-gray-200 mt-4">
                        <form action="{{ route('cuti.cancel', $cuti) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition-all">
                                <i class="fas fa-ban mr-2"></i> Batalkan Pengajuan Cuti
                            </button>
                        </form>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-layout-users>