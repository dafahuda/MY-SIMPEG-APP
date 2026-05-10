<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKeluargaSuamiIstri extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatKeluargaSuamiIstriFactory> */
    protected $table = "tb_riwayat_keluarga_suami_istri";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'no_ktp_nik',
        'nama',
        'tgl_lahir',
        'tempat_lahir',
        'pendidikan',
        'pekerjaan',
        'status_hubungan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
