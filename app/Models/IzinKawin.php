<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinKawin extends Model
{
    /** @use HasFactory<\Database\Factories\IzinKawinFactory> */
    protected $table = 'tb_izin_kawin';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'no_surat_izin_perkawinan',
        'tgl_izin_surat_perkawinan',
        'kebangsaan_pegawai',
        'nama_wali_bapak_pegawai',
        'pekerjaan_wali_bapak_pegawai',
        'alamat_wali_bapak',
        'nama_wali_ibu_pegawai',
        'pekerjaan_wali_ibu_pegawai',
        'alamat_wali_ibu_pegawai',
        'nama_calon_suami_istri',
        'tempat_lahir_calon_suami_istri',
        'tgl_lahir_calon_suami_istri',
        'pekerjaan_calon_suami_istri',
        'nip_nik_calon_suami_istri',
        'pangkat_golongan_calon_suami_istri',
        'jabatan_calon_suami_istri',
        'instansi_calon_suami_istri',
        'kebangsaan_calon_suami_istri',
        'agama_calon_suami_istri',
        'alamat_calon_suami_istri',
        'nama_wali_bapak_calon_suami_istri',
        'pekerjaan_wali_bapak_calon_suami_istri',
        'alamat_wali_bapak_calon_suami_istri',
        'nama_wali_ibu_calon_suami_istri',
        'pekerjaan_wali_ibu_calon_suami_istri',
        'alamat_wali_ibu_calon_suami_istri',
        'tempat_perkawinan',
        'tgl_perkawinan',
        'tgl_ditetapkan_perkawinan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    use HasFactory;
}
