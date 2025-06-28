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
    <label for="is_active" class="inline-flex items-center cursor-pointer">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            class="form-checkbox h-5 w-5 text-gray-600"
            value="1"
            {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
        <span class="ml-3 text-gray-800 font-semibold select-none">Actief</span>
    </label>
    @error('is_active')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
