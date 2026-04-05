<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SkorController extends Controller
{
    /**
     * Kriteria pemotongan skor kehadiran berdasarkan dokumen resmi
     */
    const KRITERIA = [
        'KT1' => ['label' => 'Terlambat hadir sampai dengan 30 (tiga puluh) menit',                                              'persen' => 0.25],
        'KT2' => ['label' => 'Terlambat hadir lebih dari 30 (tiga puluh) menit sampai 60 (enam puluh) menit',                   'persen' => 0.5],
        'KT3' => ['label' => 'Terlambat hadir lebih dari 60 (enam puluh) menit sampai 90 (sembilan puluh) menit',               'persen' => 0.75],
        'KT4' => ['label' => 'Terlambat lebih dari 90 (sembilan puluh) menit',                                                   'persen' => 1],
        'KT5' => ['label' => 'Tidak melakukan absen pulang',                                                                     'persen' => 0.25],
        'KT6' => ['label' => 'Tidak hadir bekerja dan/atau tidak melakukan absensi dalam 1 (satu) hari',                        'persen' => 3],
        'KT7' => ['label' => 'Tidak mengikuti upacara atau apel pagi setiap hari senin yang diperintahkan oleh kepala daerah',  'persen' => 1],
    ];

    public function index(Request $request)
    {
        $bulan   = $request->get('bulan', Carbon::now()->month);
        $tahun   = $request->get('tahun', Carbon::now()->year);
        $userId  = $request->get('user_id');

        $pegawais = User::where('role', 'pegawai')->orderBy('name')->get();
        $pegawai  = $userId ? User::find($userId) : null;
        $skor     = $pegawai ? $this->hitungSkor($pegawai, $bulan, $tahun) : null;
        $pejabatPenilai = User::where('role', 'admin')->first();

        return view('laporan.skor', compact('pegawais', 'pegawai', 'skor', 'bulan', 'tahun', 'pejabatPenilai'));
    }

    public function cetak(Request $request)
    {
        $bulan  = $request->get('bulan', Carbon::now()->month);
        $tahun  = $request->get('tahun', Carbon::now()->year);
        $userId = $request->get('user_id');

        $pegawai = User::findOrFail($userId);
        $skor    = $this->hitungSkor($pegawai, $bulan, $tahun);
        $pejabatPenilai = User::where('role', 'admin')->first();

        return view('laporan.skor-cetak', compact('pegawai', 'skor', 'bulan', 'tahun', 'pejabatPenilai'));
    }

    /**
     * Hitung skor kehadiran pegawai berdasarkan data absensi
     */
    public function hitungSkor(User $pegawai, int $bulan, int $tahun): array
    {
        $absensis = Absensi::with('shift')
            ->where('user_id', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $kt = [
            'KT1' => 0, 'KT2' => 0, 'KT3' => 0, 'KT4' => 0,
            'KT5' => 0, 'KT6' => 0, 'KT7' => 0,
        ];

        foreach ($absensis as $a) {
            // KT6: Alpha / tidak hadir
            if ($a->status === 'alpha') {
                $kt['KT6']++;
                continue;
            }

            // KT5: Ada check-in tapi tidak check-out
            if ($a->check_in && !$a->check_out) {
                $kt['KT5']++;
            }

            // KT1-KT4: Hitung menit terlambat
            if ($a->check_in && $a->shift) {
                $jamMasuk  = Carbon::parse($a->tanggal->format('Y-m-d') . ' ' . $a->shift->jam_masuk);
                $checkIn   = Carbon::parse($a->check_in);
                $menit     = $jamMasuk->diffInMinutes($checkIn, false); // positif = terlambat

                if ($menit > 90) {
                    $kt['KT4']++;
                } elseif ($menit > 60) {
                    $kt['KT3']++;
                } elseif ($menit > 30) {
                    $kt['KT2']++;
                } elseif ($menit > 0) {
                    $kt['KT1']++;
                }
            } elseif ($a->status === 'terlambat' && !$a->shift) {
                // Fallback jika tidak ada shift: anggap KT1
                $kt['KT1']++;
            }
        }

        // Hitung potongan dan total
        $detail = [];
        $totalPotongan = 0;

        foreach (self::KRITERIA as $kode => $info) {
            $kali    = $kt[$kode];
            $jumlah  = round($kali * $info['persen'], 2);
            $totalPotongan += $jumlah;

            $detail[$kode] = [
                'label'  => $info['label'],
                'persen' => $info['persen'],
                'kali'   => $kali,
                'jumlah' => $jumlah,
            ];
        }

        $skorAkhir = round(100 - $totalPotongan, 2);

        return [
            'detail'        => $detail,
            'total_potongan'=> round($totalPotongan, 2),
            'skor_akhir'    => $skorAkhir,
            'total_hadir'   => $absensis->whereIn('status', ['hadir', 'terlambat'])->count(),
            'total_alpha'   => $absensis->where('status', 'alpha')->count(),
            'total_izin'    => $absensis->whereIn('status', ['izin', 'sakit'])->count(),
        ];
    }
}
