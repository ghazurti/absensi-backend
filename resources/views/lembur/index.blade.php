@extends('layouts.app')
@section('title', auth()->user()->isAdmin() ? 'Persetujuan Lembur' : 'Pengajuan Lembur')
@section('breadcrumb')Kehadiran / <span>Lembur</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:4px">
    {{ auth()->user()->isAdmin() ? 'Persetujuan Lembur' : 'Daftar Pengajuan Lembur' }}
</div>
<div style="font-size:13px;color:var(--gray-400);margin-bottom:24px">
    {{ auth()->user()->isAdmin() ? 'Kelola dan verifikasi pengajuan jam lembur pegawai.' : 'Ajukan dan pantau status jam lembur Anda.' }}
</div>

<div style="display:grid;grid-template-columns: {{ auth()->user()->isAdmin() ? '1fr' : '340px 1fr' }}; gap:20px; align-items: start;">
    
    @if(!auth()->user()->isAdmin())
    {{-- Form Pengajuan (Hanya Pegawai) --}}
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-plus-circle me-2" style="color:var(--primary)"></i>Form Pengajuan</span>
        </div>
        <div class="card-body">
            <form action="{{ route('lembur.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Tanggal Lembur</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div style="display:grid;grid-template-columns: 1fr 1fr; gap:12px">
                    <div class="form-group">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" value="16:00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" value="18:00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan / Alasan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan pekerjaan lembur..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px">
                    <i class="bi bi-send"></i> Kirim Pengajuan
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Daftar Pengajuan --}}
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-list-task me-2" style="color:var(--primary)"></i>Riwayat Lembur</span>
            <form style="display:flex;gap:8px">
                <select name="bulan" class="form-control form-select" style="width:130px;padding:6px 10px;font-size:13px" onchange="this.form.submit()">
                    @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ $bulan==$i?'selected':'' }}>
                        {{ \Carbon\Carbon::create(null,$i)->locale('id')->isoFormat('MMMM') }}
                    </option>
                    @endfor
                </select>
                <select name="tahun" class="form-control form-select" style="width:90px;padding:6px 10px;font-size:13px" onchange="this.form.submit()">
                    @for($y=date('Y');$y<=date('Y')+5;$y++)
                    <option value="{{ $y }}" {{ $tahun==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        @if(auth()->user()->isAdmin()) <th>Pegawai</th> @endif
                        <th>Jam</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lemburs as $lembur)
                    <tr>
                        <td style="font-weight:600; white-space:nowrap">
                            {{ $lembur->tanggal->locale('id')->isoFormat('ddd, D MMM Y') }}
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <div style="font-weight:600">{{ $lembur->user->name }}</div>
                            <div style="font-size:11px; color:var(--gray-400)">{{ $lembur->user->unit ?? '-' }}</div>
                        </td>
                        @endif
                        <td style="white-space:nowrap">
                            <span class="badge" style="background:var(--primary-light); color:var(--primary)">
                                {{ substr($lembur->jam_mulai,0,5) }} - {{ substr($lembur->jam_selesai,0,5) }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:13px">{{ $lembur->keterangan }}</div>
                            @if($lembur->status == 'rejected' && $lembur->catatan_admin)
                            <div style="font-size:11px; color:#dc2626; margin-top:4px; padding:4px 8px; background:#fef2f2; border-radius:4px; border-left:3px solid #dc2626">
                                <strong>Catatan:</strong> {{ $lembur->catatan_admin }}
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($lembur->status == 'pending')
                                <span class="badge" style="background:#fff7ed; color:#c2410c"><i class="bi bi-clock me-1"></i>Menunggu</span>
                            @elseif($lembur->status == 'approved')
                                <span class="badge badge-hadir"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                            @else
                                <span class="badge badge-alpha"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px">
                                @if(auth()->user()->isAdmin() && $lembur->status == 'pending')
                                    <form action="{{ route('lembur.approve', $lembur) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-icon" style="background:#dcfce7; color:#166534" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-icon" style="background:#fef2f2; color:#dc2626" 
                                        onclick="openRejectModal({{ $lembur->id }})" title="Tolak">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif

                                @if(!auth()->user()->isAdmin() && $lembur->status == 'pending')
                                    <form action="{{ route('lembur.destroy', $lembur) }}" method="POST" onsubmit="return confirm('Hapus pengajuan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-outline" style="color:#ef4444">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if(auth()->user()->isAdmin())
                                    <form action="{{ route('lembur.destroy', $lembur) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-outline" style="opacity:.5">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:48px; color:var(--gray-400)">
                            <i class="bi bi-alarm" style="font-size:32px; opacity:.3; display:block; margin-bottom:12px"></i>
                            Tidak ada data pengajuan lembur.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lemburs->hasPages())
        <div class="card-footer" style="padding:16px; border-top:1px solid var(--gray-100)">
            {{ $lemburs->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Penolakan --}}
<div id="rejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="width:400px; box-shadow:0 20px 25px -5px rgba(0,0,0,.1)">
        <div class="card-header">Tolak Pengajuan Lembur</div>
        <div class="card-body">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end">
                    <button type="button" class="btn btn-outline" onclick="closeRejectModal()">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(id) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/lembur/${id}/reject`;
    modal.style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
// Close modal on click outside
window.onclick = function(event) {
    const modal = document.getElementById('rejectModal');
    if (event.target == modal) closeRejectModal();
}
</script>
@endpush

<style>
    .card-footer nav { display: flex; justify-content: center; }
    .card-footer .pagination { margin-bottom: 0; }
</style>
@endsection
