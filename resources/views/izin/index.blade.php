@extends('layouts.app')
@section('title', 'Cuti & Izin')
@section('breadcrumb')Kehadiran / <span>Cuti & Izin</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:4px">Cuti & Izin</div>
<div style="font-size:13px;color:var(--gray-400);margin-bottom:24px">Kelola pengajuan izin, sakit, dan cuti pegawai</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:20px">

    {{-- Form --}}
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
                    <input type="date" name="tanggal_mulai" class="form-control" required value="{{ old('tanggal_mulai') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required value="{{ old('tanggal_selesai') }}">
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

    {{-- Daftar --}}
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-list me-2" style="color:var(--primary)"></i>Riwayat Pengajuan</span>
            <span style="font-size:12px;color:var(--gray-400);font-weight:400">{{ $izins->total() }} data</span>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin())<th>Pegawai</th>@endif
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Keterangan</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izins as $izin)
                    @php
                        $durasi = \Carbon\Carbon::parse($izin->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tanggal_selesai)) + 1;
                        $jenisMap = ['izin'=>['#eff6ff','#1d4ed8'], 'sakit'=>['#fef2f2','#dc2626'], 'cuti'=>['#f0fdf4','#16a34a']];
                        $jc = $jenisMap[$izin->jenis] ?? ['#f3f4f6','#374151'];
                    @endphp
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $izin->user->name ?? '-' }}</div>
                            <div style="font-size:11px;color:var(--gray-400)">{{ $izin->user->unit ?? '' }}</div>
                        </td>
                        @endif
                        <td>
                            <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $jc[0] }};color:{{ $jc[1] }}">
                                {{ ucfirst($izin->jenis) }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}</div>
                            <div style="font-size:11px;color:var(--gray-400)">s/d {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span style="background:var(--gray-100);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;color:var(--gray-700)">
                                {{ $durasi }} hari
                            </span>
                        </td>
                        <td style="max-width:180px;color:var(--gray-500);font-size:12px" title="{{ $izin->keterangan }}">
                            {{ Str::limit($izin->keterangan, 45) }}
                        </td>
                        <td>
                            @if($izin->file_pendukung)
                            <a href="{{ asset('storage/' . $izin->file_pendukung) }}" target="_blank"
                                class="btn btn-outline btn-sm btn-icon" title="Unduh file">
                                <i class="bi bi-paperclip"></i>
                            </a>
                            @else
                            <span style="color:var(--gray-300)">—</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('izin.destroy', $izin) }}" method="POST"
                                onsubmit="return confirm('Hapus data izin ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-sm"
                                    style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:6px;cursor:pointer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:48px;color:var(--gray-400)">
                            <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:10px;opacity:.3"></i>
                            Belum ada pengajuan izin
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px;border-top:1px solid var(--gray-100)">{{ $izins->links() }}</div>
    </div>
</div>
@endsection
