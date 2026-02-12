@php($edit = isset($publication))
@if ($errors->any())
    <div class="bg-red-100 text-red-800 p-2 rounded mb-3">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Titolo</label>
        <input type="text" name="title" value="{{ old('title', $publication->title ?? '') }}" class="w-full border rounded p-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium">Tipo</label>
        <input type="text" name="type" value="{{ old('type', $publication->type ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Venue</label>
        <input type="text" name="venue" value="{{ old('venue', $publication->venue ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">DOI</label>
        <input type="text" name="doi" value="{{ old('doi', $publication->doi ?? '') }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Stato</label>
        <select name="status" class="w-full border rounded p-2">
            <option value="">—</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}" @selected(old('status', $publication->status ?? '') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Target deadline</label>
        <input type="date" name="target_deadline" value="{{ old('target_deadline', isset($publication->target_deadline) ?
            (\Illuminate\Support\Carbon::parse($publication->target_deadline)->format('Y-m-d')) : '') }}" class="w-full border rounded p-2">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Progetti collegati</label>
        <select name="projects[]" multiple class="w-full border rounded p-2 h-32">
            @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected(collect(old('projects', $publication->projects->pluck('id') ?? []))->contains($p->id))>{{ $p->title }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Tieni premuto Ctrl/Cmd per selezioni multiple.</p>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Autori (in ordine)</label>
        <div id="authors-list">
            @php
                $existing = $edit ? $publication->authors->sortBy('order')->values() : collect();
                $rowsCount = max(1, old('authors.user_id') ? count(old('authors.user_id')) : $existing->count());
            @endphp
            @for($i=0; $i < $rowsCount; $i++)
            <div class="flex items-center gap-2 mb-2">
                <select name="authors[user_id][]" class="border rounded p-2 flex-1">
                    <option value="">— utente —</option>
                    @foreach($users as $u)
                        @php($val = old("authors.user_id.$i", optional($existing->get($i))->user_id))
                        <option value="{{ $u->id }}" @selected($val == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <input type="number" min="1" name="authors[order][]" value="{{ old("authors.order.$i", optional($existing->get($i))->order ?? ($i+1)) }}" class="w-20 border rounded p-2" placeholder="#">
                <label class="text-sm flex items-center gap-1">
                    @php($corr = old("authors.is_corresponding.$i", optional($existing->get($i))->is_corresponding))
                    <input type="checkbox" name="authors[is_corresponding][]" value="1" @checked($corr)> Corrispondente
                </label>
                <button type="button" class="text-red-600" onclick="this.parentElement.remove()">Rimuovi</button>
            </div>
            @endfor
        </div>
        <button type="button" class="mt-2 text-blue-700" onclick="addAuthorRow()">+ Aggiungi autore</button>

        <script>
        function addAuthorRow(){
            const tpl = `
            <div class=\"flex items-center gap-2 mb-2\">
                <select name=\"authors[user_id][]\" class=\"border rounded p-2 flex-1\">
                    <option value=\"\">— utente —</option>
                    @foreach($users as $u)
                        <option value=\"{{ $u->id }}\">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <input type=\"number\" min=\"1\" name=\"authors[order][]\" value=\"\" class=\"w-20 border rounded p-2\" placeholder=\"#\">
                <label class=\"text-sm flex items-center gap-1\">
                    <input type=\"checkbox\" name=\"authors[is_corresponding][]\" value=\"1\"> Corrispondente
                </label>
                <button type=\"button\" class=\"text-red-600\" onclick=\"this.parentElement.remove()\">Rimuovi</button>
            </div>`;
            document.getElementById('authors-list').insertAdjacentHTML('beforeend', tpl);
        }
        </script>
    </div>

    <div>
        <label class="block text-sm font-medium">PDF principale</label>
        <input type="file" name="main_pdf" accept="application/pdf" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Materiali aggiuntivi</label>
        <input type="file" name="materials[]" multiple class="w-full border rounded p-2">
        <p class="text-xs text-gray-500 mt-1">Max 20MB per file.</p>
    </div>
</div>
