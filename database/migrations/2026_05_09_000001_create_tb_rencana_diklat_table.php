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
        Schema::create('tb_rencana_diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');
            $table->string('tahun_rencana', 4);
            $table->string('nama_diklat_rencana');
            $table->string('target_kompetensi');
            $table->string('kategori_diklat');
            $table->string('prioritas');
            $table->integer('target_jam');
            $table->string('target_penyelenggara');
            $table->text('alasan_kebutuhan');
            $table->text('catatan')->nullable();
            $table->string('status')->default('planned');
            $table->string('active_duplicate_guard')->nullable()->storedAs("case when `status` in ('planned','realized') then concat(`pegawai_id`,'|',`tahun_rencana`,'|',lower(trim(`nama_diklat_rencana`))) else null end");
            $table->timestamps();

            $table->index(['pegawai_id', 'tahun_rencana', 'status'], 'tb_rencana_diklat_pegawai_tahun_status_index');
            $table->unique('active_duplicate_guard', 'tb_rencana_diklat_active_guard_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_rencana_diklat');
    }
};
