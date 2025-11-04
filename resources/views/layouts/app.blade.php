// resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionale Gruppo di Ricerca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Gestionale Ricerca</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">Progetti</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('publications.index') }}">Pubblicazioni</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('tasks.index') }}">Task</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Persone</a></li>
        </ul>
    </div>
</nav>

<main class="container">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
