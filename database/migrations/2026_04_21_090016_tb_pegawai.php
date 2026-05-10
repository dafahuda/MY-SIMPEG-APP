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
        Schema::create('tb_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('unit_kerja_id')->references('id')->on('tb_unit_kerja');
            $table->string("foto");
            $table->string("nip");
            $table->string("nama");
            $table->string("gelar");
            $table->string("tmpt_lahir");
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->enum('agama', ['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Kong Hu Cu']);
            $table->enum('golongan_darah', ['A', 'AB', 'B', 'O', 'Tidak Tahu']);
            $table->enum('status_pernikahan', ['Nikah', 'Belum Nikah', 'Cerai Mati', 'Cerai Hidup']);
            $table->string('nik');
            $table->string("alamat");
            $table->string("no_hp");
            $table->string("email");
            $table->string('email_gov');
            $table->string("no_npwp");
            $table->string("no_bpjs");
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'TKK', 'HONORER', 'CPNS']);
            $table->string("karpeg");
            $table->string("no_sk_cpns")->nullable(); // Kolom untuk nomor SK CPNS
            $table->date('tmt_cpns')->nullable();    // Kolom untuk tanggal TMT CPNS

            // Data PNS
            $table->string("no_sk_pns")->nullable();  // Kolom untuk nomor SK PNS
            $table->date('tmt_pns')->nullable();     // Kolom untuk tanggal TMT PNS

            $table->string("gol_awal");
            $table->integer("nilai_tpp")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pegawai');
    }
};
