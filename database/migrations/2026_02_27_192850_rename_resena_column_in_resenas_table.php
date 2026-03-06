<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Este método se ejecuta cuando haces php artisan migrate
     */
    public function up(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            // 🔴 IMPORTANTE: Renombramos la columna de 'reseña' a 'texto'
            $table->renameColumn('reseña', 'texto');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Este método se ejecuta cuando haces php artisan migrate:rollback
     * Sirve para deshacer el cambio si es necesario
     */
    public function down(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            // 🔴 Volvemos a renombrar de 'texto' a 'reseña' (por si hay que revertir)
            $table->renameColumn('texto', 'reseña');
        });
    }
};