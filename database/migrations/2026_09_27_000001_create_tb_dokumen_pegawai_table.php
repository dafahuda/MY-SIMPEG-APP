<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_dokumen_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('tb_pegawai')->cascadeOnDelete();
            $table->string('jenis_dokumen', 50); // sk_cpns, sk_pns, sk_pangkat, ktp, kk, ijazah, foto, lainnya
            $table->string('nama_dokumen', 150); // nama/label dokumen, mis "SK CPNS 2015"
            $table->string('file_path');         // path relatif di disk private
            $table->string('file_name');         // nama file asli saat upload
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');  // byte
            $table->foreignId('uploaded_by')->constrained('users'); // siapa yang upload
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['pegawai_id', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_dokumen_pegawai');
    }
};
