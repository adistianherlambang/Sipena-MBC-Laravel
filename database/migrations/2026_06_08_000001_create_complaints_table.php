<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 30)->unique();
            $table->string('public_token', 100)->unique();
            $table->string('nama_pelanggan', 100);
            $table->string('nomor_wa', 30);
            $table->string('nomor_wa_clean', 30);
            $table->enum('kategori', ['pelayanan', 'return_produk', 'produk', 'masalah_lain']);
            $table->string('kategori_lain', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('struk_file', 255)->nullable();
            $table->string('dokumen_file', 255)->nullable();
            $table->enum('status', [
                'diajukan',
                'diproses',
                'diteruskan',
                'menunggu_keputusan',
                'ditanggapi',
                'selesai',
                'ditolak',
            ])->default('diajukan');

            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('escalated_to_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();
            $table->dateTime('closed_at')->nullable();

            $table->index('ticket_no');
            $table->index('nomor_wa_clean');
            $table->index('status');
            $table->index('kategori');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
