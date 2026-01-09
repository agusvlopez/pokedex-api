@extends('layouts.app')

@section('title', 'Todos los Pokemon')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Todos los Pokémon</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($pokemons as $pokemon)
            <a href="{{ route('pokemons.show', $pokemon['name']) }}" class="bg-white rounded-lg shadow-md p-4 text-center flex flex-col gap-4 hover:shadow-xl hover:bg-gray-50 transition duration-300">
                <img class="mx-auto" src="{{ $pokemon['image'] }}" alt="{{ $pokemon['name'] }}">
                <h3 class="text-xl font-semibold">{{ ucfirst($pokemon['name']) }}</h3>
            </a>
        @endforeach
    </div>
@endsection
