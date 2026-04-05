<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $userId = $request->get('user_id');

        $query = Absensi::with('user')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($userId) $query->where('user_id', $userId);

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        $rekap = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pegawais = User::where('role', 'pegawai')->orderBy('name')->get();

        return view('laporan.index', compact('absensis', 'rekap', 'bulan', 'tahun', 'pegawais', 'userId'));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $userId = $request->get('user_id');

        $absensis = Absensi::with('user')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderBy('tanggal')
            ->get();

        $namaBulan = Carbon::create($tahun, $bulan)->locale('id')->isoFormat('MMMM YYYY');
        $filename = "laporan-absensi-{$bulan}-{$tahun}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($absensis) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama', 'NIP', 'Unit', 'Tanggal', 'Check In', 'Check Out', 'Status', 'Keterangan']);

            foreach ($absensis as $i => $a) {
                fputcsv($file, [
                    $i + 1,
                    $a->user->name ?? '-',
                    $a->user->nip ?? '-',
                    $a->user->unit ?? '-',
                    Carbon::parse($a->tanggal)->format('d/m/Y'),
                    $a->check_in ? Carbon::parse($a->check_in)->format('H:i') : '-',
                    $a->check_out ? Carbon::parse($a->check_out)->format('H:i') : '-',
                    strtoupper($a->status),
                    $a->keterangan ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
