@extends('layouts.app')
@section('title', 'Edit Pegawai')
@section('breadcrumb')Manajemen / <a href="{{ route('pegawai.index') }}" style="color:inherit">Pegawai</a> / <span>Edit</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:24px">Edit Pegawai</div>

<div style="max-width:680px">
    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700">
                    {{ strtoupper(substr($pegawai->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:600">{{ $pegawai->name }}</div>
                    <div style="font-size:12px;color:var(--gray-400)">{{ $pegawai->email }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('pegawai.update', $pegawai) }}" method="POST">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Nama Lengkap <span style="color:#dc2626">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $pegawai->name) }}" required>
                        @error('name')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}" required>
                        @error('nip')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $pegawai->no_hp) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $pegawai->jabatan) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_gol" class="form-control" value="{{ old('pangkat_gol', $pegawai->pangkat_gol) }}"
                            placeholder="Contoh: Penata Muda / III-a">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit / Departemen</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', $pegawai->unit) }}">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Email <span style="color:#dc2626">*</span></label>
                        <div class="input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $pegawai->email) }}" required>
                        </div>
                        @error('email')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:9px;padding:10px 14px;font-size:12.5px;color:#92400e;display:flex;align-items:center;gap:8px">
                            <i class="bi bi-info-circle"></i>
                            Kosongkan field password jika tidak ingin mengubah password pegawai.
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Biarkan kosong">
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px">
                        <i class="bi bi-save"></i> Update Data
                    </button>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-outline" style="padding:10px 20px">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
