<x-layout-admin>
    <x-slot:title>Pengaturan Jatah Cuti</x-slot:title>

    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-6">Pengaturan Jatah Cuti Karyawan</h1>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="bg-green-500/10 border-l-4 border-green-500 text-green-400 p-4 rounded-md mb-6" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Kontainer Utama (Card) --}}
        <div class="bg-zinc-800 rounded-lg shadow-lg border border-zinc-700">
            <form action="{{ route('admin.cuti.updatePengaturan') }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-zinc-300">
                        {{-- Header Tabel --}}
                        <thead class="bg-zinc-700 text-xs uppercase font-semibold text-zinc-200">
                            <tr>
                                <th class="px-6 py-4">Nama Karyawan</th>
                                <th class="px-6 py-4 text-center text-amber-500">Terpakai (Thn Ini)</th>
                                <th class="px-6 py-4 text-center text-emerald-400">Sisa Cuti</th>
                                <th class="px-6 py-4">Jatah Cuti Tahunan (Input)</th>
                            </tr>
                        </thead>

                        {{-- Body Tabel --}}
                        <tbody class="divide-y divide-zinc-700">
                            @forelse ($users as $user)
                                <tr class="hover:bg-zinc-700/30">
                                    {{-- Nama --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-zinc-400">{{ $user->jabatan ?? 'Jabatan tidak diatur' }}</div>
                                    </td>

                                    {{-- Terpakai --}}
                                    <td class="px-6 py-4 text-center font-mono">
                                        {{ $user->cuti_terpakai }} Hari
                                    </td>

                                    {{-- Sisa (Merah jika minus) --}}
                                    <td class="px-6 py-4 text-center font-bold font-mono {{ $user->sisa_cuti < 0 ? 'text-red-500' : 'text-emerald-400' }}">
                                        {{ $user->sisa_cuti }} Hari
                                    </td>

                                    {{-- Input Jatah --}}
                                    <td class="px-6 py-4">
                                        <input 
                                            type="number" 
                                            name="jatah_cuti[{{ $user->id }}]" 
                                            value="{{ $user->jatah_cuti ?? 0 }}" 
                                            class="w-full max-w-xs bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                            min="0"
                                            placeholder="0">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-zinc-400">
                                        <i class="fas fa-users-slash fa-2x mb-2"></i>
                                        <p>Belum ada data karyawan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Aksi Form (Tombol Simpan) --}}
                <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-end rounded-b-lg">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout-admin>