@extends('layouts.app')
@section('title', 'Tukar Shift')
@section('breadcrumb')Kehadiran / <span>Tukar Shift</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:4px">Tukar Jadwal Shift</div>
<div style="font-size:13px;color:var(--gray-400);margin-bottom:24px">Fasilitas pertukaran jadwal antar rekan sejawat (perawat/petugas).</div>

<div style="display:grid;grid-template-columns: {{ auth()->user()->isAdmin() ? '1fr' : '380px 1fr' }}; gap:24px; align-items: start;">
    
    @if(!auth()->user()->isAdmin())
    {{-- Form Pengajuan --}}
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-arrow-left-right me-2" style="color:var(--primary)"></i>Ajukan Tukar Shift</span>
        </div>
        <div class="card-body">
            <form action="{{ route('tukar_shift.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Shift Saya yang Akan Ditukar</label>
                    <select name="shift_pengaju_id" class="form-control form-select" required>
                        <option value="">Pilih Jadwal Anda</option>
                        @foreach($myShifts as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->tanggal->format('d M') }} - {{ $s->jenis_shift }} ({{ substr($s->jam_masuk,0,5) }}-{{ substr($s->jam_keluar,0,5) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tukar Dengan Rekan</label>
                    <select name="user_penerima_id" id="peerSelect" class="form-control form-select" onchange="loadPeerShifts(this.value)" required>
                        <option value="">Pilih Rekan</option>
                        @foreach($peers as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->unit ?? 'General' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="peerShiftContainer" style="display:none">
                    <label class="form-label">Jadwal Rekan yang Diambil</label>
                    <select name="shift_penerima_id" id="peerShiftSelect" class="form-control form-select" required>
                        <option value="">Pilih Jadwal Rekan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Alasan Pertukaran</label>
                    <textarea name="alasan" class="form-control" rows="2" placeholder="Contoh: Ada keperluan keluarga mendadak..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px">
                    <i class="bi bi-send-check"></i> Kirim Permintaan
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Riwayat & Persetujuan --}}
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-clock-history me-2" style="color:var(--primary)"></i>Daftar Pengajuan Pertukaran</span>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Pengaju</th>
                        <th>Jadwal Ditukar</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tukarShifts as $t)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $t->pengaju->name }}</div>
                            <div style="font-size:11px; color:var(--gray-400)">Ke : {{ $t->penerima->name }}</div>
                        </td>
                        <td style="font-size:12px">
                            <div style="padding:6px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:4px">
                                <span style="color:var(--gray-400)">Diberikan:</span><br>
                                <strong>{{ $t->shiftPengaju->tanggal->format('d/m') }}</strong> ({{ $t->shiftPengaju->jenis_shift }})
                            </div>
                            <div style="padding:6px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd">
                                <span style="color:var(--gray-400)">Diambil:</span><br>
                                <strong>{{ $t->shiftPenerima->tanggal->format('d/m') }}</strong> ({{ $t->shiftPenerima->jenis_shift }})
                            </div>
                        </td>
                        <td><div style="font-size:12px; max-width:200px">{{ $t->alasan }}</div></td>
                        <td>
                            @if($t->status == 'pending_penerima')
                                <span class="badge" style="background:#fff7ed; color:#c2410c">Menunggu Rekan</span>
                            @elseif($t->status == 'pending_admin')
                                <span class="badge" style="background:#eff6ff; color:#1d4ed8">Menunggu Admin</span>
                            @elseif($t->status == 'approved')
                                <span class="badge badge-hadir">Selesai/Disetujui</span>
                            @elseif($t->status == 'rejected_penerima')
                                <span class="badge badge-alpha">Ditolak Rekan</span>
                            @else
                                <span class="badge badge-alpha">Ditolak Admin</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px">
                                {{-- Aksi untuk Penerima (Rekan) --}}
                                @if(auth()->id() == $t->user_penerima_id && $t->status == 'pending_penerima')
                                    <form action="{{ route('tukar_shift.confirm', $t) }}" method="POST">
                                        @csrf
                                        <button name="action" value="accept" class="btn btn-sm btn-primary">Setuju</button>
                                        <button name="action" value="reject" class="btn btn-sm btn-outline" style="color:#ef4444">Tolak</button>
                                    </form>
                                @endif

                                {{-- Aksi untuk Admin --}}
                                @if(auth()->user()->isAdmin() && $t->status == 'pending_admin')
                                    <form action="{{ route('tukar_shift.approve', $t) }}" method="POST">
                                        @csrf
                                        <button name="action" value="approve" class="btn btn-sm btn-primary">Approve</button>
                                        <button name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @endif
                                
                                @if($t->status == 'rejected_admin' && $t->catatan_admin)
                                    <i class="bi bi-info-circle text-danger" title="{{ $t->catatan_admin }}"></i>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:48px; color:var(--gray-400)">Belum ada data pertukaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px">{{ $tukarShifts->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
async function loadPeerShifts(userId) {
    const container = document.getElementById('peerShiftContainer');
    const select = document.getElementById('peerShiftSelect');
    
    if (!userId) {
        container.style.display = 'none';
        return;
    }

    try {
        const response = await fetch(`/tukar-shift/peer-shifts/${userId}`);
        const shifts = await response.json();
        
        select.innerHTML = '<option value="">Pilih Jadwal Rekan</option>';
        shifts.forEach(s => {
            const date = new Date(s.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            select.innerHTML += `<option value="${s.id}">${date} - ${s.jenis_shift} (${s.jam_masuk.substring(0,5)}-${s.jam_keluar.substring(0,5)})</option>`;
        });
        
        container.style.display = 'block';
    } catch (error) {
        console.error('Failed to load peer shifts:', error);
    }
}
</script>
@endpush
@endsection
