<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiKerja extends Model
{
    /** @use HasFactory<\Database\Factories\PrestasiKerjaFactory> */
    protected $table = 'tb_prestasi_kerja';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'periode_nilai_dari',
        'periode_nilai_sampai',
        'tahun_periode',
        'nama_pejabat_nilai',
        'nama_atasan_pejabat_penilai',
        'skp',
        'orientasi_pelayanan',
        'integritas',
        'komitmen',
        'disiplin',
        'kerjasama',
        'kepemimpinan',
        'tgl_keberatan_pegawai',
        'isi_keberatan',
        'tgl_pejabat_penilai',
        'isi_tanggapan',
        'tgl_keputusan_atasan_pejabat_penilai',
        'isi_keputusan',
        'rekomendasi',
        'tgl_diterima_pegawai',
        'tgl_diterima_atasan',
        'total_nilai'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
