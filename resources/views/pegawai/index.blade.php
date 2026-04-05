@extends('layouts.app')
@section('title', 'Data Pegawai')
@section('breadcrumb')Manajemen / <span>Pegawai</span>@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <div style="font-size:22px;font-weight:800;color:var(--gray-900)">Data Pegawai</div>
        <div style="font-size:13px;color:var(--gray-400);margin-top:3px">Kelola data pegawai RSUD Kota Baubau</div>
    </div>
    <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Pegawai
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span style="color:var(--gray-500);font-weight:400;font-size:13px">
            Total: <strong style="color:var(--gray-900)">{{ $pegawais->total() }}</strong> pegawai
        </span>
        <form style="display:flex;gap:8px">
            <div class="input-group" style="width:260px">
                <i class="bi bi-search input-icon"></i>
                <input type="text" name="search" class="form-control" style="padding-left:36px"
                    placeholder="Cari nama, NIP, unit..." value="{{ request('search') }}">
            </div>
            <button class="btn btn-outline btn-sm">Cari</button>
            @if(request('search'))
            <a href="{{ route('pegawai.index') }}" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">
                <i class="bi bi-x"></i>
            </a>
            @endif
        </form>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Unit</th>
                    <th>No. HP</th>
                    <th>Email</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $p)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px">
                            <div style="width:38px;height:38px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px">{{ $p->name }}</div>
                                <div style="font-size:11px;color:var(--gray-400)">ID #{{ $p->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:monospace;font-size:13px;color:var(--gray-500)">{{ $p->nip ?? '-' }}</td>
                    <td>{{ $p->jabatan ?? '-' }}</td>
                    <td>
                        @if($p->unit)
                        <span style="background:var(--primary-light);color:var(--primary);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">
                            {{ $p->unit }}
                        </span>
                        @else -
                        @endif
                    </td>
                    <td style="color:var(--gray-500)">{{ $p->no_hp ?? '-' }}</td>
                    <td style="color:var(--gray-500);font-size:12px">{{ $p->email }}</td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:flex-end">
                            <a href="{{ route('pegawai.edit', $p) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('pegawai.destroy', $p) }}" method="POST"
                                onsubmit="return confirm('Hapus pegawai {{ addslashes($p->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-sm" title="Hapus"
                                    style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:6px;cursor:pointer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--gray-400)">
                        <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:10px;opacity:.3"></i>
                        Tidak ada data pegawai
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px;border-top:1px solid var(--gray-100)">{{ $pegawais->links() }}</div>
</div>
@endsection
