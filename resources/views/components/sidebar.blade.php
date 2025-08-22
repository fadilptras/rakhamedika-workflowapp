<div id="sidebar" class="bg-blue-500 text-white h-full flex flex-col w-20 transition-all duration-300 ease-in-out">
    
    <!-- Logo -->
    <div class="p-4 border-b border-blue-400 flex items-center justify-center h-[68px]">
        <i class="fas fa-clinic-medical text-3xl"></i>
    </div>

    <!-- Menu -->
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
                    <a href="{{ route('pengajuan_dana') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                        <i class="fas fa-coins text-xl"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengajuan_dokumen') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                        <i class="fas fa-folder text-xl"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('email') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50">
                        <i class="fas fa-envelope-open-text text-xl"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Bottom Menu -->
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
