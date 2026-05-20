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
        Schema::table('tb_diklat', function (Blueprint $table) {
            $table->foreignId('rencana_diklat_id')->nullable()->after('pegawai_id')->references('id')->on('tb_rencana_diklat');
            $table->unique('rencana_diklat_id', 'tb_diklat_rencana_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_diklat', function (Blueprint $table) {
            $table->dropUnique('tb_diklat_rencana_unique');
            $table->dropForeign(['rencana_diklat_id']);
            $table->dropColumn('rencana_diklat_id');
        });
    }
};
