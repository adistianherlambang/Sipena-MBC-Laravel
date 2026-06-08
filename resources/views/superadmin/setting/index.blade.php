@extends('layouts.app', [
    'layout' => 'superadmin',
    'activeMenu' => 'pengaturan'
])

@section('content')
<div class="card">
    <h1>Pengaturan Landing Page</h1>
    <p class="meta">Ubah informasi yang tampil di halaman pelanggan.</p>
    <form method="post" action="{{ route('superadmin.setting.update') }}">
        @csrf
        <div class="form-group">
            <label>Judul Hero</label>
            <input name="judul_hero" type="text" value="{{ $judulHero }}">
        </div>
        <div class="form-group">
            <label>Deskripsi Hero</label>
            <textarea name="deskripsi_hero">{{ $deskripsiHero }}</textarea>
        </div>
        <div class="form-group">
            <label>Running Text</label>
            <textarea name="running_text">{{ $runningText }}</textarea>
        </div>
        <div class="form-group">
            <label>URL YouTube</label>
            <input name="youtube_url" type="text" value="{{ $youtubeUrl }}">
            <div class="help">Bisa memakai URL biasa YouTube atau URL embed.</div>
        </div>
        <div class="form-group">
            <label>Kontak/Jam Layanan</label>
            <input name="kontak_layanan" type="text" value="{{ $kontakLayanan }}">
        </div>
        <button class="btn btn-primary" type="submit">Simpan Pengaturan</button>
    </form>
</div>
@endsection
