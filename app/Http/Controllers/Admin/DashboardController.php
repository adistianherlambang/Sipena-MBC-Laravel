<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $total = Complaint::count();
        $baru = Complaint::where('status', 'diajukan')->count();
        $proses = Complaint::whereIn('status', ['diproses', 'diteruskan', 'menunggu_keputusan'])->count();
        $selesai = Complaint::where('status', 'selesai')->count();

        $latest = Complaint::with('assignedAdmin')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $isSuperArea = $request->is('superadmin*');

        return view('admin.dashboard', compact('total', 'baru', 'proses', 'selesai', 'latest', 'isSuperArea'));
    }
}
