importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

// Konfigurasi Firebase Projek Kamu
const firebaseConfig = {
    apiKey: "AIzaSyAipKu1aZwvZaOFR_FbCtkD6jtPYI2e4XE",
    authDomain: "rakha-workflow.firebaseapp.com",
    projectId: "rakha-workflow",
    storageBucket: "rakha-workflow.firebasestorage.app",
    messagingSenderId: "1024207088181",
    appId: "1:1024207088181:web:cc835edf846ac65cf59f7c",
    measurementId: "G-3T6QNML81B"
};

// Inisialisasi di Background
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Handler Pesan saat Browser di Background (Tab Tertutup/Minimize)
messaging.onBackgroundMessage((payload) => {
    console.log('[SW] Pesan Background diterima:', payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/asset/images/logo-192x192.png', // Pastikan logo ini ada
        data: payload.data // Menyimpan data URL click_action
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Event Klik Notifikasi
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    // Buka URL jika ada di data click_action
    if (event.notification.data && event.notification.data.click_action) {
        event.waitUntil(
            clients.openWindow(event.notification.data.click_action)
        );
    }
});