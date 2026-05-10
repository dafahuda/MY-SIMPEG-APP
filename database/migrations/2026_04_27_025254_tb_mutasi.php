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
        Schema::create('tb_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->enum('jenis_mutasi', ['Masuk', 'Keluar', 'Pindah Antar Instansi', 'Pensiun', 'Wafat', 'Kenaikan Pangkat']);
            $table->string('instansi_tujuan');
            $table->string('no_sk_mutasi');
            $table->date('tgl_sk_mutasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_mutasi');
    }
};
