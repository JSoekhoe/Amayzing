<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6">Afrekenen</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto p-8 bg-gray-100 rounded-3xl shadow-lg border border-gray-300">
        {{-- Foutmeldingen --}}
        @if($errors->any())
            <div class="mb-6 p-5 bg-gray-200 text-gray-800 rounded-xl shadow-inner">
                <ul class="list-disc list-inside space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bestelling overzicht --}}
        <div class="mb-8 bg-white p-6 rounded-xl shadow-md border border-gray-300">
            <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Bestelling overzicht</h2>
            @foreach($cart as $productId => $types)
                @foreach($types as $type => $data)
                    @php
                        $product = \App\Models\Product::find($productId);
                        $quantity = $data['quantity'];
                        $subtotal = $product->price * $quantity;
                    @endphp
                    <p class="text-gray-800 font-semibold mb-1">{{ $product->name }} <span class="text-sm lowercase font-normal">({{ ucfirst($type) }})</span> × {{ $quantity }} — <span class="font-normal">€{{ number_format($subtotal, 2, ',', '.') }}</span></p>
                @endforeach
            @endforeach

            <p class="mt-4 font-semibold text-gray-700">Bezorgkosten: <span class="font-normal">€{{ number_format($deliveryFee, 2, ',', '.') }}</span></p>
            <p class="text-xl font-serif font-bold mt-1 text-gray-800">Totaal te betalen: <span class="font-normal">€{{ number_format($grandTotal, 2, ',', '.') }}</span></p>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-8 bg-white p-8 rounded-xl shadow-md border border-gray-300">
            @csrf
            <input type="hidden" name="type" value="{{ $deliveryMethod }}">

            <div>
                <label for="name" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">Naam</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="Jouw naam"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                >
            </div>

            <div>
                <label for="email" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="email@voorbeeld.nl"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                >
            </div>

            <div>
                <label for="phone" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">Telefoonnummer</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    placeholder="+31 6 12345678"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                >
            </div>

            @if($deliveryMethod === 'bezorgen')
                <div>
                    <label for="address" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">Bezorgadres</label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="{{ old('address') }}"
                        required
                        placeholder="Straatnaam 123"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                    >
                </div>

                <div>
                    <label for="postcode" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">Postcode</label>
                    <input
                        type="text"
                        id="postcode"
                        name="postcode"
                        value="{{ old('postcode') }}"
                        required
                        placeholder="1234 AB"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                    >
                </div>
            @endif

            @if($deliveryMethod === 'afhalen')
                <div>
                    <label for="pickup_time" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">Afhaaltijd</label>
                    <input
                        type="time"
                        id="pickup_time"
                        name="pickup_time"
                        value="{{ old('pickup_time') }}"
                        required
                        min="{{ $minPickupTime }}"
                        max="21:00"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                    >
                    <small class="text-gray-500 italic">Afhalen kan vanaf {{ $minPickupTime }} tot 21:00 uur</small>
                </div>
            @endif

            <button
                type="submit"
                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-serif font-semibold py-3 rounded-2xl shadow-lg transition"
            >
                Bestelling plaatsen
            </button>
        </form>
    </div>
</x-app-layout>
