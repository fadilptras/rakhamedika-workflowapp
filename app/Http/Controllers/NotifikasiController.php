<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification; // Tambahan untuk fitur kirim
use Carbon\Carbon; // Tambahan untuk tanggal
use App\Models\User; // Tambahan untuk ambil data user
use App\Notifications\BirthdayNotification; // Tambahan untuk class notifikasi

class NotifikasiController extends Controller
{
    /**
     * Menampilkan halaman notifikasi dengan filter.
     */
    public function index(Request $request)
    {
        $title = 'Notifikasi';
        $user = Auth::user();
        
        // Ambil filter tipe dari URL, defaultnya 'semua'
        $filterType = $request->query('type', 'semua');

        // Fungsi helper untuk menentukan tipe notifikasi
        $determineType = function ($notification) {
            $url = $notification->data['url'] ?? '';
            if (Str::contains($url, '/pengajuan-dana/')) return 'Pengajuan Dana';
            if (Str::contains($url, '/agendas')) return 'Agenda';
            if (Str::contains($url, '/cuti/')) return 'Pengajuan Cuti';
            if (Str::contains($url, '/pengajuan-dokumen/')) return 'Pengajuan Dokumen';
            if (Str::contains($url, 'ulang-tahun')) return 'Ulang Tahun'; // Tambahan agar masuk grup
            return 'Lainnya';
        };

        // Ambil notifikasi untuk filter button
        $allNotificationsForTypes = $user->notifications()->latest()->take(100)->get();
        $availableTypes = $allNotificationsForTypes->map($determineType)->unique()->sort()->values()->all();

        // Query dasar notifikasi
        $query = $user->notifications()->latest();

        if ($filterType !== 'semua') {
            // Ambil lebih banyak dulu untuk difilter di collection (karena data['url'] ada di JSON)
            $allNotifications = $query->take(200)->get();
            $filteredNotifications = $allNotifications->filter(function ($notification) use ($determineType, $filterType) {
                return $determineType($notification) === $filterType;
            });
            $notificationsToGroup = $filteredNotifications->take(50);
        } else {
            $notificationsToGroup = $query->take(50)->get();
        }
        
        // Tandai terbaca
        $unreadIds = $notificationsToGroup->whereNull('read_at')->pluck('id');
        if ($unreadIds->isNotEmpty()) {
            $user->notifications()->whereIn('id', $unreadIds)->update(['read_at' => now()]);
            // Refresh data
             $notificationsToGroup = $user->notifications()->whereIn('id', $notificationsToGroup->pluck('id'))->latest()->get();
        }

        // Kelompokkan
        $groupedNotifications = $notificationsToGroup->groupBy($determineType);

        // Tentukan urutan grup (Tambahkan Ulang Tahun di sini jika mau)
        $groupOrder = [
            'Ulang Tahun', // Prioritas paling atas biar kelihatan
            'Pengajuan Dana',
            'Agenda',
            'Pengajuan Cuti',
            'Pengajuan Dokumen',
            'Lainnya',
        ];

        return view('users.notifikasi', compact(
            'groupedNotifications', 
            'groupOrder', 
            'title', 
            'availableTypes',
            'filterType'
        ));
    }

    /**
     * Fitur Manual: Kirim Notifikasi Ulang Tahun (Triggered by Admin/Route)
     */
    public function kirimUlangTahun()
    {
        // Hanya admin yang boleh akses (bisa juga via middleware di route)
        if (Auth::user()->role !== 'admin') { // Sesuaikan dengan logika role Anda
            abort(403, 'Hanya admin yang bisa mengirim notifikasi ini.');
        }

        $today = Carbon::now();

        // 1. Cari user yang ulang tahun hari ini
        $birthdayUsers = User::whereMonth('tanggal_lahir', $today->month)
                             ->whereDay('tanggal_lahir', $today->day)
                             ->get();

        if ($birthdayUsers->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada karyawan yang ulang tahun hari ini.');
        }

        // 2. Ambil semua user aktif untuk dikirimi notifikasi
        $allUsers = User::all();

        // 3. Kirim notifikasi loop
        $count = 0;
        foreach ($birthdayUsers as $birthdayPerson) {
            Notification::send($allUsers, new BirthdayNotification($birthdayPerson));
            $count++;
        }

        return redirect()->back()->with('success', "Berhasil mengirim notifikasi ulang tahun untuk $count orang!");
    }
}