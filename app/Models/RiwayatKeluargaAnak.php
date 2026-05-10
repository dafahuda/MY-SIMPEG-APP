<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKeluargaAnak extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatKeluargaAnakFactory> */
    protected $table = "tb_riwayat_keluarga_anak";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'pendidikan',
        'status_hubungan',
        'pekerjaan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }


    use HasFactory;
}
