<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengaduan</title>
</head>
<body>
<h1>Laporan Pengaduan Sipena MBC Swalayan</h1>
<table border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr>
            <th>No</th>
            <th>Tiket</th>
            <th>Pelanggan</th>
            <th>Nomor WA</th>
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
                <td>{{ $row->nomor_wa }}</td>
                <td>{{ $row->kategori_label }}</td>
                <td>{{ $row->status_label }}</td>
                <td>{{ $row->assignedAdmin ? $row->assignedAdmin->nama : '-' }}</td>
                <td>{{ $row->created_at ? $row->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                <td>{{ $row->closed_at ? $row->closed_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
            </tr>
        @endforeach
        @if (count($rows) === 0)
            <tr>
                <td colspan="9">Tidak ada data laporan.</td>
            </tr>
        @endif
    </tbody>
</table>
</body>
</html>
