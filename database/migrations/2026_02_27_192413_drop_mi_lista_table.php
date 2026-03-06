<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('mi_lista'); // Elimina la tabla si existe
    }

    public function down(): void
    {
        // Si quieres poder revertir, recrea la tabla
        Schema::create('mi_lista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->string('titulo');
            $table->string('poster_path')->nullable();
            $table->integer('anio')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'tmdb_id']);
        });
    }
};