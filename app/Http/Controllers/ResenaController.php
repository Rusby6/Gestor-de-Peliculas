<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ResenaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Pelicula $pelicula)
    {
        $request->validate([
            'puntuacion' => 'required|integer|min:1|max:10',
            'texto' => 'nullable|string|max:1000',
        ]);

        $existe = Resena::where('user_id', Auth::id())
                        ->where('pelicula_id', $pelicula->id)
                        ->exists();

        if ($existe) {
            return back()->with('error', 'Ya has valorado esta película');
        }

        Resena::create([
            'user_id' => Auth::id(),
            'pelicula_id' => $pelicula->id,
            'puntuacion' => $request->puntuacion,
            'texto' => $request->texto,
            'visible' => true,
        ]);

        return back()->with('success', 'Valoración guardada correctamente');
    }

    public function destroy(Resena $resena)
    {
        Gate::authorize('delete', $resena);
        $resena->delete();
        return back()->with('success', 'Reseña eliminada');
    }

    public function toggleVisibility(Resena $resena)
    {
        //  Solo admin puede moderar
        Gate::authorize('admin-only');
        
        $resena->update(['visible' => !$resena->visible]);
        
        return back()->with('success', "Reseña " . ($resena->visible ? 'visible' : 'oculta'));
    }
}
