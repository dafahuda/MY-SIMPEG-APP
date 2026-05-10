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
        Schema::create('tb_pangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->foreignId('master_pangkat_id')->constrained('tb_master_pangkat');
            $table->foreignId('master_golongan_id')->constrained('tb_master_golongan');
            $table->string('jenis_pangkat');
            $table->date('tmt_pangkat_mulai');
            $table->date('tmt_pangkat_selesai');
            $table->string('no_sk');
            $table->date('tgl_sk');
            $table->string('pejabat_pengesah_sk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pangkat');
    }
};
