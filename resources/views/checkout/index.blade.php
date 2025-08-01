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
                        $product = $products->get($productId);
                        $quantity = $data['quantity'];
                        $subtotal = $product->price * $quantity;
                    @endphp
                    <p class="text-gray-800 font-semibold mb-1">
                        {{ $product->name }}
                        <span class="text-sm lowercase font-normal">({{ ucfirst($type) }})</span> × {{ $quantity }} —
                        <span class="font-normal">€{{ number_format($subtotal, 2, ',', '.') }}</span>
                    </p>
                @endforeach
            @endforeach

            <p class="mt-4 font-semibold text-gray-700">
                Bezorgkosten: <span class="font-normal">€{{ number_format($deliveryFee, 2, ',', '.') }}</span>
            </p>
            <p class="text-xl font-serif font-bold mt-1 text-gray-800">
                Totaal te betalen: <span class="font-normal">€{{ number_format($grandTotal, 2, ',', '.') }}</span>
            </p>
        </div>

        @php
            $currentDeliveryMethod = old('type', $deliveryMethod);
        @endphp

        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-8 bg-white p-8 rounded-xl shadow-md border border-gray-300">
            @csrf
            <input type="hidden" name="type" value="{{ $currentDeliveryMethod }}">

            {{-- Algemene klantgegevens --}}
            @foreach([
                ['name' => 'name', 'type' => 'text', 'label' => 'Naam', 'placeholder' => 'Jouw naam'],
                ['name' => 'email', 'type' => 'email', 'label' => 'E-mail', 'placeholder' => 'email@voorbeeld.nl'],
                ['name' => 'phone', 'type' => 'tel', 'label' => 'Telefoonnummer', 'placeholder' => '+31 6 12345678'],
            ] as $field)
                <div>
                    <label for="{{ $field['name'] }}" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">
                        {{ $field['label'] }}
                    </label>
                    <input
                        type="{{ $field['type'] }}"
                        id="{{ $field['name'] }}"
                        name="{{ $field['name'] }}"
                        value="{{ old($field['name']) }}"
                        required
                        placeholder="{{ $field['placeholder'] }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition @error($field['name']) border-red-500 @enderror"
                    >
                    @error($field['name'])
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            {{-- Bezorgadres --}}
            @if($currentDeliveryMethod === 'bezorgen')
                @php $isReadonly = true; @endphp

                @foreach([
                    ['name' => 'straat', 'label' => 'Straat', 'placeholder' => 'Straatnaam', 'value' => old('straat', $straat ?? '')],
                    ['name' => 'housenumber', 'label' => 'Huisnummer', 'placeholder' => '123', 'value' => old('housenumber', $housenumber ?? '')],
                    ['name' => 'addition', 'label' => 'Toevoeging', 'placeholder' => 'Bijv. A, Bus', 'value' => old('addition', $addition ?? '')],
                    ['name' => 'postcode', 'label' => 'Postcode', 'placeholder' => '1234 AB', 'value' => old('postcode', $postcode ?? '')],
                ] as $field)
                    <div>
                        <label for="{{ $field['name'] }}" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">
                            {{ $field['label'] }}
                        </label>
                        <input
                            type="text"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            value="{{ $field['value'] }}"
                            {{ $field['name'] !== 'addition' ? 'required' : '' }}
                            placeholder="{{ $field['placeholder'] }}"
                            @if($isReadonly) readonly @endif
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-100 text-gray-700 focus:outline-none focus:ring-0 focus:border-gray-300 transition @error($field['name']) border-red-500 @enderror"
                        >
                        @error($field['name'])
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            @endif
            @if($deliveryMethod === 'bezorgen' && !empty($availableDeliveryDates))
                <div>
                    <label for="delivery_date" class="block mb-2 font-serif font-semibold text-gray-700 text-lg">Bezorgdatum</label>
                    <select
                        name="delivery_date"
                        id="delivery_date"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition @error('delivery_date') border-red-500 @enderror"
                    >
                        <option value="" disabled {{ old('delivery_date') ? '' : 'selected' }}>Kies een datum</option>
                        @foreach($availableDeliveryDates as $date)
                            <option value="{{ $date['iso'] }}" {{ old('delivery_date') == $date['iso'] ? 'selected' : '' }}>
                                {{ $date['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('delivery_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Afhalen --}}
            @if($currentDeliveryMethod === 'afhalen')
                <div class="space-y-4">
                    <div>
                        <label for="pickup_location" class="block mb-1 font-semibold">Afhaallocatie</label>
                        <select
                            name="pickup_location"
                            id="pickup_location"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition @error('pickup_location') border-red-500 @enderror"
                        >
                            @foreach($pickupLocations as $key => $name)
                                <option value="{{ $key }}" @if(old('pickup_location', $selectedPickupLocation) === $key) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('pickup_location')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pickup_date" class="block mb-1 font-semibold">Afhaal datum</label>
                        <select
                            name="pickup_date"
                            id="pickup_date"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition @error('pickup_date') border-red-500 @enderror"
                        >
                            @foreach ($availablePickupDatesFormatted as $value => $label)
                                <option value="{{ $value }}" {{ old('pickup_date', $availablePickupDates) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('pickup_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pickup_time" class="block mb-1 font-semibold">Afhaaltijd</label>
                        <select
                            name="pickup_time"
                            id="pickup_time"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition @error('pickup_time') border-red-500 @enderror"
                        >
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot }}" @if(old('pickup_time') === $slot) selected @endif>{{ $slot }}</option>
                            @endforeach
                        </select>
                        @error('pickup_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            <button
                type="submit"
                class="bg-gray-900 w-full mt-6 py-3 rounded-lg text-white text-lg font-semibold tracking-wide hover:bg-gray-800 transition"
            >
                Plaats bestelling
            </button>
        </form>
    </div>
</x-app-layout>
