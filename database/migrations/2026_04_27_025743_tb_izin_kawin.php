<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_izin_kawin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string('no_surat_izin_perkawinan');
            $table->date('tgl_izin_surat_perkawinan');
            $table->string('kebangsaan_pegawai');

            // wali untuk pegawai
            $table->string('nama_wali_bapak_pegawai');
            $table->string('pekerjaan_wali_bapak_pegawai');
            $table->string('alamat_wali_bapak');
            $table->string('nama_wali_ibu_pegawai');
            $table->string('pekerjaan_wali_ibu_pegawai');
            $table->string('alamat_wali_ibu_pegawai');

            // wali untuk calon suami / istri
            $table->string('nama_calon_suami_istri');
            $table->string('tempat_lahir_calon_suami_istri');
            $table->date('tgl_lahir_calon_suami_istri');
            $table->string('pekerjaan_calon_suami_istri');
            $table->string('nip_nik_calon_suami_istri');
            $table->string('pangkat_golongan_calon_suami_istri');
            $table->string('jabatan_calon_suami_istri');
            $table->string('instansi_calon_suami_istri');
            $table->string('kebangsaan_calon_suami_istri');
            $table->string('agama_calon_suami_istri');
            $table->string('alamat_calon_suami_istri');
            $table->string('nama_wali_bapak_calon_suami_istri');
            $table->string('pekerjaan_wali_bapak_calon_suami_istri');
            $table->string('alamat_wali_bapak_calon_suami_istri');
            $table->string('nama_wali_ibu_calon_suami_istri');
            $table->string('pekerjaan_wali_ibu_calon_suami_istri');
            $table->string('alamat_wali_ibu_calon_suami_istri');

            // tempat, tanggal dan tanggal ditetapkan perkawinan
            $table->string('tempat_perkawinan');
            $table->date('tgl_perkawinan');
            $table->date('tgl_ditetapkan_perkawinan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_izin_kawin');
    }
};
