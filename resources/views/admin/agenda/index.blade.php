<x-layout-admin>
    <x-slot:title>{{ $title }}</x-slot:title>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        #admin-calendar-wrapper .fc { border: none !important; background: #27272a; border-radius: 0.75rem; padding: 1rem; color: #d1d5db; }
        #admin-calendar-wrapper .fc .fc-toolbar-title { font-size: 1.3rem; font-weight: 700; color: #ffffff; }
        #admin-calendar-wrapper .fc .fc-button { background: transparent !important; border: none !important; box-shadow: none !important; color: #9ca3af; transition: all 0.2s; }
        #admin-calendar-wrapper .fc .fc-button:hover { color: #ffffff; background: #374151 !important; }
        #admin-calendar-wrapper .fc .fc-col-header-cell-cushion { color: #9ca3af; font-weight: 600; }
        #admin-calendar-wrapper .fc .fc-daygrid-day-number { color: #d1d5db; }
        #admin-calendar-wrapper .fc .fc-day-other .fc-daygrid-day-number { color: #4b5563; }
        #admin-calendar-wrapper .fc .fc-day-today .fc-daygrid-day-number { font-weight: 700; color: #f59e0b; background: #374151; border-radius: 9999px; }
        #admin-calendar-wrapper .fc-daygrid-event { display: none !important; }
    </style>
    @endpush

    @if (session('success'))
        <div class="mb-6 bg-emerald-500/10 text-emerald-300 text-sm p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: KALENDER & TOMBOL --}}
        <div class="lg:col-span-2 bg-zinc-800 rounded-xl shadow-lg border border-zinc-700 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">Kalender Agenda</h2>
                <button id="add-agenda-btn" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i> Buat Agenda Baru
                </button>
            </div>
            <div id="admin-calendar-wrapper">
                <div id="admin-calendar"></div>
            </div>
        </div>

        {{-- KOLOM KANAN: DAFTAR SEMUA AGENDA --}}
        <div class="lg:col-span-1 bg-zinc-800 rounded-xl shadow-lg border border-zinc-700 p-6 flex flex-col">
            <h2 class="text-xl font-bold text-white mb-4 flex-shrink-0">Semua Agenda</h2>
            <div class="space-y-3 overflow-y-auto flex-grow">
                @forelse ($allAgendas as $agenda)
                    <div class="bg-zinc-700/50 p-4 rounded-lg border-l-4" style="border-color: {{ $agenda->color ?? '#F59E0B' }};">
                        <p class="font-bold text-white">{{ $agenda->title }}</p>
                        
                        {{-- ====================================================== --}}
                        {{-- PERBAIKAN FORMAT TANGGAL ADA DI BARIS DI BAWAH INI --}}
                        {{-- ====================================================== --}}
                        <p class="text-sm text-zinc-400">{{ \Carbon\Carbon::parse($agenda->start_time)->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</p>
                        
                        <p class="text-xs text-zinc-500 mt-1">Dibuat oleh: {{ $agenda->creator->name ?? 'N/A' }}</p>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center text-center text-zinc-500">
                        <p>Belum ada agenda yang dibuat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal untuk membuat agenda --}}
    <div id="agenda-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-zinc-800 border border-zinc-700 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 p-6 transform transition-all" id="agenda-modal-content">
            <div class="flex justify-between items-center border-b border-zinc-700 pb-3 mb-6">
                <h4 class="text-xl font-bold text-white">Buat Agenda / Pengumuman Baru</h4>
                <button id="close-modal-btn" class="text-zinc-500 hover:text-white"><i class="fas fa-times text-2xl"></i></button>
            </div>

            <form id="agenda-form" action="{{ route('admin.agenda.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-zinc-400 mb-1">Judul Agenda</label>
                            <input type="text" id="title" name="title" required class="w-full px-3 py-2 border bg-zinc-700 border-zinc-600 rounded-lg text-white" placeholder="Contoh: Rapat Evaluasi Bulanan">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-zinc-400 mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border bg-zinc-700 border-zinc-600 rounded-lg text-white" placeholder="Jelaskan detail agenda..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Waktu Acara</label>
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                                <div class="sm:col-span-3">
                                    <input type="text" id="agenda_date" required class="w-full px-3 py-2 border bg-zinc-700 border-zinc-600 rounded-lg text-white" placeholder="Pilih Tanggal">
                                </div>
                                <div class="sm:col-span-2">
                                    <input type="text" id="start_hour" required class="w-full px-3 py-2 border bg-zinc-700 border-zinc-600 rounded-lg text-white" placeholder="Mulai">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Undang Karyawan (Opsional)</label>
                            <div id="guest-list-container" class="h-40 overflow-y-auto rounded-lg border bg-zinc-700 border-zinc-600 p-3 space-y-2">
                                <p class="text-zinc-500">Memuat karyawan...</p>
                            </div>
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-zinc-400 mb-1">Lokasi</label>
                            <input type="text" id="location" name="location" class="w-full px-3 py-2 border bg-zinc-700 border-zinc-600 rounded-lg text-white" placeholder="Contoh: Ruang Meeting">
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-zinc-400 mb-1">Warna Label</label>
                            <input type="color" id="color" name="color" value="#F59E0B" class="w-full h-10 px-1 py-1 border bg-zinc-700 border-zinc-600 rounded-lg cursor-pointer">
                        </div>
                    </div>
                </div>

                <input type="hidden" id="start_time" name="start_time">

                <div class="flex justify-end mt-8 pt-4 border-t border-zinc-700">
                    <button type="button" id="cancel-btn" class="bg-zinc-600 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded-lg mr-2">Batal</button>
                    <button type="submit" id="save-agenda-btn" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('admin-calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                locale: 'id',
                events: "{{ route('admin.agenda.getEvents') }}"
            });
            calendar.render();

            const agendaModal = document.getElementById('agenda-modal');
            const addAgendaBtn = document.getElementById('add-agenda-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const agendaForm = document.getElementById('agenda-form');
            const agendaDate = flatpickr("#agenda_date", { dateFormat: "Y-m-d", altInput: true, altFormat: "d F Y", locale: "id" });
            const startHour = flatpickr("#start_hour", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
            
            function openModal() { agendaModal.classList.remove('hidden'); }
            function closeModal() { agendaModal.classList.add('hidden'); }

            addAgendaBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            
            const guestContainer = document.getElementById('guest-list-container');
            fetch("{{ route('admin.agenda.getAllUsers') }}")
                .then(response => response.json())
                .then(users => {
                    guestContainer.innerHTML = '';
                    if (users.length > 0) {
                        users.forEach(user => {
                            const checkboxHTML = `<div class="flex items-center"><input id="guest-${user.id}" name="guests[]" value="${user.id}" type="checkbox" class="h-4 w-4 rounded border-zinc-500 bg-zinc-700 text-amber-500 focus:ring-amber-500"><label for="guest-${user.id}" class="ml-3 block text-sm font-medium text-zinc-300">${user.name}</label></div>`;
                            guestContainer.insertAdjacentHTML('beforeend', checkboxHTML);
                        });
                    } else {
                        guestContainer.innerHTML = '<p class="text-zinc-500 text-sm">Tidak ada karyawan untuk diundang.</p>';
                    }
                });

            agendaForm.addEventListener('submit', function(e) {
                const dateValue = document.getElementById('agenda_date').value;
                const timeValue = document.getElementById('start_hour').value;
                if (dateValue && timeValue) {
                    document.getElementById('start_time').value = `${dateValue} ${timeValue}`;
                }
            });
        });
    </script>
    @endpush
</x-layout-admin>