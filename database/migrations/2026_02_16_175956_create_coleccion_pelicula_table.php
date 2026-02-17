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
        Schema::create('coleccion_pelicula', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('coleccion_id')->constrained('colecciones')->onDelete('cascade');
                  
            $table->foreignId('pelicula_id')->constrained('peliculas')->onDelete('cascade');
                  
            $table->timestamps();
            
            // Evitar duplicados en la misma colección
            $table->unique(['coleccion_id', 'pelicula_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coleccion_pelicula');
    }
};