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
        Schema::create('tb_kgb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai')->onDelete('cascade');

            // Nomor dan Tanggal KGB
            $table->string('no_kgb')->nullable();
            $table->date('tgl_kgb')->nullable();

            // Gaji Pokok Lama - Pejabat yang menetapkan
            $table->string('pejabat')->nullable();

            // Nomor dan Tanggal SK Terakhir
            $table->string('no_sk_terakhir')->nullable();
            $table->date('tgl_sk_terakhir')->nullable();

            // Tanggal Berlakunya Gaji
            $table->date('tgl_berlaku_gaji')->nullable();

            // Masa Kerja dan Gaji Lama
            $table->string('masa_kerja_lama_tahun')->nullable();
            $table->string('masa_kerja_lama_bulan')->nullable();

            // Gaji Baru / Terbilang
            $table->string('gaji_baru')->nullable();
            $table->string('gaji_baru_terbilang')->nullable();

            // Masa Kerja / Golongan Baru
            $table->string('masa_kerja_baru_tahun')->nullable();
            $table->string('masa_kerja_baru_bulan')->nullable();

            // Terhitung Mulai Tanggal
            $table->date('tmt_kgb')->nullable();

            // Tembusan (bisa lebih dari satu baris, disimpan sebagai JSON)
            $table->json('tembusan')->nullable();

            // Periode notifikasi (tahun)
            $table->string('periode', 4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kgb');
    }
};
