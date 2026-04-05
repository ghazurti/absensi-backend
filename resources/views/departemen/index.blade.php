@extends('layouts.app')
@section('title', 'Data Departemen')
@section('breadcrumb')Manajemen / <span>Departemen</span>@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <div style="font-size:22px;font-weight:800;color:var(--gray-900)">Data Departemen</div>
        <div style="font-size:13px;color:var(--gray-400);margin-top:3px">Kelola data departemen / unit kerja RSUD Kota Baubau</div>
    </div>
    <a href="{{ route('departemen.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Departemen
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span style="color:var(--gray-500);font-weight:400;font-size:13px">
            Total: <strong style="color:var(--gray-900)">{{ $departments->total() }}</strong> departemen
        </span>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th style="width:100px">Kode</th>
                    <th>Nama Departemen</th>
                    <th>Keterangan</th>
                    <th>Tgl. Dibuat</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $d)
                <tr>
                    <td style="color:var(--gray-400);font-family:monospace">#{{ $d->id }}</td>
                    <td style="font-weight:700;color:var(--gray-700)">{{ $d->kode ?? '-' }}</td>
                    <td>
                        <div style="font-weight:600;font-size:14px;color:var(--primary)">{{ $d->nama }}</div>
                    </td>
                    <td style="color:var(--gray-500);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ $d->keterangan ?? '-' }}
                    </td>
                    <td style="color:var(--gray-400);font-size:12px">
                        {{ $d->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:flex-end">
                            <a href="{{ route('departemen.edit', $d) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('departemen.destroy', $d) }}" method="POST"
                                onsubmit="return confirm('Hapus departemen {{ addslashes($d->nama) }}?')">
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
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--gray-400)">
                        <i class="bi bi-building" style="font-size:36px;display:block;margin-bottom:10px;opacity:.3"></i>
                        Tidak ada data departemen
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:20px;border-top:1px solid var(--gray-100)">
        <div style="display:flex;flex-direction:column;gap:12px">
            {{ $departments->links() }}
            <div class="pagination-info">
                Menampilkan <strong>{{ $departments->firstItem() ?? 0 }}</strong> - <strong>{{ $departments->lastItem() ?? 0 }}</strong> dari <strong>{{ $departments->total() }}</strong> departemen
            </div>
        </div>
    </div>
</div>
@endsection
