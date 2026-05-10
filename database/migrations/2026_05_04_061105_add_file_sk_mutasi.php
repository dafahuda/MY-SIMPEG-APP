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
        Schema::table('tb_mutasi', function (Blueprint $table) {
            $table->string('file_sk_mutasi')->nullable()->after('tgl_sk_mutasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_mutasi', function (Blueprint $table) {
            $table->dropColumn('file_sk_mutasi');
        });
    }
};
