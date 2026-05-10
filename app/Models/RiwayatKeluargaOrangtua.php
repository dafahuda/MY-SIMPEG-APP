<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKeluargaOrangtua extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatKeluargaOrangtuaFactory> */
    protected $table = "tb_riwayat_orangtua";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'pendidikan',
        'pekerjaan',
        'status_pekerjaan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }


    use HasFactory;
}
