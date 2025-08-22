<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign In</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
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
<body class="bg-[#ffffff] min-h-screen">
  
 <div class="flex items-center justify-center min-h-screen bg-gray-200">
    <div class="flex w-full max-w-3xl rounded-lg shadow-lg bg-white overflow-hidden">
      <div class="hidden lg:flex w-2/5 bg-gradient-to-br from-blue-500 to-blue-600 items-center justify-center p-4">
        <div class="text-center max-w-sm">
          <img 
            src="{{ asset('asset/images/ilustrasi1.png') }}"
            alt="Illustration of a person unlocking access" 
          />
          <h2 class="text-left text-2xl font-bold text-white mt-20 mx-2">Selamat datang kembali</h2>
        </div>
      </div>

      <div class="w-full lg:w-3/5 flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-sm space-y-8">
          <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Sign In</h1>
            <p class="mt-6 text-gray-600">Silakan masuk untuk melanjutkan</p>
          </div>

          <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
            @csrf @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm" role="alert">
                    <p class="font-bold">Login Gagal</p>
                    <p>{{ $errors->first('email') ?: $errors->first('password') }}</p>
                </div>
            @endif

            <div class="space-y-4">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                  id="email" 
                  name="email" 
                  type="email" 
                  required 
                  placeholder="Enter your email"
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
                    placeholder="Enter your password"
                    class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-10"
                  />
                  <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat Saya</label>
              </div>
              <div class="text-sm">
                <a href="{{ route('password.request') }} " ... class="text-blue-600 hover:underline">Lupa Password?</a>
              </div>
            </div>

            <div>
              <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                Masuk
              </button>
            </div>
          </form>
          <div class="text-center text-sm text-gray-600">
            <p>Belum memiliki akun? <a href="{{ route('register') }}" ... class="text-blue-600 hover:underline">Buat akun disini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = 
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.393m1.772-1.772A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-3.572 4.633M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' +
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />';
      } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = 
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' +
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
      }
    }
  </script>
</body>
</html>