@extends('layouts.app')

@section('content')
<section class="running"><span>{{ $runningText }}</span></section>

<section class="hero" id="informasi">
    <div class="hero-card">
        <p class="badge badge-green">Layanan Pengaduan Online</p>
        <h1>{{ $judulHero }}</h1>
        <p>{{ $deskripsiHero }}</p>
        <div class="btn-row">
            <a class="btn btn-primary" href="#form-pengaduan">Buat Pengaduan</a>
            <a class="btn btn-outline" href="#tracking">Tracking Tiket</a>
        </div>
    </div>
    <div class="hero-media card">
        @if ($youtubeUrl)
            <iframe class="video-frame" src="{{ $youtubeUrl }}" title="Video informasi Sipena" allowfullscreen></iframe>
        @else
            <div class="video-frame"></div>
        @endif
        <p class="meta">{{ $kontakLayanan }}</p>
    </div>
</section>

<section class="grid grid-4">
    <div class="stat"><span class="num">{{ $total }}</span><span class="label">Total Pengaduan</span></div>
    <div class="stat"><span class="num">{{ $diproses }}</span><span class="label">Sedang Diproses</span></div>
    <div class="stat"><span class="num">{{ $ditanggapi }}</span><span class="label">Sudah Ditanggapi</span></div>
    <div class="stat"><span class="num">{{ $selesai }}</span><span class="label">Selesai</span></div>
</section>

<section class="grid grid-3" style="margin-top: 18px;">
    <div class="card">
        <h3>1. Isi Form</h3>
        <p>Pelanggan mengisi nama, nomor WhatsApp, jenis masalah, dan keterangan pengaduan.</p>
    </div>
    <div class="card">
        <h3>2. Dapatkan Tiket</h3>
        <p>Sistem membuat nomor tiket otomatis. Simpan tiket untuk memantau proses pengaduan.</p>
    </div>
    <div class="card">
        <h3>3. Pantau Status</h3>
        <p>Gunakan nomor tiket dan nomor WhatsApp untuk melihat status serta tanggapan karyawan.</p>
    </div>
</section>

<section class="card" id="form-pengaduan">
    <h2>Isi Form Pengaduan</h2>
    <p class="meta">Kolom bertanda wajib harus diisi. Untuk return produk, upload struk belanja wajib dilakukan.</p>
    <form method="post" action="{{ route('complaint.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-2">
            <div class="form-group">
                <label for="nama_pelanggan">Nama Pelanggan *</label>
                <input id="nama_pelanggan" name="nama_pelanggan" type="text" maxlength="100" value="{{ old('nama_pelanggan') }}" required>
            </div>
            <div class="form-group">
                <label for="nomor_wa">Nomor WhatsApp *</label>
                <input id="nomor_wa" name="nomor_wa" type="text" maxlength="30" placeholder="Contoh: 08123456789" value="{{ old('nomor_wa') }}" required>
            </div>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label for="kategori">Pilihan Masalah *</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Masalah --</option>
                    <option value="pelayanan" {{ old('kategori') === 'pelayanan' ? 'selected' : '' }}>Pelayanan</option>
                    <option value="produk" {{ old('kategori') === 'produk' ? 'selected' : '' }}>Produk</option>
                    <option value="return_produk" {{ old('kategori') === 'return_produk' ? 'selected' : '' }}>Return Produk</option>
                    <option value="masalah_lain" {{ old('kategori') === 'masalah_lain' ? 'selected' : '' }}>Masalah Lain</option>
                </select>
            </div>
            <div class="form-group" id="kategori-lain-wrap" style="{{ old('kategori') === 'masalah_lain' ? '' : 'display:none;' }}">
                <label for="kategori_lain">Isi Jenis Masalah Lain *</label>
                <input id="kategori_lain" name="kategori_lain" type="text" value="{{ old('kategori_lain') }}" maxlength="150">
            </div>
        </div>
        <div class="grid grid-2">
            <div class="form-group" id="struk-wrap" style="{{ old('kategori') === 'return_produk' ? '' : 'display:none;' }}">
                <label for="struk_file">Upload Struk Belanja *</label>
                <input id="struk_file" name="struk_file" type="file" accept=".jpg,.jpeg,.png,.pdf">
                <div class="help">Format: JPG, PNG, PDF. Maksimal 2 MB.</div>
            </div>
            <div class="form-group">
                <label for="dokumen_file">Upload Dokumen Pendukung</label>
                <input id="dokumen_file" name="dokumen_file" type="file" accept=".jpg,.jpeg,.png,.pdf">
                <div class="help">Opsional. Bisa foto produk, bukti, atau dokumen lain.</div>
            </div>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan Lainnya</label>
            <textarea id="keterangan" name="keterangan" placeholder="Tuliskan kronologi atau detail keluhan Anda...">{{ old('keterangan') }}</textarea>
        </div>
        <button class="btn btn-primary" type="submit">Kirim Pengaduan</button>
    </form>
</section>

<section class="card" id="tracking">
    <h2>Tracking Tiket Pengaduan</h2>
    <p class="meta">Masukkan nomor tiket dan nomor WhatsApp yang digunakan saat mengisi form.</p>
    <form class="grid grid-3" method="get" action="{{ route('complaint.tracking') }}">
        <div class="form-group">
            <label for="ticket_no">Nomor Tiket</label>
            <input id="ticket_no" name="ticket_no" type="text" placeholder="SPN-20260602-0001" required>
        </div>
        <div class="form-group">
            <label for="tracking_wa">Nomor WhatsApp</label>
            <input id="tracking_wa" name="nomor_wa" type="text" placeholder="08123456789" required>
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button class="btn btn-primary" type="submit">Cek Status</button>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const kategori = document.getElementById('kategori');
    const kategoriLainWrap = document.getElementById('kategori-lain-wrap');
    const kategoriLain = document.getElementById('kategori_lain');
    const strukWrap = document.getElementById('struk-wrap');
    const strukFile = document.getElementById('struk_file');

    kategori.addEventListener('change', () => {
        if (kategori.value === 'masalah_lain') {
            kategoriLainWrap.style.display = '';
            kategoriLain.required = true;
        } else {
            kategoriLainWrap.style.display = 'none';
            kategoriLain.required = false;
            kategoriLain.value = '';
        }

        if (kategori.value === 'return_produk') {
            strukWrap.style.display = '';
            strukFile.required = true;
        } else {
            strukWrap.style.display = 'none';
            strukFile.required = false;
            strukFile.value = '';
        }
    });
});
</script>
@endsection
