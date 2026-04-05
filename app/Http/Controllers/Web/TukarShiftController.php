<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\TukarShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TukarShiftController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Data untuk Form Pengajuan
        $myShifts = Shift::where('user_id', $user->id)
            ->where('tanggal', '>=', now()->format('Y-m-d'))
            ->orderBy('tanggal')
            ->get();

        $peers = User::where('role', 'pegawai')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        // Riwayat Pengajuan
        $query = TukarShift::with(['pengaju', 'penerima', 'shiftPengaju', 'shiftPenerima']);

        if (!$user->isAdmin()) {
            $query->where(function($q) use ($user) {
                $q->where('user_pengaju_id', $user->id)
                  ->orWhere('user_penerima_id', $user->id);
            });
        } else {
            // Admin can see all, but prioritizes 'pending_admin'
        }

        $tukarShifts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tukar_shift.index', compact('myShifts', 'peers', 'tukarShifts'));
    }

    public function getPeerShifts(User $user)
    {
        $shifts = Shift::where('user_id', $user->id)
            ->where('tanggal', '>=', now()->format('Y-m-d'))
            ->orderBy('tanggal')
            ->get();
            
        return response()->json($shifts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift_pengaju_id' => 'required|exists:shifts,id',
            'user_penerima_id' => 'required|exists:users,id',
            'shift_penerima_id' => 'required|exists:shifts,id',
            'alasan' => 'required|string',
        ]);

        TukarShift::create([
            'user_pengaju_id' => auth()->id(),
            'user_penerima_id' => $request->user_penerima_id,
            'shift_pengaju_id' => $request->shift_pengaju_id,
            'shift_penerima_id' => $request->shift_penerima_id,
            'alasan' => $request->alasan,
            'status' => 'pending_penerima',
        ]);

        return back()->with('success', 'Permintaan tukar shift telah dikirim ke rekan Anda.');
    }

    public function confirm(Request $request, TukarShift $tukarShift)
    {
        if (auth()->id() != $tukarShift->user_penerima_id) abort(403);

        $status = $request->action == 'accept' ? 'pending_admin' : 'rejected_penerima';
        $tukarShift->update(['status' => $status]);

        $msg = $status == 'pending_admin' ? 'Terima kasih! Permintaan diteruskan ke Admin.' : 'Permintaan ditolak.';
        return back()->with('success', $msg);
    }

    public function approve(Request $request, TukarShift $tukarShift)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        if ($request->action == 'approve') {
            DB::transaction(function() use ($tukarShift) {
                // TUKAR USER_ID
                $shiftPengaju = $tukarShift->shiftPengaju;
                $shiftPenerima = $tukarShift->shiftPenerima;

                $userPengajuId = $shiftPengaju->user_id;
                $userPenerimaId = $shiftPenerima->user_id;

                $shiftPengaju->update(['user_id' => $userPenerimaId]);
                $shiftPenerima->update(['user_id' => $userPengajuId]);

                $tukarShift->update(['status' => 'approved']);
            });

            return back()->with('success', 'Pertukaran shift berhasil disetujui dan jadwal telah diperbarui.');
        } else {
            $tukarShift->update([
                'status' => 'rejected_admin',
                'catatan_admin' => $request->catatan_admin
            ]);
            return back()->with('success', 'Pertukaran shift ditolak oleh Admin.');
        }
    }
}
