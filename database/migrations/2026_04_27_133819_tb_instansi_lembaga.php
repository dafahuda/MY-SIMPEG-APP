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
        Schema::create('tb_instansi_lembaga', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi_lembaga');
            $table->enum('kabupaten_kota', ['Kabupaten', 'Kota']);
            $table->string('nama_kota_kabupaten');
            $table->text('alamat');
            $table->string('no_telp');
            $table->string('email');
            $table->string('kepala_dinas');
            $table->string('nip');
            $table->string('gambar_logo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_instansi_lembaga');
    }
};
