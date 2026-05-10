<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanSekolah extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatPendidikanSekolahFactory> */
    protected $table = "tb_riwayat_pendidikan_sekolah";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'jenjang_pendidikan',
        'nama_sekolah_universitas',
        'lokasi',
        'jurusan',
        'no_ijazah',
        'tgl_ijazah',
        'nama_kepsek_rektor'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }


    use HasFactory;
}
