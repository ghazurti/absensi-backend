<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KoreksiAbsensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KoreksiAbsensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = KoreksiAbsensi::with('user');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $koreksis = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('koreksi.index', compact('koreksis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'alasan' => 'required|string',
        ]);

        KoreksiAbsensi::create([
            'user_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
            'alasan' => $request->alasan,
        ]);

        return back()->with('success', 'Pengajuan koreksi berhasil dikirim.');
    }

    public function approve(Request $request, KoreksiAbsensi $koreksi)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $koreksi->update(['status' => 'approved', 'catatan_admin' => $request->catatan_admin]);

        // Update atau Buat data Absensi asli
        $tanggalStr = \Carbon\Carbon::parse($koreksi->tanggal)->format('Y-m-d');
        $absensi = Absensi::updateOrCreate(
            ['user_id' => $koreksi->user_id, 'tanggal' => $tanggalStr],
            [
                'check_in' => $koreksi->jam_masuk ? Carbon::parse($tanggalStr . ' ' . $koreksi->jam_masuk) : null,
                'check_out' => $koreksi->jam_keluar ? Carbon::parse($tanggalStr . ' ' . $koreksi->jam_keluar) : null,
                'status' => 'hadir', // Koreksi biasanya dianggap hadir normal
            ]
        );

        return back()->with('success', 'Koreksi disetujui dan data absensi telah diperbarui.');
    }

    public function reject(Request $request, KoreksiAbsensi $koreksi)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $request->validate(['catatan_admin' => 'required']);

        $koreksi->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin
        ]);

        return back()->with('success', 'Pengajuan koreksi ditolak.');
    }

    public function destroy(KoreksiAbsensi $koreksi)
    {
        if ($koreksi->status !== 'pending') {
            return back()->with('error', 'Tidak dapat menghapus pengajuan yang sudah diproses.');
        }
        $koreksi->delete();
        return back()->with('success', 'Pengajuan koreksi dihapus.');
    }
}
