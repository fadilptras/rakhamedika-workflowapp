<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminAgendaController extends Controller
{
    /**
     * Menampilkan halaman utama agenda untuk admin.
     * Mengambil SEMUA AGENDA untuk ditampilkan di daftar.
     */
    public function index()
    {
        $allAgendas = Agenda::with('creator')->orderBy('start_time', 'desc')->get();
        return view('admin.agenda.index', [
            'title' => 'Kelola Agenda',
            'allAgendas' => $allAgendas
        ]);
    }

    /**
     * Menyediakan data SEMUA agenda dalam format JSON untuk di-fetch oleh kalender.
     */
    public function getAdminAgendas()
    {
        // Ambil semua agenda dari semua user
        $agendas = Agenda::with('creator')->get();

        $events = $agendas->map(function($agenda) {
            // Pastikan creator ada untuk menghindari error
            if ($agenda->creator) {
                return [
                    'title' => $agenda->title,
                    'start' => $agenda->start_time,
                    'end' => $agenda->end_time,
                    'backgroundColor' => $agenda->color,
                    'borderColor' => $agenda->color,
                ];
            }
            return null;
        })->filter(); // Hapus item null jika ada creator yang terhapus

        return response()->json($events);
    }

    /**
     * Menyimpan agenda baru yang dibuat oleh admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'guests' => 'nullable|array',
            'guests.*' => 'exists:users,id',
        ]);

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addHour();

        $agenda = Agenda::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_time' => $validated['start_time'],
            'end_time' => $endTime,
            'location' => $validated['location'],
            'color' => $validated['color'],
            'user_id' => Auth::id(),
        ]);

        if (!empty($validated['guests'])) {
            $agenda->guests()->sync($validated['guests']);
        }

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda berhasil dibuat!');
    }

    /**
     * Menyediakan daftar semua user (karyawan) untuk form.
     */
    public function getAllUsers()
    {
        $users = User::where('role', 'user')
                     ->select('id', 'name')
                     ->orderBy('name', 'asc')
                     ->get();
        return response()->json($users);
    }
}