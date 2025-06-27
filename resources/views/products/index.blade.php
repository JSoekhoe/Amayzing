<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-800">Producten</h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- Leveringswijze + postcode formulier --}}
        <form method="GET" action="{{ route('products.index') }}" class="mb-6 max-w-md mx-auto px-4 border rounded p-4 shadow-sm">
            <label class="block font-semibold mb-2">Kies je gewenste leveringswijze:</label>
            <select name="delivery_method" id="delivery-method" onchange="this.form.submit()" class="w-full border p-2 rounded mb-4">
                <option value="afhalen" {{ $selectedDeliveryMethod === 'afhalen' ? 'selected' : '' }}>Afhalen</option>
                <option value="bezorgen" {{ $selectedDeliveryMethod === 'bezorgen' ? 'selected' : '' }}>Bezorgen</option>
            </select>

            @if($selectedDeliveryMethod === 'bezorgen')
                <label for="postcode" class="block font-semibold mb-1">Voer je postcode in voor bezorging:</label>
                <input
                    type="text"
                    id="postcode"
                    name="postcode"
                    value="{{ old('postcode', $postcode ?? '') }}"
                    placeholder="Bijv. 1234AB"
                    class="w-full border p-2 rounded mb-2"
                >
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                    Check bezorgbaarheid
                </button>
            @endif

            @if($deliveryMessage)
                <p class="text-red-600 mt-2">{{ $deliveryMessage }}</p>
            @endif
        </form>

        @if(!$hasProducts)
            <p class="text-gray-600">Er zijn momenteel geen producten beschikbaar.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($products as $product)
                    @php
                        $maxStock = ($selectedDeliveryMethod === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;
                        $outOfStock = $maxStock <= 0;
                    @endphp

                    <div class="border rounded p-4 shadow-sm">
                        <h2 class="text-xl font-semibold mb-2 text-gray-800">{{ $product->name }}</h2>
                        <p class="mb-4 text-gray-700">Prijs: €{{ number_format($product->price, 2, ',', '.') }}</p>

                        @if(!$outOfStock)
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="product-form">
                                @csrf

                                <label class="block text-sm font-medium text-gray-700 mb-1">Aantal:</label>
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="{{ $maxStock }}"
                                    value="1"
                                    class="mb-2 w-full border-gray-300 rounded quantity-input"
                                    required
                                >

                                <button
                                    type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full submit-btn"
                                >
                                    Voeg toe aan winkelwagen
                                </button>
                            </form>
                        @else
                            <p class="text-red-600 font-semibold mb-2">Niet op voorraad voor {{ $selectedDeliveryMethod }}.</p>
                            <button disabled class="bg-gray-400 text-white px-4 py-2 rounded w-full cursor-not-allowed">
                                Niet beschikbaar
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <script>
        // Alleen JS om postcode-veld tonen/verbergen te regelen, verder geen logica
        const deliveryMethodSelect = document.getElementById('delivery-method');
        const postcodeField = document.getElementById('postcode');

        deliveryMethodSelect.addEventListener('change', () => {
            // De pagina wordt sowieso gerefresht door onchange submit, dus hier kan dit leeg
        });
    </script>
</x-app-layout>
