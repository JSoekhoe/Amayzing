<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-3xl font-semibold text-gray-900 leading-tight tracking-tight">
            FAQ bewerken
        </h1>
    </x-slot>

    <section class="max-w-4xl mx-auto px-6 sm:px-12 lg:px-24 py-12 bg-gray-50 rounded-2xl shadow-lg">

        <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Vraag --}}
            <div>
                <label for="question"
                       class="block text-sm font-semibold text-gray-700 mb-2">
                    Vraag
                </label>

                <input
                    type="text"
                    name="question"
                    id="question"
                    value="{{ old('question', $faq->question) }}"
                    required
                    maxlength="255"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-gray-600 focus:ring-gray-600"
                >

                @error('question')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Antwoord --}}
            <div>
                <label for="answer"
                       class="block text-sm font-semibold text-gray-700 mb-2">
                    Antwoord
                </label>

                <textarea
                    name="answer"
                    id="answer"
                    rows="8"
                    required
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-gray-600 focus:ring-gray-600"
                >{{ old('answer', $faq->answer) }}</textarea>

                @error('answer')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Volgorde --}}
            <div>
                <label for="sort_order"
                       class="block text-sm font-semibold text-gray-700 mb-2">
                    Volgorde
                </label>

                <input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    value="{{ old('sort_order', $faq->sort_order) }}"
                    min="0"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-gray-600 focus:ring-gray-600"
                >

                <p class="mt-2 text-sm text-gray-500">
                    Een lager nummer wordt eerder weergegeven.
                </p>

                @error('sort_order')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actief --}}
            <div class="flex items-center">
                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    {{ old('is_active', $faq->is_active) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-600"
                >

                <label for="is_active"
                       class="ml-3 text-sm font-medium text-gray-700">
                    FAQ zichtbaar op de website
                </label>
            </div>

            {{-- Knoppen --}}
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('admin.faqs.index') }}"
                   class="text-gray-600 hover:text-gray-900 font-semibold transition">
                    ← Annuleren
                </a>

                <button
                    type="submit"
                    class="bg-gray-900 hover:bg-gray-800 text-white font-semibold px-8 py-3 rounded-full shadow-md transition">
                    Wijzigingen opslaan
                </button>
            </div>

        </form>
    </section>

</x-app-layout>
