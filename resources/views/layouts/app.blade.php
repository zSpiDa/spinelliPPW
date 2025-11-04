<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestionale Gruppo di Ricerca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<header class="mb-3 border-bottom pb-2 d-flex align-items-center justify-content-between">
    <h1 class="h4 m-0">Gestionale IVU Lab</h1>
    <nav class="d-flex gap-3">
        <a href="/" class="link-primary">Home</a>
        <a href="/projects" class="link-primary">Progetti</a>
    </nav>
</header>

<main class="container">
    @yield('content')
</main>

<footer class="mt-4 text-center text-muted">
    <small>&copy; 2025 Università di Bari</small>
</footer>
</body>
</html>
