@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">{{ $publication->title }}</h1>
        <div>
            @if(Auth::user() && in_array(Auth::user()->role, ['admin','pi','manager']))
            <a href="{{ route('publications.edit', $publication) }}" class="text-blue-700 mr-3">Modifica</a>
            @endif
            <a href="{{ route('publications.index') }}" class="text-gray-700">Torna alla lista</a>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow grid md:grid-cols-2 gap-4">
        <div>
            <div class="mb-2"><span class="font-semibold">Tipo:</span> {{ $publication->type ?? '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Venue:</span> {{ $publication->venue ?? '—' }}</div>
            <div class="mb-2"><span class="font-semibold">DOI:</span> {{ $publication->doi ?? '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Stato:</span> <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ $publication->status ?? '—' }}</span></div>
            <div class="mb-2"><span class="font-semibold">Target deadline:</span> {{ $publication->target_deadline ?? '—' }}</div>
        </div>
        <div>
            <div class="mb-2"><span class="font-semibold">Progetti:</span>
                <div>
                    @forelse($publication->projects as $pr)
                        <span class="inline-block bg-slate-100 px-2 py-0.5 rounded mr-1 mb-1">{{ $pr->title }}</span>
                    @empty
                        —
                    @endforelse
                </div>
            </div>
            <div class="mb-2"><span class="font-semibold">Autori:</span>
                <ol class="list-decimal ml-5">
                    @forelse($publication->authors->sortBy('order') as $a)
                        <li>{{ $a->user->name }} @if($a->is_corresponding)<sup>*</sup>@endif</li>
                    @empty
                        <li>—</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow mt-4">
        <h2 class="font-semibold mb-2">File</h2>
        <ul class="list-disc ml-5">
            @forelse($publication->attachments as $att)
                <li>
                    @php($url = \Illuminate\Support\Facades\Storage::url($att->path))
                    <a class="text-blue-700" href="{{ $url }}" target="_blank">Scarica</a>
                    <span class="text-xs text-gray-500 ml-2">{{ $att->path }}</span>
                </li>
            @empty
                <li>Nessun file caricato.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
