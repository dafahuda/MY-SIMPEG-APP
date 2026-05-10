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
        Schema::create('tb_riwayat_pendidikan_sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->enum('jenjang_pendidikan', ['SD', 'MI', 'SMP', 'MTS', 'SMK', 'SMA', 'MA', 'D3', 'S1', 'S2', 'S3', 'Profesi']);
            $table->string('nama_sekolah_universitas');
            $table->string('lokasi');
            $table->string('jurusan');
            $table->string('no_ijazah');
            $table->date('tgl_ijazah');
            $table->string('nama_kepsek_rektor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_riwayat_pendidikan_sekolah');
    }
};
