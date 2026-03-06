<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColeccionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');  //significa: para usar este controlador, tienes que haber iniciado sesión
    }

    /**
     * Mostrar listado de colecciones del usuario y colecciones públicas
     * GET /colecciones
     */
    public function index()
    {
        $user = Auth::user();   // Obtiene el usuario registrado
        
        $misColecciones = $user->colecciones()->latest()->get();
        
        $otrasColecciones = Coleccion::where('user_id', '!=', $user->id)
                                     ->where('publica', true)
                                     ->with('user')//carga datos de usuarios en una sola consulta, evita hacer una consulta por cada colección.
                                     ->latest()
                                     ->get();
        
        return view('colecciones.index', compact('misColecciones', 'otrasColecciones'));    //compact envia datos a la vista
    }

    /**
     * Mostrar formulario para crear nueva colección
     * GET /colecciones/create
     */
    public function create()
    {
        return view('colecciones.create');
    }

    /**
     * Guardar nueva colección en la BD
     * POST /colecciones
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'publica' => 'nullable|boolean',
        ]);

        $coleccion = Coleccion::create([
            'user_id' => Auth::id(),          
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'publica' => $request->has('publica'),
        ]);

        return redirect()->route('colecciones.index')
            ->with('success', 'Colección creada correctamente');
    }

    /**
     * Mostrar una colección específica
     * GET /colecciones/{id}
     */
    public function show($id)
    {
        $coleccion = Coleccion::findOrFail($id);
        
        if (!$coleccion->publica && (!Auth::check() || Auth::id() != $coleccion->user_id)) {  
            abort(403, 'Esta colección es privada');
        }

        $peliculas = $coleccion->peliculas()->latest()->get();
        $peliculasCatalogo = Pelicula::latest()->paginate(12);  
        
        return view('colecciones.show', compact('coleccion', 'peliculas', 'peliculasCatalogo'));
    }


    /**
     * Mostrar formulario para editar colección
     * GET /colecciones/{id}/edit
     */
    public function edit($id)
    {
        $coleccion = Coleccion::findOrFail($id);
        
        // Solo el dueño puede editar
        if (!Auth::check() || Auth::id() != $coleccion->user_id) {
            abort(403);
        }

        return view('colecciones.edit', compact('coleccion'));
    }

    /**
     * Actualizar colección en la BD
     * PUT /colecciones/{id}
     */
    public function update(Request $request, $id)
    {
        $coleccion = Coleccion::findOrFail($id);
        
        // Solo el dueño puede actualizar
        if (!Auth::check() || Auth::id() != $coleccion->user_id) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'publica' => 'nullable|boolean',
        ]);

        $coleccion->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'publica' => $request->has('publica'),
        ]);

        return redirect()->route('colecciones.show', $coleccion->id)
            ->with('success', 'Colección actualizada correctamente');
    }

    /**
     * Eliminar colección de la BD
     * DELETE /colecciones/{id}
     */
    public function destroy($id)
    {
        $coleccion = Coleccion::findOrFail($id);
        
        // Solo el dueño puede eliminar
        if (!Auth::check() || Auth::id() != $coleccion->user_id) {
            abort(403);
        }

        $coleccion->delete();

        return redirect()->route('colecciones.index')
            ->with('success', 'Colección eliminada correctamente');
    }

    /**
     * Añadir película a una colección
     * POST /colecciones/{id}/peliculas/{pelicula}
     */
    public function addPelicula($id, Pelicula $pelicula)    // no entiendo lo de que $pelicula es inyectado automáticamente
    {
        $coleccion = Coleccion::findOrFail($id);
        
        // Solo el dueño puede añadir
        if (!Auth::check() || Auth::id() != $coleccion->user_id) {
            abort(403);
        }

        // Verificar si ya está en la colección
        if ($coleccion->peliculas()->where('pelicula_id', $pelicula->id)->exists()) {
            return back()->with('error', 'La película ya está en la colección');
        }

        $coleccion->peliculas()->attach($pelicula);     // attach() es un método de relaciones N-N para insertar en la tabla pivote

        return back()->with('success', 'Película añadida a la colección');
    }

    /**
     * Quitar película de una colección
     * DELETE /colecciones/{id}/peliculas/{pelicula}
     */
    public function removePelicula($id, Pelicula $pelicula)
    {
        $coleccion = Coleccion::findOrFail($id);
        
        // Solo el dueño puede quitar
        if (!Auth::check() || Auth::id() != $coleccion->user_id) {
            abort(403);
        }

        $coleccion->peliculas()->detach($pelicula);     // detach() es un método de relaciones N-N para eliminar de la tabla pivote

        return back()->with('success', 'Película eliminada de la colección');
    }
}