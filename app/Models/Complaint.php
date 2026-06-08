<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    protected $fillable = [
        'ticket_no',
        'public_token',
        'nama_pelanggan',
        'nomor_wa',
        'nomor_wa_clean',
        'kategori',
        'kategori_lain',
        'keterangan',
        'struk_file',
        'dokumen_file',
        'status',
        'assigned_admin_id',
        'escalated_to_id',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ComplaintResponse::class, 'complaint_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'complaint_id');
    }

    // Accessors
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'pelayanan' => 'Pelayanan',
            'return_produk' => 'Return Produk',
            'produk' => 'Produk',
            'masalah_lain' => $this->kategori_lain ? 'Masalah Lain: '.$this->kategori_lain : 'Masalah Lain',
            default => ucfirst(str_replace('_', ' ', (string) $this->kategori)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Diajukan',
            'diproses' => 'Diproses',
            'diteruskan' => 'Diteruskan',
            'menunggu_keputusan' => 'Menunggu Keputusan',
            'ditanggapi' => 'Ditanggapi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function getPublicStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Pengaduan Anda sudah diterima',
            'diproses' => 'Pengaduan sedang diproses',
            'diteruskan' => 'Pengaduan sedang ditinjau lebih lanjut',
            'menunggu_keputusan' => 'Menunggu keputusan penanggung jawab',
            'ditanggapi' => 'Tanggapan sudah tersedia',
            'selesai' => 'Pengaduan selesai',
            'ditolak' => 'Pengaduan tidak dapat diproses',
            default => $this->status_label,
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'badge-gray',
            'diproses' => 'badge-blue',
            'diteruskan', 'menunggu_keputusan' => 'badge-orange',
            'ditanggapi' => 'badge-purple',
            'selesai' => 'badge-green',
            'ditolak' => 'badge-red',
            default => 'badge-gray',
        };
    }
}
