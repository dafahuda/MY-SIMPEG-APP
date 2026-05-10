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
        Schema::create('tb_sekretariat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekretariat');
            $table->enum('kabupaten_kota', ['Kabupaten', 'Kota']);
            $table->string('nama_kabupaten_kota');
            $table->text('alamat');
            $table->string('no_telp');
            $table->string('email');
            $table->string('sekretaris');
            $table->string('nip');
            $table->string('gambar_logo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sekretariat');
    }
};
