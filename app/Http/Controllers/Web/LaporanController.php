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
        $unit = $request->get('unit');
        $authUser = auth()->user();

        if ($authUser->isKepalaUnit()) {
            $unit = $authUser->unit;
        }

        $query = Absensi::with('user')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($userId) $query->where('user_id', $userId);

        if ($unit) {
            $query->whereHas('user', fn($q) => $q->where('unit', $unit));
        }

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        $rekapQuery = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($unit, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('unit', $unit)));

        $rekap = $rekapQuery->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pegawais = User::where('role', 'pegawai')
            ->when($unit, fn($q) => $q->where('unit', $unit))
            ->orderBy('name')->get();

        if ($authUser->isKepalaUnit()) {
            $units = collect([$authUser->unit]);
        } else {
            $units = User::where('role', 'pegawai')->whereNotNull('unit')
                ->distinct()->orderBy('unit')->pluck('unit');
        }

        return view('laporan.index', compact('absensis', 'rekap', 'bulan', 'tahun', 'pegawais', 'userId', 'unit', 'units'));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $userId = $request->get('user_id');
        $unit = $request->get('unit');
        $authUser = auth()->user();

        if ($authUser->isKepalaUnit()) {
            $unit = $authUser->unit;
        }

        $absensis = Absensi::with('user')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($unit, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('unit', $unit)))
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
    public function rekap(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $unit = $request->get('unit');
        $authUser = auth()->user();

        if ($authUser->isKepalaUnit()) {
            $unit = $authUser->unit;
        }

        $users = User::where('role', 'pegawai')
            ->when($unit, fn($q) => $q->where('unit', $unit))
            ->with(['absensis' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            }])
            ->orderBy('unit')->orderBy('name')
            ->get();

        if ($authUser->isKepalaUnit()) {
            $units = collect([$authUser->unit]);
        } else {
            $units = User::where('role', 'pegawai')->whereNotNull('unit')
                ->distinct()->orderBy('unit')->pluck('unit');
        }

        return view('laporan.rekap', compact('users', 'bulan', 'tahun', 'unit', 'units'));
    }

    public function exportRekap(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $unit = $request->get('unit');
        $authUser = auth()->user();

        if ($authUser->isKepalaUnit()) {
            $unit = $authUser->unit;
        }

        $users = User::where('role', 'pegawai')
            ->when($unit, fn($q) => $q->where('unit', $unit))
            ->with(['absensis' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            }])
            ->orderBy('unit')->orderBy('name')
            ->get();

        $filename = "rekapitulasi-absensi-{$bulan}-{$tahun}.csv";
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen("php://output", "w");
        fputcsv($output, ['No', 'Nama Pegawai', 'NIP', 'Unit', 'Hadir', 'Terlambat', 'Izin/Sakit', 'Alpha', 'Total Lembur (Jam)']);

        foreach ($users as $i => $u) {
            $stats = $u->absensis->groupBy('status');
            $hadir = ($stats['hadir'] ?? collect())->count();
            $telat = ($stats['terlambat'] ?? collect())->count();
            $izin = ($stats['izin'] ?? collect())->count() + ($stats['sakit'] ?? collect())->count();
            $alpha = ($stats['alpha'] ?? collect())->count();
            
            // Total Lembur (Asumsi kita hitung record lembur atau durasi)
            $totalLembur = \App\Models\Lembur::where('user_id', $u->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'approved')
                ->count(); // Sederhana: hitung berapa kali lembur

            fputcsv($output, [
                $i + 1,
                $u->name,
                $u->nip ?? '-',
                $u->unit ?? '-',
                $hadir,
                $telat,
                $izin,
                $alpha,
                $totalLembur
            ]);
        }
        fclose($output);
        exit;
    }
}
