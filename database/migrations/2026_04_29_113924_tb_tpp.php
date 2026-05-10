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
        Schema::create('tb_tpp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->references('id')->on('tb_pegawai');

            $table->enum('periode', [
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ]);

            $table->string('tahun', 4);
            $table->integer('jml_hari_kerja')->default(0);

            // Produktifitas Kerja
            $table->integer('tidak_ada_produktifitas')->default(0);

            // Keterlambatan
            $table->integer('terlambat_1_30')->default(0);
            $table->integer('terlambat_31_60')->default(0);
            $table->integer('terlambat_61_90')->default(0);
            $table->integer('terlambat_91_lebih')->default(0);

            // Pulang Sebelum Waktunya
            $table->integer('pulang_awal_1_30')->default(0);
            $table->integer('pulang_awal_31_60')->default(0);
            $table->integer('pulang_awal_61_90')->default(0);
            $table->integer('pulang_awal_91_lebih')->default(0);

            // Mangkir
            $table->integer('tidak_masuk_kerja')->default(0);

            // Hasil Perhitungan (disimpan untuk keperluan laporan)
            $table->decimal('nilai_basic_tpp', 15, 2)->default(0);
            $table->decimal('pengurangan_produktifitas', 15, 2)->default(0);
            $table->decimal('pengurangan_disiplin', 15, 2)->default(0);
            $table->decimal('tpp_diterima', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tpp');
    }
};
