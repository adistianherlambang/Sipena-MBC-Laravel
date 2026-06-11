<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        $rows = $this->getReportRows($filters);

        $isSuperArea = $request->is('superadmin*');
        $areaPath = $isSuperArea ? 'superadmin' : 'admin';
        $queryString = http_build_query($filters);

        return view('admin.report.index', compact('rows', 'filters', 'isSuperArea', 'areaPath', 'queryString'));
    }

    public function exportCsv(Request $request)
    {
        $filters = $this->getFilters($request);
        $rows = $this->getReportRows($filters);

        $filename = 'laporan_pengaduan_'.date('Ymd_His').'.csv';

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No', 'Nomor Tiket', 'Nama Pelanggan', 'Nomor WA', 'Masalah',
                'Keterangan', 'Status', 'Admin', 'Kepala Shift', 'Tanggal Diajukan', 'Tanggal Selesai',
            ]);

            foreach ($rows as $i => $row) {
                fputcsv($handle, [
                    $i + 1,
                    $row->ticket_no,
                    $row->nama_pelanggan,
                    $row->nomor_wa,
                    $row->kategori_label,
                    $row->keterangan,
                    $row->status_label,
                    $row->assignedAdmin ? $row->assignedAdmin->nama : '-',
                    $row->escalatedTo ? $row->escalatedTo->nama : '-',
                    $row->created_at ? $row->created_at->format('d/m/Y H:i').' WIB' : '-',
                    $row->closed_at ? $row->closed_at->format('d/m/Y H:i').' WIB' : '-',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    public function exportWord(Request $request)
    {
        $filters = $this->getFilters($request);
        $rows = $this->getReportRows($filters);

        $filename = 'laporan_pengaduan_'.date('Ymd_His').'.doc';

        $isSuperArea = $request->is('superadmin*');

        return response()
            ->view('admin.report.word', compact('rows', 'isSuperArea'))
            ->header('Content-Type', 'application/vnd.ms-word; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function cetakPdf(Request $request)
    {
        $filters = $this->getFilters($request);
        $rows = $this->getReportRows($filters);

        $isSuperArea = $request->is('superadmin*');
        $areaPath = $isSuperArea ? 'superadmin' : 'admin';

        return view('admin.report.print', compact('rows', 'isSuperArea', 'areaPath'));
    }

    private function getFilters(Request $request): array
    {
        $status = $request->input('status', '');
        if ($status !== '' && ! in_array($status, ['diajukan', 'diproses', 'diteruskan', 'menunggu_keputusan', 'ditanggapi', 'selesai', 'ditolak'])) {
            $status = '';
        }

        $bulan = $request->input('bulan', '');
        if ($bulan !== '' && (! ctype_digit((string) $bulan) || (int) $bulan < 1 || (int) $bulan > 12)) {
            $bulan = '';
        }

        $tahun = $request->input('tahun', '');
        if ($tahun !== '' && (! ctype_digit((string) $tahun) || (int) $tahun < 2020 || (int) $tahun > 2100)) {
            $tahun = '';
        }

        $limit = (int) $request->input('limit', 10);
        if (! in_array($limit, [10, 50, 100, 500])) {
            $limit = 10;
        }

        return [
            'status' => $status,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'limit' => $limit,
        ];
    }

    private function getReportRows(array $filters)
    {
        $query = Complaint::with(['assignedAdmin', 'escalatedTo']);

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['bulan'] !== '') {
            $monthPad = str_pad($filters['bulan'], 2, '0', STR_PAD_LEFT);
            if (config('database.default') === 'sqlite') {
                $query->whereRaw("strftime('%m', created_at) = ?", [$monthPad]);
            } else {
                $query->whereMonth('created_at', $filters['bulan']);
            }
        }

        if ($filters['tahun'] !== '') {
            if (config('database.default') === 'sqlite') {
                $query->whereRaw("strftime('%Y', created_at) = ?", [$filters['tahun']]);
            } else {
                $query->whereYear('created_at', $filters['tahun']);
            }
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($filters['limit'])
            ->get();
    }
}
