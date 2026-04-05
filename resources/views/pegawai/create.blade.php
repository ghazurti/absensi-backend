@extends('layouts.app')
@section('title', 'Tambah Pegawai')
@section('breadcrumb')Manajemen / <a href="{{ route('pegawai.index') }}" style="color:inherit">Pegawai</a> / <span>Tambah</span>@endsection

@section('content')
<div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:24px">Tambah Pegawai Baru</div>

<div style="max-width:680px">
    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-person-plus me-2" style="color:var(--primary)"></i>Form Data Pegawai</span>
        </div>
        <div class="card-body">
            <form action="{{ route('pegawai.store') }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Nama Lengkap <span style="color:#dc2626">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Nama lengkap sesuai KTP" required>
                        @error('name')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}"
                            placeholder="Nomor Induk Pegawai" required>
                        @error('nip')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}"
                            placeholder="08xx-xxxx-xxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}"
                            placeholder="Dokter, Perawat, Bidan, dll">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_gol" class="form-control" value="{{ old('pangkat_gol') }}"
                            placeholder="Contoh: Penata Muda / III-a">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit / Departemen</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit') }}"
                            placeholder="IGD, Poli Umum, Rawat Inap, dll">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Email <span style="color:#dc2626">*</span></label>
                        <div class="input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="nama@rsud-baubau.go.id" required>
                        </div>
                        @error('email')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:#dc2626">*</span></label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" class="form-control"
                                placeholder="Min. 6 karakter" required>
                        </div>
                        @error('password')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password <span style="color:#dc2626">*</span></label>
                        <div class="input-group">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password" required>
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px">
                        <i class="bi bi-save"></i> Simpan Pegawai
                    </button>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-outline" style="padding:10px 20px">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
