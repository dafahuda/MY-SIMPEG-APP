<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    /** @use HasFactory<\Database\Factories\TunjanganFactory> */
    protected $table = 'tb_tunjangan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'no_tunjangan',
        'jenis_tunjangan_anak',
        'tgl_tunjangan',
        'terhitung_mulai',
        'akta_perkawinan_dari',
        'no_akta_perkawinan',
        'tgl_akta_perkawinan',
        'akta_kelahiran_dari',
        'no_akta_kelahiran',
        'tgl_akta_kelahiran',
        'tebusan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
