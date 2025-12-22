<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Notifications\CutiNotification;

class WhatsAppChannel
{
    /**
     * Mengirim pesan WhatsApp via Fonnte
     */
    public function send($notifiable, $notification)
    {
        // 1. Cek apakah notifikasi punya method 'toWhatsApp'
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // 2. Ambil data pesan dari notifikasi
        $data = $notification->toWhatsApp($notifiable);
        
        // 3. Ambil nomor telepon user
        // Pastikan kolom di database users adalah 'nomor_telepon'
        $rawPhone = $notifiable->nomor_telepon; 

        if (empty($rawPhone)) {
            Log::warning("WA Skip: User {$notifiable->name} tidak memiliki nomor telepon.");
            return;
        }

        // 4. Bersihkan Format Nomor HP (Hapus spasi, strip, +)
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);

        // Ubah 08xxx menjadi 628xxx
        if (substr($cleanPhone, 0, 2) === '08') {
            $target = '62' . substr($cleanPhone, 1);
        } elseif (substr($cleanPhone, 0, 2) === '62') {
            $target = $cleanPhone;
        } else {
            $target = $cleanPhone; // Kirim apa adanya jika format lain
        }

        // 5. Kirim Request ke API Fonnte
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Nr83eEgjuodLSc8deiDG', // PASTIKAN TOKEN INI BENAR
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $data['message'],
                'countryCode' => '62', 
            ]);

            // Cek status response
            if ($response->successful()) {
                Log::info("WA Terkirim ke {$target}");
            } else {
                Log::error("WA Gagal (Fonnte): " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("WA Error Koneksi: " . $e->getMessage());
        }
    }
}