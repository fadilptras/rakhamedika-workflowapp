<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\BirthdayNotification;
use Illuminate\Support\Facades\Notification;

class SendBirthdayNotifications extends Command
{
    /**
     * Nama perintah yang nanti dipanggil scheduler atau via terminal:
     * php artisan app:send-birthday-notifications
     */
    protected $signature = 'app:send-birthday-notifications';
    
    protected $description = 'Kirim notifikasi ulang tahun (ucapan ke ybs & info ke rekan kerja)';

    public function handle()
    {
        $today = now();

        // 1. Cari User yang ulang tahun HARI INI
        // whereMonth dan whereDay otomatis mengambil bulan & tanggal dari format 'YYYY-MM-DD'
        $birthdayUsers = User::whereMonth('tanggal_lahir', $today->month)
                             ->whereDay('tanggal_lahir', $today->day)
                             ->get();

        if ($birthdayUsers->isEmpty()) {
            $this->info('Tidak ada yang ulang tahun hari ini.');
            return;
        }

        // 2. Loop setiap orang yang ulang tahun hari ini (bisa jadi lebih dari 1 orang)
        foreach ($birthdayUsers as $birthdayPerson) {
            
            $this->info("Memproses ulang tahun: {$birthdayPerson->name}");

            // --- A. Kirim Notifikasi ke REKAN KERJA (Semua user KECUALI yang ultah) ---
            $colleagues = User::where('id', '!=', $birthdayPerson->id)->get();
            
            if ($colleagues->isNotEmpty()) {
                // Gunakan Facade Notification untuk kirim ke banyak user sekaligus
                Notification::send($colleagues, new BirthdayNotification($birthdayPerson));
                $this->info(" - Info terkirim ke " . $colleagues->count() . " rekan kerja.");
            }

            // --- B. Kirim Notifikasi ke USER YANG ULANG TAHUN (Ucapan Selamat) ---
            // Kita panggil method notify() langsung dari model User ybs
            $birthdayPerson->notify(new BirthdayNotification($birthdayPerson));
            $this->info(" - Ucapan selamat terkirim ke {$birthdayPerson->name}.");
        }
        
        $this->info('Selesai mengirim semua notifikasi ulang tahun.');
    }
}