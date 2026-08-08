<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-3xl font-semibold text-gray-900 leading-tight tracking-tight">
            FAQ toevoegen
        </h1>
    </x-slot>

    <section class="max-w-4xl mx-auto px-6 sm:px-12 lg:px-24 py-12 bg-gray-50 rounded-2xl shadow-lg">

        <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-8">
            @csrf

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
                    value="{{ old('question') }}"
                    required
                    maxlength="255"
                    placeholder="Bijvoorbeeld: Hoe kan ik mijn bestelling afhalen?"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-gray-600 focus:ring-gray-600"
                >

                @error('question')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Antwoord --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Antwoord
                </label>

                <div id="answer-editor" class="bg-white"></div>

                <textarea
                    name="answer"
                    id="answer"
                    class="hidden"
                    required
                >{{ old('answer') }}</textarea>

                <p class="mt-2 text-sm text-gray-500">
                    Gebruik de werkbalk om tekst op te maken of links toe te voegen.
                </p>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const editor = new Quill('#answer-editor', {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });

                        const textarea = document.getElementById('answer');

                        editor.root.innerHTML = textarea.value;

                        editor.on('text-change', function () {
                            textarea.value = editor.root.innerHTML;
                        });

                        document.querySelector('form').addEventListener('submit', function () {
                            textarea.value = editor.root.innerHTML;
                        });
                    });
                </script>
            @endpush

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
                    value="{{ old('sort_order', 0) }}"
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
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-600"
                >

                <label for="is_active"
                       class="ml-3 text-sm font-medium text-gray-700">
                    FAQ zichtbaar op de website
                </label>
            </div>

            @error('is_active')
            <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            {{-- Knoppen --}}
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('admin.faqs.index') }}"
                   class="text-gray-600 hover:text-gray-900 font-semibold transition">
                    ← Annuleren
                </a>

                <button
                    type="submit"
                    class="bg-gray-900 hover:bg-gray-800 text-white font-semibold px-8 py-3 rounded-full shadow-md transition">
                    FAQ toevoegen
                </button>
            </div>

        </form>
    </section>

</x-app-layout>
