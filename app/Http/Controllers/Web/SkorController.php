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
        'KT1' => ['label' => 'Terlambat hadir / Pulang sebelum waktunya (1 - 30 menit)',                                        'persen' => 0.25],
        'KT2' => ['label' => 'Terlambat hadir / Pulang sebelum waktunya (31 - 60) menit',                                       'persen' => 0.5],
        'KT3' => ['label' => 'Terlambat hadir / Pulang sebelum waktunya (61 - 90) menit',                                       'persen' => 0.75],
        'KT4' => ['label' => 'Terlambat / Pulang sebelum waktunya (> 90) menit',                                                'persen' => 1],
        'KT5' => ['label' => 'Tidak melakukan absen pulang',                                                                     'persen' => 0.25],
        'KT6' => ['label' => 'Tidak hadir bekerja dan/atau tidak melakukan absensi dalam 1 (satu) hari',                        'persen' => 3],
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
            'KT5' => 0, 'KT6' => 0,
        ];
        
        $detailHari = [];

        foreach ($absensis as $a) {
            $hariTL  = 0;
            $hariPSW = 0;

            // KT6: Alpha / tidak hadir
            if ($a->status === 'alpha') {
                $kt['KT6']++;
                $detailHari[$a->tanggal->format('Y-m-d')] = ['tl' => 0, 'psw' => 0, 'status' => 'alpha'];
                continue;
            }

            // KT5: Ada check-in tapi tidak check-out
            if ($a->check_in && !$a->check_out && $a->status !== 'izin' && $a->status !== 'sakit') {
                $kt['KT5']++;
            }

            // 1. Hitung TL (Terlambat)
            if ($a->check_in && $a->shift) {
                $jamMasuk  = Carbon::parse($a->tanggal->format('Y-m-d') . ' ' . $a->shift->jam_masuk);
                $checkIn   = Carbon::parse($a->check_in);
                $menitTL   = $jamMasuk->diffInMinutes($checkIn, false); // positif = terlambat

                if ($menitTL > 0) {
                    $hariTL = $menitTL;
                    $this->assignTier($menitTL, $kt);
                }
            }

            // 2. Hitung PSW (Pulang Sebelum Waktunya)
            if ($a->check_out && $a->shift) {
                $jamKeluar  = Carbon::parse($a->tanggal->format('Y-m-d') . ' ' . $a->shift->jam_keluar);
                $checkOut   = Carbon::parse($a->check_out);
                $menitPSW   = $checkOut->diffInMinutes($jamKeluar, false); // positif = pulang awal (PSW)

                if ($menitPSW > 0) {
                    $hariPSW = $menitPSW;
                    $this->assignTier($menitPSW, $kt);
                }
            }

            $detailHari[$a->tanggal->format('Y-m-d')] = [
                'tl'     => $hariTL,
                'psw'    => $hariPSW,
                'status' => $a->status
            ];
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
            'hari'          => $detailHari,
        ];
    }

    /**
     * Masukkan durasi (menit) ke tier KT1-KT4
     */
    private function assignTier(int $menit, &$kt)
    {
        if ($menit > 90) {
            $kt['KT4']++;
        } elseif ($menit > 60) {
            $kt['KT3']++;
        } elseif ($menit > 30) {
            $kt['KT2']++;
        } elseif ($menit > 0) {
            $kt['KT1']++;
        }
    }

}
