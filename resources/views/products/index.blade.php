<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-semibold text-gray-900 leading-tight">
            Producten
        </h2>
    </x-slot>

    <section class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Afhalen / Bezorgen toggle --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex justify-center space-x-10 border-b-4 border-gray-300 pb-4 mb-8">
            <input type="hidden" name="postcode" value="{{ request('postcode') }}">
            <input type="hidden" name="housenumber" value="{{ request('housenumber') }}">

            <button type="submit" name="delivery_method" value="afhalen"
                    class="font-serif text-xl px-8 py-3 rounded-3xl border-b-4 transition
                {{ (request('delivery_method') === 'afhalen' || !request('delivery_method'))
                    ? 'border-gray-700 text-gray-800 font-bold'
                    : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                Afhalen
            </button>

            <button type="submit" name="delivery_method" value="bezorgen"
                    class="font-serif text-xl px-8 py-3 rounded-3xl border-b-4 transition
                {{ (request('delivery_method') === 'bezorgen')
                    ? 'border-gray-700 text-gray-800 font-bold'
                    : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                Bezorgen
            </button>
        </form>

        {{-- Postcode check formulier alleen tonen bij bezorgen --}}
        @if(request('delivery_method') === 'bezorgen')
            <div class="max-w-md mx-auto mb-12 p-6 bg-white rounded-3xl shadow-md">
                <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                    <input type="hidden" name="delivery_method" value="bezorgen">

                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                        <div class="flex-1">
                            <label for="postcode" class="block mb-1 font-semibold text-gray-700">Postcode</label>
                            <input id="postcode" name="postcode" required
                                   value="{{ request('postcode') }}"
                                   class="w-full border border-gray-300 rounded-xl p-3 text-gray-900" />
                        </div>

                        <div class="flex-1">
                            <label for="housenumber" class="block mb-1 font-semibold text-gray-700">Huisnummer</label>
                            <input id="housenumber" name="housenumber" required
                                   value="{{ request('housenumber') }}"
                                   class="w-full border border-gray-300 rounded-xl p-3 text-gray-900" />
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold rounded-full py-3 shadow transition">
                        Controleer postcode
                    </button>
                </form>
            </div>
        @endif

        {{-- Producten overzicht --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($products as $product)
                @php
                    $deliveryMethod = request('delivery_method', 'afhalen');
                    $availableStock = ($deliveryMethod === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;
                @endphp

                <div class="bg-white rounded-3xl shadow-lg p-6 flex flex-col justify-between hover:shadow-xl transition">
                    <h3 class="font-serif text-2xl text-gray-800 mb-3">{{ $product->name }}</h3>
                    <p class="text-gray-700 font-semibold mb-4 text-lg">€{{ number_format($product->price, 2, ',', '.') }}</p>

                    @if($availableStock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-auto space-y-4">
                            @csrf
                            <input type="hidden" name="type" value="{{ $deliveryMethod }}">
                            <label for="quantity_{{ $product->id }}" class="block text-gray-800 font-semibold mb-1">Aantal:</label>
                            <input type="number"
                                   id="quantity_{{ $product->id }}"
                                   name="quantity"
                                   value="1"
                                   min="1"
                                   max="{{ $availableStock }}"
                                   class="w-full text-center p-3 border border-gray-300 rounded-xl font-semibold text-gray-800"
                                   required
                            >
                            <button type="submit"
                                    class="w-full bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold rounded-full py-3 shadow transition">
                                In winkelwagen
                            </button>
                        </form>
                    @else
                        <p class="text-red-600 font-semibold mt-4 text-center">
                            Niet beschikbaar voor {{ $deliveryMethod === 'afhalen' ? 'afhalen' : 'bezorgen' }}.
                        </p>
                    @endif
                </div>
            @empty
                <p class="col-span-full text-center text-gray-400 italic text-lg">Geen producten gevonden.</p>
            @endforelse
        </div>

        {{-- Paginering --}}
        <div class="mt-10 flex justify-center">
            {{ $products->withQueryString()->links() }}
        </div>
    </section>
</x-app-layout>
