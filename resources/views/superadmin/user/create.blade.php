@extends('layouts.app', [
    'layout' => 'superadmin',
    'activeMenu' => 'user'
])

@section('content')
<div class="card">
    <h1>Tambah User</h1>
    <form method="post" action="{{ route('superadmin.user.store') }}">
        @csrf
        <div class="grid grid-2">
            <div class="form-group">
                <label>Nama</label>
                <input name="nama" type="text" value="{{ old('nama') }}" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input name="username" type="text" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input name="password" type="password" required minlength="8">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin / Karyawan</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin / Kepala Shift</option>
                </select>
            </div>
        </div>
        <label class="small">
            <input type="checkbox" name="is_active" value="1" checked style="width:auto;"> 
            Aktif
        </label>
        <br><br>
        <div class="btn-row">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-outline" href="{{ route('superadmin.user.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
