@extends('layouts.app')
@section('title', 'Log Audit Keamanan')
@section('breadcrumb')Manajemen / <span>Audit Log</span>@endsection

@section('content')
<div style="margin-bottom:28px">
    <h1 style="margin:0;font-size:26px;font-weight:800;color:var(--gray-900);letter-spacing:-0.5px">Log Audit & Keamanan</h1>
    <p style="margin:4px 0 0;font-size:14px;color:var(--gray-500)">Riwayat aktivitas penting dan perubahan data pada sistem.</p>
</div>

<div class="card" style="border:none;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);border-radius:16px;overflow:hidden">
    <div class="card-header" style="background:#fff;padding:20px 24px;border-bottom:1px solid var(--gray-100);display:flex;justify-content:space-between;align-items:center">
        <form style="display:flex;gap:10px;width:100%">
            <div style="width:200px">
                <select name="action" class="form-control" onchange="this.form.submit()" style="padding:10px;border-radius:10px;border:1px solid var(--gray-200);width:100%">
                    <option value="">Semua Aksi</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div style="flex:1">
                <input type="text" name="user" class="form-control" placeholder="Cari nama pengakses..." value="{{ request('user') }}" style="padding:10px 16px;border-radius:10px;border:1px solid var(--gray-200);width:100%">
            </div>
            <button class="btn btn-primary" style="padding:10px 20px;border-radius:10px">Filter</button>
            @if(request()->anyFilled(['action', 'user']))
                <a href="{{ route('audit.logs') }}" class="btn" style="padding:10px;background:var(--gray-100);border-radius:10px;color:var(--gray-500)"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>

    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:var(--gray-50)">
                    <th style="padding:16px 24px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase">User</th>
                    <th style="padding:16px 24px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase">Aksi</th>
                    <th style="padding:16px 24px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase">Entitas</th>
                    <th style="padding:16px 24px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase">Perubahan</th>
                    <th style="padding:16px 24px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase">Waktu & IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom:1px solid var(--gray-50)">
                    <td style="padding:16px 24px">
                        <div style="font-weight:700;font-size:14px;color:var(--gray-900)">{{ $log->user->name ?? 'System' }}</div>
                        <div style="font-size:11px;color:var(--gray-400)">{{ $log->user->role ?? '-' }}</div>
                    </td>
                    <td style="padding:16px 24px">
                        <span class="badge" style="background: {{ $log->action == 'created' ? '#ecfdf5' : ($log->action == 'updated' ? '#eff6ff' : '#fef2f2') }}; color: {{ $log->action == 'created' ? '#059669' : ($log->action == 'updated' ? '#2563eb' : '#dc2626') }}; padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px">
                            {{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td style="padding:16px 24px">
                        <div style="font-size:13px;font-weight:600">
                            {{ class_basename($log->model_type) }}
                        </div>
                        <div style="font-size:11px;color:var(--gray-400)">ID: #{{ $log->model_id }}</div>
                    </td>
                    <td style="padding:16px 24px">
                        @if($log->action == 'updated')
                            <div style="font-size:12px;max-width:250px">
                                @foreach($log->new_values ?? [] as $key => $val)
                                    @if($key !== 'updated_at')
                                    <div style="margin-bottom:4px">
                                        <span style="font-weight:700;color:var(--gray-500)">{{ $key }}:</span> 
                                        <span style="text-decoration:line-through;color:#dc2626">{{ is_scalar($log->old_values[$key] ?? '') ? $log->old_values[$key] : '...' }}</span>
                                        <i class="bi bi-arrow-right"></i>
                                        <span style="color:#16a34a;font-weight:600">{{ is_scalar($val) ? $val : '...' }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($log->action == 'created')
                            <span style="font-size:11px;color:var(--gray-400)">Data baru dibuat</span>
                        @else
                            <span style="font-size:11px;color:#dc2626">Data dihapus permanen</span>
                        @endif
                    </td>
                    <td style="padding:16px 24px">
                        <div style="font-size:13px;font-weight:600">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                        <div style="font-size:11px;color:var(--gray-400)">{{ $log->ip_address }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:40px;text-align:center;color:var(--gray-400)">Tidak ada log aktivitas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div style="padding:20px;border-top:1px solid var(--gray-100)">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
