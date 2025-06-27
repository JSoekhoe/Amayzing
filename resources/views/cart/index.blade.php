<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-800">Winkelwagen</h1>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Controleer of winkelwagen leeg is --}}
        @if(empty($cart))
            <p class="text-gray-600">Je winkelwagen is leeg.</p>
            <a href="{{ route('products.index') }}" class="text-blue-600 underline mt-2 inline-block">
                Ga terug naar producten
            </a>
        @else
            @php
                $total = 0;
                $deliveryFee = 0;
            @endphp

            @foreach($cart as $productId => $types)
                @php
                    $product = $types[array_key_first($types)]['product']; // Als je product object meestuurt in de sessie, anders Product::find($productId)
                @endphp

                @foreach($types as $type => $data)
                    @php
                        $quantity = $data['quantity'];
                        $price = $product->price;
                        $subtotal = $price * $quantity;
                        $total += $subtotal;
                        $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;
                    @endphp

                    <div class="border p-4 mb-4 rounded shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="mb-3 md:mb-0 w-full md:w-3/4">
                            <strong class="block text-lg text-gray-800">{{ $product->name }} ({{ ucfirst($type) }})</strong>
                            <span class="text-gray-600">Prijs per stuk: €{{ number_format($price, 2, ',', '.') }}</span>

                            <form action="{{ route('cart.update', $product) }}" method="POST" class="mt-2 flex items-center flex-wrap gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="type" value="{{ $type }}">
                                <label for="quantity_{{ $product->id }}_{{ $type }}" class="text-sm text-gray-700">Aantal:</label>
                                <input type="number"
                                       name="quantity"
                                       id="quantity_{{ $product->id }}_{{ $type }}"
                                       value="{{ $quantity }}"
                                       min="1"
                                       max="{{ $maxStock }}"
                                       class="border p-1 w-20 text-center rounded shadow-sm">
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    Update
                                </button>
                            </form>

                            <p class="mt-2 text-sm text-gray-700">Subtotaal: €{{ number_format($subtotal, 2, ',', '.') }}</p>
                        </div>

                        <form action="{{ route('cart.remove', $product) }}" method="POST" class="mt-2 md:mt-0">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="{{ $type }}">
                            <button type="submit" class="text-red-600 hover:underline text-sm">Verwijder</button>
                        </form>
                    </div>
                @endforeach
            @endforeach

            @php
                $deliveryFee = ($total < 99) ? 5.50 : 0;
                $grandTotal = $total + $deliveryFee;
            @endphp

            <div class="bg-gray-50 p-4 mt-6 rounded shadow text-right space-y-1 text-gray-800">
                <p><strong>Totaal producten:</strong> €{{ number_format($total, 2, ',', '.') }}</p>
                <p><strong>Bezorgkosten:</strong> €{{ number_format($deliveryFee, 2, ',', '.') }}</p>
                <p class="text-xl"><strong>Totaal te betalen:</strong> €{{ number_format($grandTotal, 2, ',', '.') }}</p>
            </div>

            <a href="{{ route('checkout.index') }}"
               class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 mt-6 inline-block text-center">
                Doorgaan naar afrekenen
            </a>
        @endif
    </div>
</x-app-layout>
