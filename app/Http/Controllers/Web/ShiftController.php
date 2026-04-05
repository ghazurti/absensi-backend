<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $query = Shift::with('user')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $shifts = $query->orderBy('tanggal')->get();
        return view('shift.index', compact('shifts', 'bulan', 'tahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_shift' => 'required|in:pagi,siang,malam',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'keterangan' => 'nullable|string',
        ]);

        $user = auth()->user();

        $existing = Shift::where('user_id', $user->id)->where('tanggal', $request->tanggal)->first();
        if ($existing) {
            return back()->with('error', 'Shift untuk tanggal ini sudah ada.');
        }

        Shift::create([
            'user_id' => $user->id,
            'tanggal' => $request->tanggal,
            'jenis_shift' => $request->jenis_shift,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Shift berhasil ditambahkan.');
    }

    public function destroy(Shift $shift)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $shift->user_id !== $user->id) {
            abort(403);
        }
        $shift->delete();
        return back()->with('success', 'Shift berhasil dihapus.');
    }
}
