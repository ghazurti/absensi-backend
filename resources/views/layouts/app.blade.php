<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Absensi RSUD Kota Baubau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #3949ab;
            --primary-light: #eef0fb;
            --sidebar-w: 248px;
            --topbar-h: 64px;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: #fff;
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            z-index: 200;
            overflow-y: auto;
            transition: transform .3s;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 18px;
            border-bottom: 1px solid var(--gray-100);
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 17px;
            flex-shrink: 0;
        }
        .sidebar-brand-text { font-size: 15px; font-weight: 700; color: var(--gray-900); line-height: 1.2; }
        .sidebar-brand-sub { font-size: 11px; color: var(--gray-400); font-weight: 400; }

        .sidebar-nav { flex: 1; padding: 12px 10px; }

        .nav-section {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .8px;
            color: var(--gray-400);
            text-transform: uppercase;
            padding: 16px 10px 6px;
        }

        .nav-item { margin-bottom: 2px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            color: var(--gray-700);
            text-decoration: none;
            transition: background .15s, color .15s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
        }
        .nav-link i { font-size: 16px; flex-shrink: 0; color: var(--gray-400); transition: color .15s; }
        .nav-link .nav-arrow { margin-left: auto; font-size: 12px; transition: transform .2s; }
        .nav-link:hover { background: var(--gray-100); color: var(--gray-900); }
        .nav-link:hover i { color: var(--gray-700); }
        .nav-link.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }
        .nav-link.active i { color: var(--primary); }
        .nav-link.open .nav-arrow { transform: rotate(180deg); }

        /* Sub-menu */
        .sub-menu { display: none; padding-left: 22px; margin-top: 2px; }
        .sub-menu.show { display: block; }
        .sub-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 13px;
            color: var(--gray-500);
            text-decoration: none;
            transition: all .15s;
            margin-bottom: 1px;
        }
        .sub-link::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--gray-300, #d1d5db);
            flex-shrink: 0;
            transition: background .15s;
        }
        .sub-link:hover { color: var(--primary); background: var(--primary-light); }
        .sub-link:hover::before { background: var(--primary); }
        .sub-link.active { color: var(--primary); font-weight: 600; }
        .sub-link.active::before { background: var(--primary); }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            padding: 0 28px;
            z-index: 100;
            gap: 16px;
        }

        .topbar-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: var(--gray-700);
            cursor: pointer;
            padding: 4px;
        }

        .topbar-breadcrumb {
            flex: 1;
            font-size: 14px;
            color: var(--gray-500);
        }
        .topbar-breadcrumb span { color: var(--gray-900); font-weight: 600; }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .topbar-hospital {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-700);
            background: var(--gray-100);
            padding: 6px 12px;
            border-radius: 8px;
        }
        .topbar-hospital i { color: var(--primary); }

        .topbar-notif {
            position: relative;
            width: 38px; height: 38px;
            border-radius: 8px;
            border: 1px solid var(--gray-200);
            display: flex; align-items: center; justify-content: center;
            background: #fff;
            cursor: pointer;
            color: var(--gray-500);
            font-size: 18px;
        }
        .topbar-notif .badge {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 1.5px solid #fff;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: background .15s;
            position: relative;
        }
        .topbar-user:hover { background: var(--gray-100); }
        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: var(--primary);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .user-info { line-height: 1.3; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--gray-900); }
        .user-role { font-size: 11px; color: var(--gray-400); }
        .user-chevron { font-size: 12px; color: var(--gray-400); }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 110%; right: 0;
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.10);
            min-width: 180px;
            padding: 6px;
            z-index: 300;
        }
        .user-dropdown.show { display: block; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: var(--gray-700);
            text-decoration: none;
            cursor: pointer;
            transition: background .15s;
            border: none;
            background: none;
            width: 100%;
        }
        .dropdown-item:hover { background: var(--gray-100); }
        .dropdown-item.danger { color: #dc2626; }
        .dropdown-item.danger:hover { background: #fef2f2; }
        .dropdown-divider { border: none; border-top: 1px solid var(--gray-100); margin: 4px 0; }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }
        .main-content { padding: 28px; }

        /* ===== ALERTS ===== */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-close { margin-left: auto; background: none; border: none; cursor: pointer; color: inherit; opacity: .6; font-size: 16px; }

        /* ===== CARDS ===== */
        .card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            overflow: hidden;
        }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-900);
        }
        .card-body { padding: 20px; }

        /* ===== TABLE ===== */
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead tr { border-bottom: 1px solid var(--gray-100); }
        thead th { padding: 10px 14px; color: var(--gray-500); font-weight: 600; font-size: 12px; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-50); transition: background .12s; }
        tbody tr:hover { background: var(--gray-50); }
        tbody td { padding: 11px 14px; color: var(--gray-700); vertical-align: middle; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-hadir   { background: #dcfce7; color: #166534; }
        .badge-terlambat { background: #fff7ed; color: #c2410c; }
        .badge-izin    { background: #eff6ff; color: #1d4ed8; }
        .badge-sakit   { background: #f5f3ff; color: #6d28d9; }
        .badge-alpha   { background: #fef2f2; color: #dc2626; }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all .15s;
        }
        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-primary:hover { background: #303f9f; }
        .btn-outline { background: #fff; color: var(--gray-700); border-color: var(--gray-200); }
        .btn-outline:hover { background: var(--gray-100); }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .btn-icon { padding: 7px; border-radius: 8px; }

        /* ===== FORMS ===== */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--gray-200);
            border-radius: 9px;
            font-size: 13.5px;
            color: var(--gray-900);
            background: #fafafa;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(57,73,171,.1); background: #fff; }
        .form-control::placeholder { color: var(--gray-400); }
        .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }
        .form-text { font-size: 12px; color: var(--gray-400); margin-top: 4px; }
        .input-group { position: relative; }
        .input-group .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--gray-400); font-size: 15px; pointer-events: none; }
        .input-group .form-control { padding-left: 38px; }

        /* ===== PAGINATION ===== */
        .pagination { 
            display: flex; 
            list-style: none; 
            padding: 0; 
            margin: 0; 
            gap: 6px; 
            justify-content: center;
        }
        .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            text-decoration: none;
            border: 1px solid var(--gray-200);
            background: #fff;
            transition: all .2s;
        }
        .page-item .page-link:hover { background: var(--gray-100); border-color: var(--gray-300); transform: translateY(-1px); }
        .page-item.active .page-link { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 4px 10px rgba(57,73,171,.3); }
        .page-item.disabled .page-link { opacity: .5; cursor: not-allowed; background: var(--gray-50); }
        .page-item:first-child .page-link, .page-item:last-child .page-link { width: auto; padding: 0 16px; }

        .pagination-info { font-size: 13px; color: var(--gray-500); text-align: center; margin-top: 12px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 0 0 0 100vw rgba(0,0,0,.3); }
            .topbar { left: 0; }
            .topbar-menu-btn { display: flex; }
            .main-wrapper { margin-left: 0; }
            .topbar-hospital { display: none; }
        }
        @media (max-width: 640px) {
            .main-content { padding: 16px; }
            .user-info { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-hospital-fill"></i>
        </div>
        <div>
            <div class="sidebar-brand-text">RSUD Baubau</div>
            <div class="sidebar-brand-sub">Sistem Absensi</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </div>

        @if(!auth()->user()->isAdmin())
        <div class="nav-section">Kehadiran</div>

        <div class="nav-item">
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="bi bi-fingerprint"></i>
                Absensi
            </a>
        </div>

        <div class="nav-item">
            <button class="nav-link {{ request()->routeIs('shift.*') ? 'active open' : '' }}"
                onclick="toggleMenu('menu-shift', this)">
                <i class="bi bi-calendar3"></i>
                Jadwal Shift
                <i class="bi bi-chevron-down nav-arrow"></i>
            </button>
            <div class="sub-menu {{ request()->routeIs('shift.*') ? 'show' : '' }}" id="menu-shift">
                <a href="{{ route('shift.index') }}" class="sub-link {{ request()->routeIs('shift.index') ? 'active' : '' }}">
                    Daftar Shift
                </a>
                <a href="{{ route('tukar_shift.index') }}" class="sub-link {{ request()->routeIs('tukar_shift.*') ? 'active' : '' }}">
                    Tukar Shift
                </a>
            </div>
        </div>

        <div class="nav-item">
            <button class="nav-link {{ request()->routeIs('izin.*') ? 'active open' : '' }}"
                onclick="toggleMenu('menu-izin', this)">
                <i class="bi bi-calendar-x"></i>
                Cuti & Izin
                <i class="bi bi-chevron-down nav-arrow"></i>
            </button>
            <div class="sub-menu {{ request()->routeIs('izin.*') ? 'show' : '' }}" id="menu-izin">
                <a href="{{ route('izin.index') }}" class="sub-link {{ request()->routeIs('izin.index') ? 'active' : '' }}">
                    Pengajuan Izin
                </a>
            </div>
        </div>

        <div class="nav-item">
            <a href="{{ route('lembur.index') }}" class="nav-link {{ request()->routeIs('lembur.*') ? 'active' : '' }}">
                <i class="bi bi-alarm"></i>
                Pengajuan Lembur
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('koreksi.index') }}" class="nav-link {{ request()->routeIs('koreksi.*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i>
                Koreksi Absensi
            </a>
        </div>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="nav-section">Manajemen</div>

        <div class="nav-item">
            <a href="{{ route('pegawai.index') }}" class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                Daftar Pegawai
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('izin.index') }}" class="nav-link {{ request()->routeIs('izin.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                Persetujuan Cuti
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('lembur.index') }}" class="nav-link {{ request()->routeIs('lembur.*') ? 'active' : '' }}">
                <i class="bi bi-alarm-fill"></i>
                Persetujuan Lembur
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('libur.index') }}" class="nav-link {{ request()->routeIs('libur.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                Hari Libur
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('koreksi.index') }}" class="nav-link {{ request()->routeIs('koreksi.*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i>
                Persetujuan Koreksi
            </a>
        </div>

        <div class="nav-item">
            <button class="nav-link {{ request()->routeIs('departemen.*') ? 'active open' : '' }}"
                onclick="toggleMenu('menu-departemen', this)">
                <i class="bi bi-building"></i>
                Departemen
                <i class="bi bi-chevron-down nav-arrow"></i>
            </button>
            <div class="sub-menu {{ request()->routeIs('departemen.*') ? 'show' : '' }}" id="menu-departemen">
                <a href="{{ route('departemen.index') }}" class="sub-link {{ request()->routeIs('departemen.index') ? 'active' : '' }}">
                    Daftar Departemen
                </a>
                <a href="{{ route('departemen.create') }}" class="sub-link {{ request()->routeIs('departemen.create') ? 'active' : '' }}">
                    Tambah Departemen
                </a>
            </div>
        </div>

        <div class="nav-section">Laporan</div>

        <div class="nav-item">
            <button class="nav-link {{ request()->routeIs('laporan.*') || request()->routeIs('skor.*') ? 'active open' : '' }}"
                onclick="toggleMenu('menu-laporan', this)">
                <i class="bi bi-bar-chart-line"></i>
                Laporan
                <i class="bi bi-chevron-down nav-arrow"></i>
            </button>
            <div class="sub-menu {{ request()->routeIs('laporan.*') || request()->routeIs('skor.*') ? 'show' : '' }}" id="menu-laporan">
                <a href="{{ route('laporan.index') }}" class="sub-link {{ request()->routeIs('laporan.index') || request()->routeIs('laporan.export') ? 'active' : '' }}">
                    Detail Absensi
                </a>
                <a href="{{ route('laporan.rekap') }}" class="sub-link {{ request()->routeIs('laporan.rekap') ? 'active' : '' }}">
                    Rekapitulasi Bulanan
                </a>
                <a href="{{ route('skor.index') }}" class="sub-link {{ request()->routeIs('skor.*') ? 'active' : '' }}">
                    Skor Kehadiran
                </a>
            </div>
        </div>
        @endif
    </nav>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <button class="topbar-menu-btn" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-breadcrumb">
        {!! $__env->yieldContent('breadcrumb', '<span>' . ucfirst(request()->segment(1) ?: 'dashboard') . '</span>') !!}
    </div>

    <div class="topbar-right">
        <div class="topbar-hospital">
            <i class="bi bi-building-fill-cross"></i>
            RSUD Kota Baubau
        </div>

        <div class="topbar-notif">
            <i class="bi bi-bell"></i>
            <span class="badge"></span>
        </div>

        <div class="topbar-user" onclick="toggleDropdown()">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Pegawai' }}</div>
            </div>
            <i class="bi bi-chevron-down user-chevron"></i>

            <div class="user-dropdown" id="userDropdown">
                <div style="padding: 10px 12px 8px; border-bottom: 1px solid var(--gray-100); margin-bottom: 4px;">
                    <div style="font-size:13px;font-weight:600;color:var(--gray-900)">{{ auth()->user()->name }}</div>
                    <div style="font-size:12px;color:var(--gray-400)">{{ auth()->user()->email }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item danger">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- MAIN -->
<main class="main-wrapper">
    <div class="main-content">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
        </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Overlay for mobile -->
<div id="overlay" onclick="closeSidebar()"
    style="display:none;position:fixed;inset:0;z-index:199;background:rgba(0,0,0,.4)"></div>

<script>
    function toggleMenu(id, btn) {
        const menu = document.getElementById(id);
        const isOpen = menu.classList.contains('show');
        menu.classList.toggle('show', !isOpen);
        btn.classList.toggle('open', !isOpen);
    }

    function toggleSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').style.display = 'block';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').style.display = 'none';
    }

    function toggleDropdown() {
        document.getElementById('userDropdown').classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.topbar-user')) {
            document.getElementById('userDropdown').classList.remove('show');
        }
    });
</script>
@stack('scripts')
</body>
</html>
