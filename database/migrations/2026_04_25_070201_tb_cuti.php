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
        Schema::create('tb_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');

            $table->enum('jenis_cuti', [
                'Tahunan',
                'Besar',
                'Sakit',
                'Menikah',
                'Bersalin',
                'Meninggalkan Pekerjaan',
                'Karena Alasan Penting',
                'Diluar Tanggungan Negara'
            ]);

            $table->string('no_surat_cuti');
            $table->date('tgl_surat_cuti');

            $table->date('pelaksanaan_cuti_mulai');
            $table->date('pelaksanaan_cuti_selesai');
            $table->string('durasi_cuti');

            $table->string('ketentuan_a');
            $table->string('ketentuan_b');
            $table->string('ketentuan_c');
            $table->string('tebusan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_cuti');
    }
};
