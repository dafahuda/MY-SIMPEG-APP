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
        Schema::create('tb_latihan_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string('tempat_latihan');
            $table->date('waktu_latihan');
            $table->string('nama_pelatih');
            $table->string('tahun_latihan');
            $table->string('jumlah_jam');
            $table->string('nomor_sertifikat');
            $table->date('tgl_sertifikat');
            $table->string('file_sertifikat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_latihan_jabatan');
    }
};
