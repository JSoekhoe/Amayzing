@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Bestelling #{{ $order->id }}</h1>
        <p><strong>Klant:</strong> {{ $order->name }} ({{ $order->email }})</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>

        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
            @csrf
            @method('PATCH')
            <label for="status">Wijzig status:</label>
            <select name="status" onchange="this.form.submit()">
                @foreach(['in_behandeling', 'verzonden', 'afgerond', 'geannuleerd'] as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </form>

        <hr>

        <h4>Producten</h4>
        <ul>
            @foreach($order->items as $item)
                <li>{{ $item->quantity }}× {{ $item->product->name }} (€{{ number_format($item->price, 2) }})</li>
            @endforeach
        </ul>

        <p><strong>Totaalprijs:</strong> €{{ number_format($order->total_price, 2) }}</p>
    </div>
@endsection
@if($order->type === 'bezorgen' && $order->address)
    <hr>
    <h4>Bezorglocatie</h4>
    <div id="map" style="height: 400px;"></div>

    <script>
        function initMap() {
            const geocoder = new google.maps.Geocoder();
            const address = @json($order->address . ', ' . $order->postcode);

            geocoder.geocode({ address: address }, function(results, status) {
                if (status === 'OK') {
                    const map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 14,
                        center: results[0].geometry.location
                    });

                    new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                } else {
                    alert('Adres kon niet worden gevonden: ' + status);
                }
            });
        }
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap">
    </script>
@endif
