@extends('layouts.app', [
    'layout' => $isSuperArea ? 'superadmin' : 'admin',
    'activeMenu' => 'pengaduan'
])

@section('content')
<div class="card">
    <div class="btn-row" style="justify-content: space-between;">
        <div>
            <p class="badge badge-blue">Nomor Tiket: {{ $complaint->ticket_no }}</p>
            <h1>Detail Pengaduan</h1>
        </div>
        <a class="btn btn-outline" href="{{ route($areaPath . '.complaint.index') }}">Kembali</a>
    </div>
    <div class="grid grid-2">
        <div><strong>Nama Pelanggan</strong><br>{{ $complaint->nama_pelanggan }}</div>
        <div><strong>Nomor WhatsApp</strong><br>{{ $complaint->nomor_wa }}</div>
        <div><strong>Masalah</strong><br>{{ $complaint->kategori_label }}</div>
        <div><strong>Status</strong><br><span class="badge {{ $complaint->status_class }}">{{ $complaint->status_label }}</span></div>
        <div><strong>Admin Aktif</strong><br>{{ $complaint->assignedAdmin ? $complaint->assignedAdmin->nama : '-' }}</div>
        <div><strong>Kepala Shift</strong><br>{{ $complaint->escalatedTo ? $complaint->escalatedTo->nama : '-' }}</div>
        <div><strong>Dibuat</strong><br>{{ $complaint->created_at ? $complaint->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</div>
        <div><strong>Selesai</strong><br>{{ $complaint->closed_at ? $complaint->closed_at->format('d/m/Y H:i') . ' WIB' : '-' }}</div>
    </div>
    @if ($complaint->keterangan)
        <hr>
        <strong>Keterangan</strong>
        <p>{!! nl2br(e($complaint->keterangan)) !!}</p>
    @endif
    <div class="btn-row">
        @if ($complaint->struk_file)
            <a class="btn btn-sm btn-outline" target="_blank" href="{{ asset('storage/uploads/' . $complaint->struk_file) }}">Lihat Struk</a>
        @endif
        @if ($complaint->dokumen_file)
            <a class="btn btn-sm btn-outline" target="_blank" href="{{ asset('storage/uploads/' . $complaint->dokumen_file) }}">Lihat Dokumen</a>
        @endif
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>Ubah Status</h2>
        <form method="post" action="{{ route('admin.complaint.update_status', $complaint->id) }}">
            @csrf
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    @foreach (['diajukan', 'diproses', 'diteruskan', 'menunggu_keputusan', 'ditanggapi', 'selesai', 'ditolak'] as $statusOption)
                        <option value="{{ $statusOption }}" {{ $complaint->status === $statusOption ? 'selected' : '' }}>
                            {{ match ($statusOption) {
                                'diajukan' => 'Diajukan',
                                'diproses' => 'Diproses',
                                'diteruskan' => 'Diteruskan',
                                'menunggu_keputusan' => 'Menunggu Keputusan',
                                'ditanggapi' => 'Ditanggapi',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                                default => ucfirst($statusOption),
                            } }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Catatan Perubahan Status</label>
                <textarea name="note" placeholder="Opsional, catatan internal untuk riwayat status..."></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Simpan Status</button>
        </form>
    </div>

    @if (!$isSuperArea && Auth::user()->role === 'admin')
        <div class="card">
            <h2>Teruskan ke Kepala Shift</h2>
            <p class="meta">Gunakan jika masalah tidak dapat diselesaikan oleh karyawan/admin.</p>
            <form method="post" action="{{ route('admin.complaint.escalate', $complaint->id) }}">
                @csrf
                <div class="form-group">
                    <label>Catatan untuk Kepala Shift</label>
                    <textarea name="escalation_note" required placeholder="Tuliskan alasan eskalasi dan informasi yang dibutuhkan..."></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Teruskan</button>
            </form>
        </div>
    @else
        <div class="card">
            <h2>Informasi Eskalasi</h2>
            <p>Jika pengaduan diteruskan oleh admin, kepala shift dapat memberi tanggapan internal atau publik melalui form tanggapan di bawah.</p>
            <p class="meta">Tanggapan internal hanya terlihat di dashboard. Tanggapan publik tampil di tracking tiket pelanggan.</p>
        </div>
    @endif
</div>

<div class="card">
    <h2>Workplace / Isi Tanggapan</h2>
    <form method="post" action="{{ route('admin.complaint.add_response', $complaint->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-2">
            <div class="form-group">
                <label>Jenis Tanggapan</label>
                <select name="visibility">
                    <option value="internal">Internal dashboard saja</option>
                    <option value="public">Publik, tampil di tracking pelanggan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Lampiran Tanggapan</label>
                <input type="file" name="attachment_file" accept=".jpg,.jpeg,.png,.pdf">
                <div class="help">Opsional. Format JPG, PNG, PDF. Maksimal 2 MB.</div>
            </div>
        </div>
        <div class="form-group">
            <label>Isi Tanggapan</label>
            <textarea name="message" required placeholder="Tulis tanggapan, keputusan, instruksi, atau follow-up..."></textarea>
        </div>
        <label class="small">
            <input type="checkbox" name="update_to_responded" value="1" style="width:auto;"> 
            Jika publik, ubah status menjadi Ditanggapi
        </label>
        <br><br>
        <button class="btn btn-primary" type="submit">Kirim Tanggapan</button>
    </form>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>Riwayat Tanggapan</h2>
        @if (count($responses) === 0)
            <p class="meta">Belum ada tanggapan.</p>
        @else
            <div class="timeline">
                @foreach ($responses as $response)
                    <div class="timeline-item {{ $response->visibility === 'internal' ? 'internal' : '' }}">
                        <strong>{{ $response->user ? $response->user->nama : 'System' }}</strong>
                        <span class="badge {{ $response->visibility === 'public' ? 'badge-green' : 'badge-orange' }}">
                            {{ $response->visibility === 'public' ? 'Publik' : 'Internal' }}
                        </span>
                        <span class="meta"> - {{ $response->created_at ? $response->created_at->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                        <p>{!! nl2br(e($response->message)) !!}</p>
                        @if ($response->attachment_file)
                            <a class="btn btn-sm btn-outline" target="_blank" href="{{ asset('storage/uploads/' . $response->attachment_file) }}">Lampiran</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Riwayat Status</h2>
        @if (count($logs) === 0)
            <p class="meta">Belum ada log status.</p>
        @else
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
                        <p class="meta">Oleh: {{ $log->user ? $log->user->nama : 'Pelanggan/System' }}</p>
                        @if ($log->note)
                            <p>{!! nl2br(e($log->note)) !!}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
