<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanLanjut extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatPendidikanLanjutFactory> */
    protected $table = "tb_riwayat_pendidikan_lanjut";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'jenjang_pendidikan',
        'nama_sekolah_universitas',
        'jurusan',
        'thn_mulai',
        'thn_selesai',
        'status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
