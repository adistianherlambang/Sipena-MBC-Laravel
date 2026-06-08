@extends('layouts.app', [
    'layout' => 'superadmin',
    'activeMenu' => 'user'
])

@section('content')
<div class="card">
    <h1>Edit User</h1>
    <form method="post" action="{{ route('superadmin.user.update', $editUser->id) }}">
        @csrf
        <div class="grid grid-2">
            <div class="form-group">
                <label>Nama</label>
                <input name="nama" type="text" value="{{ old('nama', $editUser->nama) }}" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input name="username" type="text" value="{{ old('username', $editUser->username) }}" required>
            </div>
            <div class="form-group">
                <label>Password Baru</label>
                <input name="password" type="password" minlength="8">
                <div class="help">Kosongkan jika tidak ingin mengganti password.</div>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="admin" {{ old('role', $editUser->role) === 'admin' ? 'selected' : '' }}>Admin / Karyawan</option>
                    <option value="super_admin" {{ old('role', $editUser->role) === 'super_admin' ? 'selected' : '' }}>Super Admin / Kepala Shift</option>
                </select>
            </div>
        </div>
        <label class="small">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $editUser->is_active) ? 'checked' : '' }} style="width:auto;"> 
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
