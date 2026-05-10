<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seminar extends Model
{
    /** @use HasFactory<\Database\Factories\SeminarFactory> */
    protected $table = "tb_seminar";
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'nama_seminar',
        'tingkat_kegiatan',
        'tempat_seminar',
        'tgl_seminar',
        'penyelenggara',
        'jumlah_jam',
        'no_piagam',
        'tgl_piagam',
        'file_piagam'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
