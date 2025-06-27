@extends('layouts.app')

@section('content')
    <div class="text-center mt-10">
        <h1 class="text-3xl font-bold mb-4">Bedankt voor je bestelling!</h1>
        <p>Je ontvangt binnenkort een bevestiging per e-mail.</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block text-blue-600 underline">Terug naar home</a>
    </div>
@endsection
