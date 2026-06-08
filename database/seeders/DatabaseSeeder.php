<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Add users
        DB::table('users')->insert([
            [
                'nama' => 'Kepala Shift',
                'username' => 'superadmin',
                'password' => Hash::make('Admin@12345'),
                'role' => 'super_admin',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'nama' => 'Karyawan Admin',
                'username' => 'admin',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
            ],
        ]);

        // Add landing settings
        DB::table('landing_settings')->insert([
            [
                'setting_key' => 'running_text',
                'setting_value' => 'Selamat datang di Sipena MBC Swalayan. Sampaikan pengaduan Anda dengan jelas. Untuk return produk, mohon upload struk belanja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'youtube_url',
                'setting_value' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'judul_hero',
                'setting_value' => 'Sistem Pengaduan MBC Swalayan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'deskripsi_hero',
                'setting_value' => 'Sampaikan pengaduan produk, pelayanan, return produk, dan keluhan lainnya secara daring. Pantau status pengaduan Anda menggunakan nomor tiket.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'kontak_layanan',
                'setting_value' => 'Jam layanan pengaduan: 08.00 - 21.00 WIB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
