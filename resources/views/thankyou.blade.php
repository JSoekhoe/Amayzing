@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Bedankt voor je bestelling!</h1>
        <p>We hebben je bestelling ontvangen en sturen je zo snel mogelijk een bevestiging per e-mail.</p>
        <a href="{{ route('home') }}">Terug naar homepage</a>
    </div>
@endsection
