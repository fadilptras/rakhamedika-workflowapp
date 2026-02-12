<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    <div>
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
                <p class="text-sm text-zinc-400 mt-1">Atur Approver 1 dan Approver 2 untuk setiap karyawan dalam pengajuan cuti.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-emerald-500/10 text-emerald-400 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.cuti.set_approvers.save') }}" method="POST">
            @csrf

            <div class="bg-zinc-800 rounded-xl shadow-lg border border-zinc-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-700">
                        <thead class="bg-zinc-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-zinc-400 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-zinc-400 uppercase tracking-wider">Approver Cuti 1</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-zinc-400 uppercase tracking-wider">Approver Cuti 2</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-700">
                            @forelse ($employees as $employee)
                                <tr class="hover:bg-zinc-700/50 approver-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ $employee->name }}</div>
                                        <div class="text-xs text-zinc-400">{{ $employee->divisi ?? 'Belum ada divisi' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="approver_cuti_1[{{ $employee->id }}]" class="w-full max-w-xs p-2 bg-zinc-700 border border-zinc-600 rounded-lg text-sm text-white focus:ring-amber-500 focus:border-amber-500 approver-select">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}" @selected($employee->approver_cuti_1_id == $approver->id)>
                                                    {{ $approver->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="approver_cuti_2[{{ $employee->id }}]" class="w-full max-w-xs p-2 bg-zinc-700 border border-zinc-600 rounded-lg text-sm text-white focus:ring-amber-500 focus:border-amber-500 approver-select">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}" @selected($employee->approver_cuti_2_id == $approver->id)>
                                                    {{ $approver->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-zinc-500">Belum ada data karyawan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateApproverOptions(row) {
                const selects = row.querySelectorAll('.approver-select');
                const selectedValues = [];
                selects.forEach(select => { if (select.value !== "") selectedValues.push(select.value); });

                selects.forEach(currentSelect => {
                    const currentValue = currentSelect.value;
                    currentSelect.querySelectorAll('option').forEach(option => {
                        if (option.value === "") { option.disabled = false; return; }
                        const isSelectedElsewhere = selectedValues.includes(option.value) && option.value !== currentValue;
                        option.disabled = isSelectedElsewhere;
                    });
                });
            }

            const allRows = document.querySelectorAll('.approver-row');
            allRows.forEach(row => updateApproverOptions(row));

            document.querySelectorAll('.approver-select').forEach(select => {
                select.addEventListener('change', function(e) {
                    const row = e.target.closest('.approver-row');
                    if (row) updateApproverOptions(row);
                });
            });
        });
    </script>
    @endpush
</x-layout-admin>