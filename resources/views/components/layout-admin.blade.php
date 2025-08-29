<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #27272a; }
        ::-webkit-scrollbar-thumb { background: #52525b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #71717a; }
    </style>
</head>
<body class="bg-zinc-900 text-zinc-300 font-sans h-screen flex">

    {{-- Sidebar --}}
    <aside id="sidebar" class="bg-zinc-800 h-full flex flex-col w-64 flex-shrink-0">
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
                    <li>
                        <a href="{{ route('admin.absensi.index') }}" class="flex items-center p-3 rounded-lg transition-colors duration-200
                            {{ request()->routeIs('admin.absensi.*') ? 'bg-amber-600 text-white shadow-lg' : 'hover:bg-zinc-700' }}">
                            
                            {{-- Ikon bisa diganti sesuai selera, misal fa-calendar-check --}}
                            <i class="fas fa-calendar-check text-xl w-8 text-center"></i> 
                            
                            <span class="ml-3 font-semibold">Kelola Absen</span>
                        </a>
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
    <div class="flex-1 flex flex-col">
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