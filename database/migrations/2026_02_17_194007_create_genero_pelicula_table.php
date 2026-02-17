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
        Schema::create('genero_pelicula', function (Blueprint $table) {
        $table->id();
        $table->foreignId('genero_id')->constrained()->onDelete('cascade');
        $table->foreignId('pelicula_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        
        $table->unique(['genero_id', 'pelicula_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genero_pelicula');
    }
};
