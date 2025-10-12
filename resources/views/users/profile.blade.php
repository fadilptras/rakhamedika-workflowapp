<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main class="min-h-screen">
        <div class="max-w-4xl mx-auto">
            
            <a href="{{ route('dashboard') }}" class="flex items-center text-blue-600 hover:underline font-semibold mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Kembali ke Dashboard</span>
            </a>

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

            {{-- Form pembungkus untuk semua kartu --}}
            <form id="profile-form" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b">
                        <h3 class="text-lg font-bold text-gray-800">Informasi Akun</h3>
                    </div>
                    <div class="p-6">
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
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b">
                        <h3 class="text-lg font-bold text-gray-800">Biodata Diri</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="nomor_telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon Aktif</label>
                            <input type="tel" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0812xxxxxxxx">
                        </div>
                        <div>
                            <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                            <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="SMA/SMK Sederajat" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'SMA/SMK Sederajat')>SMA/SMK Sederajat</option>
                                <option value="D3" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'D3')>D3</option>
                                <option value="S1" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'S1')>S1</option>
                                <option value="S2" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'S2')>S2</option>
                                <option value="S3" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'S3')>S3</option>
                                <option value="Lainnya" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'Lainnya')>Lainnya</option>
                            </select>
                        </div>
                         <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Kota Kelahiran">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : null) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki')>Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan')>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (Nomor KTP)</label>
                            <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="16 digit NIK">
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Sesuai KTP</label>
                            <textarea id="alamat" name="alamat" rows="3" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Alamat lengkap...">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b">
                        <h3 class="text-lg font-bold text-gray-800">Kontak Darurat</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="kontak_darurat_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" id="kontak_darurat_nama" name="kontak_darurat_nama" value="{{ old('kontak_darurat_nama', $user->kontak_darurat_nama) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama kontak darurat">
                        </div>
                        <div>
                            <label for="kontak_darurat_nomor" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="tel" id="kontak_darurat_nomor" name="kontak_darurat_nomor" value="{{ old('kontak_darurat_nomor', $user->kontak_darurat_nomor) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor kontak darurat">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                     <div class="p-4 sm:p-5 border-b">
                        <h3 class="text-lg font-bold text-gray-800">Keamanan & Informasi Pekerjaan</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="relative">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror" placeholder="Isi untuk ganti password">
                            <div class="absolute inset-y-0 right-0 top-6 flex items-center pr-3">
                                <span id="toggle-current-password" class="cursor-pointer text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                            @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" id="password" name="password" minlength="8" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" placeholder="Minimal 8 karakter">
                            <div class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600" id="toggle-password">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <p id="password-error" class="hidden text-red-500 text-xs mt-1"></p>
                        </div>
                        <div class="relative md:col-span-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik ulang password baru">
                            <div class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600" id="toggle-password-confirmation">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->tanggal_bergabung ? $user->tanggal_bergabung->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->jabatan }}</p>
                            <p class="text-xs text-gray-500 mt-1">Hubungi Admin/HRD untuk mengubah.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->divisi }}</p>
                             <p class="text-xs text-gray-500 mt-1">Hubungi Admin/HRD untuk mengubah.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <a href="{{ url()->previous() }}" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-200 text-sm font-medium">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center font-medium shadow-md text-sm">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk preview foto
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

            // Script untuk toggle password
            function setupPasswordToggle(toggleElement, inputElement) {
                if(toggleElement){
                    toggleElement.addEventListener('click', function() {
                        const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
                        inputElement.setAttribute('type', type);
                        this.querySelector('i').classList.toggle('fa-eye');
                        this.querySelector('i').classList.toggle('fa-eye-slash');
                    });
                }
            }
            
            setupPasswordToggle(document.getElementById('toggle-password'), document.getElementById('password'));
            setupPasswordToggle(document.getElementById('toggle-password-confirmation'), document.getElementById('password_confirmation'));
            setupPasswordToggle(document.getElementById('toggle-current-password'), document.getElementById('current_password'));

            // Script validasi password baru saat submit
            const form = document.getElementById('profile-form');
            form.addEventListener('submit', function(event) {
                const passwordInput = document.getElementById('password');
                const passwordError = document.getElementById('password-error');
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