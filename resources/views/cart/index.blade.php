@foreach($cart as $item)
    <div>
        <strong>{{ $item['product']->name }}</strong><br>
        Aantal: {{ $item['quantity'] }}<br>
        <form action="{{ route('cart.remove', $item['product']) }}" method="POST">
            @csrf
            <button type="submit">Verwijder</button>
        </form>
    </div>
@endforeach

<a href="{{ route('checkout') }}">Doorgaan naar afrekenen</a>
