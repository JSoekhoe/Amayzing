@php
    $product = $product ?? null;
@endphp

<div class="mb-4">
    <label for="name" class="block font-semibold mb-1">Naam</label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $product->name ?? '') }}"
        class="w-full border p-2 rounded"
        required>
    @error('name')
    <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block font-semibold mb-1">Omschrijving (steekwoorden, één per regel)</label>
    <textarea
        name="description"
        id="description"
        rows="4"
        class="w-full border p-2 rounded"
    >{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
    <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>


<div class="mb-4">
    <label for="price" class="block font-semibold mb-1">Prijs (€)</label>
    <input
        type="number"
        name="price"
        id="price"
        step="0.01"
        min="0"
        value="{{ old('price', $product->price ?? '') }}"
        class="w-full border p-2 rounded"
        required>
    @error('price')
    <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="pickup_stock" class="block font-semibold mb-1">Voorraad (Afhalen)</label>
    <input
        type="number"
        name="pickup_stock"
        id="pickup_stock"
        min="0"
        value="{{ old('pickup_stock', $product->pickup_stock ?? 0) }}"
        class="w-full border p-2 rounded"
        required>
    @error('pickup_stock')
    <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="delivery_stock" class="block font-semibold mb-1">Voorraad (Bezorgen)</label>
    <input
        type="number"
        name="delivery_stock"
        id="delivery_stock"
        min="0"
        value="{{ old('delivery_stock', $product->delivery_stock ?? 0) }}"
        class="w-full border p-2 rounded"
        required>
    @error('delivery_stock')
    <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="is_active" class="inline-flex items-center">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            class="mr-2"
            value="1"
            {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
        Actief
    </label>
    @error('is_active')
    <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>
