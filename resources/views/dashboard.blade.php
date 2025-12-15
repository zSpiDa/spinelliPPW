@extends('layouts.app')
@section('content')
    <h2>Dashboard di {{ $user->name }}</h2>

    <h4>Progetti associati</h4>
    <ul>
        @foreach($projects as $p)
            <li>{{ $p->title }} ({{ $p->pivot->role ?? 'n/d' }})</li>
        @endforeach
    </ul>

    <h4>Task attivi</h4>
    <ul>
        @foreach($tasks as $t)
            <li>{{ $t->title }} — {{ $t->status }}</li>
        @endforeach
    </ul>

    <h4>Pubblicazioni correlate</h4>
    <ul>
        @foreach($publications as $pub)
            <li>{{ $pub->title }} {{ $pub->status ? '(' . $pub->status . ')' : '' }}</li>
        @endforeach
    </ul>
@endsection
