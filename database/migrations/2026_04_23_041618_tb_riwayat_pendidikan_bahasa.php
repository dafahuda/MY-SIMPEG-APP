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
        Schema::create('tb_riwayat_pendidikan_bahasa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string("jenis_bahasa");
            $table->string("bahasa");
            $table->enum('kemampuan_bicara', ['Aktif', 'Pasif']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_riwayat_pendidikan_bahasa');
    }
};
