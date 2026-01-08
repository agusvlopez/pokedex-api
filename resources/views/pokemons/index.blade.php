
@extends('layouts.app')

@section('title', 'Todos los Pokémon')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Pokédex</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

    </div>
@endsection


@push('scripts')
    <script>
        console.log('Script solo para esta vista');
    </script>
@endpush
