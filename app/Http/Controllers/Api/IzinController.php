<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Izin::with('user');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)
                  ->whereYear('tanggal_mulai', $request->tahun);
        }

        return response()->json($query->orderBy('tanggal_mulai', 'desc')->get());
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

        $izin = Izin::create($data);

        return response()->json(['message' => 'Izin berhasil diajukan', 'izin' => $izin], 201);
    }

    public function show(string $id)
    {
        $izin = Izin::with('user')->findOrFail($id);
        return response()->json($izin);
    }

    public function destroy(string $id)
    {
        $izin = Izin::findOrFail($id);
        $user = auth()->user();

        if (!$user->isAdmin() && $izin->user_id !== $user->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $izin->delete();
        return response()->json(['message' => 'Izin berhasil dihapus']);
    }
}
