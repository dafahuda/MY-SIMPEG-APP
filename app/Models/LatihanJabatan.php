<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatihanJabatan extends Model
{
    /** @use HasFactory<\Database\Factories\LatihanJabatanFactory> */
    protected $table = 'tb_latihan_jabatan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'tempat_latihan',
        'waktu_latihan',
        'nama_pelatih',
        'tahun_latihan',
        'jumlah_jam',
        'nomor_sertifikat',
        'tgl_sertifikat',
        'file_sertifikat'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
