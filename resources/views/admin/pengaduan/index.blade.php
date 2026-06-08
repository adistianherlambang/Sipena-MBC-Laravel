@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Complaint[] $rows */
@endphp
@extends('layouts.app', [
    'layout' => $isSuperArea ? 'superadmin' : 'admin',
    'activeMenu' => 'pengaduan'
])

@section('content')
<div class="card">
    <h1>{{ $isSuperArea ? 'Daftar Pengaduan Kepala Shift' : 'Daftar Pengaduan Karyawan' }}</h1>
    <form class="filter-form" method="get" action="{{ route($isSuperArea ? 'superadmin.complaint.index' : 'admin.complaint.index') }}">
        <div class="form-group">
            <label>Cari</label>
            <input name="q" type="text" value="{{ $q }}" placeholder="Tiket, nama, WA">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                @foreach (['diajukan', 'diproses', 'diteruskan', 'menunggu_keputusan', 'ditanggapi', 'selesai', 'ditolak'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>
                        {{ match ($s) {
                            'diajukan' => 'Diajukan',
                            'diproses' => 'Diproses',
                            'diteruskan' => 'Diteruskan',
                            'menunggu_keputusan' => 'Menunggu Keputusan',
                            'ditanggapi' => 'Ditanggapi',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                            default => ucfirst(str_replace('_', ' ', $s)),
                        } }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori">
                <option value="">Semua</option>
                @foreach (['pelayanan', 'produk', 'return_produk', 'masalah_lain'] as $cat)
                    <option value="{{ $cat }}" {{ $kategori === $cat ? 'selected' : '' }}>
                        {{ match ($cat) {
                            'pelayanan' => 'Pelayanan',
                            'produk' => 'Produk',
                            'return_produk' => 'Return Produk',
                            'masalah_lain' => 'Masalah Lain',
                            default => ucfirst($cat),
                        } }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Tampil</label>
            <select name="limit">
                @foreach ([10, 50, 100] as $l)
                    <option value="{{ $l }}" {{ $limit === $l ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Pelanggan</th>
                    <th>Masalah</th>
                    <th>Upload</th>
                    <th>Status</th>
                    <th>Admin Aktif</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td><strong>{{ $row->ticket_no }}</strong></td>
                        <td>{{ $row->nama_pelanggan }}<br><span class="meta">{{ $row->nomor_wa }}</span></td>
                        <td>{{ $row->kategori_label }}</td>
                        <td>
                            @if ($row->struk_file)
                                <a target="_blank" href="{{ asset('storage/uploads/' . $row->struk_file) }}">Struk</a><br>
                            @endif
                            @if ($row->dokumen_file)
                                <a target="_blank" href="{{ asset('storage/uploads/' . $row->dokumen_file) }}">Dokumen</a>
                            @endif
                            @if (!$row->struk_file && !$row->dokumen_file)
                                -
                            @endif
                        </td>
                        <td><span class="badge {{ $row->status_class }}">{{ $row->status_label }}</span></td>
                        <td>{{ $row->assignedAdmin ? $row->assignedAdmin->nama : '-' }}</td>
                        <td>{{ $row->created_at ? $row->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-sm btn-outline" href="{{ route($areaPath . '.complaint.show', $row->id) }}">Detail</a>
                                @if ($isSuperArea)
                                    <form method="post" action="{{ route('superadmin.complaint.destroy') }}" onsubmit="return confirm('Hapus pengaduan ini? Data tanggapan dan log ikut terhapus.');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $row->id }}">
                                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if (count($rows) === 0)
                    <tr><td colspan="8">Data tidak ditemukan.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Custom pagination matching style.css --}}
    @if ($rows->hasPages())
        <div class="pagination-wrap" style="margin-top: 15px; display: flex; gap: 5px; justify-content: center;">
            @if ($rows->onFirstPage())
                <span class="btn btn-sm btn-outline disabled" style="opacity: 0.5; cursor: not-allowed;">Prev</span>
            @else
                <a class="btn btn-sm btn-outline" href="{{ $rows->previousPageUrl() }}">Prev</a>
            @endif

            @foreach ($rows->getUrlRange(max(1, $rows->currentPage() - 2), min($rows->lastPage(), $rows->currentPage() + 2)) as $page => $url)
                @if ($page == $rows->currentPage())
                    <span class="btn btn-sm btn-primary">{{ $page }}</span>
                @else
                    <a class="btn btn-sm btn-outline" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($rows->hasMorePages())
                <a class="btn btn-sm btn-outline" href="{{ $rows->nextPageUrl() }}">Next</a>
            @else
                <span class="btn btn-sm btn-outline disabled" style="opacity: 0.5; cursor: not-allowed;">Next</span>
            @endif
        </div>
    @endif
</div>
@endsection
