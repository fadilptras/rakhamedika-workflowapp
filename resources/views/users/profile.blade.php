<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main class="min-h-screen">
        <div class="max-w-4xl mx-auto px-0 py-2 sm:py-2">
            
            <a href="{{ route('dashboard') }}" class="flex items-center text-blue-600 hover:underline font-semibold mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Kembali ke Dashboard</span>
            </a>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center text-sm" role="alert">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span class="font-medium">Sukses! </span> {{ session('success') }}
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

                {{-- KARTU 1: INFORMASI AKUN --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Akun</h3>

                        <a href="{{ route('profile.downloadPdf') }}"
                        class="bg-white hover:bg-blue-100 text-blue-700 px-2.5 py-1 rounded-md text-xs font-medium flex items-center shadow-sm transition-colors" 
                        title="Download Data Profil Lengkap">
                            <i class="fas fa-file-pdf mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="hidden sm:inline">Cetak Data Pribadi</span> {{-- Teks diubah & tetap tersembunyi di mobile --}}
                        </a>
                    </div>
                    
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col items-center mb-6">
                            <div class="relative mb-4 w-24 h-24 sm:w-32 sm:h-32">
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

                {{-- KARTU 2: INFO PRIBADI --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Info Pribadi</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (Nomor KTP)</label>
                            <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="16 digit NIK">
                        </div>
                        <div>
                            <label for="nomor_telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon Aktif</label>
                            <input type="tel" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0812xxxxxxxx">
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
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</Labe>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki')>Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan')>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="agama" class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                            <select id="agama" name="agama" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Islam" @selected(old('agama', $user->agama) == 'Islam')>Islam</option>
                                <option value="Kristen Protestan" @selected(old('agama', $user->agama) == 'Kristen Protestan')>Kristen Protestan</option>
                                <option value="Kristen Katolik" @selected(old('agama', $user->agama) == 'Kristen Katolik')>Kristen Katolik</option>
                                <option value="Hindu" @selected(old('agama', $user->agama) == 'Hindu')>Hindu</option>
                                <option value="Buddha" @selected(old('agama', $user->agama) == 'Buddha')>Buddha</option>
                                <option value="Konghucu" @selected(old('agama', $user->agama) == 'Konghucu')>Konghucu</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                            <select id="status_pernikahan" name="status_pernikahan" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Belum Menikah" @selected(old('status_pernikahan', $user->status_pernikahan) == 'Belum Menikah')>Belum Menikah</option>
                                <option value="Menikah" @selected(old('status_pernikahan', $user->status_pernikahan) == 'Menikah')>Menikah</option>
                                <option value="Cerai" @selected(old('status_pernikahan', $user->status_pernikahan) == 'Cerai')>Cerai</option>
                            </select>
                        </div>
                        <div>
                            <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                            <select id="golongan_darah" name="golongan_darah" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="A" @selected(old('golongan_darah', $user->golongan_darah) == 'A')>A</option>
                                <option value="B" @selected(old('golongan_darah', $user->golongan_darah) == 'B')>B</option>
                                <option value="AB" @selected(old('golongan_darah', $user->golongan_darah) == 'AB')>AB</option>
                                <option value="O" @selected(old('golongan_darah', $user->golongan_darah) == 'O')>O</option>
                                <option value="Tidak Tahu" @selected(old('golongan_darah', $user->golongan_darah) == 'Tidak Tahu')>Tidak Tahu</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat_ktp" class="block text-sm font-medium text-gray-700 mb-1">Alamat Sesuai KTP</label>
                            <textarea id="alamat_ktp" name="alamat_ktp" rows="3" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Alamat lengkap sesuai KTP...">{{ old('alamat_ktp', $user->alamat_ktp) }}</textarea>
                        </div>
                         <div class="md:col-span-2">
                            <label for="alamat_domisili" class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili Saat Ini</label>
                            <textarea id="alamat_domisili" name="alamat_domisili" rows="3" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Isi jika berbeda dengan alamat KTP...">{{ old('alamat_domisili', $user->alamat_domisili) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- KARTU 3: INFORMASI KETENAGAKERJAAN (READ-ONLY) --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                     <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Informasi Ketenagakerjaan</h3>
                        <p class="text-sm text-slate-200 mt-1">Informasi berikut hanya dapat diubah oleh Admin/HRD.</p>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->nip ?? '-' }}</p>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->status_karyawan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->jabatan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->divisi ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kerja</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->lokasi_kerja ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                            <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->tanggal_bergabung ? $user->tanggal_bergabung->format('d M Y') : '-' }}</p>
                        </div>
                        @if($user->status_karyawan == 'Kontrak')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Kontrak</label>
                                <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->tanggal_mulai_kontrak ? $user->tanggal_mulai_kontrak->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir Kontrak</label>
                                <p class="w-full px-4 py-2 text-sm bg-gray-100 rounded-lg text-gray-600 border border-gray-300">{{ $user->tanggal_akhir_kontrak ? $user->tanggal_akhir_kontrak->format('d M Y') : '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KARTU 4: INFORMASI ADMINISTRASI (PAJAK & BPJS) --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Informasi Administrasi (Pajak & BPJS)</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="npwp" class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                            <input type="text" id="npwp" name="npwp" value="{{ old('npwp', $user->npwp) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor NPWP">
                        </div>
                        <div>
                            <label for="ptkp" class="block text-sm font-medium text-gray-700 mb-1">Status PTKP (Pajak)</label>
                            <select id="ptkp" name="ptkp" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Status --</option>
                                <option value="TK/0" @selected(old('ptkp', $user->ptkp) == 'TK/0')>TK/0 (Tidak Kawin, 0 Tanggungan)</option>
                                <option value="TK/1" @selected(old('ptkp', $user->ptkp) == 'TK/1')>TK/1 (Tidak Kawin, 1 Tanggungan)</option>
                                <option value="TK/2" @selected(old('ptkp', $user->ptkp) == 'TK/2')>TK/2 (Tidak Kawin, 2 Tanggungan)</option>
                                <option value="TK/3" @selected(old('ptkp', $user->ptkp) == 'TK/3')>TK/3 (Tidak Kawin, 3 Tanggungan)</option>
                                <option value="K/0" @selected(old('ptkp', $user->ptkp) == 'K/0')>K/0 (Kawin, 0 Tanggungan)</option>
                                <option value="K/1" @selected(old('ptkp', $user->ptkp) == 'K/1')>K/1 (Kawin, 1 Tanggungan)</option>
                                <option value="K/2" @selected(old('ptkp', $user->ptkp) == 'K/2')>K/2 (Kawin, 2 Tanggungan)</option>
                                <option value="K/3" @selected(old('ptkp', $user->ptkp) == 'K/3')>K/3 (Kawin, 3 Tanggungan)</option>
                            </select>
                        </div>
                        <div>
                            <label for="bpjs_kesehatan" class="block text-sm font-medium text-gray-700 mb-1">No. BPJS Kesehatan</label>
                            <input type="text" id="bpjs_kesehatan" name="bpjs_kesehatan" value="{{ old('bpjs_kesehatan', $user->bpjs_kesehatan) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor BPJS Kesehatan">
                        </div>
                        <div>
                            <label for="bpjs_ketenagakerjaan" class="block text-sm font-medium text-gray-700 mb-1">No. BPJS Ketenagakerjaan</label>
                            <input type="text" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" value="{{ old('bpjs_ketenagakerjaan', $user->bpjs_ketenagakerjaan) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor BPJS Ketenagakerjaan">
                        </div>
                    </div>
                </div>

                {{-- KARTU 5: INFORMASI BANK (PAYROLL) --}}
                 <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Informasi Bank (Payroll)</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="nama_bank" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                            <input type="text" id="nama_bank" name="nama_bank" value="{{ old('nama_bank', $user->nama_bank) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., BCA, Mandiri, BNI">
                        </div>
                        <div>
                            <label for="nomor_rekening" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                            <input type="text" id="nomor_rekening" name="nomor_rekening" value="{{ old('nomor_rekening', $user->nomor_rekening) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor rekening bank">
                        </div>
                        <div class="md:col-span-2">
                            <label for="pemilik_rekening" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                            <input type="text" id="pemilik_rekening" name="pemilik_rekening" value="{{ old('pemilik_rekening', $user->pemilik_rekening) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama lengkap sesuai buku tabungan">
                        </div>
                    </div>
                </div>
                
                {{-- KARTU BARU: RIWAYAT PENDIDIKAN --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Riwayat Pendidikan</h3>
                        <button type="button" id="add-pendidikan-btn" class="bg-white hover:bg-purple-100 text-purple-700 px-3 py-1 rounded-lg text-sm font-medium flex items-center w-full sm:w-auto justify-center">
                            <i class="fas fa-plus mr-2"></i> Tambah
                        </button>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div id="pendidikan-container" class="space-y-6">
                            {{-- Data yang ada dilooping di sini --}}
                            @foreach($user->riwayatPendidikan ?? [] as $index => $pendidikan)
                                <div class="riwayat-item p-4 border border-gray-300 rounded-lg relative">
                                    <input type="hidden" name="pendidikan[{{ $pendidikan->id }}][id]" value="{{ $pendidikan->id }}">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                                            <select name="pendidikan[{{ $pendidikan->id }}][jenjang]" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Pilih --</option>
                                                <option value="SMA/SMK Sederajat" @selected($pendidikan->jenjang == 'SMA/SMK Sederajat')>SMA/SMK Sederajat</option>
                                                <option value="D3" @selected($pendidikan->jenjang == 'D3')>D3</option>
                                                <option value="S1" @selected($pendidikan->jenjang == 'S1')>S1</option>
                                                <option value="S2" @selected($pendidikan->jenjang == 'S2')>S2</option>
                                                <option value="S3" @selected($pendidikan->jenjang == 'S3')>S3</option>
                                                <option value="Lainnya" @selected($pendidikan->jenjang == 'Lainnya')>Lainnya</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Institusi</label>
                                            <input type="text" name="pendidikan[{{ $pendidikan->id }}][nama_institusi]" value="{{ $pendidikan->nama_institusi }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama Sekolah/Universitas">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                            <input type="text" name="pendidikan[{{ $pendidikan->id }}][jurusan]" value="{{ $pendidikan->jurusan }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Ilmu Komputer">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
                                            <input type="text" name="pendidikan[{{ $pendidikan->id }}][tahun_lulus]" value="{{ $pendidikan->tahun_lulus }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2020">
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="delete-riwayat-btn absolute top-2 right-2 text-red-500 hover:text-red-700" title="Hapus Riwayat">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Template untuk item baru --}}
                        <div id="pendidikan-template" class="hidden riwayat-item p-4 border border-gray-300 rounded-lg relative">
                            {{-- PERBAIKAN: Tambahkan 'disabled' di semua field --}}
                            <input type="hidden" name="pendidikan[__NEW_INDEX__][id]" value="" disabled> 
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                                    <select name="pendidikan[__NEW_INDEX__][jenjang]" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                                        <option value="">-- Pilih --</option>
                                        <option value="SMA/SMK Sederajat">SMA/SMK Sederajat</option>
                                        <option value="D3">D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Institusi</label>
                                    <input type="text" name="pendidikan[__NEW_INDEX__][nama_institusi]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama Sekolah/Universitas" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                    <input type="text" name="pendidikan[__NEW_INDEX__][jurusan]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Ilmu Komputer" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
                                    <input type="text" name="pendidikan[__NEW_INDEX__][tahun_lulus]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2020" disabled>
                                </div>
                            </div>
                            
                            <button type="button" class="delete-riwayat-btn absolute top-2 right-2 text-red-500 hover:text-red-700" title="Hapus Riwayat">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- KARTU BARU: RIWAYAT PEKERJAAN --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Riwayat Pekerjaan</h3>
                        <button type="button" id="add-pekerjaan-btn" class="bg-white hover:bg-violet-100 text-violet-700 px-3 py-1 rounded-lg text-sm font-medium flex items-center w-full sm:w-auto justify-center">
                            <i class="fas fa-plus mr-2"></i> Tambah
                        </button>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div id="pekerjaan-container" class="space-y-6">
                            {{-- Data yang ada dilooping di sini --}}
                            @foreach($user->riwayatPekerjaan ?? [] as $index => $pekerjaan)
                                <div class="riwayat-item p-4 border border-gray-300 rounded-lg relative">
                                    <input type="hidden" name="pekerjaan[{{ $pekerjaan->id }}][id]" value="{{ $pekerjaan->id }}">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                                            <input type="text" name="pekerjaan[{{ $pekerjaan->id }}][nama_perusahaan]" value="{{ $pekerjaan->nama_perusahaan }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama perusahaan">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Posisi/Jabatan</label>
                                            <input type="text" name="pekerjaan[{{ $pekerjaan->id }}][posisi]" value="{{ $pekerjaan->posisi }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Posisi terakhir">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                            <input type="date" name="pekerjaan[{{ $pekerjaan->id }}][tanggal_mulai]" value="{{ $pekerjaan->tanggal_mulai ? \Carbon\Carbon::parse($pekerjaan->tanggal_mulai)->format('Y-m-d') : '' }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                            <input type="date" name="pekerjaan[{{ $pekerjaan->id }}][tanggal_selesai]" value="{{ $pekerjaan->tanggal_selesai ? \Carbon\Carbon::parse($pekerjaan->tanggal_selesai)->format('Y-m-d') : '' }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan (Opsional)</label>
                                            <textarea name="pekerjaan[{{ $pekerjaan->id }}][deskripsi_pekerjaan]" rows="2" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ringkasan tugas...">{{ $pekerjaan->deskripsi_pekerjaan }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="delete-riwayat-btn absolute top-2 right-2 text-red-500 hover:text-red-700" title="Hapus Riwayat">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Template untuk item baru --}}
                        <div id="pekerjaan-template" class="hidden riwayat-item p-4 border border-gray-300 rounded-lg relative">
                            {{-- PERBAIKAN: Tambahkan 'disabled' di semua field --}}
                            <input type="hidden" name="pekerjaan[__NEW_INDEX__][id]" value="" disabled>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                                    <input type="text" name="pekerjaan[__NEW_INDEX__][nama_perusahaan]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama perusahaan" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Posisi/Jabatan</label>
                                    <input type="text" name="pekerjaan[__NEW_INDEX__][posisi]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Posisi terakhir" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="date" name="pekerjaan[__NEW_INDEX__][tanggal_mulai]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                    <input type="date" name="pekerjaan[__NEW_INDEX__][tanggal_selesai]" value="" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan (Opsional)</label>
                                    <textarea name="pekerjaan[__NEW_INDEX__][deskripsi_pekerjaan]" rows="2" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ringkasan tugas..." disabled></textarea>
                                </div>
                                    
                            </div>
                            
                            <button type="button" class="delete-riwayat-btn absolute top-2 right-2 text-red-500 hover:text-red-700" title="Hapus Riwayat">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                    </div>
                </div>


                {{-- KARTU 6: KONTAK DARURAT --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Kontak Darurat</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="kontak_darurat_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" id="kontak_darurat_nama" name="kontak_darurat_nama" value="{{ old('kontak_darurat_nama', $user->kontak_darurat_nama) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama kontak darurat">
                        </div>
                        <div>
                            <label for="kontak_darurat_nomor" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="tel" id="kontak_darurat_nomor" name="kontak_darurat_nomor" value="{{ old('kontak_darurat_nomor', $user->kontak_darurat_nomor) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor kontak darurat">
                        </div>
                        <div class="md:col-span-2">
                            <label for="kontak_darurat_hubungan" class="block text-sm font-medium text-gray-700 mb-1">Hubungan</label>
                            <input type="text" id="kontak_darurat_hubungan" name="kontak_darurat_hubungan" value="{{ old('kontak_darurat_hubungan', $user->kontak_darurat_hubungan) }}" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Orang Tua, Pasangan, Saudara">
                        </div>
                    </div>
                </div>

                {{-- KARTU 7: UBAH PASSWORD --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                     <div class="p-4 sm:p-5 border-b bg-blue-600 rounded-t-xl">
                        <h3 class="text-lg font-bold text-white">Ubah Password</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
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
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 gap-3 sm:gap-0 pt-2">
                    <a href="{{ url()->previous() }}" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-200 text-sm font-medium w-full sm:w-auto flex justify-center">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center font-medium shadow-md text-sm w-full sm:w-auto justify-center">
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


            // --- SCRIPT UNTUK RIWAYAT DINAMIS (SUDAH DIPERBAIKI) ---
            function setupDynamicForm(containerId, addButtonId, templateId, deleteButtonClass) {
                const container = document.getElementById(containerId);
                const addButton = document.getElementById(addButtonId);
                const template = document.getElementById(templateId);

                if (!container || !addButton || !template) {
                    return;
                }

                // 1. Logika Tombol TAMBAH
                addButton.addEventListener('click', () => {
                    const newRow = template.cloneNode(true);
                    newRow.id = ''; 
                    newRow.classList.remove('hidden'); 
                    const newIndex = 'new_' + Date.now(); 

                    // --- PERBAIKAN DI SINI ---
                    // Ganti nama dan hapus 'disabled' dari semua field di dalam klon
                    newRow.querySelectorAll('input, select, textarea').forEach(field => {
                        if (field.name) {
                            field.name = field.name.replace(/__NEW_INDEX__/g, newIndex);
                        }
                        // Hapus atribut 'disabled' agar terkirim
                        field.disabled = false;
                    });
                    // --- AKHIR PERBAIKAN ---
                    
                    container.appendChild(newRow);
                });

                // 2. Logika Tombol HAPUS
                container.addEventListener('click', function(e) {
                    const deleteButton = e.target.closest(deleteButtonClass);
                    if (deleteButton) {
                        const itemToRemove = deleteButton.closest('.riwayat-item');
                        if (itemToRemove) {
                            itemToRemove.remove();
                        }
                    }
                });
            }

            // Terapkan fungsi setup untuk Riwayat Pendidikan
            setupDynamicForm(
                'pendidikan-container', 
                'add-pendidikan-btn', 
                'pendidikan-template', 
                '.delete-riwayat-btn'
            );

            // Terapkan fungsi setup untuk Riwayat Pekerjaan
            setupDynamicForm(
                'pekerjaan-container', 
                'add-pekerjaan-btn', 
                'pekerjaan-template', 
                '.delete-riwayat-btn'
            );
        });
    </script>
</x-layout-users>