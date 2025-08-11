<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-semibold text-gray-900 leading-tight">
            Producten
        </h2>
    </x-slot>

    <section class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Afhalen / Bezorgen toggle --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex justify-center space-x-10 border-b-4 border-gray-400 pb-4 mb-6">
            <button type="submit" name="delivery_method" value="afhalen"
                    class="font-serif text-xl px-8 py-3 rounded-3xl border-b-4 transition
                    {{ ($deliveryMethod === 'afhalen' || !$deliveryMethod)
                        ? 'border-gray-900 text-gray-900 font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Afhalen
            </button>

            <button type="submit" name="delivery_method" value="bezorgen"
                    class="font-serif text-xl px-8 py-3 rounded-3xl border-b-4 transition
                    {{ ($deliveryMethod === 'bezorgen')
                        ? 'border-gray-900 text-gray-900 font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Bezorgen
            </button>
        </form>
        {{--melding voor winkelwagen toevoeging--}}
        @if(session('success'))
            <div class="max-w-7xl mx-auto mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        {{-- Afhaalinformatie --}}
        @if ($deliveryMethod === 'afhalen' || !$deliveryMethod)
            <div class="max-w-6xl mx-auto mb-6 bg-gray-100 rounded-3xl shadow-sm p-6 text-gray-900">
                <h3 class="font-serif text-xl font-semibold mb-4">Afhaalinformatie</h3>

                <p class="mb-4">{{ $pickupMessage }}</p>

                <ul class="space-y-6">
                    @foreach ($pickupLocations as $location)
                        <li>
                            <h4 class="font-semibold text-lg">{{ $location['name'] }}</h4>
                            <p class="mb-2 whitespace-pre-line text-gray-800">{!! $location['days'] !!}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bezorginformatie en postcodecheck --}}
        @if ($deliveryMethod === 'bezorgen')
            <div class="flex flex-col lg:flex-row max-w-6xl mx-auto gap-6 mb-6">

            {{-- Linker kader: Bezorginformatie --}}
                <aside class="w-full lg:w-2/3 bg-gray-100 rounded-3xl shadow-sm p-6 text-gray-900">
                <h3 class="font-serif text-xl font-semibold mb-4">Bezorginformatie</h3>

                    {{-- Leverschema’s voor huidige week en volgende week --}}
                    @if (!empty($scheduleThisWeek) || !empty($scheduleNextWeek))
                        <div class="space-y-6 text-gray-800">

                            {{-- Huidige week --}}
                            @if (!empty($scheduleThisWeek))
                                <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-sm">
                                    <h4 class="font-serif text-lg font-semibold mb-2">
                                        Leverdagen – week {{ $weekNow }}
                                    </h4>
                                    <ul class="space-y-1">
                                        @foreach($scheduleThisWeek as $item)
                                            <li>
                                                {{ $item['day'] }} ({{ $item['date'] }}): {{ $item['city'] }} – {{ $item['time'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Volgende week --}}
                            @if (!empty($scheduleNextWeek))
                                <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-sm">
                                    <h4 class="font-serif text-lg font-semibold mb-2">
                                        Leverdagen – week {{ $weekNext }}
                                    </h4>
                                    <ul class="space-y-1">
                                        @foreach($scheduleNextWeek as $item)
                                            <li>
                                                {{ $item['day'] }} ({{ $item['date'] }}): {{ $item['city'] }} – {{ $item['time'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>
                    @endif

                    <p class="mb-3 text-gray-800">
                        We bezorgen binnen een straal van <strong>{{ $radiusKm }} km</strong> vanaf het centrum van elke stad.
                        Bestellen kan tot <strong>{{ $orderCutoff }}</strong> de avond voor de bezorging.
                        Bezorging vindt plaats <strong>{{ $deliveryStartWeekday }}</strong> op weekdagen, en <strong>{{ $deliveryStartWeekend }}</strong> in het weekend.
                        Bezorging loopt tot uiterlijk <strong>{{ $deliveryEnd }}</strong>.
                        Val je buiten de bezorgstraal of ben je te laat met bestellen? Bel dan naar <strong>06 44042554</strong> om te kijken of je je desserts alsnog kunt afhalen bij een afhaalpunt!
                    </p>
                </aside>

                {{-- Rechter kader: Postcode check formulier --}}
                <section class="w-full lg:w-1/3 bg-gray-100 rounded-3xl shadow-sm p-4 text-gray-900">
                <form method="GET" action="{{ route('products.index') }}" class="space-y-4" novalidate>
                        <input type="hidden" name="delivery_method" value="bezorgen">

                        <div class="flex flex-col space-y-3">
                            <div>
                                <label for="postcode" class="block mb-1 font-semibold">Postcode</label>
                                <input id="postcode" name="postcode" required
                                       value="{{ $postcode ?? '' }}"
                                       pattern="^[1-9][0-9]{3}\s?[a-zA-Z]{2}$"
                                       title="Voer een geldige Nederlandse postcode in (bijv. 1234 AB)"
                                       class="w-full border border-gray-400 rounded-xl p-2" />
                            </div>

                            <div>
                                <label for="housenumber" class="block mb-1 font-semibold">Huisnummer</label>
                                <input id="housenumber" name="housenumber" required
                                       value="{{ $housenumber ?? '' }}"
                                       pattern="^[0-9]+$"
                                       title="Voer een geldig huisnummer in"
                                       class="w-full border border-gray-400 rounded-xl p-2" />
                            </div>

                            <div>
                                <label for="addition" class="block mb-1 font-semibold">Toevoeging (optioneel)</label>
                                <input id="addition" name="addition"
                                       value="{{ $addition ?? '' }}"
                                       pattern="^[a-zA-Z0-9\s\-]*$"
                                       title="Voer een geldige toevoeging in (letters, cijfers, spaties, streepje)"
                                       class="w-full border border-gray-400 rounded-xl p-2" />
                            </div>

                            <div>
                                <label for="straatnaam" class="block mb-1 font-semibold">Straatnaam</label>
                                <input id="straatnaam" name="straatnaam"
                                       value="{{ old('straatnaam', $straatnaam ?? '') }}"
                                       class="w-full border border-gray-400 rounded-xl p-2"
                                       placeholder="Verplicht bij Belgische postcode"
                                       readonly />
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded-full py-2 shadow">
                            Controleer postcode
                        </button>
                    </form>

                    @if (!empty($deliveryMessage))
                        <div class="mt-4 text-center text-sm {{ $deliveryAllowed ? 'text-green-700' : 'text-red-700' }}">
                            {!! $deliveryMessage !!}
                        </div>
                    @endif
                </section>

            </div>
        @endif

        {{-- Producten overzicht --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($products as $product)
                @php
                    $availableStock = ($deliveryMethod === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;
                @endphp

                <article class="bg-white rounded-3xl shadow-lg p-6 flex flex-col justify-between hover:shadow-xl transition">
                    {{-- Productfoto --}}
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                              alt="{{ $product->name }}"
                              class="rounded-xl mb-4 max-h-36 w-full object-contain bg-white">
                    @else
                        <img src="{{ asset('images/placeholder.jpg') }}"
                             alt="Geen afbeelding beschikbaar"
                             class="rounded-xl mb-4 max-h-36 w-full object-cover">
                    @endif

                    {{-- Naam en prijs --}}
                    <h3 class="font-serif text-2xl text-gray-900 mb-3">{{ $product->name }}</h3>
                    <p class="text-gray-800 font-semibold mb-4 text-lg">€{{ number_format($product->price, 2, ',', '.') }}</p>

                    {{-- Omschrijving --}}
                    @if(!empty($product->description))
                        <p class="text-gray-700 mb-4">{{ $product->description }}</p>
                    @endif

                    {{-- Voorraad en winkelwagen --}}
                    @if ($availableStock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-auto space-y-4">
                            @csrf
                            <input type="hidden" name="type" value="{{ $deliveryMethod }}">
                            <input type="hidden" name="postcode" value="{{ $postcode ?? '' }}">
                            <input type="hidden" name="housenumber" value="{{ $housenumber ?? '' }}">
                            <input type="hidden" name="addition" value="{{ $addition ?? '' }}">

                            <label for="quantity_{{ $product->id }}" class="block text-gray-800 font-semibold mb-1">Aantal:</label>
                            <input type="number"
                                   id="quantity_{{ $product->id }}"
                                   name="quantity"
                                   value="1"
                                   min="1"
                                   max="{{ $availableStock }}"
                                   class="w-full text-center p-3 border border-gray-400 rounded-xl font-semibold text-gray-900"
                                   required
                            >

                            <button type="submit"
                                    class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded-full py-3 shadow">
                                In winkelwagen
                            </button>
                        </form>
                    @else
                        <p class="text-red-600 font-semibold mt-4 text-center">
                            Niet beschikbaar voor {{ $deliveryMethod === 'afhalen' ? 'afhalen' : 'bezorgen' }}.
                        </p>
                    @endif
                </article>
            @empty
                <p class="col-span-full text-center text-gray-500 italic text-lg">Geen producten gevonden.</p>
            @endforelse
        </div>

        {{-- Paginering --}}
        <div class="mt-10 flex justify-center">
            {{ $products->withQueryString()->links() }}
        </div>


        {{-- Paginering --}}
        <div class="mt-10 flex justify-center">
            {{ $products->withQueryString()->links() }}
        </div>
    </section>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const postcodeInput = document.querySelector('input[name="postcode"]');
        const straatnaamInput = document.querySelector('input[name="straatnaam"]');

        postcodeInput.addEventListener('input', function () {
            const postcode = postcodeInput.value.trim();

            // Belgische postcode: 4 cijfers, geen letters
            const isBelgianPostcode = /^[1-9][0-9]{3}$/.test(postcode);

            if (isBelgianPostcode) {
                straatnaamInput.readOnly = false;
                straatnaamInput.placeholder = "Verplicht bij Belgische postcode";
                straatnaamInput.required = true;
            } else {
                straatnaamInput.readOnly = true;
                straatnaamInput.placeholder = "Niet vereist bij Nederlandse postcode";
                straatnaamInput.required = false;
                straatnaamInput.value = ''; // evt. leegmaken
            }

        });

        // Optioneel: trigger bij laden om gelijk goede status te zetten
        postcodeInput.dispatchEvent(new Event('input'));
    });
</script>

