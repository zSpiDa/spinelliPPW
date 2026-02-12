@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Pubblicazioni</h1>
        @if(Auth::user() && in_array(Auth::user()->role, ['admin','pi','manager']))
        <a href="{{ route('publications.create') }}" class="inline-block bg-blue-600 text-black font-bold px-4 py-2 rounded shadow hover:bg-blue-700">Nuova</a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded shadow">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="p-2">Titolo</th>
                    <th class="p-2">Stato</th>
                    <th class="p-2">Progetti</th>
                    <th class="p-2">Autori</th>
                    <th class="p-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($publications as $pub)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2">
                        <a class="text-blue-700" href="{{ route('publications.show', $pub) }}">{{ $pub->title }}</a>
                        <div class="text-xs text-gray-500">{{ $pub->type }} @if($pub->venue) • {{ $pub->venue }} @endif</div>
                    </td>
                    <td class="p-2">
                        <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ $pub->status ?? '—' }}</span>
                    </td>
                    <td class="p-2 text-sm">
                        @foreach($pub->projects as $pr)
                            <span class="inline-block bg-slate-100 px-2 py-0.5 rounded mr-1 mb-1">{{ $pr->title }}</span>
                        @endforeach
                    </td>
                    <td class="p-2 text-sm">
                        @foreach($pub->authors->sortBy('order') as $a)
                            <span class="mr-1">{{ $a->user->name }}@if($a->is_corresponding)<sup>*</sup>@endif</span>
                        @endforeach
                    </td>
                    <td class="p-2 text-right">
                        @if(Auth::user() && in_array(Auth::user()->role, ['admin','pi','manager']))
                        <a class="text-blue-700 mr-2" href="{{ route('publications.edit', $pub) }}">Modifica</a>
                        <form action="{{ route('publications.destroy', $pub) }}" method="POST" class="inline" onsubmit="return confirm('Eliminare?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Elimina</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td class="p-4 text-gray-500" colspan="5">Nessuna pubblicazione.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $publications->links() }}</div>
</div>
@endsection
