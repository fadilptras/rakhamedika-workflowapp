<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Holiday; 
use App\Models\User;
use App\Notifications\HolidayNotification; 
use Illuminate\Support\Facades\Notification;

class SendHolidayNotifications extends Command
{
    // Nama command: php artisan app:send-holiday-info
    protected $signature = 'app:send-holiday-info';
    protected $description = 'Kirim notifikasi informasi hari libur nasional/cuti bersama';

    public function handle()
    {
        $today = now();
        $this->info("Cek Hari Libur {$today->toDateString()}");

        // 1. Cek tabel holidays
        // Pastikan nama kolom tanggal di tabel holidays adalah 'tanggal'
        $holiday = Holiday::whereDate('tanggal', $today)->first();

        if (!$holiday) {
            $this->info('Hari ini bukan hari libur (Masuk Kerja).');
            return;
        }

        $this->info("Hari ini libur: {$holiday->keterangan}");

        // 2. Kirim ke Semua User
        $users = User::all();
        Notification::send($users, new HolidayNotification($holiday));
        
        $this->info("Notifikasi libur dikirim ke " . $users->count() . " karyawan.");
    }
}