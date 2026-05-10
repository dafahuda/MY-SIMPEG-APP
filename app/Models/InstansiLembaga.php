<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstansiLembaga extends Model
{
    /** @use HasFactory<\Database\Factories\InstansiLembagaFactory> */
    protected $table = 'tb_instansi_lembaga';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_instansi_lembaga',
        'kabupaten_kota',
        'nama_kota_kabupaten',
        'alamat',
        'no_telp',
        'email',
        'kepala_dinas',
        'nip',
        'gambar_logo'
    ];

    use HasFactory;
}
