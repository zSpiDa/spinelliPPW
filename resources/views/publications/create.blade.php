@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Nuova pubblicazione</h1>
    <form action="{{ route('publications.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
        @csrf
        @include('publications.form')
        <div class="mt-4">
            <button type="submit" class="bg-blue-600 text-white font-bold px-4 py-2 rounded shadow hover:bg-blue-700">Salva</button>
            <a href="{{ route('publications.index') }}" class="ml-2 text-gray-700">Annulla</a>
        </div>
    </form>
</div>
@endsection
