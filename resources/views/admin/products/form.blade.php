@php
    $product = $product ?? null;
@endphp

<div class="mb-6">
    <label for="name" class="block text-gray-800 font-semibold mb-2">Naam</label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $product->name ?? '') }}"
        class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"
        required>
    @error('name')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label for="description" class="block text-gray-800 font-semibold mb-2">Omschrijving (steekwoorden, één per regel)</label>
    <textarea
        name="description"
        id="description"
        rows="5"
        class="w-full border border-gray-300 rounded-md p-3 resize-y focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"
    >{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <label for="price" class="block text-gray-800 font-semibold mb-2">Prijs (€)</label>
        <input
            type="number"
            name="price"
            id="price"
            step="0.01"
            min="0"
            value="{{ old('price', $product->price ?? '') }}"
            class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"
            required>
        @error('price')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="pickup_stock" class="block text-gray-800 font-semibold mb-2">Voorraad (Afhalen)</label>
        <input
            type="number"
            name="pickup_stock"
            id="pickup_stock"
            min="0"
            value="{{ old('pickup_stock', $product->pickup_stock ?? 0) }}"
            class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"
            required>
        @error('pickup_stock')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="delivery_stock" class="block text-gray-800 font-semibold mb-2">Voorraad (Bezorgen)</label>
        <input
            type="number"
            name="delivery_stock"
            id="delivery_stock"
            min="0"
            value="{{ old('delivery_stock', $product->delivery_stock ?? 0) }}"
            class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"
            required>
        @error('delivery_stock')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label for="image" class="block text-gray-800 font-semibold mb-2">Productfoto</label>
    <input
        type="file"
        name="image"
        id="image"
        accept="image/*"
        class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"
    >
    @error('image')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if(isset($product) && $product->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="Productfoto" class="mt-4 max-h-48 rounded">
    @endif
</div>
