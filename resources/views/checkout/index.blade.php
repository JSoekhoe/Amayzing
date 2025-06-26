@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Bestelling afronden</h1>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="type" class="block font-semibold mb-1">Bezorging of afhalen?</label>
                <select name="type" id="type" required onchange="toggleFields()" class="w-full border p-2 rounded">
                    <option value="">Kies een optie</option>
                    <option value="bezorgen" {{ old('type') == 'bezorgen' ? 'selected' : '' }}>Bezorgen</option>
                    <option value="afhalen" {{ old('type') == 'afhalen' ? 'selected' : '' }}>Afhalen</option>
                </select>
                @error('type') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="name" class="block font-semibold mb-1">Naam</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border p-2 rounded">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block font-semibold mb-1">E-mailadres</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border p-2 rounded">
                @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block font-semibold mb-1">Telefoonnummer</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border p-2 rounded">
                @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div id="bezorgen_fields" class="mb-4 hidden">
                <label for="address" class="block font-semibold mb-1">Adres</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full border p-2 rounded">
                @error('address') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                <label for="postcode" class="block font-semibold mt-4 mb-1">Postcode</label>
                <input type="text" name="postcode" value="{{ old('postcode') }}" class="w-full border p-2 rounded">
                @error('postcode') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div id="afhalen_fields" class="mb-4 hidden">
                <label for="pickup_time" class="block font-semibold mb-1">Verwachte afhaaltijd</label>
                <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" class="w-full border p-2 rounded">
                @error('pickup_time') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Bestelling plaatsen
            </button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('type').value;
            document.getElementById('bezorgen_fields').classList.toggle('hidden', type !== 'bezorgen');
            document.getElementById('afhalen_fields').classList.toggle('hidden', type !== 'afhalen');
        }

        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
@endsection
