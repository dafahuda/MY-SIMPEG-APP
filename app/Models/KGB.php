<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KGB extends Model
{
    /** @use HasFactory<\Database\Factories\KGBFactory> */
    protected $table = 'tb_kgb';
    protected $fillable = [
        'pegawai_id',
        'no_kgb',
        'tgl_kgb',
        'pejabat',
        'no_sk_terakhir',
        'tgl_sk_terakhir',
        'tgl_berlaku_gaji',
        'masa_kerja_lama_tahun',
        'masa_kerja_lama_bulan',
        'gaji_baru',
        'gaji_baru_terbilang',
        'masa_kerja_baru_tahun',
        'masa_kerja_baru_bulan',
        'tmt_kgb',
        'tembusan',
        'periode',
    ];

    protected $casts = [
        'tembusan'         => 'array',
        'tgl_kgb'          => 'date',
        'tgl_sk_terakhir'  => 'date',
        'tgl_berlaku_gaji' => 'date',
        'tmt_kgb'          => 'date',
    ];

    use HasFactory;

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
