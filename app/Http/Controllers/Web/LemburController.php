<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $query = Lembur::with('user')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $lemburs = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('lembur.index', compact('lemburs', 'bulan', 'tahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string',
        ]);

        Lembur::create([
            'user_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function approve(Lembur $lembur)
    {
        $lembur->update(['status' => 'approved']);
        return back()->with('success', 'Pengajuan lembur berhasil disetujui.');
    }

    public function reject(Request $request, Lembur $lembur)
    {
        $request->validate(['catatan_admin' => 'nullable|string|max:255']);
        
        $lembur->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin
        ]);
        
        return back()->with('success', 'Pengajuan lembur berhasil ditolak.');
    }

    public function destroy(Lembur $lembur)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $lembur->user_id !== $user->id) abort(403);
        
        if (!$user->isAdmin() && $lembur->status !== 'pending') {
            return back()->with('error', 'Tidak dapat menghapus pengajuan yang sudah diproses.');
        }

        $lembur->delete();
        return back()->with('success', 'Pengajuan lembur berhasil dihapus.');
    }
}
