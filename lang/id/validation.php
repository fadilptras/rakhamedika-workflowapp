<?php

return [
    // ... pesan-pesan validasi lainnya ...

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Baris berikut digunakan untuk menukar placeholder atribut kita
    | dengan sesuatu yang lebih mudah dibaca seperti "Alamat E-Mail"
    | daripada "email". Ini membantu kita membuat pesan lebih ekspresif.
    |
    */

    'attributes' => [
        'judul_pengajuan' => 'Judul Pengajuan',
        'no_rekening' => 'Nomor Rekening',
        'file_pendukung.*' => 'File Lampiran', // <-- TAMBAHKAN INI
        'file_pendukung.0' => 'File Lampiran', // <-- Ini juga boleh ditambahkan
        'rincian_deskripsi.*' => 'Deskripsi Rincian',
        'rincian_jumlah.*' => 'Jumlah Rincian',
    ],

];