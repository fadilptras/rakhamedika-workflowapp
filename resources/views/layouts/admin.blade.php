<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin Dashboard' }} - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        :root { font-family: 'Inter', sans-serif; }
        .modal { transition: opacity 0.25s ease; }
        .modal-content::-webkit-scrollbar { width: 8px; }
        .modal-content::-webkit-scrollbar-track { background: #f1f1f1; }
        .modal-content::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        .modal-content::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-slate-100 font-sans">

<div class="flex h-screen bg-slate-100">

    <aside class="w-64 flex-shrink-0 bg-slate-800 text-white flex flex-col">
        <div class="h-20 flex items-center justify-center bg-slate-900">
            <h2 class="text-2xl font-bold">Admin Panel</h2>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('admin.employees.index') }}" class="flex items-center px-4 py-2.5 font-semibold rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.employees.index') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197"></path></svg>
                Kelola Karyawan
            </a>
            <a href="{{ route('admin.admins.index') }}" class="flex items-center px-4 py-2.5 font-semibold rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.admins.index') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Kelola Admin
            </a>
        </nav>
        <div class="p-4 border-t border-slate-700">
             <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-2.5 font-semibold text-red-400 rounded-lg transition-colors duration-200 hover:bg-red-900/50 hover:text-white">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <div class="p-6 md:p-8 flex-1 overflow-y-auto">
            {{-- Konten spesifik halaman akan dimuat di sini --}}
            @yield('content')
        </div>
    </main>
</div>

{{-- Komponen Modal dan Script akan dimuat di sini --}}
@include('admin.partials.modals')
@include('admin.partials.scripts')

</body>
</html>