<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prosedur Pendaftaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .animated-image {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

 <div class="flex w-full max-w-4xl rounded-lg shadow-lg bg-white overflow-hidden">
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-blue-500 to-blue-600 items-center justify-center p-8">
        <div class="text-center">
          <img 
            src="{{ asset('asset/images/ilustrasi1.png') }}"
            alt="Ilustrasi login" 
            class="w-full max-w-xs mx-auto animated-image"
          />
          <h2 class="text-2xl font-bold text-right text-white mt-12">Satu Langkah Lagi Untuk Bergabung</h2>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8 md:p-12">
      <div class="w-full max-w-md">
        <div class="text-center lg:text-left mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900">Pendaftaran Akun</h1>
          <p class="mt-2 text-gray-600">Hubungi Admin untuk mendaftarkan akun baru.</p>
        </div>

        <div class="text-left bg-gray-50 p-4 rounded-lg border">
            <p class="font-semibold text-gray-800">Kontak Admin:</p>
            <ul class="list-disc list-inside mt-2 space-y-2 text-gray-700">
                <li>
                    <strong>Email:</strong> 
                    <a href="mailto:admin@proyekanda.com" class="text-blue-600 hover:underline">admin@proyekanda.com</a>
                </li>
                <li>
                    <strong>WhatsApp:</strong> 
                    <a href="https://wa.me/6281572496312" target="_blank" class="text-blue-600 hover:underline">0815-7249-6312</a>
                </li>
            </ul>
        </div>

        <div class="pt-4">
            <a href="{{ route('login') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 transition">
                Kembali ke Halaman Login
            </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>