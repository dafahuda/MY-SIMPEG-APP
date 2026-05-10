<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hukuman extends Model
{
    /** @use HasFactory<\Database\Factories\HukumanFactory> */
    protected $table = "tb_hukuman";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'pelanggaran_yg_dilakukan',
        'tingkat_hukuman',
        'jenis_hukuman',
        'isi_teguran',
        'pejabat_pengesahan_sk_hukuman',
        'no_sk',
        'file_sk_hukuman',
        'tgl_pengesahan_sk',
        'tmt_hukuman_mulai',
        'tmt_hukuman_pemulihan',
        'no_pemulihan_hukuman',
        'pejabat_pemulihan_hukuman',
        'tgl_pemulihan_hukuman'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
