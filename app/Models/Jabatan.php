<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    /** @use HasFactory<\Database\Factories\JabatanFactory> */
    protected $table = 'tb_jabatan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'master_jabatan_id',
        'master_eselon_id',
        'jenis_jabatan',
        'tmt_jabatan_mulai',
        'tmt_jabatan_selesai',
        'periode',
        'tahun_ke',
        'no_sk',
        'tgl_sk',
        'terbit'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function master_jabatan()
    {
        return $this->belongsTo(MasterJabatan::class, 'master_jabatan_id');
    }

    public function master_eselon()
    {
        return $this->belongsTo(MasterEselon::class, 'master_eselon_id');
    }


    use HasFactory;
}
