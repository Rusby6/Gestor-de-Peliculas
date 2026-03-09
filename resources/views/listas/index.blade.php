<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Listas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-400 rounded-md p-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- PENDIENTES -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-yellow-50 border-b border-yellow-200 flex justify-between items-center">
                    <h3 class="font-semibold text-yellow-800 text-lg">⏳ Pendientes</h3>
                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs">
                        {{ $pendientes->count() }} películas
                    </span>
                </div>
                <div class="p-6">
                    @forelse($pendientes as $item)
                        <div class="border rounded-lg overflow-hidden shadow-sm mb-4 last:mb-0">
                            <div class="flex flex-col md:flex-row">
                                @if($item->pelicula->poster_path)
                                    @php
                                        $imageUrl = $item->pelicula->poster_path;
                                        if (!str_starts_with($imageUrl, 'http')) {
                                            $imageUrl = 'https://image.tmdb.org/t/p/w200' . $imageUrl;
                                        }
                                    @endphp
                                    <img src="{{ $imageUrl }}" 
                                         class="w-full md:w-32 h-48 object-cover"
                                         alt="{{ $item->pelicula->titulo }}">
                                @else
                                    <div class="w-full md:w-32 h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                        Sin imagen
                                    </div>
                                @endif
                                
                                <div class="p-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-lg">{{ $item->pelicula->titulo }}</h4>
                                            <p class="text-sm text-gray-600">{{ $item->pelicula->anio }} • {{ $item->pelicula->duracion ?? '?' }} min</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-yellow-600 font-semibold">
                                                ⭐ {{ number_format($item->pelicula->mediaValoraciones(), 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                   @if($item->pelicula->generos && $item->pelicula->generos->count() > 0)
                                        <p class="text-sm text-gray-600 mt-2">
                                            <span class="font-semibold">Géneros:</span> 
                                            {{ $item->pelicula->generos->pluck('nombre')->implode(', ') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400 italic mt-2">Géneros no disponibles</p>
                                    @endif
                                    
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ $item->pelicula->sinopsis ?? 'Sin sinopsis disponible' }}
                                    </p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('peliculas.show', $item->pelicula) }}" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                                Ver detalles
                                            </a>
                                            
                                            <!-- 🔴 FORMULARIO CON ESTADO PENDIENTE -->
                                            <form method="POST" action="{{ route('listas.quitar', $item->pelicula) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="estado" value="pendiente">
                                                <button type="submit" 
                                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded"
                                                        onclick="return confirm('¿Quitar de pendientes?')">
                                                    Quitar
                                                </button>
                                            </form>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            Añadida: {{ $item->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay películas pendientes</p>
                    @endforelse
                </div>
            </div>
            
            <!-- VISTAS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-green-50 border-b border-green-200 flex justify-between items-center">
                    <h3 class="font-semibold text-green-800 text-lg">✓ Vistas</h3>
                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">
                        {{ $vistas->count() }} películas
                    </span>
                </div>
                <div class="p-6">
                    @forelse($vistas as $item)
                        <div class="border rounded-lg overflow-hidden shadow-sm mb-4 last:mb-0">
                            <div class="flex flex-col md:flex-row">
                                @if($item->pelicula->poster_path)
                                    @php
                                        $imageUrl = $item->pelicula->poster_path;
                                        if (!str_starts_with($imageUrl, 'http')) {
                                            $imageUrl = 'https://image.tmdb.org/t/p/w200' . $imageUrl;
                                        }
                                    @endphp
                                    <img src="{{ $imageUrl }}" 
                                         class="w-full md:w-32 h-48 object-cover"
                                         alt="{{ $item->pelicula->titulo }}">
                                @else
                                    <div class="w-full md:w-32 h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                        Sin imagen
                                    </div>
                                @endif
                                
                                <div class="p-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-lg">{{ $item->pelicula->titulo }}</h4>
                                            <p class="text-sm text-gray-600">{{ $item->pelicula->anio }} • {{ $item->pelicula->duracion ?? '?' }} min</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-yellow-600 font-semibold">
                                                ⭐ {{ number_format($item->pelicula->mediaValoraciones(), 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($item->pelicula->generos)
                                        <p class="text-sm text-gray-600 mt-2">
                                            <span class="font-semibold">Géneros:</span> {{ $item->pelicula->generos }}
                                        </p>
                                    @endif
                                    
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ $item->pelicula->sinopsis ?? 'Sin sinopsis disponible' }}
                                    </p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('peliculas.show', $item->pelicula) }}" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                                Ver detalles
                                            </a>
                                            
                                            <!-- 🔴 FORMULARIO CON ESTADO VISTA -->
                                            <form method="POST" action="{{ route('listas.quitar', $item->pelicula) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="estado" value="vista">
                                                <button type="submit" 
                                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded"
                                                        onclick="return confirm('¿Quitar de vistas?')">
                                                    Quitar
                                                </button>
                                            </form>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            Añadida: {{ $item->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay películas vistas</p>
                    @endforelse
                </div>
            </div>
            
            <!-- FAVORITAS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-red-50 border-b border-red-200 flex justify-between items-center">
                    <h3 class="font-semibold text-red-800 text-lg">♥ Favoritas</h3>
                    <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs">
                        {{ $favoritas->count() }} películas
                    </span>
                </div>
                <div class="p-6">
                    @forelse($favoritas as $item)
                        <div class="border rounded-lg overflow-hidden shadow-sm mb-4 last:mb-0">
                            <div class="flex flex-col md:flex-row">
                                @if($item->pelicula->poster_path)
                                    @php
                                        $imageUrl = $item->pelicula->poster_path;
                                        if (!str_starts_with($imageUrl, 'http')) {
                                            $imageUrl = 'https://image.tmdb.org/t/p/w200' . $imageUrl;
                                        }
                                    @endphp
                                    <img src="{{ $imageUrl }}" 
                                         class="w-full md:w-32 h-48 object-cover"
                                         alt="{{ $item->pelicula->titulo }}">
                                @else
                                    <div class="w-full md:w-32 h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                        Sin imagen
                                    </div>
                                @endif
                                
                                <div class="p-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-lg">{{ $item->pelicula->titulo }}</h4>
                                            <p class="text-sm text-gray-600">{{ $item->pelicula->anio }} • {{ $item->pelicula->duracion ?? '?' }} min</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-yellow-600 font-semibold">
                                                ⭐ {{ number_format($item->pelicula->mediaValoraciones(), 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($item->pelicula->generos)
                                        <p class="text-sm text-gray-600 mt-2">
                                            <span class="font-semibold">Géneros:</span> {{ $item->pelicula->generos }}
                                        </p>
                                    @endif
                                    
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ $item->pelicula->sinopsis ?? 'Sin sinopsis disponible' }}
                                    </p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('peliculas.show', $item->pelicula) }}" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                                Ver detalles
                                            </a>
                                            
                                            <!-- 🔴 FORMULARIO CON ESTADO FAVORITA -->
                                            <form method="POST" action="{{ route('listas.quitar', $item->pelicula) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="estado" value="favorita">
                                                <button type="submit" 
                                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded"
                                                        onclick="return confirm('¿Quitar de favoritas?')">
                                                    Quitar
                                                </button>
                                            </form>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            Añadida: {{ $item->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay películas favoritas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
