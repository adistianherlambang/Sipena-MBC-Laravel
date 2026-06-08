@php
    /** @var \App\Models\Complaint|null $complaint */
    /** @var \Illuminate\Support\Collection|\App\Models\ComplaintResponse[] $responses */
    /** @var \Illuminate\Support\Collection|\App\Models\StatusLog[] $logs */
@endphp
@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Tracking Tiket Pengaduan</h1>
    <form class="grid grid-3" method="get" action="{{ route('complaint.tracking') }}">
        <div class="form-group">
            <label for="ticket_no">Nomor Tiket</label>
            <input id="ticket_no" name="ticket_no" type="text" value="{{ $ticket }}" required>
        </div>
        <div class="form-group">
            <label for="nomor_wa">Nomor WhatsApp</label>
            <input id="nomor_wa" name="nomor_wa" type="text" value="{{ $nomorWa }}" required>
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button class="btn btn-primary" type="submit">Cek Status</button>
        </div>
    </form>
</div>

@if ($ticket !== '' && $nomorWa !== '' && !$complaint)
    <div class="alert alert-warning">Tiket tidak ditemukan. Pastikan nomor tiket dan nomor WhatsApp sesuai.</div>
@endif

@if ($complaint)
    <div class="card">
        <p class="badge badge-blue">Nomor Tiket: {{ $complaint->ticket_no }}</p>
        <h2>{{ $complaint->public_status_label }}</h2>
        <div class="grid grid-2">
            <div><strong>Nama</strong><br>{{ $complaint->nama_pelanggan }}</div>
            <div><strong>Nomor WA</strong><br>{{ $complaint->nomor_wa }}</div>
            <div><strong>Masalah</strong><br>{{ $complaint->kategori_label }}</div>
            <div><strong>Status</strong><br><span class="badge {{ $complaint->status_class }}">{{ $complaint->status_label }}</span></div>
            <div><strong>Tanggal Diajukan</strong><br>{{ $complaint->created_at ? $complaint->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</div>
            <div><strong>Ditangani Oleh</strong><br>{{ $complaint->assignedAdmin ? $complaint->assignedAdmin->nama : '-' }}</div>
        </div>
        @if ($complaint->keterangan)
            <hr>
            <strong>Keterangan Pelanggan</strong>
            <p>{!! nl2br(e($complaint->keterangan)) !!}</p>
        @endif
    </div>

    <div class="card">
        <h2>Tindak Lanjut dari Karyawan</h2>
        @if (count($responses) === 0)
            <p class="meta">Belum ada tanggapan publik dari karyawan.</p>
        @else
            <div class="timeline">
                @foreach ($responses as $response)
                    <div class="timeline-item">
                        <strong>{{ $response->user ? $response->user->nama : 'Petugas' }}</strong>
                        <span class="meta"> - {{ $response->created_at ? $response->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                        <p>{!! nl2br(e($response->message)) !!}</p>
                        @if ($response->attachment_file)
                            <a class="btn btn-sm btn-outline" target="_blank" href="{{ asset('storage/uploads/' . $response->attachment_file) }}">Lihat Lampiran</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Riwayat Status</h2>
        <div class="timeline">
            @foreach ($logs as $log)
                <div class="timeline-item">
                    <span class="badge {{ match ($log->new_status) {
                        'diajukan' => 'badge-gray',
                        'diproses' => 'badge-blue',
                        'diteruskan', 'menunggu_keputusan' => 'badge-orange',
                        'ditanggapi' => 'badge-purple',
                        'selesai' => 'badge-green',
                        'ditolak' => 'badge-red',
                        default => 'badge-gray',
                    } }}">{{ match ($log->new_status) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'diteruskan' => 'Diteruskan',
                        'menunggu_keputusan' => 'Menunggu Keputusan',
                        'ditanggapi' => 'Ditanggapi',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => ucfirst(str_replace('_', ' ', (string) $log->new_status)),
                    } }}</span>
                    <span class="meta"> - {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
