<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Background gradient dan padding --}}
    <div class="flex-1 overflow-auto bg-gradient-to-br from-sky-50 to-blue-100 py-0 px-0 md:px-0 min-h-screen">
        <div class="max-w-4xl mx-auto"> {{-- Max width sedikit dikurangi untuk single list --}}

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Notifikasi Terbaru</h1>
                <p class="text-sm text-gray-500 mt-1">Gunakan filter untuk melihat notifikasi berdasarkan jenisnya.</p>
            </div>

            {{-- BAGIAN FILTER --}}
            <div class="mb-8 bg-white p-4 rounded-lg shadow border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">Filter berdasarkan tipe:</p>
                <div class="flex flex-wrap gap-2">
                    {{-- Tombol Semua --}}
                    <a href="{{ route('notifikasi.index') }}"
                       class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors duration-150
                              {{ $filterType === 'semua' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    {{-- Tombol Tipe Lainnya --}}
                    @foreach ($availableTypes as $type)
                        <a href="{{ route('notifikasi.index', ['type' => $type]) }}"
                           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors duration-150
                                  {{ $filterType === $type ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $type }}
                        </a>
                    @endforeach
                </div>
            </div>
            {{-- AKHIR BAGIAN FILTER --}}

           {{-- [FIX] Container Utama untuk SEMUA Notifikasi --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

                {{-- Looping berdasarkan urutan grup (jika filter 'semua') atau hanya satu grup (jika difilter) --}}
                @forelse ($groupOrder as $groupName)
                    {{-- Hanya proses grup jika ada notifikasinya (sesuai filter) --}}
                    @if (isset($groupedNotifications[$groupName]) && $groupedNotifications[$groupName]->isNotEmpty())

                        {{-- Tampilkan Header Grup (jika filter 'semua' ATAU hanya ada 1 grup hasil filter) --}}
                        @if ($filterType === 'semua' || $groupedNotifications->count() === 1)
                            <div class="p-5 sm:p-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-lg font-bold text-gray-700">{{ $groupName }}</h3>
                            </div>
                        @endif

                        {{-- Daftar Notifikasi Dalam Grup --}}
                        <div class="divide-y divide-gray-200">
                            @foreach ($groupedNotifications[$groupName] as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-5 sm:p-6 hover:bg-gray-50/70 transition-colors duration-150 relative">
                                    <div class="flex items-start space-x-4">
                                        {{-- Ikon utama --}}
                                        <div class="flex-shrink-0 mt-0.5 {{ $notification->data['color'] ?? 'text-gray-400' }}">
                                            <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} text-2xl w-6 text-center"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notifikasi' }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? 'Detail tidak tersedia' }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="text-xs text-gray-500">{{ $notification->created_at->isoFormat('D MMM YYYY, HH:mm') }}</span>
                                            @if (!$notification->read_at)
                                            <span class="mt-1 inline-block h-2 w-2 bg-blue-500 rounded-full" title="Baru"></span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    @endif {{-- End if check group exists --}}
                @empty
                    {{-- Kondisi ini hanya aktif jika $groupOrder kosong (tidak mungkin terjadi) --}}
                @endforelse

                {{-- Tampilan jika tidak ada notifikasi sama sekali ATAU tidak ada hasil filter --}}
                @if($groupedNotifications->isEmpty())
                    <div class="text-center p-16 text-gray-500">
                        <i class="fas fa-bell-slash text-5xl mb-5 text-gray-400"></i>
                        @if($filterType !== 'semua')
                            <p class="text-lg font-semibold">Tidak ada notifikasi tipe "{{ $filterType }}" untuk ditampilkan.</p>
                            <a href="{{ route('notifikasi.index') }}" class="mt-4 inline-block text-sm text-blue-600 hover:underline">Tampilkan Semua Notifikasi</a>
                        @else
                            <p class="text-lg font-semibold">Tidak ada notifikasi untuk ditampilkan.</p>
                        @endif
                    </div>
                @endif

            </div> {{-- End Container Utama --}}

        </div>
    </div>
</x-layout-users>