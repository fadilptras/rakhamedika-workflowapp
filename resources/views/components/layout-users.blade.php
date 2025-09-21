<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link rel="icon" href="{{ asset('asset/images/rakhalogo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Menambahkan transisi yang lebih halus untuk transform */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">

    {{-- Overlay untuk latar belakang saat sidebar terbuka --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden"></div>

    {{-- Sidebar --}}
    {{-- Diubah: Lebar kembali ke w-20, teks dihapus, item kembali ke tengah --}}
    <div id="sidebar" class="bg-blue-600 text-white h-full flex flex-col w-20 fixed top-0 left-0 z-30 transform -translate-x-full">
        
        <div class="p-4 border-b border-blue-500 flex items-center justify-center h-[68px]">
            <i class="fas fa-clinic-medical text-3xl"></i>
        </div>
        
        <div class="flex-grow overflow-y-auto">
            <nav class="p-4 pt-6">
                <ul class="space-y-4">
                    {{-- Diubah: Kembali hanya ikon di tengah --}}
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
                        <a href="{{ route('pengajuan_dokumen') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                            <i class="fas fa-folder text-xl"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('notifikasi.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                            <i class="fas fa-envelope-open-text text-xl"></i>
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

    <div class="flex-1 flex flex-col">
        
{{-- Navbar & Header --}}
        <header class="bg-gradient-to-r from-blue-700 to-blue-600 shadow-lg sticky top-0 z-10 text-white">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-4 flex items-center justify-between">
                    
                    {{-- Sisi Kiri: Tombol Sidebar dan Judul --}}
                    <div class="flex items-center">
                        {{-- Tombol untuk membuka sidebar --}}
                        <button id="sidebar-toggle" class="mr-4 p-2 rounded-md hover:bg-blue-800/50 focus:outline-none transition-colors duration-200">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        {{-- Logo dan Judul --}}
                        <div class="flex items-center">
                            <img src="{{ asset('asset/images/logorakha.png') }}" alt="Logo" class="h-10 w-10 mr-3">
                            <div>
                                <h1 class="text-lg font-bold leading-tight">
                                    PT RAKHA NUSANTARA MEDIKA
                                </h1>
                                <p class="text-sm text-blue-200 font-semibold leading-tight">
                                    {{ $title }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan (Bisa ditambahkan item lain jika perlu) --}}
                    <div class="flex items-center">
                        {{-- Contoh: Tombol Notifikasi atau Profil bisa ditambahkan di sini --}}
                    </div>

                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>

    </div>

    {{-- JavaScript untuk fungsionalitas sidebar (Tidak ada perubahan di sini) --}}
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
    
    @stack('scripts')

</body>
</html>