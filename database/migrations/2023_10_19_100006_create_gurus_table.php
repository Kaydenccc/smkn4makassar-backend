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
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nama',100)->nullable(false);
            $table->string('nip',100)->nullable(false)->unique('nip_unique');
            $table->string('email',100)->nullable(false)->unique();
            $table->string('password',100)->nullable(false);
            $table->string('no_hp',15);
            $table->string('token',100)->nullable()->unique('token');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
