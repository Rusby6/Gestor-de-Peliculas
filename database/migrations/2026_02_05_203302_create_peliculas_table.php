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
        Schema::create('peliculas', function (Blueprint $table) {
        $table->id();
        $table->integer('tmdb_id')->unique()->nullable();
        $table->string('titulo', 100);
        $table->year('anio')->nullable();
        $table->text('sinopsis', 500)->nullable();
        $table->integer('duracion')->nullable();
        $table->string('generos')->nullable(); 
        $table->string('poster_path')->nullable();
        $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};
