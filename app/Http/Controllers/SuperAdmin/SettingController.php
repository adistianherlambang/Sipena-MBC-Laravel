<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $judulHero = LandingSetting::getValue('judul_hero');
        $deskripsiHero = LandingSetting::getValue('deskripsi_hero');
        $runningText = LandingSetting::getValue('running_text');
        $youtubeUrl = LandingSetting::getValue('youtube_url');
        $kontakLayanan = LandingSetting::getValue('kontak_layanan');

        return view('superadmin.setting.index', compact(
            'judulHero', 'deskripsiHero', 'runningText', 'youtubeUrl', 'kontakLayanan'
        ));
    }

    public function update(Request $request)
    {
        LandingSetting::setValue('judul_hero', trim((string) $request->judul_hero));
        LandingSetting::setValue('deskripsi_hero', trim((string) $request->deskripsi_hero));
        LandingSetting::setValue('running_text', trim((string) $request->running_text));
        LandingSetting::setValue('youtube_url', trim((string) $request->youtube_url));
        LandingSetting::setValue('kontak_layanan', trim((string) $request->kontak_layanan));

        return to_route('superadmin.setting.index')->with('success', 'Pengaturan landing page berhasil disimpan.');
    }
}
