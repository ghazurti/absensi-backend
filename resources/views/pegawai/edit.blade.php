@extends('layouts.app')
@section('title', 'Edit Pegawai')
@section('breadcrumb')Manajemen / <a href="{{ route('pegawai.index') }}" style="color:inherit">Pegawai</a> / <span>Edit</span>@endsection

@section('content')
<div style="display:flex;align-items:center;gap:20px;margin-bottom:28px">
    <div style="width:64px;height:64px;border-radius:18px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;box-shadow:0 10px 15px -3px rgba(37,99,235,0.1)">
        {{ strtoupper(substr($pegawai->name, 0, 1)) }}
    </div>
    <div>
        <h1 style="margin:0;font-size:26px;font-weight:800;color:var(--gray-900);letter-spacing:-0.5px">Edit Profil Pegawai</h1>
        <p style="margin:4px 0 0;font-size:14px;color:var(--gray-500)">Memperbarui informasi untuk <strong>{{ $pegawai->name }}</strong></p>
    </div>
</div>

<div style="max-width:900px">
    <form action="{{ route('pegawai.update', $pegawai) }}" method="POST">
        @csrf @method('PUT')
        
        {{-- Section 1: Informasi Pribadi --}}
        <div class="card" style="border:none;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);border-radius:16px;margin-bottom:24px">
            <div class="card-header" style="background:transparent;padding:20px 24px;border-bottom:1px solid var(--gray-100)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f0f9ff;color:#0369a1;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-person-badge" style="font-size:18px"></i>
                    </div>
                    <div style="font-weight:700;font-size:16px;color:var(--gray-900)">Informasi Pribadi</div>
                </div>
            </div>
            <div class="card-body" style="padding:24px">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Nama Lengkap <span style="color:#dc2626">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $pegawai->name) }}" required
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                        @error('name')<div style="color:#dc2626;font-size:12px;margin-top:6px;font-weight:500">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">NIK <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $pegawai->nik) }}" required
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                        @error('nik')<div style="color:#dc2626;font-size:12px;margin-top:6px;font-weight:500">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $pegawai->no_hp) }}" 
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Detail Kepegawaian --}}
        <div class="card" style="border:none;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);border-radius:16px;margin-bottom:24px">
            <div class="card-header" style="background:transparent;padding:20px 24px;border-bottom:1px solid var(--gray-100)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-briefcase" style="font-size:18px"></i>
                    </div>
                    <div style="font-weight:700;font-size:16px;color:var(--gray-900)">Detail Kepegawaian</div>
                </div>
            </div>
            <div class="card-body" style="padding:24px">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}" 
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Unit / Departemen</label>
                        <select name="unit" class="form-control form-select" style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200);height:auto">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->nama }}" {{ old('unit', $pegawai->unit) == $dept->nama ? 'selected' : '' }}>
                                    {{ $dept->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $pegawai->jabatan) }}" 
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_gol" class="form-control" value="{{ old('pangkat_gol', $pegawai->pangkat_gol) }}" 
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Jenis Absensi --}}
        <div class="card" style="border:none;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);border-radius:16px;margin-bottom:24px">
            <div class="card-header" style="background:transparent;padding:20px 24px;border-bottom:1px solid var(--gray-100)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-clock-history" style="font-size:18px"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:16px;color:var(--gray-900)">Jenis Absensi</div>
                        <div style="font-size:12px;color:var(--gray-400)">Tentukan pola kehadiran pegawai</div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding:24px">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <label style="display:flex;align-items:flex-start;gap:14px;padding:18px;border:2px solid var(--gray-200);border-radius:12px;cursor:pointer;transition:all .2s" id="card-normal">
                        <input type="radio" name="jenis_absensi" value="normal" {{ old('jenis_absensi', $pegawai->jenis_absensi)=='normal'?'checked':'' }}
                            style="margin-top:3px;accent-color:#2563eb;width:18px;height:18px" onchange="highlightCard()">
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--gray-900)">Normal</div>
                            <div style="font-size:13px;color:var(--gray-500);margin-top:4px">Jam kantor tetap: <strong>07:30 – 16:00</strong></div>
                            <div style="font-size:12px;color:var(--gray-400);margin-top:2px">Berlaku Senin – Jumat</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:flex-start;gap:14px;padding:18px;border:2px solid var(--gray-200);border-radius:12px;cursor:pointer;transition:all .2s" id="card-shift">
                        <input type="radio" name="jenis_absensi" value="shift" {{ old('jenis_absensi', $pegawai->jenis_absensi)=='shift'?'checked':'' }}
                            style="margin-top:3px;accent-color:#2563eb;width:18px;height:18px" onchange="highlightCard()">
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--gray-900)">Shift</div>
                            <div style="font-size:13px;color:var(--gray-500);margin-top:4px">Jadwal bergilir: <strong>Pagi / Siang / Malam</strong></div>
                            <div style="font-size:12px;color:var(--gray-400);margin-top:2px">Jadwal diatur per periode</div>
                        </div>
                    </label>
                </div>
                @error('jenis_absensi')<div style="color:#dc2626;font-size:12px;margin-top:8px;font-weight:500">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Section 4: Akun Sistem --}}
        <div class="card" style="border:none;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);border-radius:16px;margin-bottom:32px">
            <div class="card-header" style="background:transparent;padding:20px 24px;border-bottom:1px solid var(--gray-100)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:36px;height:36px;border-radius:10px;background:#faf5ff;color:#9333ea;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-shield-lock" style="font-size:18px"></i>
                    </div>
                    <div style="font-weight:700;font-size:16px;color:var(--gray-900)">Keamanan Akun</div>
                </div>
            </div>
            <div class="card-body" style="padding:24px">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Alamat Email <span style="color:#dc2626">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $pegawai->email) }}" required
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                        @error('email')<div style="color:#dc2626;font-size:12px;margin-top:6px;font-weight:500">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="grid-column:1/-1">
                        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:12px 16px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:10px">
                            <i class="bi bi-info-circle-fill" style="font-size:16px"></i>
                            <span>Kosongkan password jika Anda tidak ingin mengubahnya.</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Password Baru</label>
                        <input type="password" name="password" class="form-control" 
                            placeholder="Isi jika ingin diubah"
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                        @error('password')<div style="color:#dc2626;font-size:12px;margin-top:6px;font-weight:500">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;margin-bottom:8px;display:block">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" 
                            placeholder="Ulangi password baru"
                            style="padding:12px 16px;border-radius:10px;border:1.5px solid var(--gray-200)">
                    </div>
                </div>
            </div>
        </div>

        <script>
            function highlightCard() {
                const val = document.querySelector('input[name="jenis_absensi"]:checked')?.value;
                document.getElementById('card-normal').style.borderColor = val === 'normal' ? '#2563eb' : 'var(--gray-200)';
                document.getElementById('card-normal').style.background  = val === 'normal' ? '#eff6ff' : '';
                document.getElementById('card-shift').style.borderColor  = val === 'shift'  ? '#2563eb' : 'var(--gray-200)';
                document.getElementById('card-shift').style.background   = val === 'shift'  ? '#eff6ff' : '';
            }
            highlightCard();
        </script>

        <div style="display:flex;gap:12px;align-items:center;margin-bottom:40px">
            <button type="submit" class="btn btn-primary" style="padding:14px 32px;border-radius:12px;font-weight:700;font-size:15px;box-shadow:0 10px 15px -3px rgba(37,99,235,0.2)">
                <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan
            </button>
            <a href="{{ route('pegawai.index') }}" class="btn" style="padding:14px 24px;border-radius:12px;background:var(--gray-100);color:var(--gray-600);font-weight:600;text-decoration:none">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
