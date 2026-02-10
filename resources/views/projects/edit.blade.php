<form action="{{ route('projects.update', $project) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="title" value="{{ $project->title }}" required>
    <input type="text" name="status" value="{{ $project->status }}">

    <button type="submit">Aggiorna</button>
</form>
