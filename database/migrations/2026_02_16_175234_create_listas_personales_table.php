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
        Schema::create('listas_personales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pelicula_id')->constrained()->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'vista', 'favorita'])->default('pendiente');
            $table->timestamps();
            
            // Restricción: una misma película no puede estar duplicada en la misma lista del usuario
            $table->unique(['user_id', 'pelicula_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listas_personales');
    }
};
