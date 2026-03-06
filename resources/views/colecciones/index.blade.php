<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Colecciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-400 rounded-md p-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-400 rounded-md p-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Botón Nueva Colección -->
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-800">Mis Colecciones</h3>
                <a href="{{ route('colecciones.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    + Crear Nueva Colección
                </a>
            </div>

            <!-- Mis Colecciones - Grid de tarjetas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @forelse($misColecciones as $coleccion)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition duration-300">
                        <!-- Cabecera de la colección -->
                        <div class="p-5 {{ $coleccion->publica ? 'bg-gradient-to-r from-green-50 to-blue-50' : 'bg-gradient-to-r from-gray-50 to-gray-100' }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-xl text-gray-800">{{ $coleccion->nombre }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold">{{ $coleccion->peliculas->count() }}</span> películas
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $coleccion->publica ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $coleccion->publica ? 'Pública' : 'Privada' }}
                                </span>
                            </div>
                            @if($coleccion->descripcion)
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $coleccion->descripcion }}</p>
                            @endif
                        </div>

                        <!-- Miniaturas de películas (primeras 3) -->
                        <div class="px-5 py-3 bg-white">
                            @if($coleccion->peliculas->count() > 0)
                                <div class="flex -space-x-2 overflow-hidden">
                                    @foreach($coleccion->peliculas->take(3) as $pelicula)
                                        @if($pelicula->poster_path)
                                            <img src="{{ $pelicula->poster_path }}" 
                                                 class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover"
                                                 title="{{ $pelicula->titulo }}">
                                        @else
                                            <div class="inline-block h-10 w-10 rounded-full ring-2 ring-white bg-gray-300 flex items-center justify-center text-xs text-gray-600">
                                                ?
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($coleccion->peliculas->count() > 3)
                                        <span class="inline-block h-10 w-10 rounded-full ring-2 ring-white bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600">
                                            +{{ $coleccion->peliculas->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-400 italic">Sin películas aún</p>
                            @endif
                        </div>

                        <!-- Acciones -->
                        <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                            <a href="{{ route('colecciones.show', $coleccion->id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver detalles
                            </a>
                            <div class="flex gap-3">
                                <a href="{{ route('colecciones.edit', $coleccion->id) }}" 
                                   class="text-gray-600 hover:text-blue-600 transition"
                                   title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('colecciones.destroy', $coleccion->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-gray-600 hover:text-red-600 transition"
                                            title="Eliminar"
                                            onclick="return confirm('¿Eliminar colección?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 bg-white rounded-lg shadow-sm">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-gray-600 text-lg mb-4">No tienes colecciones aún</p>
                        <a href="{{ route('colecciones.create') }}" 
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Crear primera colección
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Colecciones Públicas de Otros Usuarios -->
            @if($otrasColecciones->count() > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Colecciones Públicas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($otrasColecciones as $coleccion)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition">
                                <div class="p-5 bg-gradient-to-r from-purple-50 to-pink-50">
                                    <h4 class="font-bold text-lg text-gray-800">{{ $coleccion->nombre }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Por <span class="font-semibold">{{ $coleccion->user->name }}</span>
                                    </p>
                                    @if($coleccion->descripcion)
                                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $coleccion->descripcion }}</p>
                                    @endif
                                </div>
                                <div class="px-5 py-3 bg-white flex justify-between items-center">
                                    <span class="text-sm text-gray-600">
                                        {{ $coleccion->peliculas->count() }} películas
                                    </span>
                                    <a href="{{ route('colecciones.show', $coleccion->id) }}" 
                                       class="text-purple-600 hover:text-purple-800 font-medium text-sm flex items-center gap-1">
                                        Ver colección
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Acceso rápido a búsqueda de películas -->
            <div class="mt-12 text-center">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-8 text-white">
                    <h3 class="text-2xl font-bold mb-2">¿Quieres añadir más películas?</h3>
                    <p class="mb-6">Busca nuevas películas en TMDB y añádelas a tus colecciones</p>
                    <a href="{{ route('peliculas.buscar') }}" 
                       class="inline-block bg-white text-blue-600 font-bold py-3 px-8 rounded-lg shadow-md hover:bg-gray-100 transition duration-300">
                        Buscar Películas
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>