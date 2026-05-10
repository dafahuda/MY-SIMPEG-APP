<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    /** @use HasFactory<\Database\Factories\CutiFactory> */
    protected $table = 'tb_cuti';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'jenis_cuti',
        'no_surat_cuti',
        'tgl_surat_cuti',
        'pelaksanaan_cuti_mulai',
        'pelaksanaan_cuti_selesai',
        'durasi_cuti',
        'ketentuan_a',
        'ketentuan_b',
        'ketentuan_c',
        'file_surat_cuti',
        'tebusan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }


    use HasFactory;
}
