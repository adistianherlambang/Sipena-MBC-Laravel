@extends('layouts.app')

@section('content')
@if (!$complaint)
    <div class="card">
        <h2>Tiket tidak ditemukan</h2>
        <p>Pastikan link tiket benar.</p>
        <a class="btn btn-primary" href="{{ route('home') }}">Kembali</a>
    </div>
@else
    <div class="ticket-box">
        <p class="badge badge-green">Tiket Pengaduan</p>
        <h1 class="ticket-number">{{ $complaint->ticket_no }}</h1>
        <p>Simpan nomor tiket ini untuk melakukan tracking pengaduan.</p>
        <div class="grid grid-2">
            <div>
                <strong>Nama Pelanggan</strong><br>
                {{ $complaint->nama_pelanggan }}
            </div>
            <div>
                <strong>Nomor WhatsApp</strong><br>
                {{ $complaint->nomor_wa }}
            </div>
            <div>
                <strong>Masalah</strong><br>
                {{ $complaint->kategori_label }}
            </div>
            <div>
                <strong>Status</strong><br>
                <span class="badge {{ $complaint->status_class }}">{{ $complaint->status_label }}</span>
            </div>
            <div>
                <strong>Tanggal Diajukan</strong><br>
                {{ $complaint->created_at ? $complaint->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}
            </div>
            <div>
                <strong>Tracking</strong><br>
                Gunakan nomor tiket dan nomor WhatsApp Anda.
            </div>
        </div>
        <hr>
        <div class="btn-row no-print">
            <button class="btn btn-primary" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
            <a class="btn btn-outline" href="{{ route('complaint.tracking', ['ticket_no' => $complaint->ticket_no, 'nomor_wa' => $complaint->nomor_wa]) }}">Tracking Tiket</a>
            <a class="btn btn-muted" href="{{ route('home') }}">Kembali ke Beranda</a>
        </div>
    </div>
@endif
@endsection
