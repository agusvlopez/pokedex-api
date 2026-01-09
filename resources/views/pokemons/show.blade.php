@extends('layouts.app')

@section('title', ucfirst($pokemon['name']))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('pokemons.index') }}"
           class=" text-gray-800 py-3 rounded-lg hover:text-blue-800">
            ← Volver al listado
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-8">

        <div class="text-center mb-6">
            <h1 class="text-4xl font-bold capitalize">{{ $pokemon['name'] }}</h1>
            <p class="text-gray-600 text-xl">#{{ $pokemon['id'] }}</p>
        </div>

        <div class="text-center mb-6">
            <img src="{{ $pokemon['image_hd'] }}"
                 alt="{{ $pokemon['name'] }}"
                 class="w-64 h-64 mx-auto">
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-3">Tipos</h2>
            <div class="flex gap-2">
                @foreach($pokemon['types'] as $type)
                    <span class="px-4 py-2 bg-blue-500 text-white rounded-full capitalize">
                        {{ $type['name'] }}
                    </span>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-100 p-4 rounded">
                <p class="text-gray-600">Altura</p>
                <p class="text-2xl font-bold">{{ $pokemon['height'] }} m</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <p class="text-gray-600">Peso</p>
                <p class="text-2xl font-bold">{{ $pokemon['weight'] }} kg</p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-3">Estadísticas</h2>
            @foreach($pokemon['stats'] as $stat)
                <div class="mb-2">
                    <div class="flex justify-between mb-1">
                        <span class="capitalize">{{ str_replace('-', ' ', $stat['name']) }}</span>
                        <span class="font-bold">{{ $stat['value'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full"
                             style="width: {{ ($stat['value'] / 255) * 100 }}%">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

