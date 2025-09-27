<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Pastikan untuk mengimpor Carbon

class AgendaController extends Controller
{
    /**
     * Menampilkan semua agenda untuk FullCalendar.
     * Mengambil agenda yang dibuat oleh user ATAU di mana user diundang.
     */
    public function index()
    {
        $user = Auth::user();

        $agendas = Agenda::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('guests', function ($subQuery) use ($user) {
                      $subQuery->where('user_id', $user->id);
                  });
        })
        ->with('creator') 
        ->get();

        $events = [];
        foreach ($agendas as $agenda) {
            if ($agenda->creator) {
                $events[] = [
                    // PERUBAHAN 1: Judul untuk FullCalendar kita potong pendek
                    'title' => \Illuminate\Support\Str::limit($agenda->title, 15), 
                    
                    'start' => $agenda->start_time,
                    'end' => $agenda->end_time,
                    'backgroundColor' => $agenda->color,
                    'borderColor' => $agenda->color,
                    'extendedProps' => [
                        // PERUBAHAN 2: Kita simpan judul asli yang panjang di sini
                        'fullTitle' => $agenda->title, 
                        
                        'location' => $agenda->location,
                        'organizer' => $agenda->creator->name, 
                    ]
                ];
            }
        }

        return response()->json($events);
    }

    /**
     * Menyimpan agenda baru dari form modal.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'guests' => 'nullable|array',
            'guests.*' => 'exists:users,id',
        ]);

        // Mengambil start_time dan mengubahnya menjadi objek Carbon
        $startTime = Carbon::parse($validated['start_time']);

        // Membuat end_time secara otomatis (1 jam setelah start_time)
        $endTime = $startTime->copy()->addHour();

        // Membuat agenda baru dengan semua data yang dibutuhkan
        $agenda = Agenda::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_time' => $validated['start_time'],
            'end_time' => $endTime, // Menyertakan end_time yang sudah dihitung
            'location' => $validated['location'],
            'color' => $validated['color'],
            'user_id' => Auth::id(), // ID user yang sedang login
        ]);

        // Jika ada tamu yang diundang, lampirkan ke agenda
        if (!empty($validated['guests'])) {
            $agenda->guests()->sync($validated['guests']);
        }

        return response()->json(['message' => 'Agenda berhasil dibuat!']);
    }

    /**
     * Menyediakan daftar user untuk form.
     */
    public function getUsers()
    {
        // Ambil semua user kecuali diri sendiri, hanya id dan nama
        $users = User::where('id', '!=', Auth::id())
                     ->select('id', 'name')
                     ->orderBy('name', 'asc')
                     ->get();
        return response()->json($users);
    }
}