<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanBahasa extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatPendidikanBahasaFactory> */
    protected $table = "tb_riwayat_pendidikan_bahasa";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'jenis_bahasa',
        'bahasa',
        'kemampuan_bicara'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
