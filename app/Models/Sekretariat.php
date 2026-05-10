<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekretariat extends Model
{
    /** @use HasFactory<\Database\Factories\SekretariatFactory> */
    protected $table = 'tb_sekretariat';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_sekretariat',
        'kabupaten_kota',
        'nama_kabupaten_kota',
        'alamat',
        'email',
        'no_telp',
        'sekretaris',
        'nip',
        'gambar_logo'
    ];


    use HasFactory;
}
