<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    
    {{-- Tailwind CSS & Font Awesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Alpine.js untuk fitur dropdown --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #27272a; }
        ::-webkit-scrollbar-thumb { background: #52525b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #71717a; }
    </style>
</head>
<body class="bg-zinc-900 text-zinc-300 font-sans h-screen flex">

    {{-- Sidebar --}}
    <aside id="sidebar" class="bg-zinc-800 h-full flex flex-col w-64 flex-shrink-0 fixed top-0 left-0 z-50">
        <div class="p-4 border-b border-zinc-700/50 flex items-center justify-center h-[68px]">
            <i class="fas fa-user-shield text-3xl mr-3 text-amber-400"></i>
            <span class="font-bold text-xl text-white">Admin Rakha</span>
        </div>
        
        <div class="flex-grow overflow-y-auto">
            <nav class="p-4 pt-6">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('admin.employees.index') }}" class="flex items-center p-3 rounded-lg transition-colors duration-200 
                            {{ request()->routeIs('admin.employees.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">
                            <i class="fas fa-users text-xl w-8 text-center"></i>
                            <span class="ml-3 font-semibold">Kelola Karyawan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.admins.index') }}" class="flex items-center p-3 rounded-lg transition-colors duration-200
                            {{ request()->routeIs('admin.admins.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">
                            <i class="fas fa-user-cog text-xl w-8 text-center"></i>
                            <span class="ml-3 font-semibold">Kelola Admin</span>
                        </a>
                    </li>
                    <li x-data="{ open: {{ request()->routeIs('admin.absensi.*') || request()->routeIs('admin.lembur.*') ? 'true' : 'false' }} }">
                        <a @click.prevent="open = !open" href="#" 
                            class="flex items-center p-3 rounded-lg transition-colors duration-200 cursor-pointer 
                            {{ request()->routeIs('admin.absensi.*') || request()->routeIs('admin.lembur.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">
                            
                            <i class="fas fa-calendar-check text-xl w-8 text-center"></i> 
                            
                            <span class="ml-3 font-semibold flex-1">Kelola Absen</span>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </a>
                        
                        <div x-show="open" x-collapse>
                            <ul class="ml-12 mt-2 space-y-1">
                                <li>
                                    <a href="{{ route('admin.absensi.index') }}" 
                                        class="flex items-center p-2 rounded-lg transition-colors duration-200 text-sm
                                        {{ request()->routeIs('admin.absensi.index', 'admin.lembur.index') ? 'text-amber-400 font-bold' : 'hover:bg-zinc-700' }}">
                                        Aktivitas
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.absensi.rekap') }}" 
                                        class="flex items-center p-2 rounded-lg transition-colors duration-200 text-sm
                                        {{ request()->routeIs('admin.absensi.rekap') ? 'text-amber-400 font-bold' : 'hover:bg-zinc-700' }}">
                                        Rekap Absensi
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li x-data="{ open: {{ request()->routeIs('admin.cuti.*') ? 'true' : 'false' }} }">
                        <a @click.prevent="open = !open" href="#"
                            class="flex items-center p-3 rounded-lg transition-colors duration-200 cursor-pointer
                            {{ request()->routeIs('admin.cuti.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">

                            <i class="fas fa-calendar-alt text-xl w-8 text-center"></i>

                            <span class="ml-3 font-semibold flex-1">Kelola Cuti</span>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </a>
                        <div x-show="open" x-collapse>
                            <ul class="ml-12 mt-2 space-y-1">
                                <li>
                                    <a href="{{ route('admin.cuti.pengaturan') }}"
                                        class="flex items-center p-2 rounded-lg transition-colors duration-200 text-sm
                                        {{ request()->routeIs('admin.cuti.pengaturan') ? 'text-amber-400 font-bold' : 'hover:bg-zinc-700' }}">
                                        Pengaturan Jatah Cuti
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.cuti.index') }}"
                                        class="flex items-center p-2 rounded-lg transition-colors duration-200 text-sm
                                        {{ request()->routeIs('admin.cuti.index') ? 'text-amber-400 font-bold' : 'hover:bg-zinc-700' }}">
                                        Manajemen Pengajuan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="{{ route('admin.agenda.index') }}" class="flex items-center p-3 rounded-lg transition-colors duration-200 
                            {{ request()->routeIs('admin.agenda.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">
                            <i class="fas fa-calendar-week text-xl w-8 text-center"></i>
                            <span class="ml-3 font-semibold">Kelola Agenda</span>
                        </a>
                    </li>

                    {{-- DROPDOWN BARU UNTUK SEMUA JENIS PENGAJUAN --}}
                    <li x-data="{ open: {{ request()->routeIs('admin.pengajuan_dana.*') || request()->routeIs('admin.pengajuan-dokumen.*') || request()->routeIs('admin.agenda.*') ? 'true' : 'false' }} }">
                        <a @click.prevent="open = !open" href="#" 
                            class="flex items-center p-3 rounded-lg transition-colors duration-200 cursor-pointer 
                            {{ request()->routeIs('admin.pengajuan_dana.*') || request()->routeIs('admin.pengajuan-dokumen.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">
                            <i class="fas fa-folder-open text-xl w-8 text-center"></i> 
                            <span class="ml-3 font-semibold flex-1">Kelola Pengajuan</span>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </a>
                        <div x-show="open" x-collapse>
                            <ul class="ml-12 mt-2 space-y-1">
                                <li>
                                    <a href="{{ route('admin.pengajuan_dana.index') }}" 
                                        class="flex items-center p-2 rounded-lg transition-colors duration-200 text-sm {{ request()->routeIs('admin.pengajuan_dana.*') ? 'text-amber-400 font-bold' : 'hover:bg-zinc-700' }}">
                                        Pengajuan Dana
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.pengajuan-dokumen.index') }}" 
                                        class="flex items-center p-2 rounded-lg transition-colors duration-200 text-sm {{ request()->routeIs('admin.pengajuan-dokumen.*') ? 'text-amber-400 font-bold' : 'hover:bg-zinc-700' }}">
                                        Pengajuan Dokumen
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
        
        <div class="p-4 border-t border-zinc-700/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center p-3 rounded-lg hover:bg-red-600 hover:text-white transition-colors duration-200">
                    <i class="fas fa-sign-out-alt text-xl w-8 text-center"></i>
                    <span class="ml-3 font-semibold">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Konten Utama --}}
    <div class="flex-1 flex flex-col ml-64">
        <header class="bg-zinc-800 shadow-md p-2 flex justify-between items-center px-4 sm:px-6 lg:px-8 h-[68px] flex-shrink-0">
            <h1 class="text-white text-lg font-bold">{{ $title ?? 'Dashboard' }}</h1>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>