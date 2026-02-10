@extends('layouts.app')

@section('content')
    <h1>Crea nuovo progetto</h1>

    <form action="{{ route('projects.store') }}" enctype="multipart/form-data" method="POST">
        @csrf

        <div>
            <label>Titolo:</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label>Descrizione:</label>
            <textarea name="description"></textarea>
        </div>
        <div>
            <label>Allega documento (PDF):</label>
            <input type="file" name="file" accept=".pdf">
        </div>

        <div>
            <label>Status</label>
            <input type="text" name="status">
        </div>

        <button type="submit">Salva</button>
    </form>
@endsection
