@extends('layouts.app')
@section('title', 'Ganti Password')
@section('breadcrumb')<a href="{{ route('profil') }}" style="color:inherit">Profil</a> / <span>Ganti Password</span>@endsection

@section('content')
<div style="max-width:480px;margin:0 auto">
    <div style="font-size:22px;font-weight:800;color:var(--gray-900);margin-bottom:24px">Ganti Password</div>

    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="card">
        <div class="card-body" style="padding:24px">
            <form method="POST" action="{{ route('profil.update-password') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:16px">
                    <div>
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" required>
                        @error('password_lama')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        <div style="font-size:12px;color:var(--gray-400);margin-top:4px">Minimal 8 karakter</div>
                        @error('password')<div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div style="margin-top:24px;display:flex;gap:10px">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-lock me-1"></i> Simpan Password
                    </button>
                    <a href="{{ route('profil') }}" class="btn" style="background:var(--gray-100);color:var(--gray-700)">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
