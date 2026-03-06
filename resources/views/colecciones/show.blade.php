<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $coleccion->nombre }}
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

            <!-- Información de la colección -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">{{ $coleccion->nombre }}</h3>
                            @if($coleccion->descripcion)
                                <p class="text-gray-600 mb-2">{{ $coleccion->descripcion }}</p>
                            @endif
                            <p class="text-sm text-gray-500">
                                Creada por {{ $coleccion->user->name }} • 
                                {{ $coleccion->peliculas->count() }} películas
                                @if($coleccion->publica)
                                    <span class="text-green-600 ml-2">Pública</span>
                                @else
                                    <span class="text-gray-400 ml-2">Privada</span>
                                @endif
                            </p>
                        </div>
                        
                        @if(Auth::check() && Auth::id() == $coleccion->user_id)
                            <div class="flex gap-2">
                                <a href="{{ route('colecciones.edit', $coleccion->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('colecciones.destroy', $coleccion->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
                                            onclick="return confirm('¿Eliminar colección?')">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Películas en la colección -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Películas en esta colección</h3>

                    @if($peliculas->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($peliculas as $pelicula)
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    @if($pelicula->poster_path)
                                        @php
                                            $imageUrl = $pelicula->poster_path;
                                            // Si no empieza con http, añadir la URL de TMDB
                                            if (!str_starts_with($imageUrl, 'http')) {
                                                $imageUrl = 'https://image.tmdb.org/t/p/w200' . $imageUrl;
                                            }
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                            class="w-full h-auto aspect-[2/3] object-cover"
                                            onerror="this.onerror=null; this.src='https://via.placeholder.com/300x450?text=Sin+imagen';">
                                    @else
                                        <div class="w-full h-auto aspect-[2/3] bg-gray-200 flex items-center justify-center text-gray-500">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold">{{ $pelicula->titulo }}</h4>
                                                <p class="text-sm text-gray-600">{{ $pelicula->anio }}</p>
                                            </div>
                                            
                                            @if(Auth::check() && Auth::id() == $coleccion->user_id)
                                                <form method="POST" 
                                                      action="{{ route('colecciones.removePelicula', [$coleccion->id, $pelicula->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 text-sm"
                                                            onclick="return confirm('¿Quitar de la colección?')">
                                                        ✕
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No hay películas en esta colección.</p>
                    @endif
                </div>
            </div>

            <!-- 🔴 AÑADIR PELÍCULAS - CATÁLOGO COMPLETO (solo para el dueño) -->
            @if(Auth::check() && Auth::id() == $coleccion->user_id)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Añadir películas del catálogo</h3>
                        
                        @php
                            $peliculasCatalogo = App\Models\Pelicula::latest()->paginate(12);
                        @endphp

                        @if($peliculasCatalogo->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($peliculasCatalogo as $pelicula)
                                    @php
                                        $enColeccion = $coleccion->peliculas->contains($pelicula->id);
                                    @endphp
                                    
                                    <div class="border rounded-lg overflow-hidden shadow-sm {{ $enColeccion ? 'opacity-75 bg-gray-50' : '' }}">
                                        @if($pelicula->poster_path)
                                        @php
                                            $imageUrl = $pelicula->poster_path;
                                            if (!str_starts_with($imageUrl, 'http')) {
                                                $imageUrl = 'https://image.tmdb.org/t/p/w200' . $imageUrl;
                                            }
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                            class="w-full h-auto aspect-[2/3] object-cover"
                                            onerror="this.onerror=null; this.src='https://via.placeholder.com/300x450?text=Sin+imagen';">
                                    @else
                                        <div class="w-full h-auto aspect-[2/3] bg-gray-200 flex items-center justify-center text-gray-500">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                        
                                        <div class="p-3">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-semibold">{{ $pelicula->titulo }}</h4>
                                                    <p class="text-sm text-gray-600">{{ $pelicula->anio }}</p>
                                                </div>
                                                
                                                @if(!$enColeccion)
                                                    <form method="POST" action="{{ route('colecciones.addPelicula', [$coleccion->id, $pelicula->id]) }}">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded">
                                                            + Añadir
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="bg-gray-300 text-gray-700 text-sm px-3 py-1 rounded">
                                                        ✓ Añadida
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6">
                                {{ $peliculasCatalogo->links() }}
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">
                                No hay películas en el catálogo. 
                                <a href="{{ route('peliculas.buscar') }}" class="text-blue-600 hover:underline">
                                    Importa algunas desde TMDB
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>