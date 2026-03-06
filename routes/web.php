<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // =============================================
    // 1️ PRIMERO: RUTAS ESPECÍFICAS (sin parámetros)
    // =============================================
    
    // Búsqueda en TMDB
    Route::get('/peliculas/buscar', [App\Http\Controllers\PeliculaController::class, 'buscar'])
        ->name('peliculas.buscar');
    
    // Importar películas
    Route::post('/peliculas/importar', [App\Http\Controllers\PeliculaController::class, 'importar'])
        ->name('peliculas.importar');
    
    // =============================================
    // 2️ SEGUNDO: RUTAS DEL RESOURCE (create, store, edit, update, destroy)
    // =============================================
    
    // CRUD de películas (solo admin)
    Route::resource('peliculas', App\Http\Controllers\PeliculaController::class)->except(['index', 'show'])->middleware('can:admin-only');
    
    // =============================================
    // 3️ TERCERO: RUTAS PÚBLICAS (con parámetros)
    // =============================================
    
    // Catálogo de películas (público)
    Route::get('/peliculas', [App\Http\Controllers\PeliculaController::class, 'index'])->name('peliculas.index');
    
    // Detalle de película (con parámetro)
    Route::get('/peliculas/{pelicula}', [App\Http\Controllers\PeliculaController::class, 'show'])->name('peliculas.show');
    
    // =============================================
    // 4️ LISTAS PERSONALES
    // =============================================
    
    Route::get('/mis-listas', [App\Http\Controllers\ListaPersonalController::class, 'index'])->name('listas.index');
    Route::post('/peliculas/{pelicula}/lista', [App\Http\Controllers\ListaPersonalController::class, 'agregar']) ->name('listas.agregar');
    Route::post('/lista/agregar-temporal', [App\Http\Controllers\ListaPersonalController::class, 'agregarTemporal'])->name('listas.agregarTemporal');
    Route::delete('/peliculas/{pelicula}/lista', [App\Http\Controllers\ListaPersonalController::class, 'quitar'])->name('listas.quitar');

    // =============================================
    // 5️ RESEÑAS Y VALORACIONES
    // =============================================
    
    Route::post('/peliculas/{pelicula}/resenas', [App\Http\Controllers\ResenaController::class, 'store'])->name('resenas.store');
    Route::put('/resenas/{resena}', [App\Http\Controllers\ResenaController::class, 'update'])->name('resenas.update');
    Route::delete('/resenas/{resena}', [App\Http\Controllers\ResenaController::class, 'destroy'])->name('resenas.destroy');
    Route::post('/resenas/{resena}/toggle', [App\Http\Controllers\ResenaController::class, 'toggleVisibility'])->name('resenas.toggle');

    // =============================================
    // 6️ COLECCIONES
    // =============================================
    
    Route::get('/colecciones', [App\Http\Controllers\ColeccionController::class, 'index'])->name('colecciones.index');
    Route::get('/colecciones/create', [App\Http\Controllers\ColeccionController::class, 'create'])->name('colecciones.create');
    Route::post('/colecciones', [App\Http\Controllers\ColeccionController::class, 'store'])->name('colecciones.store');
    Route::get('/colecciones/{id}/edit', [App\Http\Controllers\ColeccionController::class, 'edit'])->name('colecciones.edit');
    Route::put('/colecciones/{id}', [App\Http\Controllers\ColeccionController::class, 'update']) ->name('colecciones.update');
    Route::delete('/colecciones/{id}', [App\Http\Controllers\ColeccionController::class, 'destroy'])->name('colecciones.destroy');
    Route::get('/colecciones/{id}', [App\Http\Controllers\ColeccionController::class, 'show'])->name('colecciones.show');
    Route::post('/colecciones/{id}/peliculas/{pelicula}', [App\Http\Controllers\ColeccionController::class, 'addPelicula'])->name('colecciones.addPelicula');
    Route::delete('/colecciones/{id}/peliculas/{pelicula}', [App\Http\Controllers\ColeccionController::class, 'removePelicula'])->name('colecciones.removePelicula');
});

require __DIR__.'/auth.php';