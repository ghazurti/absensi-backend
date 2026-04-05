<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Izin::with('user');
        if (!$user->isAdmin()) $query->where('user_id', $user->id);
        $izins = $query->orderBy('tanggal_mulai', 'desc')->paginate(15);
        return view('izin.index', compact('izins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis' => 'required|in:izin,sakit,cuti',
            'keterangan' => 'required|string',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['tanggal_mulai', 'tanggal_selesai', 'jenis', 'keterangan']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('file_pendukung')) {
            $data['file_pendukung'] = $request->file('file_pendukung')->store('izin-files', 'public');
        }

        Izin::create($data);
        return back()->with('success', 'Pengajuan izin berhasil disimpan.');
    }

    public function destroy(Izin $izin)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $izin->user_id !== $user->id) abort(403);
        
        // Cegah penghapusan jika sudah disetujui/ditolak (untuk pegawai)
        if (!$user->isAdmin() && $izin->status !== 'pending') {
            return back()->with('error', 'Tidak dapat menghapus pengajuan yang sudah diproses.');
        }

        $izin->delete();
        return back()->with('success', 'Data izin berhasil dihapus.');
    }

    public function approve(Izin $izin)
    {
        $izin->update(['status' => 'approved']);
        return back()->with('success', 'Pengajuan izin berhasil disetujui.');
    }

    public function reject(Request $request, Izin $izin)
    {
        $request->validate(['catatan_admin' => 'nullable|string|max:255']);
        $izin->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin
        ]);
        return back()->with('success', 'Pengajuan izin berhasil ditolak.');
    }
}
