<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\LandingSetting;
use App\Models\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    public function index()
    {
        $total = Complaint::count();
        $diproses = Complaint::whereIn('status', ['diproses', 'diteruskan', 'menunggu_keputusan'])->count();
        $selesai = Complaint::where('status', 'selesai')->count();
        $ditanggapi = Complaint::where('status', 'ditanggapi')->count();

        $runningText = LandingSetting::getValue('running_text', 'Selamat datang di Sipena MBC Swalayan.');
        $youtubeUrl = $this->youtubeEmbedUrl(LandingSetting::getValue('youtube_url', ''));
        $judulHero = LandingSetting::getValue('judul_hero', 'Sistem Pengaduan MBC Swalayan');
        $deskripsiHero = LandingSetting::getValue('deskripsi_hero', 'Sampaikan pengaduan Anda secara daring dan pantau statusnya menggunakan nomor tiket.');
        $kontakLayanan = LandingSetting::getValue('kontak_layanan', 'Jam layanan pengaduan: 08.00 - 21.00 WIB');

        return view('public.index', compact(
            'total', 'diproses', 'selesai', 'ditanggapi',
            'runningText', 'youtubeUrl', 'judulHero', 'deskripsiHero', 'kontakLayanan'
        ));
    }

    public function submitComplaint(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|min:2|max:100',
            'nomor_wa' => 'required|string|min:9|max:30',
            'kategori' => 'required|in:pelayanan,produk,return_produk,masalah_lain',
            'kategori_lain' => 'required_if:kategori,masalah_lain|nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'struk_file' => 'required_if:kategori,return_produk|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'dokumen_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'nomor_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'kategori.required' => 'Pilihan masalah wajib diisi.',
            'kategori_lain.required_if' => 'Jenis masalah lain wajib diisi.',
            'struk_file.required_if' => 'Struk belanja wajib diupload untuk return produk.',
            'struk_file.max' => 'Ukuran struk belanja maksimal 2 MB.',
            'dokumen_file.max' => 'Ukuran dokumen pendukung maksimal 2 MB.',
            'struk_file.mimes' => 'Format struk hanya boleh JPG, PNG, atau PDF.',
            'dokumen_file.mimes' => 'Format dokumen hanya boleh JPG, PNG, atau PDF.',
        ]);

        $nomorWaClean = preg_replace('/\D+/', '', $request->nomor_wa) ?: '';
        if (strlen($nomorWaClean) < 9) {
            return back()->withErrors(['nomor_wa' => 'Nomor WhatsApp tidak valid.'])->withInput();
        }

        try {
            $strukPath = null;
            if ($request->hasFile('struk_file')) {
                $file = $request->file('struk_file');
                $filename = date('YmdHis').'-'.Str::random(16).'.'.$file->getClientOriginalExtension();
                $file->storeAs('public/uploads/struk', $filename);
                $strukPath = 'struk/'.$filename;
            }

            $dokumenPath = null;
            if ($request->hasFile('dokumen_file')) {
                $file = $request->file('dokumen_file');
                $filename = date('YmdHis').'-'.Str::random(16).'.'.$file->getClientOriginalExtension();
                $file->storeAs('public/uploads/dokumen', $filename);
                $dokumenPath = 'dokumen/'.$filename;
            }

            $ticketNo = $this->generateTicketNo();
            $publicToken = bin2hex(random_bytes(32));

            $complaint = Complaint::create([
                'ticket_no' => $ticketNo,
                'public_token' => $publicToken,
                'nama_pelanggan' => $request->nama_pelanggan,
                'nomor_wa' => $request->nomor_wa,
                'nomor_wa_clean' => $nomorWaClean,
                'kategori' => $request->kategori,
                'kategori_lain' => $request->kategori === 'masalah_lain' ? $request->kategori_lain : null,
                'keterangan' => $request->keterangan,
                'struk_file' => $strukPath,
                'dokumen_file' => $dokumenPath,
                'status' => 'diajukan',
            ]);

            StatusLog::create([
                'complaint_id' => $complaint->id,
                'old_status' => null,
                'new_status' => 'diajukan',
                'changed_by' => null,
                'note' => 'Pengaduan dibuat oleh pelanggan.',
            ]);

            return to_route('complaint.tiket', [
                'ticket' => $ticketNo,
                'token' => $publicToken,
            ])->with('success', 'Pengaduan berhasil dikirim. Simpan nomor tiket Anda.');

        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan pengaduan: '.$e->getMessage()])->withInput();
        }
    }

    public function tracking(Request $request)
    {
        $ticket = trim((string) $request->input('ticket_no'));
        $nomorWa = trim((string) $request->input('nomor_wa'));
        $nomorWaClean = preg_replace('/\D+/', '', $nomorWa) ?: '';

        $complaint = null;
        $responses = [];
        $logs = [];

        if ($ticket !== '' && $nomorWaClean !== '') {
            $complaint = Complaint::with(['assignedAdmin', 'escalatedTo'])
                ->where('ticket_no', $ticket)
                ->where('nomor_wa_clean', $nomorWaClean)
                ->first();

            if ($complaint) {
                $responses = $complaint->responses()
                    ->with('user')
                    ->where('visibility', 'public')
                    ->orderBy('created_at', 'asc')
                    ->get();

                $logs = $complaint->statusLogs()
                    ->with('user')
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('public.tracking', compact('ticket', 'nomorWa', 'complaint', 'responses', 'logs'));
    }

    public function tiket(Request $request)
    {
        $ticket = trim((string) $request->input('ticket'));
        $token = trim((string) $request->input('token'));

        $complaint = Complaint::where('ticket_no', $ticket)
            ->where('public_token', $token)
            ->first();

        return view('public.tiket', compact('complaint'));
    }

    private function generateTicketNo(): string
    {
        $date = date('Ymd');
        $todayCount = Complaint::whereDate('created_at', today())->count();

        for ($i = 1; $i <= 20; $i++) {
            $number = $todayCount + $i;
            $ticket = 'SPN-'.$date.'-'.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            if (! Complaint::where('ticket_no', $ticket)->exists()) {
                return $ticket;
            }
        }

        return 'SPN-'.$date.'-'.strtoupper(Str::random(6));
    }

    private function youtubeEmbedUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (str_contains($url, '/embed/')) {
            return $url;
        }
        if (preg_match('#youtu\.be/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }
        if (preg_match('#[?&]v=([a-zA-Z0-9_-]+)#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return $url;
    }
}
