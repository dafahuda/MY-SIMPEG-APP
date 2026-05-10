<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tpp extends Model
{
    use HasFactory;

    protected $table = 'tb_tpp';

    protected $fillable = [
        'pegawai_id',
        'periode',
        'tahun',
        'jml_hari_kerja',
        'tidak_ada_produktifitas',
        'terlambat_1_30',
        'terlambat_31_60',
        'terlambat_61_90',
        'terlambat_91_lebih',
        'pulang_awal_1_30',
        'pulang_awal_31_60',
        'pulang_awal_61_90',
        'pulang_awal_91_lebih',
        'tidak_masuk_kerja',
        'nilai_basic_tpp',
        'pengurangan_produktifitas',
        'pengurangan_disiplin',
        'tpp_diterima',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
