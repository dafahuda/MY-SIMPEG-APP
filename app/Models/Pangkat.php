<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    /** @use HasFactory<\Database\Factories\PangkatFactory> */
    protected $table = 'tb_pangkat';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'master_pangkat_id',
        'master_golongan_id',
        'jenis_pangkat',
        'tmt_pangkat_mulai',
        'tmt_pangkat_selesai',
        'no_sk',
        'tgl_sk',
        'pejabat_pengesah_sk'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function master_pangkat()
    {
        return $this->belongsTo(MasterPangkat::class, 'master_pangkat_id');
    }

    public function master_golongan()
    {
        return $this->belongsTo(MasterGolongan::class, 'master_golongan_id');
    }

    use HasFactory;
}
