<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJabatan extends Model
{
    /** @use HasFactory<\Database\Factories\MasterJabatanFactory> */
    protected $table = 'tb_master_jabatan';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_jabatan'];

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'master_jabatan_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id');
    }

    use HasFactory;
}
