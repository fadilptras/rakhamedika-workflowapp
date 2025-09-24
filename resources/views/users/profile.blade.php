<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main class="min-h-screen">
        <div class="max-w-4xl mx-auto">
            
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline font-semibold hidden lg:block mb-4">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-blue-600 px-4 py-3 sm:px-6 sm:py-4 flex justify-between items-center">
                    <h2 class="text-lg sm:text-xl font-bold text-white">Profile Karyawan</h2>
                </div>

                <div class="p-4 sm:p-6 md:p-8">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center text-sm" role="alert">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span class="font-medium">Sukses!</span> {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md text-sm" role="alert">
                            <p class="font-bold">Terjadi Kesalahan</p>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="profile-form" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col items-center mb-6">
                            <div class="relative mb-4 w-32 h-32">
                                <img id="profile-preview" src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=e0e7ff&size=128' }}" alt="Foto Profile" class="w-full h-full rounded-full object-cover border-4 border-blue-200 shadow-md">
                                <label for="profile_picture" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer shadow-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-camera text-sm"></i>
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 text-center">Klik ikon kamera untuk mengubah foto</p>
                            @error('profile_picture')
                                <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" placeholder="nama@perusahaan.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- ======================================================= --}}
                            {{-- PERUBAHAN PASSWORD LAMA DENGAN SEMUA IKON --}}
                            {{-- ======================================================= --}}
                            <div class="relative">
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                                <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror" placeholder="Isi untuk ganti password">
                                <div class="absolute inset-y-0 right-0 top-6 flex items-center pr-3">
                                    <span id="current-password-feedback" class="mr-2">
                                        <i id="current-password-correct" class="fas fa-check-circle text-green-500 hidden"></i>
                                        <i id="current-password-incorrect" class="fas fa-times-circle text-red-500 hidden"></i>
                                    </span>
                                    <span id="toggle-current-password" class="cursor-pointer text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="relative">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" id="password" name="password" minlength="8" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" placeholder="Minimal 8 karakter">
                                <div class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600" id="toggle-password">
                                    <i class="fas fa-eye-slash"></i>
                                </div>
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p id="password-error" class="hidden text-red-500 text-xs mt-1"></p>
                            </div>
                            
                            <div class="relative">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik ulang password baru">
                                <div class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600" id="toggle-password-confirmation">
                                    <i class="fas fa-eye-slash"></i>
                                </div>
                            </div>
                            
                            <div>
                                <label for="tanggal_bergabung" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                                <input type="date" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', $user->tanggal_bergabung ? $user->tanggal_bergabung->format('Y-m-d') : null) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_bergabung') border-red-500 @enderror">
                                @error('tanggal_bergabung')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->jabatan }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hubungi Admin/HRD untuk mengubah jabatan.</p>
                            </div>
                            <div>
                                <label for="divisi" class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                                <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->divisi }}</p>
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
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            const passwordError = document.getElementById('password-error');
            const form = document.getElementById('profile-form');
            
            // =======================================================
            // PENAMBAHAN SKRIP BARU
            // =======================================================
            const currentPasswordInput = document.getElementById('current_password');
            const currentPasswordCorrectIcon = document.getElementById('current-password-correct');
            const currentPasswordIncorrectIcon = document.getElementById('current-password-incorrect');
            const toggleCurrentPassword = document.getElementById('toggle-current-password');

            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const togglePasswordConfirmation = document.getElementById('toggle-password-confirmation');
            let debounceTimer;

            // Fungsi generik untuk toggle password
            function setupPasswordToggle(toggleElement, inputElement) {
                toggleElement.addEventListener('click', function() {
                    const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
                    inputElement.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
            
            setupPasswordToggle(togglePassword, passwordInput);
            setupPasswordToggle(togglePasswordConfirmation, passwordConfirmationInput);
            setupPasswordToggle(toggleCurrentPassword, currentPasswordInput); // Tambahan untuk password lama

            // Fungsi untuk cek password lama secara real-time
            currentPasswordInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimer);
                const password = this.value;

                // Sembunyikan semua ikon jika kolom kosong
                if (password.length === 0) {
                    currentPasswordCorrectIcon.classList.add('hidden');
                    currentPasswordIncorrectIcon.classList.add('hidden');
                    return;
                }

                // Gunakan debounce untuk mencegah terlalu banyak request
                debounceTimer = setTimeout(() => {
                    fetch('{{ route("profile.checkPassword") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ current_password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            currentPasswordCorrectIcon.classList.remove('hidden');
                            currentPasswordIncorrectIcon.classList.add('hidden');
                        } else {
                            currentPasswordCorrectIcon.classList.add('hidden');
                            currentPasswordIncorrectIcon.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        currentPasswordCorrectIcon.classList.add('hidden');
                        currentPasswordIncorrectIcon.classList.add('hidden');
                    });
                }, 500); // Jeda 500ms setelah user berhenti mengetik
            });
            
            // Script untuk preview foto
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

            // Script untuk validasi panjang password baru
            form.addEventListener('submit', function(event) {
                const passwordValue = passwordInput.value.trim();
                
                if (passwordValue.length > 0 && passwordValue.length < 8) {
                    event.preventDefault(); 
                    passwordError.textContent = 'Password minimal harus 8 karakter.';
                    passwordError.classList.remove('hidden');
                } else {
                    passwordError.classList.add('hidden');
                }
            });
        });
    </script>
</x-layout-users>