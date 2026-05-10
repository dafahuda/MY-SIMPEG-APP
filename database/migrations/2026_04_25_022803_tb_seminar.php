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
        Schema::create('tb_seminar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->text('nama_seminar');
            $table->enum('tingkat_kegiatan', ['Lokal', 'Regional', 'Nasional', 'Internasional']);
            $table->string("tempat_seminar");
            $table->date('tgl_seminar');
            $table->string('penyelenggara');
            $table->string('jumlah_jam');
            $table->string('no_piagam');
            $table->date('tgl_piagam');
            $table->string('file_piagam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_seminar');
    }
};
