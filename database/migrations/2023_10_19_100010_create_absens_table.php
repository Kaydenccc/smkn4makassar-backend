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
        Schema::create('absens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_guru')->nullable();
            $table->unsignedBigInteger('id_siswa')->nullable();
            $table->unsignedBigInteger('id_kelas')->nullable(false);
            $table->unsignedBigInteger('id_mapel')->nullable(false);
            $table->string('status')->nullable();
            $table->time('jam')->nullable();
            $table->date('tanggal');
            $table->string('keterangan', 100)->nullable();
            $table->string('materi', 100)->nullable();

            $table->foreign('id_guru')->references('id')->on('gurus')->onDelete('cascade');;
            $table->foreign('id_siswa')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('cascade');;
            $table->foreign('id_mapel')->references('id')->on('mapels')->onDelete('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};
