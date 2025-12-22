<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    
    <meta name="theme-color" content="#2563eb"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')

    <style>
        #sidebar { transition: transform 0.3s ease-in-out; }
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-gray-100 font-sans bg-gradient-to-br from-sky-50 to-blue-100 flex flex-col min-h-screen overflow-x-hidden">

    {{-- Overlay Sidebar --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    {{-- Sidebar (Z-Index 50) --}}
    <div id="sidebar" class="bg-blue-600 text-white h-full flex flex-col w-20 fixed top-0 left-0 z-50 transform -translate-x-full">
        <div class="p-4 border-b border-blue-500 flex items-center justify-center h-[68px]">
            <i class="fas fa-clinic-medical text-3xl"></i>
        </div>
        <div class="flex-grow overflow-y-auto">
            <nav class="p-4 pt-6">
                <ul class="space-y-4">
                    <li><a href="{{ route('dashboard') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50"><i class="fas fa-th-large text-xl"></i></a></li>
                    <li><a href="{{ route('pengajuan_dana.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50"><i class="fas fa-coins text-xl"></i></a></li>
                    <li><a href="{{ route('pengajuan_barang.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50"><i class="fas fa-box-open text-xl"></i></a></li>
                    <li><a href="{{ route('crm.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50"><i class="fas fa-users text-xl"></i></a></li>
                </ul>
            </nav>
        </div>
        <div class="p-4 border-t border-blue-500 space-y-4">
            <a href="{{ route('notifikasi.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50"><i class="fas fa-envelope-open-text text-xl"></i></a>
            <a href="{{ route('profil.index') }}" class="flex items-center justify-center p-3 rounded-lg hover:bg-blue-700/50"><i class="fas fa-user-cog text-xl"></i></a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center p-3 rounded-lg hover:bg-red-500"><i class="fas fa-sign-out-alt text-xl"></i></button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex flex-col min-h-screen relative">
        {{-- Navbar --}}
        <header class="bg-gradient-to-r from-blue-700 to-blue-600 shadow-lg sticky top-0 z-20 text-white shrink-0">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="mr-4 p-2 rounded-md hover:bg-blue-800/50 focus:outline-none"><i class="fas fa-bars text-xl"></i></button>
                        <div class="flex items-center">
                            <img src="{{ asset('asset/images/logorakha.png') }}" alt="Logo" class="h-10 w-10 mr-3">
                            <div>
                                <h1 class="text-base sm:text-lg font-bold leading-tight">PT RAKHA NUSANTARA MEDIKA</h1>
                                <p class="text-sm text-blue-200 font-semibold leading-tight">{{ $title }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 p-6 relative z-0">
            {{ $slot }}
        </main>
    </div>

    {{-- Script Sidebar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
            toggleButton.addEventListener('click', function (e) { e.stopPropagation(); toggleSidebar(); });
            overlay.addEventListener('click', function () { toggleSidebar(); });
        });
    </script>
    
    @stack('modals')
    @stack('scripts')

    {{-- =============== [MULAI] SCRIPT FIREBASE NOTIFIKASI (DIPERBAIKI) =============== --}}
    {{-- Menggunakan Versi 8.10.0 (Compat) agar sesuai dengan script import --}}
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyAipKu1aZwvZaOFR_FbCtkD6jtPYI2e4XE",
            authDomain: "rakha-workflow.firebaseapp.com",
            projectId: "rakha-workflow",
            storageBucket: "rakha-workflow.firebasestorage.app",
            messagingSenderId: "1024207088181",
            appId: "1:1024207088181:web:cc835edf846ac65cf59f7c",
            measurementId: "G-3T6QNML81B"
        };

        // Inisialisasi Firebase
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const messaging = firebase.messaging();

        function initFirebase() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    console.log('Notifikasi diizinkan.');
                    
                    // PENTING: Isi VAPID Key dari Firebase Console > Cloud Messaging > Web Push certs
                    return messaging.getToken({ 
                        vapidKey: 'BOK-BCvGay-ZsQG-Gr1XmrHwvMJhwoU8J758XEEkBiLMuk1gva2z21pN03afQYtj7xsCAh-8Dv4j68R89mbwjr0' 
                    });
                } else {
                    console.warn('Izin notifikasi ditolak.');
                    return null;
                }
            }).then((currentToken) => {
                if (currentToken) {
                    console.log('Token FCM didapat:', currentToken);
                    saveTokenToServer(currentToken);
                } else {
                    console.log('Tidak ada token instance ID yang tersedia. Minta izin untuk generate.');
                }
            }).catch((err) => {
                console.error('Terjadi error saat mengambil token:', err);
            });
        }

        function saveTokenToServer(token) {
            // Mengambil CSRF token dari meta tag
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            if (!csrfToken) {
                console.error('CSRF Token tidak ditemukan di meta tag!');
                return;
            }

            fetch("{{ route('fcm.update') }}", { // Menggunakan route name agar lebih aman
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ fcm_token: token })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => console.log('Token berhasil disimpan ke DB:', data))
            .catch(err => console.error('Gagal simpan token ke server:', err));
        }

        messaging.onMessage((payload) => {
            console.log('Pesan masuk (Foreground):', payload);
            const { title, body } = payload.notification;
            alert(`🔔 ${title}\n${body}`);
        });

        initFirebase();
    </script>
    {{-- =============== [SELESAI] SCRIPT FIREBASE NOTIFIKASI =============== --}}

</body>
</html>