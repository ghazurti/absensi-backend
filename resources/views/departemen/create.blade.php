@extends('layouts.app')
@section('title', 'Tambah Departemen')
@section('breadcrumb')Manajemen / <a href="{{ route('departemen.index') }}" style="color:inherit">Departemen</a> / <span>Tambah</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:24px">Tambah Departemen Baru</div>

<div style="max-width:680px">
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-building me-2" style="color:var(--primary)"></i>Form Data Departemen</span>
        </div>
        <div class="card-body">
            <form action="{{ route('departemen.store') }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:120px 1fr;gap:20px">
                    <div class="form-group">
                        <label class="form-label">Kode <span style="color:#dc2626">*</span></label>
                        <input type="text" name="kode" class="form-control" value="{{ old('kode') }}"
                            placeholder="Contoh: IT" required autofocus>
                        @error('kode')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nama Departemen / Unit Kerja <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                            placeholder="Contoh: Bagian IT/Programer/EDP" required>
                        @error('nama')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Keterangan / Deskripsi</label>
                    <textarea name="keterangan" class="form-control" rows="4" 
                        placeholder="Deskripsi singkat mengenai departemen ini (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;gap:10px;margin-top:16px">
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px">
                        <i class="bi bi-save"></i> Simpan Departemen
                    </button>
                    <a href="{{ route('departemen.index') }}" class="btn btn-outline" style="padding:10px 20px">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
