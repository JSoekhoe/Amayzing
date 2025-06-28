<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-serif font-semibold text-gray-900 tracking-wide mb-6">
            Bestelling #{{ $order->id }}
        </h1>
    </x-slot>

    <section class="max-w-5xl mx-auto px-6 py-12 bg-white rounded-lg shadow-lg">
        <div class="mb-8 space-y-3 text-gray-700">
            <p><span class="font-semibold text-gray-900">Klant:</span> {{ $order->name }} ({{ $order->email }})</p>
            <p><span class="font-semibold text-gray-900">Status:</span> {{ ucfirst($order->status) }}</p>
        </div>

        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mb-12">
            @csrf
            @method('PATCH')
            <label for="status" class="block mb-2 text-gray-900 font-semibold tracking-wide">Wijzig status</label>
            <select name="status" id="status" onchange="this.form.submit()"
                    class="w-full max-w-xs rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-rose-400 shadow-sm transition">
                @foreach(['in_behandeling', 'verzonden', 'afgerond', 'geannuleerd'] as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </form>

        <hr class="border-t-2 border-rose-100 mb-12">

        <h2 class="text-2xl font-serif font-semibold text-gray-900 mb-6">Producten</h2>
        <ul class="list-disc list-inside space-y-3 text-gray-800 mb-12">
            @foreach($order->items as $item)
                <li class="text-lg">{{ $item->quantity }}× {{ $item->product->name }} — <span class="font-semibold">€{{ number_format($item->price, 2, ',', '.') }}</span></li>
            @endforeach
        </ul>

        <p class="text-xl font-serif font-semibold text-gray-900 mb-16">
            Totaalprijs: <span class="text-rose-500">€{{ number_format($order->total_price, 2, ',', '.') }}</span>
        </p>

        @if($order->type === 'bezorgen' && $order->address)
            <hr class="border-t-2 border-rose-100 mb-8">
            <h2 class="text-2xl font-serif font-semibold text-gray-900 mb-6">Bezorglocatie</h2>
            <div id="map" class="rounded-xl shadow-lg" style="height: 400px;"></div>

            <script>
                function initMap() {
                    const geocoder = new google.maps.Geocoder();
                    const address = @json($order->address . ', ' . $order->postcode);

                    geocoder.geocode({ address: address }, function(results, status) {
                        if (status === 'OK') {
                            const map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 14,
                                center: results[0].geometry.location,
                                mapTypeControl: false,
                                streetViewControl: false,
                                fullscreenControl: false,
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
    </section>
</x-app-layout>
