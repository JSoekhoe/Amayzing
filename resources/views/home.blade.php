@extends('layout')
@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif


@section('content')
    <h2>Producten</h2>
    <form method="GET" action="{{ route('checkout') }}">
        <div class="grid">
            @foreach($products as $product)
                <article>
                    <h3>{{ $product->name }}</h3>
                    <p>€{{ number_format($product->price, 2) }}</p>
                    @if($product->stock > 0)
                        <label>
                            Aantal:
                            <input type="number" name="cart[{{ $product->id }}]" min="0" max="{{ $product->stock }}" value="0">
                        </label>
                    @else
                        <p><strong>Uitverkocht</strong></p>
                    @endif
                </article>
            @endforeach
        </div>
        <button type="submit">Verder naar Afrekenen</button>
    </form>
@endsection
