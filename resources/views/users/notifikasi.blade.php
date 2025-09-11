<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto bg-gray-100 py-12 px-4 md:px-8">
        <div class="max-w-6xl mx-auto">
            {{-- Container Notifikasi --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                
                {{-- Filter Notifikasi --}}
                <div class="flex border-b border-gray-200">
                    <button class="flex-1 py-4 px-6 text-center border-b-2 font-semibold text-blue-600 border-blue-600 transition-colors duration-200">
                        Semua
                    </button>
                    {{-- Tombol "Belum Dibaca" bisa diimplementasikan dengan logika JavaScript atau route terpisah --}}
                    <button class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors duration-200">
                        Belum Dibaca
                    </button>
                </div>
                
                {{-- Daftar Notifikasi Dinamis --}}
                <div class="divide-y divide-gray-200">
                    @forelse ($notifications as $notification)
                        {{-- Cek apakah notifikasi sudah dibaca atau belum --}}
                        <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-5 sm:p-8 hover:bg-gray-50 transition-colors duration-150 relative">
                            <div class="flex items-start space-x-5">
                                {{-- Ikon untuk Notifikasi Belum Dibaca --}}
                                @if (!$notification->read_at)
                                    <span class="inline-block h-2 w-2 bg-blue-600 rounded-full flex-shrink-0 mt-2 animate-pulse"></span>
                                @else
                                    <span class="inline-block h-2 w-2 flex-shrink-0"></span>
                                @endif
                                
                                {{-- Ikon utama berdasarkan data notifikasi --}}
                                <div class="flex-shrink-0 mt-0.5 {{ $notification->data['color'] ?? 'text-gray-400' }}">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $notification->data['message'] ?? 'Detail tidak tersedia' }}</p>
                                </div>
                                <span class="text-xs text-gray-500 ml-auto flex-shrink-0 mt-1.5">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-8 text-gray-500">Tidak ada notifikasi.</div>
                    @endforelse
                </div>
            </div>
            
            {{-- Tambahkan pagination links --}}
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-layout-users>