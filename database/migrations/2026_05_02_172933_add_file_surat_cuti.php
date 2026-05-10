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
        Schema::table('tb_cuti', function (Blueprint $table) {
            $table->string('file_surat_cuti')->nullable()->after('ketentuan_c');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_cuti', function (Blueprint $table) {
            $table->dropColumn('file_surat_cuti');
        });
    }
};
