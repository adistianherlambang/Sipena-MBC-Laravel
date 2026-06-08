@extends('layouts.app', [
    'layout' => $isSuperArea ? 'superadmin' : 'admin',
    'activeMenu' => 'dashboard'
])

@section('content')
<div class="card">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <strong>{{ Auth::user()->nama }}</strong>. Kelola pengaduan pelanggan dari halaman ini.</p>
</div>

<div class="grid grid-4">
    <div class="stat"><span class="num">{{ $total }}</span><span class="label">Total</span></div>
    <div class="stat"><span class="num">{{ $baru }}</span><span class="label">Diajukan</span></div>
    <div class="stat"><span class="num">{{ $proses }}</span><span class="label">Diproses</span></div>
    <div class="stat"><span class="num">{{ $selesai }}</span><span class="label">Selesai</span></div>
</div>

<div class="card">
    <div class="btn-row" style="justify-content: space-between;">
        <h2>Pengaduan Terbaru</h2>
        <a class="btn btn-primary" href="{{ route($isSuperArea ? 'superadmin.complaint.index' : 'admin.complaint.index') }}">Lihat Semua</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Pelanggan</th>
                    <th>Masalah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($latest as $row)
                    <tr>
                        <td>{{ $row->ticket_no }}</td>
                        <td>{{ $row->nama_pelanggan }}<br><span class="meta">{{ $row->nomor_wa }}</span></td>
                        <td>{{ $row->kategori_label }}</td>
                        <td><span class="badge {{ $row->status_class }}">{{ $row->status_label }}</span></td>
                        <td>{{ $row->created_at ? $row->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline" href="{{ route($isSuperArea ? 'superadmin.complaint.show' : 'admin.complaint.show', $row->id) }}">Detail</a>
                        </td>
                    </tr>
                @endforeach
                @if (count($latest) === 0)
                    <tr>
                        <td colspan="6">Belum ada pengaduan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
