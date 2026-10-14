<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IzinKawin>
 */
class IzinKawinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_surat_izin_perkawinan' => 'Contoh No Surat Izin Perkawinan',
            'tgl_izin_surat_perkawinan' => '2026-01-01',
            'kebangsaan_pegawai' => 'Contoh Kebangsaan Pegawai',
            'nama_wali_bapak_pegawai' => 'Contoh Nama Wali Bapak Pegawai',
            'pekerjaan_wali_bapak_pegawai' => 'Contoh Pekerjaan Wali Bapak Pegawai',
            'alamat_wali_bapak' => 'Contoh Alamat Wali Bapak',
            'nama_wali_ibu_pegawai' => 'Contoh Nama Wali Ibu Pegawai',
            'pekerjaan_wali_ibu_pegawai' => 'Contoh Pekerjaan Wali Ibu Pegawai',
            'alamat_wali_ibu_pegawai' => 'Contoh Alamat Wali Ibu Pegawai',
            'nama_calon_suami_istri' => 'Contoh Nama Calon Suami Istri',
            'tempat_lahir_calon_suami_istri' => 'Contoh Tempat Lahir Calon Suami Istri',
            'tgl_lahir_calon_suami_istri' => '2026-01-01',
            'pekerjaan_calon_suami_istri' => 'Contoh Pekerjaan Calon Suami Istri',
            'nip_nik_calon_suami_istri' => 'Contoh Nip Nik Calon Suami Istri',
            'pangkat_golongan_calon_suami_istri' => 'Contoh Pangkat Golongan Calon Suami Istri',
            'jabatan_calon_suami_istri' => 'Contoh Jabatan Calon Suami Istri',
            'instansi_calon_suami_istri' => 'Contoh Instansi Calon Suami Istri',
            'kebangsaan_calon_suami_istri' => 'Contoh Kebangsaan Calon Suami Istri',
            'agama_calon_suami_istri' => 'Contoh Agama Calon Suami Istri',
            'alamat_calon_suami_istri' => 'Contoh Alamat Calon Suami Istri',
            'nama_wali_bapak_calon_suami_istri' => 'Contoh Nama Wali Bapak Calon Suami Istri',
            'pekerjaan_wali_bapak_calon_suami_istri' => 'Contoh Pekerjaan Wali Bapak Calon Suami Istri',
            'alamat_wali_bapak_calon_suami_istri' => 'Contoh Alamat Wali Bapak Calon Suami Istri',
            'nama_wali_ibu_calon_suami_istri' => 'Contoh Nama Wali Ibu Calon Suami Istri',
            'pekerjaan_wali_ibu_calon_suami_istri' => 'Contoh Pekerjaan Wali Ibu Calon Suami Istri',
            'alamat_wali_ibu_calon_suami_istri' => 'Contoh Alamat Wali Ibu Calon Suami Istri',
            'tempat_perkawinan' => 'Contoh Tempat Perkawinan',
            'tgl_perkawinan' => '2026-01-01',
            'tgl_ditetapkan_perkawinan' => '2026-01-01',
        ];
    }
}
