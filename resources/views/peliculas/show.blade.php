<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $pelicula->titulo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-400 rounded-md p-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-400 rounded-md p-4">{{ session('error') }}</div>
            @endif

            <!-- Detalles de la película -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Imagen -->
                        @if($pelicula->poster_path)
                            @php
                                $imageUrl = $pelicula->poster_path;
                                if (!str_starts_with($imageUrl, 'http')) {
                                    $imageUrl = 'https://image.tmdb.org/t/p/w500' . $imageUrl;
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" class="w-64 h-96 object-cover rounded-lg" alt="{{ $pelicula->titulo }}" onerror="this.onerror=null; this.src='https://via.placeholder.com/500x750?text=Sin+imagen';">
                        @else
                            <div class="w-64 h-96 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif

                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-2">{{ $pelicula->titulo }}</h1>
                            <p class="text-gray-600 mb-4">{{ $pelicula->anio }} • {{ $pelicula->duracion }} min</p>

                            <!-- Géneros -->
                            @if($pelicula->generos && $pelicula->generos->count() > 0)
                                <p class="mb-4"><span class="font-semibold">Géneros:</span> 
                                    {{ $pelicula->generos->pluck('nombre')->implode(', ') }}
                                </p>
                            @else
                                <p class="mb-4 text-gray-500 italic">Géneros no disponibles</p>
                            @endif

                            <p class="text-gray-700 mb-6">{{ $pelicula->sinopsis ?? 'Sin sinopsis disponible' }}</p>

                            <div class="flex items-center gap-4 mb-4">
                                <span class="text-2xl font-bold text-yellow-500">⭐ {{ number_format($pelicula->mediaValoraciones(), 1) }}</span>
                                <span class="text-gray-600">({{ $pelicula->resenas()->count() }} valoraciones)</span>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('peliculas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">← Volver</a>

                                <!-- 🔴 BOTÓN EDITAR (solo admin) -->
                                @can('admin-only')
                                    <a href="{{ route('peliculas.edit', $pelicula) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                                        ✏️ Editar
                                    </a>
                                @endcan

                                <!-- 🔴 BOTÓN ELIMINAR (solo admin) -->
                                @can('admin-only')
                                    <form method="POST" action="{{ route('peliculas.destroy', $pelicula) }}" onsubmit="return confirm('¿Eliminar esta película del catálogo?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                            🗑️ Eliminar 
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            <!-- Botones para listas personales (se mantienen igual) -->
                            <div class="flex gap-2 mt-4">
                                <form method="POST" action="{{ route('listas.agregar', $pelicula) }}">
                                    @csrf
                                    <input type="hidden" name="estado" value="pendiente">
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">⏳ Pendiente</button>
                                </form>
                                <form method="POST" action="{{ route('listas.agregar', $pelicula) }}">
                                    @csrf
                                    <input type="hidden" name="estado" value="vista">
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">✓ Vista</button>
                                </form>
                                <form method="POST" action="{{ route('listas.agregar', $pelicula) }}">
                                    @csrf
                                    <input type="hidden" name="estado" value="favorita">
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">♥ Favorita</button>
                                </form>
                            </div>

                            <!-- Añadir a colecciones -->
                            @if(Auth::user()->colecciones->count() > 0)
                                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="font-semibold mb-3">Añadir a colección:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(Auth::user()->colecciones as $coleccion)
                                            @php $enColeccion = $coleccion->peliculas->contains($pelicula->id); @endphp
                                            @if(!$enColeccion)
                                                <form method="POST" action="{{ route('colecciones.addPelicula', [$coleccion, $pelicula]) }}">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded">+ {{ $coleccion->nombre }}</button>
                                                </form>
                                            @else
                                                <span class="bg-gray-300 text-gray-700 text-sm px-3 py-1 rounded cursor-not-allowed">✓ {{ $coleccion->nombre }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de valoración -->
            @php $userResena = Auth::user()->resenas()->where('pelicula_id', $pelicula->id)->first(); @endphp
            @if(!$userResena)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Valorar esta película</h3>
                        <form method="POST" action="{{ route('resenas.store', $pelicula) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Puntuación (1-10)</label>
                                <select name="puntuacion" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reseña (opcional)</label>
                                <textarea name="texto" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Escribe tu opinión..."></textarea>
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Enviar valoración</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Lista de reseñas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Reseñas ({{ $pelicula->resenas()->count() }})</h3>
                    
                    @forelse($pelicula->resenas()->latest()->get() as $resena)
                        @if($resena->visible || Auth::user()->isAdmin())
                            <div class="border-b border-gray-200 py-4 last:border-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-semibold">{{ $resena->user->name }}</span>
                                            <span class="text-yellow-500">⭐ {{ $resena->puntuacion }}/10</span>
                                            @if(!$resena->visible)
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Ocultada</span>
                                            @endif
                                        </div>
                                        
                                        @if($resena->texto)
                                            <p class="text-gray-700">{{ $resena->texto }}</p>
                                        @endif
                                        
                                        <p class="text-xs text-gray-400 mt-2">{{ $resena->created_at->diffForHumans() }}</p>
                                    </div>
                                    
                                    <!-- 🔴 BOTONES DE MODERACIÓN (solo admin) -->
                                    @can('admin-only')
                                        <div class="flex gap-2 ml-4">
                                            <form method="POST" action="{{ route('resenas.toggle', $resena) }}">
                                                @csrf
                                                <button type="submit" class="text-sm {{ $resena->visible ? 'text-yellow-600' : 'text-green-600' }} hover:underline">
                                                    {{ $resena->visible ? 'Ocultar' : 'Mostrar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('resenas.destroy', $resena) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:underline" onclick="return confirm('¿Eliminar esta reseña?')">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="text-gray-500">No hay reseñas todavía. ¡Sé el primero en valorar!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>