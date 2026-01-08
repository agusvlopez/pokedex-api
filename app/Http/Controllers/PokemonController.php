<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index()
    {
        // Obtener listado de Pokémon
        // return view con los datos
        return view('pokemons.index');
    }

    public function show($nameOrId)
    {
        // Obtener detalle de UN Pokémon
        // return view con los datos
    }

    public function search(Request $request)
    {
        // Buscar Pokémon por query
        // return view con resultados
    }
}
