<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    /** @use HasFactory<\Database\Factories\PegawaiFactory> */
    protected $table = "tb_pegawai";
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'unit_kerja_id',
        'foto',
        'nip',
        'nik',
        'nama',
        'gelar',
        'gelar_depan',
        'tmpt_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'agama',
        'golongan_darah',
        'status_pernikahan',
        'alamat',
        'no_hp',
        'email',
        'email_gov',
        'no_npwp',
        'no_bpjs',
        'status_kepegawaian',
        'karpeg',
        'no_sk_cpns',
        'tmt_cpns',
        'no_sk_pns',
        'tmt_pns',
        'gol_awal',
        'nilai_tpp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenPegawai::class, 'pegawai_id');
    }

    public function unit_kerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function riwayat_keluarga_anak()
    {
        return $this->hasOne(RiwayatKeluargaAnak::class, 'id');
    }

    public function riwayat_keluarga_orangtua()
    {
        return $this->hasOne(RiwayatKeluargaOrangtua::class, 'id');
    }

    public function riwayat_keluarga_suami_istri()
    {
        return $this->hasOne(RiwayatKeluargaSuamiIstri::class, 'id');
    }

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'pegawai_id');
    }

    public function jabatan_aktif()
    {
        return $this->hasOne(Jabatan::class, 'pegawai_id')->latest('tmt_jabatan_mulai');
    }

    public function rencanaDiklat()
    {
        return $this->hasMany(RencanaDiklat::class, 'pegawai_id');
    }

    public function diklat()
    {
        return $this->hasMany(Diklat::class, 'pegawai_id');
    }

    use HasFactory;
}
