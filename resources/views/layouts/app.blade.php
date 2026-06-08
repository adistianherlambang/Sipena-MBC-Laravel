@php
    $layout = $layout ?? 'public';
    $activeMenu = $activeMenu ?? '';
    $user = Auth::user();
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'Sipena MBC' }} - Sipena MBC</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">S</span>
            <span>Sipena MBC</span>
        </a>
        <button class="nav-toggle" type="button" data-nav-toggle>Menu</button>
        <nav class="nav" data-nav>
            @if ($layout === 'public')
                <a href="{{ route('home') }}#informasi">Informasi</a>
                <a href="{{ route('home') }}#form-pengaduan">Isi Pengaduan</a>
                <a href="{{ route('home') }}#tracking">Tracking Tiket</a>
                <a class="btn btn-sm btn-primary" href="{{ route('admin.login') }}">Login Admin</a>
            @elseif ($layout === 'superadmin')
                <a class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                <a class="{{ $activeMenu === 'pengaduan' ? 'active' : '' }}" href="{{ route('superadmin.complaint.index') }}">Pengaduan</a>
                <a class="{{ $activeMenu === 'laporan' ? 'active' : '' }}" href="{{ route('superadmin.report.index') }}">Laporan</a>
                <a class="{{ $activeMenu === 'user' ? 'active' : '' }}" href="{{ route('superadmin.user.index') }}">User</a>
                <a class="{{ $activeMenu === 'pengaturan' ? 'active' : '' }}" href="{{ route('superadmin.setting.index') }}">Pengaturan</a>
                <a href="{{ route('admin.logout') }}">Logout</a>
            @else
                <a class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ $activeMenu === 'pengaduan' ? 'active' : '' }}" href="{{ route('admin.complaint.index') }}">Pengaduan</a>
                <a class="{{ $activeMenu === 'laporan' ? 'active' : '' }}" href="{{ route('admin.report.index') }}">Laporan</a>
                @if ($user && $user->role === 'super_admin')
                    <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                @endif
                <a href="{{ route('admin.logout') }}">Logout</a>
            @endif
        </nav>
    </div>
</header>
<main class="main">
    <div class="container">
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if (session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
        
    </div>
</main>
<footer class="footer">
    <div class="container footer-inner">
        <span>&copy; {{ date('Y') }} Sipena MBC</span>
        <span>Laravel + SQLite/MySQL</span>
    </div>
</footer>
<script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
