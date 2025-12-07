<x-layout-admin>
<x-slot:title>Aktivitas Karyawan</x-slot:title>

{{-- HEADER --}}
<div class="flex justify-between items-center mb-6 flex-wrap gap-4">
    <h1 class="text-2xl font-bold text-white">Aktivitas Harian Karyawan</h1>
    {{-- Anda bisa tambahkan tombol download PDF di sini nanti jika perlu --}}
</div>

{{-- FILTER --}}
<div class="my-6 p-4 bg-zinc-800 rounded-lg shadow-md border border-zinc-700">
    <form method="GET" action="{{ route('admin.aktivitas.index') }}" id="filter-form">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 items-end">
            
            {{-- Filter Tanggal --}}
            <div>
                <label for="tanggal" class="block text-sm font-medium text-zinc-300">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" 
                       value="{{ $filters['tanggal'] ?? now()->toDateString() }}" 
                       class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Filter Divisi --}}
            <div>
                <label for="divisi" class="block text-sm font-medium text-zinc-300">Divisi</label>
                <select name="divisi" id="divisi" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d }}" @selected(isset($filters['divisi']) && $filters['divisi'] == $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Filter Karyawan --}}
            <div>
                <label for="user_id" class="block text-sm font-medium text-zinc-300">Karyawan</label>
                <select name="user_id" id="user_id" class="mt-1 w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Karyawan</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(isset($filters['user_id']) && $filters['user_id'] == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Filter & Reset --}}
            <div class="flex items-end justify-start gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.aktivitas.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-colors">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- TABEL DATA --}}
<div class="overflow-x-auto bg-zinc-800 rounded-lg shadow-lg border border-zinc-700 mt-6">
    <table class="min-w-full text-sm text-left text-zinc-300">
        <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
            <tr>
                <th class="px-4 py-3">Karyawan</th>
                <th class="px-4 py-3">Waktu</th>
                <th class="px-4 py-3">Keterangan</th>
                <th class="px-4 py-3">Lampiran & Lokasi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-700">
            
            @forelse ($aktivitasHarian as $aktivitas)
                <tr class="hover:bg-zinc-700/30">
                    
                    {{-- Karyawan --}}
                    <td class="px-4 py-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border-2 border-zinc-600 shadow-md">
                            <img src="{{ $aktivitas->user->profile_picture ? Storage::url($aktivitas->user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($aktivitas->user->name ?? 'U').'&background=4f46e5&color=e0e7ff' }}"
                                 alt="{{ $aktivitas->user->name ?? 'User' }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $aktivitas->user->name ?? 'User Dihapus' }}</p>
                            <p class="text-xs text-zinc-400">{{ $aktivitas->user->divisi ?? '-' }}</p>
                        </div>
                    </td>

                    {{-- Waktu --}}
                    <td class="px-4 py-3">
                        <span class="font-semibold text-white">{{ $aktivitas->created_at->format('H:i') }} WIB</span>
                    </td>

                    {{-- Keterangan --}}
                    <td class="px-4 py-3">
                        <p class="text-white">{{ $aktivitas->title ?? 'Judul tidak ada' }}</p>
                        <p class="text-xs text-zinc-400">{{ $aktivitas->keterangan ?? '-' }}</p>
                    </td>

                    {{-- Lampiran & Lokasi --}}
                    <td class="px-4 py-3 space-y-1">
                        @php $hasLink = false; @endphp

                        @if ($aktivitas->lampiran)
                            <a href="{{ asset('storage/' . $aktivitas->lampiran) }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                                <i class="fas fa-camera mr-1"></i> Lihat Foto
                            </a><br>
                            @php $hasLink = true; @endphp
                        @endif

                        @if ($aktivitas->latitude && $aktivitas->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $aktivitas->latitude }},{{ $aktivitas->longitude }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 underline text-xs font-medium">
                               <i class="fas fa-map-marker-alt mr-1"></i> Lihat Lokasi
                            </a>
                            @php $hasLink = true; @endphp
                        @endif

                        @if (!$hasLink)
                            <span>-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-zinc-400">
                        Tidak ada data aktivitas yang cocok dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginasi --}}
@if ($aktivitasHarian->hasPages())
    <div class="mt-6">
        {{ $aktivitasHarian->links() }}
    </div>
@endif


</x-layout-admin>