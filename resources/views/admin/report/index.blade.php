@php
    /** @var \Illuminate\Support\Collection|\App\Models\Complaint[] $rows */
@endphp
@extends('layouts.app', [
    'layout' => $isSuperArea ? 'superadmin' : 'admin',
    'activeMenu' => 'laporan'
])

@section('content')
<div class="card">
    <h1>Laporan Pengaduan</h1>
    <form class="filter-form" method="get" action="{{ route($areaPath . '.report.index') }}">
        <div class="form-group">
            <label>Bulan</label>
            <select name="bulan">
                <option value="">Semua</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (string) $filters['bulan'] === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label>Tahun</label>
            <input name="tahun" type="number" value="{{ $filters['tahun'] }}" placeholder="{{ date('Y') }}">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                @foreach (['diajukan', 'diproses', 'diteruskan', 'menunggu_keputusan', 'ditanggapi', 'selesai', 'ditolak'] as $s)
                    <option value="{{ $s }}" {{ $filters['status'] === $s ? 'selected' : '' }}>
                        {{ match ($s) {
                            'diajukan' => 'Diajukan',
                            'diproses' => 'Diproses',
                            'diteruskan' => 'Diteruskan',
                            'menunggu_keputusan' => 'Menunggu Keputusan',
                            'ditanggapi' => 'Ditanggapi',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                            default => ucfirst($s),
                        } }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Tampil</label>
            <select name="limit">
                @foreach ([10, 50, 100, 500] as $l)
                    <option value="{{ $l }}" {{ (int) $filters['limit'] === $l ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>
    <div class="btn-row">
        <a class="btn btn-outline" href="{{ route($areaPath . '.report.export_csv') }}?{{ $queryString }}">Download Excel/CSV</a>
        <a class="btn btn-outline" href="{{ route($areaPath . '.report.export_word') }}?{{ $queryString }}">Download Word</a>
        <a class="btn btn-outline" target="_blank" href="{{ route($areaPath . '.report.cetak_pdf') }}?{{ $queryString }}">Cetak / PDF</a>
    </div>
</div>

<div class="card">
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
                    @if ($isSuperArea)
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->ticket_no }}</td>
                        <td>{{ $row->nama_pelanggan }}<br><span class="meta">{{ $row->nomor_wa }}</span></td>
                        <td>{{ $row->kategori_label }}</td>
                        <td><span class="badge {{ $row->status_class }}">{{ $row->status_label }}</span></td>
                        <td>{{ $row->assignedAdmin ? $row->assignedAdmin->nama : '-' }}</td>
                        <td>{{ $row->created_at ? $row->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                        <td>{{ $row->closed_at ? $row->closed_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                        @if ($isSuperArea)
                            <td>
                                <a class="btn btn-sm btn-outline" href="{{ route('superadmin.complaint.show', $row->id) }}">Edit</a>
                            </td>
                        @endif
                    </tr>
                @endforeach
                @if (count($rows) === 0)
                    <tr>
                        <td colspan="{{ $isSuperArea ? 9 : 8 }}">Tidak ada data laporan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
