@extends('layouts.app')

@section('content')
    <h1>Crea nuovo progetto</h1>

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf

        <div>
            <label>Titolo</label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label>Status</label>
            <input type="text" name="status">
        </div>

        <button type="submit">Salva</button>
    </form>
@endsection
