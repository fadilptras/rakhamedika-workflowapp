<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto bg-gray-100 py-12 px-4 md:px-8">
        <div class="max-w-6xl mx-auto">
            {{-- Container Notifikasi --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                
                {{-- Header --}}
                <div class="p-5 sm:p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Semua Notifikasi</h2>
                    <p class="text-sm text-gray-500 mt-1">Ini adalah riwayat semua notifikasi yang Anda terima.</p>
                </div>
                
                {{-- Daftar Notifikasi Dinamis --}}
                <div class="divide-y divide-gray-200">
                    @forelse ($notifications as $notification)
                        {{-- Tautan ke halaman detail notifikasi --}}
                        <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-5 sm:p-6 hover:bg-gray-50 transition-colors duration-150 relative">
                            <div class="flex items-start space-x-5">
                                {{-- Ikon Titik Biru untuk Notifikasi Belum Dibaca --}}
                                @if (!$notification->read_at)
                                    <span class="inline-block h-2 w-2 bg-blue-600 rounded-full flex-shrink-0 mt-2 animate-pulse" title="Belum dibaca"></span>
                                @else
                                    {{-- Beri ruang kosong agar sejajar --}}
                                    <span class="inline-block h-2 w-2 flex-shrink-0"></span>
                                @endif
                                
                                {{-- Ikon utama berdasarkan data notifikasi dari controller --}}
                                <div class="flex-shrink-0 mt-0.5 {{ $notification->data['color'] ?? 'text-gray-400' }}">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    {{-- Judul dan pesan notifikasi dinamis --}}
                                    <h3 class="text-base font-bold text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $notification->data['message'] ?? 'Detail tidak tersedia' }}</p>
                                </div>
                                {{-- Waktu notifikasi (misal: 10 menit lalu) --}}
                                <span class="text-xs text-gray-500 ml-auto flex-shrink-0 mt-1.5">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        {{-- Tampilan jika tidak ada notifikasi sama sekali --}}
                        <div class="text-center p-12 text-gray-500">
                            <i class="fas fa-bell-slash text-4xl mb-4"></i>
                            <p class="font-semibold">Tidak ada notifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            {{-- Link Paginasi --}}
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-layout-users>