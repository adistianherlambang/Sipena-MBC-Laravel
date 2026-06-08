@extends('layouts.app')

@section('content')
<div class="card login-card">
    <h1>Login Admin</h1>
    <p class="meta">Masuk sebagai Karyawan/Admin atau Kepala Shift/Super Admin.</p>
    <form method="post" action="{{ route('admin.login') }}">
        @csrf
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Login</button>
    </form>
    <hr>
    <p class="help">Akun awal: superadmin / Admin@12345 atau admin / Admin@12345</p>
</div>
@endsection
