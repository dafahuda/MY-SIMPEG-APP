<?php

namespace App\Services;

use App\Models\InstansiLembaga;
use Illuminate\Support\Facades\Storage;

/**
 * Generator PDF laporan berkop surat resmi.
 *
 * Semua laporan rekapitulasi memakai satu template (pdf.laporan-berkop)
 * sehingga tampilan konsisten: kop instansi, judul, tabel, tanda tangan.
 */
class LaporanPdfService
{
    /**
     * @param array{kolom: string[], baris: array<int, array{sel: string[], total?: bool}>, judul: string,
     *              subjudul?: ?string, periode?: ?string} $data
     */
    public function generate(array $data): \Barryvdh\DomPDF\PDF
    {
        $instansi = InstansiLembaga::first();

        $data = array_merge([
            'instansi' => $instansi,
            'logoPath' => $this->logoPath($instansi),
            'jabatanTtd' => 'Kepala ' . ($instansi->nama_instansi_lembaga ?? 'Instansi'),
            'namaTtd' => $instansi->kepala_dinas ?? null,
            'nipTtd' => $instansi->nip ?? null,
        ], $data);

        $pdf = app('dompdf.wrapper')->loadView('pdf.laporan-berkop', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Path absolut logo untuk dompdf (perlu path lokal, bukan URL).
     */
    private function logoPath(?InstansiLembaga $instansi): ?string
    {
        if (! $instansi?->gambar_logo) {
            return null;
        }

        $relatif = $instansi->gambar_logo;

        // Pola path di aplikasi ini: asset('storage/...') atau asset(...)
        if (str_starts_with($relatif, 'storage/')) {
            $relatif = substr($relatif, strlen('storage/'));
        }

        $path = storage_path('app/public/' . $relatif);
        if (is_file($path)) {
            return $path;
        }

        $path = public_path($relatif);
        if (is_file($path)) {
            return $path;
        }

        return null;
    }
}
