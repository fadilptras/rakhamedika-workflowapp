<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* == Modern Glassmorphism Blue Theme == */
        
        /* Custom Scrollbar Modern */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #93c5fd; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #60a5fa; }
        
        /* == Kalender (Ukuran Asli Dipertahankan) == */
        .fc {
            border: none !important;
            background: #F0F9FF; /* bg-blue-50 */
            border-radius: 1rem;
            padding: 1rem;
        }
        .fc .fc-toolbar-title { font-size: 1.3rem; font-weight: 700; color: #111827; }
        .fc .fc-button {
            background: transparent !important; border: none !important; box-shadow: none !important;
            color: #6B7280; transition: all 0.2s; padding: 0 !important;
            width: 38px; height: 38px; display: flex; justify-content: center; align-items: center; border-radius: 9999px;
        }
        .fc .fc-button:hover { color: #111827; background: #DBEAFE !important; transform: scale(1.1); }
        .fc .fc-button .fc-icon { font-size: 1.25rem; }
        .fc .fc-col-header-cell { border: none !important; padding: 6px 0; }
        .fc .fc-col-header-cell-cushion { color: #6b7280; font-weight: 600; font-size: 0.9rem; }
        .fc .fc-daygrid-day-frame { display: flex; justify-content: center; align-items: center; height: 44px; }
        .fc .fc-daygrid-day-number {
            width: 34px; height: 34px; line-height: 34px; text-align: center; border-radius: 9999px;
            font-weight: 500; transition: all 0.2s; font-size: 0.9rem; color: #374151;
        }
        .fc .fc-day-other .fc-daygrid-day-number { color: #d1d5db; }
        .fc .fc-daygrid-day:not(.fc-day-other):hover .fc-daygrid-day-number { background-color: #DBEAFE; }
        .fc .fc-day-today .fc-daygrid-day-number {
            font-weight: 700; color: #1D4ED8; background: #BFDBFE;
            box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.4);
        }
        .fc .selected-date .fc-daygrid-day-number { background: #111827; color: #fff !important; font-weight: 700; }
        .fc .fc-day-has-event::after {
            content: ''; position: absolute; bottom: 5px; left: 50%;
            transform: translateX(-50%); width: 6px; height: 6px; border-radius: 50%;
            background-color: #3B82F6;
        }
        .fc .selected-date.fc-day-has-event::after { background-color: #fff; }
        
        .fc-daygrid-event {
            display: none !important;
        }
    </style>
    @endpush

    <div class="flex flex-col h-full bg-gradient-to-br from-sky-50 to-blue-100">
        <main class="flex-1 overflow-y-auto min-h-screen p-0 lg:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white p-6 rounded-2xl shadow-xl shadow-blue-500/20"><h2 class="text-2xl font-bold">Welcome Back, {{ Auth::user()->name }}!</h2><p class="text-sm mt-1 text-blue-100">Semoga harimu produktif.</p></div>
                    
                    <div class="bg-white/60 backdrop-blur-lg border border-white/30 shadow-xl shadow-blue-500/20 p-6 rounded-2xl">
                        <div class="flex flex-col items-center"><div class="w-32 h-32 aspect-square overflow-hidden rounded-full border-4 border-white/50 shadow-sm"><img class="w-full h-full object-cover" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=random&color=fff&size=128' }}" alt="Foto Profil"></div></div>
                        <div class="text-left space-y-3 mt-4">
                            <div><label class="text-xs text-blue-900/80 font-semibold">Nama</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</p></div>
                            <div><label class="text-xs text-blue-900/80 font-semibold">Divisi</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->divisi ?? '-' }}</p></div>
                            <div><label class="text-xs text-blue-900/80 font-semibold">Posisi</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->jabatan ?? '-' }}</p></div>
                            <div><label class="text-xs text-blue-900/80 font-semibold">Email</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->email }}</p></div>
                            <div><label class="text-xs text-blue-900/80 font-semibold">Tanggal Bergabung</label><p class="font-bold text-base text-gray-800">{{ Auth::user()->tanggal_bergabung ? Auth::user()->tanggal_bergabung->format('d F Y') : '-' }}</p></div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 xl:col-span-3 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white/60 backdrop-blur-lg border border-white/30 shadow-xl shadow-blue-500/20 p-6 rounded-2xl flex flex-col">
                            <h3 class="font-bold text-gray-900 mb-8 text-xl">Absensi</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <a href="{{ route('absen') }}" class="bg-white/80 hover:bg-white p-4 rounded-xl text-center flex flex-col items-center justify-center aspect-square transition-all duration-300 border border-blue-200 hover:border-blue-400 hover:shadow-lg hover:-translate-y-1"><i class="fas fa-fingerprint text-2xl text-blue-600 mb-2"></i><span class="font-semibold text-sm text-gray-700">Absen</span></a>
                                <a href="{{ route('cuti') }}" class="bg-white/80 hover:bg-white p-4 rounded-xl text-center flex flex-col items-center justify-center aspect-square transition-all duration-300 border border-green-200 hover:border-green-400 hover:shadow-lg hover:-translate-y-1"><i class="fas fa-calendar-alt text-2xl text-green-600 mb-2"></i><span class="font-semibold text-sm text-gray-700">Pengajuan Cuti</span></a>
                                <a href="{{ route('rekap_absen.index') }}" class="bg-white/80 hover:bg-white p-4 rounded-xl text-center flex flex-col items-center justify-center aspect-square transition-all duration-300 border border-yellow-200 hover:border-yellow-400 hover:shadow-lg hover:-translate-y-1"><i class="fas fa-history text-2xl text-yellow-600 mb-2"></i><span class="font-semibold text-sm text-gray-700">Rekap Absen</span></a>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-gray-900 to-slate-800 text-white p-6 rounded-2xl shadow-xl shadow-slate-900/40 border border-slate-700 flex flex-col">
                            <div class="flex justify-between items-center mb-4 flex-shrink-0">
                                <h3 class="font-bold text-white text-xl">Notifikasi</h3>
                                <a href="{{ route('notifikasi.index') }}" class="relative flex items-center space-x-2 text-gray-300 hover:text-white transition-colors duration-200">
                                    <span class="text-sm font-semibold">Lihat Semua</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                    @if (Auth::user()->unreadNotifications->count() > 0)
                                        <span class="absolute top-0 right-0 inline-flex items-center justify-center h-4 w-4 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                            {{ Auth::user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                            <div class="space-y-3 flex-grow flex flex-col justify-center">
                                @forelse(Auth::user()->notifications->take(2) as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-3 rounded-lg {{ $notification->read_at ? 'bg-gray-800/50' : 'bg-blue-800' }} hover:bg-gray-700/70 transition-colors duration-150">
                                    <div class="flex items-start">
                                        <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }} text-xl text-white mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-100">{{ $notification->data['title'] ?? 'Notifikasi Baru' }}</p>
                                            <p class="text-xs text-gray-300 line-clamp-1">{{ $notification->data['message'] ?? 'Tidak ada detail' }}</p>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="flex-grow flex items-center justify-center"><p class="text-center text-gray-400 py-4 text-sm">Tidak ada notifikasi baru.</p></div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white/60 backdrop-blur-lg border border-white/30 shadow-xl shadow-blue-500/20 p-6 rounded-2xl">
                        <div class="flex flex-col md:flex-row gap-8">
                            <div class="w-full lg:w-3/5">
                                <div id="mini-calendar"></div>
                            </div>
                            <div class="hidden lg:block w-1 bg-blue-200"></div>
                            <div class="w-full lg:w-2/5 flex flex-col">
                                <div class="flex justify-between items-center mb-4 flex-shrink-0">
                                    <h3 id="agenda-list-title" class="font-bold text-gray-900 text-lg">Agenda Minggu Ini</h3>
                                    <button id="add-agenda-btn" class="bg-gray-900 hover:bg-gray-800 text-white font-bold w-10 h-10 rounded-full transition-all duration-200 flex items-center justify-center shadow-md hover:scale-105">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </div>
                                <div id="agenda-list-container" class="h-80 overflow-y-auto pr-2 space-y-3 -mr-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="agenda-modal" class="fixed inset-0 bg-black bg-opacity-60 z-40 hidden flex items-center justify-center p-4">
        <div class="bg-white/80 backdrop-blur-xl border border-white/30 rounded-2xl shadow-2xl shadow-blue-900/20 w-full max-w-3xl mx-4 p-6 transform transition-all" id="agenda-modal-content">
            <div class="flex justify-between items-center border-b border-black/10 pb-3 mb-6">
                <h4 class="text-xl font-bold text-gray-800">Buat Agenda Baru</h4>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times text-2xl"></i></button>
            </div>

            <form id="agenda-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Agenda <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white/70" placeholder="Contoh: Rapat Evaluasi Bulanan">
                            <small id="title-error" class="text-red-500 text-xs mt-1 hidden"></small>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white/70" placeholder="Jelaskan detail agenda di sini..."></textarea>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Acara <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                               <div class="sm:col-span-3">
                                    <input type="text" id="agenda_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white/70" placeholder="Pilih Tanggal">
                               </div>
                               <div class="sm:col-span-2">
                                    <input type="text" id="start_hour" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white/70" placeholder="Mulai">
                               </div>
                            </div>
                            <small id="start_time-error" class="text-red-500 text-xs mt-1 hidden"></small>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Undang Karyawan</label>
                            <div id="guest-list-container" class="h-40 overflow-y-auto rounded-lg border bg-white/70 p-3 space-y-2">
                                <p class="text-gray-400">Memuat karyawan...</p>
                            </div>
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <input type="text" id="location" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white/70" placeholder="Contoh: Ruang Meeting Lt. 2">
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Warna Label</label>
                            <input type="color" id="color" name="color" value="#3B82F6" class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-8 pt-4 border-t border-black/10">
                    <button type="button" id="cancel-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">Batal</button>
                    <button type="submit" id="save-agenda-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Agenda</button>
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
            const calendarEl = document.getElementById('mini-calendar');
            const agendaListContainer = document.getElementById('agenda-list-container');
            const agendaListTitle = document.getElementById('agenda-list-title');
            let selectedDateEl = null;

            function formatFullDate(date) { return date.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
            function formatTime(date) { return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }); }

            function updateAgendaList(selectedDate) {
                const allEvents = calendar.getEvents();
                const startOfWeek = new Date(selectedDate);
                startOfWeek.setDate(selectedDate.getDate() - selectedDate.getDay());
                startOfWeek.setHours(0, 0, 0, 0);
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                endOfWeek.setHours(23, 59, 59, 999);
                
                agendaListTitle.textContent = 'Agenda Minggu Ini';

                const eventsThisWeek = allEvents.filter(event => {
                    const eventDate = new Date(event.start);
                    return eventDate >= startOfWeek && eventDate <= endOfWeek;
                });

                agendaListContainer.innerHTML = '';
                if (eventsThisWeek.length > 0) {
                    eventsThisWeek.sort((a, b) => a.start - b.start).forEach(event => {
                        const startTime = formatTime(event.start);
                        const endTime = event.end ? formatTime(event.end) : '';
                        
                        const agendaHTML = `
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-white/80 shadow-md shadow-blue-500/10 border border-blue-200 transition-all duration-200 hover:shadow-xl hover:border-blue-400 hover:bg-white">
                                <div class="flex-shrink-0 text-center bg-blue-100 text-blue-800 rounded-lg px-3 py-2 w-20">
                                    <p class="font-bold text-sm">${startTime}</p>
                                    ${endTime ? `<p class="text-xs">${endTime}</p>` : ''}
                                </div>
                                <div class="flex-grow border-l-4 pl-4" style="border-color: ${event.backgroundColor || event.borderColor || '#3B82F6'}">
                                    
                                    <p class="font-semibold text-gray-900 text-base">${event.extendedProps.fullTitle}</p>
                                    
                                    <p class="text-xs text-gray-500">${formatFullDate(event.start)}</p>
                                    ${event.extendedProps.location ? `<p class="text-sm text-gray-500 mt-1">${event.extendedProps.location}</p>` : ''}
                                </div>
                            </div>`;
                        agendaListContainer.innerHTML += agendaHTML;
                    });
                } else {
                     agendaListContainer.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full text-center text-blue-700 p-4 bg-blue-100/70 rounded-xl border border-blue-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-50 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <p class="font-semibold">Tidak ada agenda</p><p class="text-sm opacity-80">Pilih tanggal lain atau tambah agenda baru.</p>
                        </div>`;
                }
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                aspectRatio: 1.2, height: 'auto', locale: 'id',
                buttonText: { today: 'hari ini', month: 'bulan', week: 'minggu', day: 'hari' },
                events: "{{ route('agendas.index') }}",
                dateClick: function(info) {
                    if (selectedDateEl) { selectedDateEl.classList.remove('selected-date'); }
                    info.dayEl.classList.add('selected-date');
                    selectedDateEl = info.dayEl;
                    updateAgendaList(info.date);
                },
                eventsSet: function() {
                    document.querySelectorAll('.fc-day-has-event').forEach(el => el.classList.remove('fc-day-has-event'));
                    calendar.getEvents().forEach(event => {
                        const dateString = event.start.toISOString().split('T')[0];
                        const dayEl = document.querySelector(`.fc-day[data-date="${dateString}"]`);
                        if (dayEl) dayEl.classList.add('fc-day-has-event');
                    });
                    const selectedDayEl = document.querySelector('.selected-date');
                    let currentDate = new Date();
                    if (selectedDayEl) {
                        const dateStr = selectedDayEl.dataset.date + 'T00:00:00Z';
                        currentDate = new Date(dateStr);
                    }
                    updateAgendaList(currentDate);
                }
            });
            calendar.render();

            // === AWAL DARI KODE BARU UNTUK MODAL ===
            const agendaModal = document.getElementById('agenda-modal');
            const agendaModalContent = document.getElementById('agenda-modal-content');
            const addAgendaBtn = document.getElementById('add-agenda-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const agendaForm = document.getElementById('agenda-form');
            
            // Inisialisasi Flatpickr untuk tanggal dan jam
            const agendaDate = flatpickr("#agenda_date", { dateFormat: "Y-m-d", altInput: true, altFormat: "d F Y", locale: "id" });
            const startHour = flatpickr("#start_hour", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
            
            function openModal() {
                agendaForm.reset();
                agendaDate.setDate(new Date());
                startHour.clear();
                agendaModal.classList.remove('hidden');
                setTimeout(() => agendaModalContent.classList.add('scale-100'), 10);
            }

            function closeModal() {
                agendaModalContent.classList.remove('scale-100');
                setTimeout(() => agendaModal.classList.add('hidden'), 200);
            }

            addAgendaBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            agendaModal.addEventListener('click', (e) => { if (e.target === agendaModal) closeModal(); });
            
            // PERUBAHAN DI SINI: Fetch user untuk mengisi div checkbox
            const guestContainer = document.getElementById('guest-list-container');
            fetch("{{ route('agendas.getUsers') }}")
                .then(response => response.json())
                .then(users => {
                    guestContainer.innerHTML = ''; // Kosongkan container
                    if (users.length > 0) {
                        users.forEach(user => {
                            const checkboxHTML = `
                                <div class="flex items-center">
                                    <input id="guest-${user.id}" name="guests[]" value="${user.id}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="guest-${user.id}" class="ml-3 block text-sm font-medium text-gray-700">${user.name}</label>
                                </div>
                            `;
                            guestContainer.insertAdjacentHTML('beforeend', checkboxHTML);
                        });
                    } else {
                        guestContainer.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada karyawan lain untuk diundang.</p>';
                    }
                });
            
            function clearValidationErrors() {
                document.querySelectorAll('small[id$="-error"]').forEach(el => {
                    el.classList.add('hidden');
                    el.textContent = '';
                });
            }

            agendaForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearValidationErrors();
                
                const dateValue = document.getElementById('agenda_date').value;
                const timeValue = document.getElementById('start_hour').value;

                const formData = new FormData(this);
                if (dateValue && timeValue) {
                    formData.append('start_time', `${dateValue} ${timeValue}`);
                }

                const saveButton = document.getElementById('save-agenda-btn');
                saveButton.disabled = true; saveButton.innerHTML = 'Menyimpan...';

                fetch("{{ route('agendas.store') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        closeModal();
                        calendar.refetchEvents();
                    } else if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`${key}-error`);
                            if(errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    }
                })
                .catch(error => { console.error('Error:', error); alert('Terjadi kesalahan.'); })
                .finally(() => { saveButton.disabled = false; saveButton.innerHTML = 'Simpan Agenda'; });
            });
        });
    </script>
    @endpush
</x-layout-users>