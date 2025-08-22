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
  </style>
</head>
<body class="bg-gray-200 min-h-screen flex items-center justify-center">

 <div class="flex w-full max-w-3xl rounded-lg shadow-lg bg-white overflow-hidden">
    <div class="hidden lg:flex w-2/5 bg-gradient-to-br from-blue-500 to-blue-600 items-center justify-center p-4">
        <div class="text-center max-w-sm">
          <img src="{{ asset('asset/images/ilustrasi1.png') }}" alt="Illustration"/>
          <h2 class="text-left text-2xl font-bold text-white mt-20 mx-2">Satu Langkah Lagi Untuk Bergabung</h2>
        </div>
    </div>

    <div class="w-full lg:w-3/5 flex items-center justify-center p-4 sm:p-8">
      <div class="w-full max-w-sm space-y-6 text-center">
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900">Pendaftaran Akun</h1>
          <p class="mt-4 text-gray-600">Untuk mendaftarkan akun baru, silakan hubungi Administrator melalui salah satu kontak di bawah ini:</p>
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
                    <a href="https://wa.me/6281234567890" target="_blank" class="text-blue-600 hover:underline">0812-3456-7890</a>
                </li>
            </ul>
        </div>

        <div class="pt-4">
            <a href="{{ route('login') }}" class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                Kembali ke Halaman Login
            </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>