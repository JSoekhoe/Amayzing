@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Bestellingen</h1>
        @foreach ($orders as $order)
            <div class="border p-3 mb-3">
                <p><strong>#{{ $order->id }}</strong> - {{ $order->name }} - €{{ number_format($order->total_price, 2) }}</p>
                <p>Status: <strong>{{ $order->status ?? 'in_behandeling' }}</strong></p>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary btn-sm">Bekijken</a>
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline" onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt verwijderen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
                </form>
            </div>
        @endforeach

        {{ $orders->links() }}
    </div>
@endsection
