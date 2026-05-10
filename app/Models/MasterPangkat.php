<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPangkat extends Model
{
    /** @use HasFactory<\Database\Factories\MasterPangkatFactory> */
    protected $table = 'tb_master_pangkat';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_pangkat'];

    public function pangkat()
    {
        return $this->hasMany(Pangkat::class, 'master_pangkat_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id');
    }

    use HasFactory;
}
