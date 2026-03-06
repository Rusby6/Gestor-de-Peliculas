<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coleccion_pelicula', function (Blueprint $table) {
            $table->id();
            
            // 🔴 CORREGIDO: Especificamos 'colecciones'
            $table->foreignId('coleccion_id')
                  ->constrained('colecciones')
                  ->onDelete('cascade');
                  
            // 🔴 CORREGIDO: Especificamos 'peliculas' (opcional)
            $table->foreignId('pelicula_id')
                  ->constrained('peliculas')
                  ->onDelete('cascade');
                  
            $table->timestamps();
            $table->unique(['coleccion_id', 'pelicula_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleccion_pelicula');
    }
};