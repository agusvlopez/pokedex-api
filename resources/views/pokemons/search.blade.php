@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h2>Resultados para: "{{ $query }}"</h2>
    <p class="text-muted mb-4">Se encontraron {{ count($results) }} Pokemon</p>

    <a href="{{ route('pokemons.index') }}" class="btn btn-secondary">
        ← Volver al inicio
    </a>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 py-12">
        @forelse($results as $pokemon)
            <div class="group">
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 h-full flex flex-col">

                    <div class="relative bg-linear-to-br from-gray-100 to-gray-200 p-6">
                        <img
                            src="{{ $pokemon['image'] }}"
                            alt="{{ $pokemon['name'] }}"
                            class="w-full h-48 object-contain"
                            loading="lazy"
                        >
                        <span class="absolute top-2 right-2 bg-gray-800 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            #{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="p-4 flex flex-col grow">
                        <h3 class="text-lg font-bold text-gray-800 capitalize text-center mb-3">
                            {{ $pokemon['name'] }}
                        </h3>

                        @if(isset($pokemon['types']))
                            <div class="flex justify-center gap-2 mb-4">
                                @foreach($pokemon['types'] as $type)
                                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($type['name']) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <a
                            href="{{ route('pokemons.show', $pokemon['id']) }}"
                            class="mt-auto w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center"
                        >
                            Ver detalle →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron Pokemon</h3>
                    <p class="text-gray-500">Intentá con otro término de búsqueda</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
