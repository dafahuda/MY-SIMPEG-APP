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
        Schema::create('tb_riwayat_keluarga_suami_istri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string("no_ktp_nik");
            $table->string("nama");
            $table->date('tgl_lahir');
            $table->string("tempat_lahir");
            $table->enum('pendidikan', ['SD', 'SLTP', 'SLTA', 'D3', 'S1', 'S2', 'S3']);
            $table->string("pekerjaan");
            $table->enum('status_hubungan', ['Suami', 'Istri']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_riwayat_keluarga_suami_istri');
    }
};
