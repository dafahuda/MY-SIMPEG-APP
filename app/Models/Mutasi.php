<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    /** @use HasFactory<\Database\Factories\MutasiFactory> */
    protected $table = 'tb_mutasi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'jenis_mutasi',
        'instansi_tujuan',
        'no_sk_mutasi',
        'tgl_sk_mutasi',
        'file_sk_mutasi'
    ];


    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
