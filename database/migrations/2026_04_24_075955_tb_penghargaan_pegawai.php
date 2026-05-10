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
        Schema::create('tb_penghargaan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string('nama_penghargaan');
            $table->string('instansi_pemberi');
            $table->enum('tingkat_kegiatan', ['Lokal', 'Regional', 'Nasional', 'Internasional']);
            $table->string('tempat_penghargaan');
            $table->date('tgl_penghargaan');
            $table->string('tahun');
            $table->string('no_sertifikat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penghargaan_pegawai');
    }
};
