<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In</title>

    <meta name="theme-color" content="#ffffff">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      /* Background gambar dihapus dari sini */
    }
    .animated-image {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }
  </style>
</head>
{{-- --- PERUBAHAN BACKGROUND KEMBALI KE ABU-ABU --- --}}
<body class="bg-gray-100">

  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="flex w-full max-w-4xl rounded-lg shadow-lg bg-white overflow-hidden">
      
      <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-blue-700 to-indigo-900 items-center justify-center p-8">
        <div class="text-center">
          <img 
            src="{{ asset('asset/images/ilustrasi1.png') }}"
            alt="Ilustrasi login" 
            class="w-full max-w-xs mx-auto animated-image"
          />
          <h2 class="text-2xl font-bold text-left text-white mt-4">Selamat Datang Kembali</h2>
          <p class="text-blue-100 mt-0 text-left">Masuk untuk mengakses Sistem Workflow Rakha.</p>
        </div>
      </div>

      <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8 md:p-12">
        <div class="w-full max-w-md">
          <div class="text-center lg:text-left mb-8">
              <h1 class="text-3xl font-extrabold text-gray-900">Sign In</h1>
              <p class="mt-2 text-gray-600">Silakan masuk untuk melanjutkan</p>
          </div>

          @if ($errors->any())
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm rounded" role="alert">
                <p class="font-bold">Login Gagal</p>
                <p>{{ $errors->first('email') ?: $errors->first('password') }}</p>
            </div>
          @endif

          <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input 
                id="email" 
                name="email" 
                type="email" 
                required 
                placeholder="email@anda.com"
                autocomplete="username"
                class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                value="{{ old('email') }}" />
            </div>
            
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <div class="relative">
                <input 
                  id="password" 
                  name="password" 
                  type="password" 
                  required 
                  placeholder="Masukkan password"
                  autocomplete="current-password"
                  class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-10"
                />
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                  <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  </svg>
                </button>
              </div>
            </div>

            <div class="flex items-center justify-between text-sm">
              <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-gray-700">Ingat Saya</label>
              </div>
              <div>
                <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:underline">Lupa Password?</a>
              </div>
            </div>

            <div>
              {{-- --- PERUBAHAN WARNA TOMBOL --- --}}
              <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-700 to-indigo-900 hover:from-blue-800 hover:to-indigo-950 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                Masuk
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      const eyeOpenPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
      const eyeClosedPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65" />';

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = eyeClosedPath;
      } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = eyeOpenPath;
      }
    }
    document.getElementById('eyeIcon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
  </script>
</body>
</html>