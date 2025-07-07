<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Je winkelwagen</h1>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-gray-100 border border-gray-300 text-gray-800 px-6 py-4 rounded-md mb-6" role="alert" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-4 rounded-md mb-6" role="alert" aria-live="assertive">
                {{ session('error') }}
            </div>
        @endif

        @if(empty($cart))
            <div class="text-center py-20 text-gray-500">
                <p class="text-xl mb-4">Je winkelwagen is leeg.</p>
                <div class="max-w-7xl mx-auto px-6 mb-6">
                    <a href="{{ route('products.index') }}"
                       class="inline-block bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded-full py-3 px-6 shadow transition">
                        &larr; Ga terug naar producten
                    </a>
                </div>
            </div>
        @else
            @php $total = 0; @endphp

            <div class="space-y-8">
                @foreach($cart as $productId => $types)
                    @php
                        $firstType = array_key_first($types);
                        $product = $types[$firstType]['product'];
                    @endphp

                    @foreach($types as $type => $data)
                        @php
                            $quantity = $data['quantity'];
                            $price = $product->price;
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                            $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;
                        @endphp

                        <div class="flex flex-col sm:flex-row sm:items-center bg-white rounded-lg shadow-sm p-6 gap-6 border border-gray-200 hover:shadow-md transition">
                            {{-- Productafbeelding --}}
                            <div class="w-full sm:w-32 flex-shrink-0">
                                <img src="{{ $product->image_url ?? '/images/default-product.jpg' }}" alt="{{ $product->name }}" class="rounded-lg object-cover w-full h-32 sm:h-24" />
                            </div>

                            {{-- Product info --}}
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }} <span class="text-gray-500 text-sm">({{ ucfirst($type) }})</span></h2>
                                <p class="mt-1 text-gray-600">Prijs per stuk: <span class="font-medium">€{{ number_format($price, 2, ',', '.') }}</span></p>

                                <form action="{{ route('cart.update', $product) }}" method="POST" id="form_{{ $product->id }}_{{ $type }}" class="mt-4 flex items-center space-x-3 max-w-xs">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="type" value="{{ $type }}">

                                    <label for="quantity_{{ $product->id }}_{{ $type }}" class="sr-only">Aantal</label>
                                    <input
                                        type="number"
                                        id="quantity_{{ $product->id }}_{{ $type }}"
                                        name="quantity"
                                        min="1"
                                        max="{{ $maxStock }}"
                                        value="{{ $quantity }}"
                                        required
                                        onchange="document.getElementById('form_{{ $product->id }}_{{ $type }}').submit()"
                                        class="border border-gray-300 rounded-md text-center w-20 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700"
                                        aria-label="Aantal {{ $product->name }} ({{ ucfirst($type) }})"
                                    >

                                    <div class="flex flex-col space-y-1">
                                        <button type="button"
                                                onclick="changeQty('quantity_{{ $product->id }}_{{ $type }}', {{ $maxStock }}, 'form_{{ $product->id }}_{{ $type }}', 1)"
                                                class="bg-gray-100 hover:bg-gray-200 rounded px-2 py-1 text-lg select-none"
                                                aria-label="Verhoog aantal">+</button>

                                        <button type="button"
                                                onclick="changeQty('quantity_{{ $product->id }}_{{ $type }}', {{ $maxStock }}, 'form_{{ $product->id }}_{{ $type }}', -1)"
                                                class="bg-gray-100 hover:bg-gray-200 rounded px-2 py-1 text-lg select-none"
                                                aria-label="Verlaag aantal">&minus;</button>
                                    </div>
                                </form>

                                <p class="mt-3 text-gray-800 font-semibold">Subtotaal: €{{ number_format($subtotal, 2, ',', '.') }}</p>
                            </div>

                            {{-- Verwijder knop --}}
                            <div class="mt-4 sm:mt-0 sm:ml-6 flex-shrink-0">
                                <form action="{{ route('cart.remove', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <button type="submit" class="text-gray-600 hover:text-gray-900 hover:underline font-semibold" aria-label="Verwijder {{ $product->name }} ({{ ucfirst($type) }})">
                                        Verwijder
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Totaal en afrekenen --}}
            @php
                $hasDelivery = false;
                foreach ($cart as $productId => $types) {
                    if (isset($types['bezorgen'])) {
                        $hasDelivery = true;
                        break;
                    }
                }

                $deliveryFee = ($hasDelivery && $total < 99) ? 5.50 : 0;
                $grandTotal = $total + $deliveryFee;
            @endphp

            <div class="mt-12 max-w-md mx-auto bg-white p-6 rounded-lg shadow-md text-center border border-gray-200">
                <p class="text-gray-700 text-lg mb-2"><strong>Producttotaal:</strong> €{{ number_format($total, 2, ',', '.') }}</p>
                <p class="text-gray-700 text-lg mb-4"><strong>Bezorgkosten:</strong> €{{ number_format($deliveryFee, 2, ',', '.') }}</p>
                <p class="text-2xl font-extrabold mb-6 text-gray-900">Totaal te betalen: €{{ number_format($grandTotal, 2, ',', '.') }}</p>

                <form action="{{ route('checkout.index') }}" method="GET">
                    <button type="submit"
                            name="type"
                            value="{{ $deliveryMethod }}"
                            class="inline-block w-auto px-8 py-3 bg-gray-800 hover:bg-gray-900 focus:bg-gray-900 text-white font-bold rounded-lg shadow-md focus:outline-none focus:ring-4 focus:ring-gray-700 transition"
                    >
                        Bestelling plaatsen
                    </button>
                </form>

            </div>
        @endif
    </div>

    <script>
        function changeQty(inputId, max, formId, delta) {
            const input = document.getElementById(inputId);
            let currentValue = parseInt(input.value) || 1;
            let newValue = currentValue + delta;

            if (newValue < 1) newValue = 1;
            if (newValue > max) newValue = max;

            if (newValue !== currentValue) {
                input.value = newValue;
                document.getElementById(formId).submit();
            }
        }
    </script>
</x-app-layout>
