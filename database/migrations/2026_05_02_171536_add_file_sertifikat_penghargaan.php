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
        Schema::table('tb_penghargaan_pegawai', function (Blueprint $table) {
            $table->string('file_sertifikat_penghargaan')->nullable()->after('no_sertifikat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penghargaan_pegawai', function (Blueprint $table) {
            $table->dropColumn('file_sertifikat_penghargaan');
        });
    }
};
