<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Profile Karyawan</h2>
                    <a href="{{ route('dashboard') }}" class="text-blue-200 hover:text-white transition duration-200 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                    </a>
                </div>

                <div class="p-4 sm:p-8">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center" role="alert">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span class="font-medium">Sukses!</span> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col items-center mb-6">
                            <div class="relative mb-4">
                                <img id="profile-preview" src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://via.placeholder.com/150' }}" alt="Foto Profile" class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-md">
                                <label for="profile_picture" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer shadow-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-camera text-sm"></i>
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Klik ikon kamera untuk mengubah foto</p>
                            @error('profile_picture')
                                <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" placeholder="nama@perusahaan.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" placeholder="Kosongkan jika tidak ingin mengubah">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="tanggal_bergabung" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                                <input type="date" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', $user->tanggal_bergabung ? $user->tanggal_bergabung->format('Y-m-d') : null) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_bergabung') border-red-500 @enderror">
                                @error('tanggal_bergabung')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <p class="w-full px-4 py-2 bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->jabatan }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hubungi Admin/HRD untuk mengubah jabatan.</p>
                            </div>
                            <div>
                                <label for="divisi" class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                                <p class="w-full px-4 py-2 bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->divisi }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hubungi Admin/HRD untuk mengubah divisi.</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-2 pt-4">
                            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center font-medium shadow-md text-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profilePhotoInput = document.getElementById('profile_picture');
            const profilePreview = document.getElementById('profile-preview');
            
            profilePhotoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</x-layout-users>