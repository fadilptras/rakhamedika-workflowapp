<header class="bg-white shadow-sm p-2 flex justify-between items-center px-4 sm:px-6 lg:px-8">
    <div class="py-2">
        <a href="/" class="text-black text-lg font-bold">
                {{ $slot }}
        </a>
    </div>
    
    <div class="flex items-center space-x-4">
        <div class="flex items-center">
            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=random' }}"
                    alt="User" class="w-8 h-8 rounded-full mr-2 object-cover">
                <span class="text-gray-700">{{ Auth::user()->name }}</span>
        </div>
    </div>
</header>