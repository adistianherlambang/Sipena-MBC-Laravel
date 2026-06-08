<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $status = trim((string) $request->get('status'));
        $kategori = trim((string) $request->get('kategori'));
        $limit = (int) $request->get('limit', 10);
        if (! in_array($limit, [10, 50, 100])) {
            $limit = 10;
        }

        $query = Complaint::with(['assignedAdmin', 'escalatedTo']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('ticket_no', 'like', '%'.$q.'%')
                    ->orWhere('nama_pelanggan', 'like', '%'.$q.'%')
                    ->orWhere('nomor_wa', 'like', '%'.$q.'%')
                    ->orWhere('keterangan', 'like', '%'.$q.'%');
            });
        }

        if ($status !== '' && in_array($status, ['diajukan', 'diproses', 'diteruskan', 'menunggu_keputusan', 'ditanggapi', 'selesai', 'ditolak'])) {
            $query->where('status', $status);
        }

        if ($kategori !== '' && in_array($kategori, ['pelayanan', 'produk', 'return_produk', 'masalah_lain'])) {
            $query->where('kategori', $kategori);
        }

        $rows = $query->orderBy('created_at', 'desc')->paginate($limit)->withQueryString();

        $isSuperArea = $request->is('superadmin*');
        $areaPath = $isSuperArea ? 'superadmin' : 'admin';

        return view('admin.pengaduan.index', compact('rows', 'q', 'status', 'kategori', 'limit', 'isSuperArea', 'areaPath'));
    }

    public function show($id, Request $request)
    {
        $complaint = Complaint::with(['assignedAdmin', 'escalatedTo'])->findOrFail($id);

        $responses = $complaint->responses()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $logs = $complaint->statusLogs()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $isSuperArea = $request->is('superadmin*');
        $areaPath = $isSuperArea ? 'superadmin' : 'admin';

        return view('admin.pengaduan.show', compact('complaint', 'responses', 'logs', 'isSuperArea', 'areaPath'));
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:diajukan,diproses,diteruskan,menunggu_keputusan,ditanggapi,selesai,ditolak',
            'note' => 'nullable|string',
        ]);

        $complaint = Complaint::findOrFail($id);
        $user = Auth::user();
        $oldStatus = $complaint->status;
        $newStatus = $request->status;

        $updateData = [
            'status' => $newStatus,
            'closed_at' => $newStatus === 'selesai' ? ($complaint->closed_at ?? now()) : null,
        ];

        if (empty($complaint->assigned_admin_id) && $user->role === 'admin') {
            $updateData['assigned_admin_id'] = $user->id;
        }

        $complaint->update($updateData);

        StatusLog::create([
            'complaint_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $user->id,
            'note' => $request->filled('note') ? $request->note : null,
        ]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function addResponse($id, Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'visibility' => 'required|in:internal,public',
            'attachment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'message.required' => 'Tanggapan wajib diisi.',
            'attachment_file.max' => 'Ukuran file lampiran maksimal 2 MB.',
            'attachment_file.mimes' => 'Format file lampiran hanya boleh JPG, PNG, atau PDF.',
        ]);

        $complaint = Complaint::findOrFail($id);
        $user = Auth::user();

        $attachmentPath = null;
        if ($request->hasFile('attachment_file')) {
            $file = $request->file('attachment_file');
            $filename = date('YmdHis').'-'.Str::random(16).'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/uploads/tanggapan', $filename);
            $attachmentPath = 'tanggapan/'.$filename;
        }

        ComplaintResponse::create([
            'complaint_id' => $id,
            'user_id' => $user->id,
            'sender_role' => $user->role,
            'visibility' => $request->visibility,
            'message' => $request->message,
            'attachment_file' => $attachmentPath,
        ]);

        if ($request->visibility === 'public' && $request->has('update_to_responded')) {
            $oldStatus = $complaint->status;
            $complaint->update(['status' => 'ditanggapi']);

            StatusLog::create([
                'complaint_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => 'ditanggapi',
                'changed_by' => $user->id,
                'note' => 'Tanggapan publik ditambahkan.',
            ]);
        }

        $successMsg = $request->visibility === 'public' ? 'Tanggapan publik berhasil ditambahkan.' : 'Catatan internal berhasil ditambahkan.';

        return back()->with('success', $successMsg);
    }

    public function escalate($id, Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat meneruskan tiket.');
        }

        $request->validate([
            'escalation_note' => 'required|string',
        ]);

        $complaint = Complaint::findOrFail($id);

        $superAdmin = User::where('role', 'super_admin')->where('is_active', true)->orderBy('id', 'asc')->first();
        if (! $superAdmin) {
            return back()->withErrors(['error' => 'Belum ada kepala shift / super admin aktif.']);
        }

        $oldStatus = $complaint->status;
        $complaint->update([
            'status' => 'menunggu_keputusan',
            'assigned_admin_id' => $user->id,
            'escalated_to_id' => $superAdmin->id,
        ]);

        StatusLog::create([
            'complaint_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => 'menunggu_keputusan',
            'changed_by' => $user->id,
            'note' => 'Diteruskan ke kepala shift.',
        ]);

        ComplaintResponse::create([
            'complaint_id' => $id,
            'user_id' => $user->id,
            'sender_role' => 'admin',
            'visibility' => 'internal',
            'message' => 'Eskalasi ke kepala shift: '.$request->escalation_note,
        ]);

        return back()->with('success', 'Pengaduan berhasil diteruskan ke kepala shift.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $complaint = Complaint::findOrFail($request->id);
        $complaint->delete();

        return redirect()->route('superadmin.complaint.index')->with('success', 'Pengaduan berhasil dihapus.');
    }
}
