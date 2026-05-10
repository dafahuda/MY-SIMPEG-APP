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
        Schema::create('tb_diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string('nama_diklat');
            $table->string('jumlah_jam');
            $table->string('penyelenggara');
            $table->string('tempat');
            $table->string('angkatan');
            $table->string('tahun');
            $table->string('no_sttpp');
            $table->date('tgl_sttpp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_diklat');
    }
};
