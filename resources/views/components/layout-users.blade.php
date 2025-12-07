<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    
    <meta name="theme-color" content="#2563eb"> 
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')

    <style>
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
        /* Kustomisasi Scrollbar Browser Bawaan */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
</head>

<body class="bg-gray-100 font-sans bg-gradient-to-br from-sky-50 to-blue-100 flex flex-col min-h-screen overflow-x-hidden">

    {{-- Overlay Sidebar --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    {{-- Sidebar (Z-Index 50) --}}
    <div id="sidebar" class="bg-blue-600 text-white h-full flex flex-col w-20 fixed top-0 left-0 z-50 transform -translate-x-full">
        
        <div class="p-4 border-b border-blue-500 flex items-center justify-center h-[68px]">
            <i class="fas fa-clinic-medical text-3xl"></i>
        </div>
        
        <div class="flex-grow overflow-y-auto">
            <nav class="p-4 pt-6">
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center justify-center p-3 rounded-lg transition-colors duration-200 
                            {{ request()->routeIs('dashboard') ? 'bg-blue-800 shadow-lg' : 'hover:bg-blue-700/50' }}">
                            <i class="fas fa-th-large text-xl"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pengajuan_dana.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                            <i class="fas fa-coins text-xl"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pengajuan_dokumen.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                            <i class="fas fa-folder text-xl"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('notifikasi.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                            <i class="fas fa-envelope-open-text text-xl"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('crm.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                            <i class="fas fa-users text-xl"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <div class="p-4 border-t border-blue-500 space-y-4">
            <a href="{{ route('profil.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                <i class="fas fa-user-cog text-xl"></i>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center p-3 rounded-lg hover:bg-red-500">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex flex-col min-h-screen relative">
        
        {{-- Navbar Header (Z-Index 20) --}}
        <header class="bg-gradient-to-r from-blue-700 to-blue-600 shadow-lg sticky top-0 z-20 text-white shrink-0">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="mr-4 p-2 rounded-md hover:bg-blue-800/50 focus:outline-none transition-colors duration-200">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center">
                            <img src="{{ asset('asset/images/logorakha.png') }}" alt="Logo" class="h-10 w-10 mr-3">
                            <div>
                                <h1 class="text-base sm:text-lg font-bold leading-tight">
                                    PT RAKHA NUSANTARA MEDIKA
                                </h1>
                                <p class="text-sm text-blue-200 font-semibold leading-tight">
                                    {{ $title }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content (Z-Index 0) --}}
        <main class="flex-1 p-6 relative z-0">
            {{ $slot }}
        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            toggleButton.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleSidebar();
            });

            overlay.addEventListener('click', function () {
                toggleSidebar();
            });
        });
    </script>
    
    {{-- PENTING: TEMPAT MODAL AKAN DI-RENDER DI SINI (LAYER PALING ATAS) --}}
    @stack('modals')
    
    @stack('scripts')

</body>
</html>