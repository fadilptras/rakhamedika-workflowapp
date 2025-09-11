<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans h-screen flex">

    {{-- Sidebar --}}
    <div id="sidebar" class="bg-blue-500 text-white h-full flex flex-col w-20 transition-all duration-300 ease-in-out">
    
        <div class="p-4 border-b border-blue-400 flex items-center justify-center h-[68px]">
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
    
        <div class="p-4 border-t border-blue-400 space-y-4">
            <a href="#" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
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
        
        {{-- Navbar --}}
        <nav class="w-full bg-[#1153b4]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-4">
                <a href="/" class="text-white text-lg font-bold">
                    PT RAKHA MEDIKA NUSANTARA
                </a>
                </div>
            </div>
        </nav>
        
        {{-- Header --}}
        <header class="bg-white shadow-sm p-2 flex justify-between items-center px-4 sm:px-6 lg:px-8">
            <div class="py-2">
                <a href="/" class="text-black text-lg font-bold">
                        {{ $title }}
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto">
            {{ $slot }}
        </div>

    </div>

    @stack('scripts')

</body>
</html>