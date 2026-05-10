<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterEselon extends Model
{
    /** @use HasFactory<\Database\Factories\MasterEselonFactory> */
    protected $table = 'tb_master_eselon';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_eselon'];

    public function eselon()
    {
        return $this->hasMany(Eselon::class, 'master_eselon_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id');
    }


    use HasFactory;
}
