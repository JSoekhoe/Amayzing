<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Amayzing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
</head>
<body>
<main class="container">
    <header>
        <h1><a href="{{ route('home') }}">Amayzing</a></h1>
        <nav>
            <a href="{{ route('checkout.index') }}">Winkelmandje</a>
        </nav>
    </header>
    @yield('content')
</main>
</body>
</html>
