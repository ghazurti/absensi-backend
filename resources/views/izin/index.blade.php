@extends('layouts.app')
@section('title', auth()->user()->isAdmin() ? 'Persetujuan Cuti' : 'Cuti & Izin')
@section('breadcrumb'){{ auth()->user()->isAdmin() ? 'Manajemen' : 'Kehadiran' }} / <span>{{ auth()->user()->isAdmin() ? 'Persetujuan Cuti' : 'Cuti & Izin' }}</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:4px">{{ auth()->user()->isAdmin() ? 'Persetujuan Cuti' : 'Cuti & Izin' }}</div>
<div style="font-size:13px;color:var(--gray-400);margin-bottom:24px">{{ auth()->user()->isAdmin() ? 'Proses pengajuan izin dan cuti pegawai' : 'Kelola pengajuan izin, sakit, dan cuti Anda' }}</div>

<div style="display:grid;grid-template-columns: {{ auth()->user()->isAdmin() ? '1fr' : '340px 1fr' }}; gap:20px">

    {{-- Form (Hanya untuk Pegawai) --}}
    @if(!auth()->user()->isAdmin())
    <div class="card" style="align-self:start">
        <div class="card-header">
            <span><i class="bi bi-plus-circle me-2" style="color:var(--primary)"></i>Ajukan Izin</span>
        </div>
        <div class="card-body">
            <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-control form-select" required>
                        <option value="izin" {{ old('jenis')=='izin'?'selected':'' }}>📋 Izin</option>
                        <option value="sakit" {{ old('jenis')=='sakit'?'selected':'' }}>🏥 Sakit</option>
                        <option value="cuti" {{ old('jenis')=='cuti'?'selected':'' }}>🌴 Cuti</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required value="{{ old('tanggal_mulai', date('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required value="{{ old('tanggal_selesai', date('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"
                        placeholder="Jelaskan alasan izin..." required>{{ old('keterangan') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">File Pendukung <span style="color:var(--gray-400);font-weight:400">(opsional)</span></label>
                    <input type="file" name="file_pendukung" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text">PDF, JPG, PNG — maks 2MB</div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">
                    <i class="bi bi-send"></i> Ajukan Sekarang
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Daftar --}}
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-list me-2" style="color:var(--primary)"></i>{{ auth()->user()->isAdmin() ? 'Daftar Pengajuan Masuk' : 'Riwayat Pengajuan' }}</span>
            <span style="font-size:12px;color:var(--gray-400);font-weight:400">{{ $izins->total() }} data</span>
        </div>
        <div style="overflow-x:auto">
            <table style="min-width: 800px">
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin())<th>Pegawai</th>@endif
                        <th>Jenis & Tanggal</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>File</th>
                        <th style="text-align: right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izins as $izin)
                    @php
                        $durasi = \Carbon\Carbon::parse($izin->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tanggal_selesai)) + 1;
                        $jenisMap = ['izin'=>['#eff6ff','#1d4ed8'], 'sakit'=>['#fef2f2','#dc2626'], 'cuti'=>['#f0fdf4','#16a34a']];
                        $jc = $jenisMap[$izin->jenis] ?? ['#f3f4f6','#374151'];
                        
                        $statusMap = [
                            'pending'  => ['#fffbeb', '#92400e', 'Menunggu'],
                            'approved' => ['#f0fdf4', '#166534', 'Disetujui'],
                            'rejected' => ['#fef2f2', '#991b1b', 'Ditolak']
                        ];
                        $sc = $statusMap[$izin->status] ?? ['#f3f4f6', '#374151', $izin->status];
                    @endphp
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $izin->user->name ?? '-' }}</div>
                            <div style="font-size:11px;color:var(--gray-400)">{{ $izin->user->unit ?? '' }}</div>
                        </td>
                        @endif
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                <span style="padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;background:{{ $jc[0] }};color:{{ $jc[1] }}">
                                    {{ strtoupper($izin->jenis) }}
                                </span>
                                <span style="font-size:12px;font-weight:600;color:var(--gray-500)">{{ $durasi }} Hari</span>
                            </div>
                            <div style="font-size:12.5px;color:var(--gray-700)">
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M') }} — {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                            </div>
                        </td>
                        <td style="max-width:220px">
                            <div style="font-size:12.5px;color:var(--gray-600);line-height:1.4">{{ $izin->keterangan }}</div>
                            @if($izin->catatan_admin)
                                <div style="margin-top:6px;padding:6px 10px;background:#f8fafc;border-left:3px solid var(--gray-300);font-size:11.5px;color:var(--gray-500)">
                                    <strong>Catatan:</strong> {{ $izin->catatan_admin }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $sc[0] }};color:{{ $sc[1] }}">
                                <i class="bi bi-{{ $izin->status == 'pending' ? 'clock-history' : ($izin->status == 'approved' ? 'check-circle-fill' : 'x-circle-fill') }}"></i>
                                {{ $sc[2] }}
                            </span>
                        </td>
                        <td>
                            @if($izin->file_pendukung)
                            <a href="{{ asset('storage/' . $izin->file_pendukung) }}" target="_blank"
                                class="btn btn-outline btn-sm btn-icon" title="Lihat Lampiran">
                                <i class="bi bi-paperclip"></i>
                            </a>
                            @else
                            <span style="color:var(--gray-300);font-size:12px">n/a</span>
                            @endif
                        </td>
                        <td style="text-align: right">
                            <div style="display:flex;gap:6px;justify-content: flex-end">
                                @if(auth()->user()->isAdmin() && $izin->status == 'pending')
                                    <form action="{{ route('izin.approve', $izin) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm" style="background:#16a34a;color:#fff;border:none">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm" style="background:#dc2626;color:#fff;border:none"
                                        onclick="showRejectModal({{ $izin->id }})">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif

                                @if($izin->status == 'pending' || auth()->user()->isAdmin())
                                    <form action="{{ route('izin.destroy', $izin) }}" method="POST"
                                        onsubmit="return confirm('Hapus pengajuan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-sm" style="background:#f1f5f9;color:var(--gray-400);border:none">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" style="text-align:center;padding:48px;color:var(--gray-400)">
                            <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:10px;opacity:.3"></i>
                            Belum ada pengajuan untuk saat ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px;border-top:1px solid var(--gray-100)">{{ $izins->links() }}</div>
    </div>
</div>

{{-- Reject Modal (Simple) --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center">
    <div style="background:#fff;width:90%;max-width:400px;border-radius:16px;padding:24px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1)">
        <h3 style="margin-top:0;font-size:18px;font-weight:700;margin-bottom:8px">Tolak Pengajuan</h3>
        <p style="font-size:13px;color:var(--gray-500);margin-bottom:20px">Berikan alasan singkat mengapa pengajuan ini ditolak.</p>
        
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Alasan Penolakan</label>
                <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: Lampiran tidak lengkap atau kuota cuti habis."></textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:24px">
                <button type="button" onclick="hideRejectModal()" class="btn btn-outline" style="flex:1;justify-content:center">Batal</button>
                <button type="submit" class="btn" style="flex:1;justify-content:center;background:#dc2626;color:#fff">Tolak Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/izin/${id}/reject`;
        modal.style.display = 'flex';
    }
    function hideRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
</script>
@endsection
