<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghargaan extends Model
{
    /** @use HasFactory<\Database\Factories\PenghargaanFactory> */
    protected $table = "tb_penghargaan_pegawai";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'nama_penghargaan',
        'instansi_pemberi',
        'tingkat_kegiatan',
        'tempat_penghargaan',
        'tgl_penghargaan',
        'file_sertifikat_penghargaan',
        'tahun',
        'no_sertifikat'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
