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
        Schema::create('tb_hukuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->text('pelanggaran_yg_dilakukan');
            $table->enum('tingkat_hukuman', ['Ringan', 'Sedang', 'Berat']);
            $table->enum('jenis_hukuman', ['Teguran Lisan', 'Teguran Tertulis', 'Tunda Kenaikan berkala', 'Tunda Kenaikan Pangkat', 'Pemberhentian']);
            $table->text('isi_teguran');
            $table->string("pejabat_pengesahan_sk_hukuman");
            $table->string('no_sk');
            $table->date('tgl_pengesahan_sk');
            $table->date('tmt_hukuman_mulai');
            $table->date('tmt_hukuman_pemulihan');
            $table->string('pejabat_pemulihan_hukuman');
            $table->string('no_pemulihan_hukuman');
            $table->date('tgl_pemulihan_hukuman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_hukuman');
    }
};
