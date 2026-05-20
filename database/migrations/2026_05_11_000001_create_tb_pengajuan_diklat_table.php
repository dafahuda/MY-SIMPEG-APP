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
        Schema::create('tb_pengajuan_diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_diklat_id')->references('id')->on('tb_rencana_diklat');
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->foreignId('diklat_id')->nullable()->references('id')->on('tb_diklat');
            $table->string('status')->default('pending');
            $table->string('file_bukti');
            $table->string('nomor_sertifikat')->nullable();
            $table->date('tanggal_sertifikat')->nullable();
            $table->integer('jumlah_jam_realisasi')->nullable();
            $table->text('catatan_pegawai')->nullable();
            $table->text('catatan_verifikator')->nullable();
            $table->foreignId('verified_by')->nullable()->references('id')->on('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('revision_count')->nullable()->default(0);
            $table->string('active_duplicate_guard')->nullable()->storedAs("case when `status` in ('pending','revision_requested','approved') then concat(`rencana_diklat_id`,'|',`pegawai_id`) else null end");
            $table->timestamps();

            $table->index(['rencana_diklat_id', 'pegawai_id', 'status'], 'tb_pengajuan_diklat_rencana_pegawai_status_index');
            $table->unique('active_duplicate_guard', 'tb_pengajuan_diklat_active_guard_unique');
            $table->unique('diklat_id', 'tb_pengajuan_diklat_diklat_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pengajuan_diklat');
    }
};
