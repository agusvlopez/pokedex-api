<?php

namespace App\Http\Controllers;

use App\Services\PokemonService;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function index()
    {
        $pokemons = $this->pokemonService->getPokemons(20);

        if (empty($pokemons)) {
            abort(404, 'No se encontraron Pokémon');
        }

        return view('pokemons.index', compact('pokemons'));
    }

    public function show($nameOrId)
    {
        $pokemon = $this->pokemonService->getPokemon($nameOrId);

        if (!$pokemon) {
            abort(404, 'Pokemon no encontrado');
        }

        return view('pokemons.show', compact('pokemon'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:50'
        ], [
            'query.required' => 'Por favor ingresá un nombre',
            'query.min' => 'Ingresá al menos 2 caracteres'
        ]);

        $query = $request->input('query');

        $exactMatch = $this->pokemonService->getPokemon($query);
        if ($exactMatch) {
            return view('pokemons.show', ['pokemon' => $exactMatch]);
        }

        $results = $this->pokemonService->searchByPartialName($query);

        if (empty($results)) {
            return redirect()->route('pokemons.index')
                ->with('error', "No se encontraron Pokemon con '{$query}'");
        }

        return view('pokemons.search', [
            'results' => $results,
            'query' => $query
        ]);
    }
}
