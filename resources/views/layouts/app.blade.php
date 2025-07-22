<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'aMayzing') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            opacity: 0;
            transition: opacity 0.15s ease-in-out;
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }
    </style>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-white text-gray-800 antialiased">
{{-- Optioneel: Navigatie --}}
@include('layouts.navigation')

{{-- Header (optioneel per pagina) --}}
@isset($header)
    <header class="bg-white">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
@endisset

{{-- Inhoud --}}
<main>
    @php
        $previousUrl = url()->previous();
    @endphp

    <div class="max-w-7xl mx-auto px-6 mb-6">
        <button
            onclick="if(document.referrer !== '') { window.history.back(); } else { window.location.href='{{ url('/') }}'; }"
            class="inline-block text-gray-600 hover:text-gray-800 transition cursor-pointer font-medium text-sm"
            style="background: none; border: none; padding: 0;"
            aria-label="Terug"
        >
            &larr; Terug
        </button>
    </div>


    {{ $slot }}
</main>

{{-- Footer (optioneel) --}}
{{-- @include('layouts.footer') --}}
<script>
    // Scrollpositie opslaan
    window.addEventListener('beforeunload', function () {
        localStorage.setItem('scrollPosition', window.scrollY);
    });

    // Scrollpositie herstellen zodra DOM geladen is
    window.addEventListener('DOMContentLoaded', function () {
        const scrollPosition = localStorage.getItem('scrollPosition');
        if (scrollPosition !== null) {
            window.scrollTo(0, parseInt(scrollPosition));
            localStorage.removeItem('scrollPosition');
        }
        document.body.style.opacity = '1';
    });
</script>
</body>
</html>
