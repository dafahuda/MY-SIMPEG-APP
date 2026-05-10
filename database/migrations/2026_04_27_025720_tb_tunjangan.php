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
        Schema::create('tb_tunjangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string('no_tunjangan');
            $table->string('jenis_tunjangan_anak');
            $table->date('tgl_tunjangan');
            $table->date('terhitung_mulai');
            $table->string('akta_perkawinan_dari');
            $table->string('no_akta_perkawinan');
            $table->date('tgl_akta_perkawinan');
            $table->string('akta_kelahiran_dari');
            $table->string('no_akta_kelahiran');
            $table->date('tgl_akta_kelahiran');
            $table->string('tebusan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tunjangan');
    }
};
