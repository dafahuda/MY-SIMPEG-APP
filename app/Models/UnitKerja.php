<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    /** @use HasFactory<\Database\Factories\UnitKerjaFactory> */
    protected $table = "tb_unit_kerja";
    protected $primaryKey = 'id';
    protected $fillable = ['nama_unit', 'alamat'];

    public function pegawai()
    {
        // Parameter kedua haruslah nama kolom 'foreign key' yang ada di tabel tb_pegawai
        return $this->hasMany(Pegawai::class, 'unit_kerja_id');
    }

    use HasFactory;
}
