<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        if ($user->isAdmin()) {
            return $this->adminDashboard($today, $bulan, $tahun);
        }

        return $this->pegawaiDashboard($user, $today, $bulan, $tahun);
    }

    private function adminDashboard($today, $bulan, $tahun)
    {
        $totalPegawai = User::where('role', 'pegawai')->count();
        $hadirHariIni = Absensi::whereDate('tanggal', $today)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
        $terlambatHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->count();
        $absenHariIni = $totalPegawai - Absensi::whereDate('tanggal', $today)->count();

        $rekapBulan = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'total_pegawai' => $totalPegawai,
            'hadir_hari_ini' => $hadirHariIni,
            'terlambat_hari_ini' => $terlambatHariIni,
            'belum_absen_hari_ini' => $absenHariIni,
            'rekap_bulan' => $rekapBulan,
        ]);
    }

    private function pegawaiDashboard($user, $today, $bulan, $tahun)
    {
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $rekapBulan = [
            'hadir' => Absensi::where('user_id', $user->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'hadir')->count(),
            'terlambat' => Absensi::where('user_id', $user->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'terlambat')->count(),
            'izin' => Absensi::where('user_id', $user->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'izin')->count(),
            'alpha' => Absensi::where('user_id', $user->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'alpha')->count(),
        ];

        return response()->json([
            'absensi_hari_ini' => $absensiHariIni,
            'rekap_bulan' => $rekapBulan,
        ]);
    }
}
