<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FingerprintController extends Controller
{
    /**
     * Pendaftaran sidik jari pegawai (Enrollment)
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint_data' => 'required|string',
            'fingerprint_id' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);
        
        $user->update([
            'fingerprint_data' => $request->fingerprint_data,
            'fingerprint_id' => $request->fingerprint_id,
        ]);

        return response()->json([
            'message' => 'Sidik jari berhasil didaftarkan untuk ' . $user->name,
            'user' => $user
        ]);
    }

    /**
     * Absensi menggunakan sidik jari (Verification)
     */
    public function attendance(Request $request)
    {
        $request->validate([
            'fingerprint_data' => 'required|string',
        ]);

        // Cari user berdasarkan data sidik jari
        // Catatan: Jika pencocokan dilakukan di server (1:N), kita cari di DB.
        // Jika pencocokan dilakukan di alat, alat biasanya mengirimkan ID User.
        $user = User::where('fingerprint_data', $request->fingerprint_data)->first();

        if (!$user) {
            return response()->json(['message' => 'Sidik jari tidak dikenali'], 404);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Cari data absensi hari ini
        $absensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if ($absensi && $absensi->check_in && $absensi->check_out) {
            return response()->json(['message' => 'Anda sudah melakukan check-in dan check-out hari ini'], 422);
        }

        if (!$absensi || !$absensi->check_in) {
            // Proses Check-in
            return $this->handleCheckIn($user, $today, $now);
        } else {
            // Proses Check-out
            return $this->handleCheckOut($absensi, $now);
        }
    }

    private function handleCheckIn(User $user, $today, $now)
    {
        // Tentukan shift (cari yang paling dekat atau aktif)
        $shift = Shift::where('id', $user->shift_id)->first(); // Asumsi user punya default shift_id
        
        $status = 'hadir';
        if ($shift) {
            $jamMasuk = Carbon::parse($today . ' ' . $shift->jam_masuk);
            if ($now->gt($jamMasuk->addMinutes(15))) {
                $status = 'terlambat';
            }
        }

        $absensi = Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            [
                'shift_id' => $shift ? $shift->id : null,
                'check_in' => $now,
                'status' => $status,
                // Kita kosongkan foto dan koordinat karena menggunakan mesin sidik jari di lokasi tetap
            ]
        );

        return response()->json([
            'message' => 'Check-in sidik jari berhasil',
            'user' => $user->name,
            'time' => $now->format('H:i:s'),
            'status' => $status
        ], 201);
    }

    private function handleCheckOut($absensi, $now)
    {
        $absensi->update([
            'check_out' => $now,
        ]);

        return response()->json([
            'message' => 'Check-out sidik jari berhasil',
            'user' => $absensi->user->name,
            'time' => $now->format('H:i:s')
        ]);
    }
}
