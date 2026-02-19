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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/peliculas/buscar', [App\Http\Controllers\PeliculaController::class, 'buscar'])->name('peliculas.buscar');
    Route::get('/mi-lista', [App\Http\Controllers\MiListaController::class, 'index'])->name('mi-lista.index');
    Route::post('/mi-lista/guardar', [App\Http\Controllers\MiListaController::class, 'guardar'])->name('mi-lista.guardar');
    Route::delete('/mi-lista/{id}', [App\Http\Controllers\MiListaController::class, 'eliminar'])->name('mi-lista.eliminar');
});

require __DIR__.'/auth.php';
