@extends('layouts.app', [
    'layout' => 'superadmin',
    'activeMenu' => 'user'
])

@section('content')
<div class="card">
    <div class="btn-row" style="justify-content: space-between;">
        <div>
            <h1>Pengaturan User</h1>
            <p class="meta">Tambah, edit, nonaktifkan, dan atur role admin/super admin.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('superadmin.user.create') }}">Tambah User</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td>{{ $u->nama }}</td>
                        <td>{{ $u->username }}</td>
                        <td>{{ $u->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</td>
                        <td>
                            @if ($u->is_active)
                                <span class="badge badge-green">Aktif</span>
                            @else
                                <span class="badge badge-red">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $u->last_login ? $u->last_login->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-sm btn-outline" href="{{ route('superadmin.user.edit', $u->id) }}">Edit</a>
                                @if ($u->id !== Auth::id())
                                    <form method="post" action="{{ route('superadmin.user.destroy') }}" onsubmit="return confirm('Nonaktifkan user ini?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $u->id }}">
                                        <button class="btn btn-sm btn-danger" type="submit">Nonaktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
