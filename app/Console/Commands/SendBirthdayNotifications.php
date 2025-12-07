<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\BirthdayNotification;
use Illuminate\Support\Facades\Notification;

class SendBirthdayNotifications extends Command
{
    // Nama perintah yang nanti dipanggil scheduler
    protected $signature = 'app:send-birthday-notifications';
    protected $description = 'Kirim notifikasi ulang tahun karyawan';

    public function handle()
    {
        $today = now();

        // 1. Cari User yang ulang tahun HARI INI
        // Asumsi nama kolom di database Anda 'tgl_lahir' atau 'birth_date'
        $birthdayUsers = User::whereMonth('tanggal_lahir', $today->month)
                             ->whereDay('tanggal_lahir', $today->day)
                             ->get();

        if ($birthdayUsers->isEmpty()) {
            $this->info('Tidak ada yang ulang tahun hari ini.');
            return;
        }

        // 2. Loop setiap orang yang ulang tahun
        foreach ($birthdayUsers as $birthdayPerson) {
            
            // 3. Kirim notifikasi ke SEMUA User KECUALI yang sedang ulang tahun
            // (Agar yang ultah tidak dapat notif "Jangan lupa ucapkan selamat ke diri sendiri")
            $recipients = User::where('id', '!=', $birthdayPerson->id)->get();

            // Panggil file Notification yang Anda buat tadi
            Notification::send($recipients, new BirthdayNotification($birthdayPerson));
            
            $this->info("Notifikasi ultah {$birthdayPerson->name} berhasil dikirim.");
        }
    }
}