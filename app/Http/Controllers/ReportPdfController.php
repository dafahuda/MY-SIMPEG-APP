<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unduh PDF untuk laporan Report (nominatif, DUK, keadaan pegawai,
 * bezetting, pensiun).
 *
 * Pendekatan: memanggil ulang method print* di ReportController sehingga
 * data yang dirender PERSIS sama dengan tombol Print yang sudah ada,
 * lalu hasil HTML-nya dikonversi menjadi PDF via dompdf.
 */
class ReportPdfController extends Controller
{
    public function __construct(private ReportController $report)
    {
    }

    public function unduh(Request $request, string $jenis)
    {
        if (Auth::user()->role === 'pegawai') {
            abort(403, 'Laporan hanya untuk admin dan superadmin.');
        }

        [$response, $paper, $orientasi, $judul] = match ($jenis) {
            'nominatif' => [$this->report->printNominatif($request), 'a3', 'landscape', 'Laporan Nominatif'],
            'duk' => [$this->report->printDUK($request), 'a3', 'landscape', 'Daftar Urut Kepangkatan (DUK)'],
            'keadaan_pegawai' => [$this->report->printKeadaanPegawai($request), 'a4', 'landscape', 'Laporan Keadaan Pegawai'],
            'bezetting' => [$this->report->printBezetting($request), 'a4', 'landscape', 'Laporan Bezetting (Formasi)'],
            'pensiun' => [$this->cetakPensiun($request), 'a4', 'portrait', 'Daftar Pegawai yang Akan Pensiun'],
            default => abort(404, 'Jenis laporan tidak dikenal.'),
        };

        if ($response instanceof View) {
            $html = $response->render();
        } elseif ($response instanceof Response) {
            $html = $response->getContent();
        } else {
            // print* bisa mengembalikan redirect (mis. unit kerja tidak ada)
            return $response;
        }

        // Sembunyikan tombol Print/Kembali (tidak relevan di PDF)
        $html = preg_replace('/<div class="no-print".*?<\/div>/s', '', $html);

        $pdf = app('dompdf.wrapper')->loadHTML($html);
        $pdf->setPaper($paper, $orientasi);

        $namaFile = 'laporan-' . Str::slug($judul) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($namaFile);
    }

    /**
     * Laporan pensiun belum punya method print sendiri;
     * bangun ulang query yang sama dengan ReportController::reportPensiun.
     */
    private function cetakPensiun(Request $request): Response
    {
        $instansi = \App\Models\InstansiLembaga::first();
        $selectedPeriode = $request->input('periode', 'tahun_ini');

        $periodeLabel = [
            'tahun_ini' => 'Tahun Ini',
            '1_tahun' => '1 Tahun Yang Akan Datang',
            '2_tahun' => '2 Tahun Yang Akan Datang',
        ][$selectedPeriode] ?? 'Tahun Ini';

        $batasUsiaPensiun = 58;
        $tahunSekarang = now()->year;
        $tahunPensiun = match ($selectedPeriode) {
            '1_tahun' => $tahunSekarang + 1,
            '2_tahun' => $tahunSekarang + 2,
            default => $tahunSekarang,
        };

        $pegawaiList = \App\Models\Pegawai::with(['jabatan_aktif.master_jabatan', 'unit_kerja'])
            ->whereYear('tgl_lahir', '>=', $tahunPensiun - $batasUsiaPensiun)
            ->whereYear('tgl_lahir', '<=', $tahunPensiun - $batasUsiaPensiun)
            ->orderBy('tgl_lahir')
            ->get()
            ->map(function ($pegawai) use ($batasUsiaPensiun) {
                $tglLahir = \Carbon\Carbon::parse($pegawai->tgl_lahir);
                $tglPensiun = $tglLahir->copy()->addYears($batasUsiaPensiun);

                $pegawai->tgl_pensiun = $tglPensiun->format('Y-m-d');
                $pegawai->periode_pensiun = $tglPensiun->translatedFormat('F Y');

                return $pegawai;
            });

        $html = view('pdf.pensiun-berkop', [
            'pegawaiList' => $pegawaiList,
            'instansi' => $instansi,
            'periodeLabel' => $periodeLabel,
            'tahun' => $tahunPensiun,
        ])->render();

        return response($html);
    }
}
