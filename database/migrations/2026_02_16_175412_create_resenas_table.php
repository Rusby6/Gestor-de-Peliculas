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
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pelicula_id')->constrained()->onDelete('cascade');
            $table->integer('puntuacion'); 
            $table->text('reseña')->nullable(); 
            $table->boolean('visible')->default(true); // para moderación (admin puede ocultar)
            $table->timestamps();
            
            // Una valoración por usuario y película
            $table->unique(['user_id', 'pelicula_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
