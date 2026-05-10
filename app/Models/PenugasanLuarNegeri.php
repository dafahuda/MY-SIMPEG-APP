<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanLuarNegeri extends Model
{
    /** @use HasFactory<\Database\Factories\PenugasanLuarNegeriFactory> */
    protected $table = 'tb_penugasan_luar_negeri';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'alasan_penugasan',
        'negara_tujuan',
        'tahun',
        'durasi_hari',
        'no_st',
        'st'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
