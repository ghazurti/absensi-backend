<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\SkorController as WebSkorController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SkorController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'bulan'   => 'nullable|integer|min:1|max:12',
            'tahun'   => 'nullable|integer|min:2000|max:2100',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $authUser = auth()->user();
        $bulan    = $request->get('bulan', Carbon::now()->month);
        $tahun    = $request->get('tahun', Carbon::now()->year);

        if ($authUser->isAdmin() && $request->filled('user_id')) {
            $pegawai = User::findOrFail($request->user_id);
        } else {
            $pegawai = $authUser;
        }

        $webSkor = new WebSkorController();
        $skor    = $webSkor->hitungSkor($pegawai, $bulan, $tahun);

        return response()->json([
            'pegawai' => [
                'id'          => $pegawai->id,
                'name'        => $pegawai->name,
                'nip'         => $pegawai->nip,
                'unit'        => $pegawai->unit,
                'jabatan'     => $pegawai->jabatan,
                'pangkat_gol' => $pegawai->pangkat_gol,
            ],
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'label' => Carbon::create($tahun, $bulan)->locale('id')->isoFormat('MMMM YYYY'),
            ],
            'skor_akhir'    => $skor['skor_akhir'],
            'total_potongan'=> $skor['total_potongan'],
            'total_hadir'   => $skor['total_hadir'],
            'total_alpha'   => $skor['total_alpha'],
            'total_izin'    => $skor['total_izin'],
            'detail'        => $skor['detail'],
        ]);
    }
}
