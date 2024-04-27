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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama',100);
            $table->string('nis',100)->unique('nis_siswa_unique');
            $table->string('password', 100)->nullable(false);
            $table->string('token', 100)->nullable()->unique('token');
            $table->unsignedBigInteger('id_kelas')->nullable(false);
            $table->string('jenis_kelamin', 15);
            $table->string('kontak', 15);
            $table->string('kontak_orang_tua', 15);
            $table->string('alamat',100);

            $table->foreign('id_kelas')->references('id')->on('kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
