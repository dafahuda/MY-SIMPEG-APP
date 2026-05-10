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
        Schema::create('tb_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->foreignId("master_jabatan_id")->constrained('tb_master_jabatan');
            $table->foreignId('master_eselon_id')->constrained('tb_master_eselon');

            $table->enum('jenis_jabatan', ['Jabatan Struktural', 'Jabatan Fungsional Tertentu', 'Jabatan Fungsional Umum']);

            $table->enum('periode', ['-', 'I', 'II', 'Sudah Selesai']);
            $table->enum('tahun_ke', ['-', '1', '2', '3', '4', 'Sudah Selesai']);

            $table->string('no_sk');
            $table->date('tgl_sk');
            $table->string('terbit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_jabatan');
    }
};
