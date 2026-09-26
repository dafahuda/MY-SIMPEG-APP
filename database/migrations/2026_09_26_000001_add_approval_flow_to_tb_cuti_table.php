<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_cuti', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('tebusan');
            $table->text('alasan_penolakan')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->after('alasan_penolakan');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('tb_cuti', function (Blueprint $table) {
            $table->dropColumn(['status', 'alasan_penolakan', 'approved_by', 'approved_at']);
        });
    }
};
