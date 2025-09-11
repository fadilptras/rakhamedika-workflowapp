<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $title = 'Notifikasi';
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        
        // Tandai semua notifikasi yang belum dibaca sebagai sudah dibaca
        $user->unreadNotifications->markAsRead();

        return view('users.notifikasi', compact('notifications', 'title'));
    }
}