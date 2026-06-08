@extends('layouts.app', [
    'layout' => $isSuperArea ? 'superadmin' : 'admin',
    'pageTitle' => 'Cetak Laporan'
])

@section('content')
<div class="card">
    <div class="btn-row no-print" style="justify-content: space-between;">
        <h1>Laporan Pengaduan</h1>
        <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
    <h2 class="print-only">Laporan Pengaduan Sipena MBC Swalayan</h2>
    <p class="meta">Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tiket</th>
                    <th>Pelanggan</th>
                    <th>Masalah</th>
                    <th>Status</th>
                    <th>Admin</th>
                    <th>Tanggal</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->ticket_no }}</td>
                        <td>{{ $row->nama_pelanggan }}</td>
                        <td>{{ $row->kategori_label }}</td>
                        <td>{{ $row->status_label }}</td>
                        <td>{{ $row->assignedAdmin ? $row->assignedAdmin->nama : '-' }}</td>
                        <td>{{ $row->created_at ? $row->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                        <td>{{ $row->closed_at ? $row->closed_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                    </tr>
                @endforeach
                @if (count($rows) === 0)
                    <tr>
                        <td colspan="8">Tidak ada data laporan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
