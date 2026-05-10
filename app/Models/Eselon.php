<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eselon extends Model
{
    /** @use HasFactory<\Database\Factories\EselonFactory> */
    protected $table = "tb_eselon";
    protected $primaryKey = 'id';
    protected $fillable = ['master_eselon_id'];

    public function jabatan()
    {
        return $this->hasOne(Jabatan::class, 'id');
    }

    public function master_eselon()
    {
        return $this->belongsTo(MasterEselon::class, 'master_eselon_id');
    }

    use HasFactory;
}
