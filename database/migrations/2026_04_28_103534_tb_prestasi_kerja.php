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
        Schema::create('tb_prestasi_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->date('periode_nilai_dari');
            $table->date('periode_nilai_sampai');
            $table->string('tahun_periode');
            $table->string('nama_pejabat_nilai');
            $table->string('nama_atasan_pejabat_penilai');

            // struktur data untuk unsur yang di nilai
            $table->integer('skp')->default(0);

            // struktur data perilaku nilai
            $table->integer('orientasi_pelayanan')->default(0);
            $table->integer('integritas')->default(0);
            $table->integer('komitmen')->default(0);
            $table->integer('disiplin')->default(0);
            $table->integer('kerjasama')->default(0);
            $table->integer('kepemimpinan')->default(0);

            // keberatan pegawai
            $table->date('tgl_keberatan_pegawai');
            $table->text('isi_keberatan');

            // tanggapan pejabat nilai
            $table->date('tgl_pejabat_penilai');
            $table->text('isi_tanggapan');

            $table->date('tgl_keputusan_atasan_pejabat_penilai');
            $table->text('isi_keputusan');

            $table->string('rekomendasi');

            $table->date('tgl_diterima_pegawai');
            $table->date('tgl_diterima_atasan');

            $table->integer('total_nilai')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_prestasi_kerja');
    }
};
