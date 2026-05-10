<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGolongan extends Model
{
    /** @use HasFactory<\Database\Factories\MasterGolonganFactory> */
    protected $table = 'tb_master_golongan';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_golongan'];

    public function golongan()
    {
        return $this->hasMany(Golongan::class, 'master_golongan_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id');
    }

    use HasFactory;
}
