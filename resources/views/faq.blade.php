<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-3xl font-semibold text-gray-900 leading-tight tracking-tight">
            Veelgestelde vragen
        </h1>
    </x-slot>

    <section class="max-w-4xl mx-auto px-6 sm:px-12 lg:px-8 py-12">

        <div class="text-center mb-12">
            <h2 class="font-serif text-4xl font-semibold text-gray-900 mb-4">
                Veelgestelde vragen
            </h2>

            <p class="text-gray-600 text-lg">
                Hier vind je de antwoorden op de meest gestelde vragen.
            </p>
        </div>

        @if($faqs->isEmpty())
            <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-sm">
                <p class="text-gray-500">
                    Er zijn momenteel geen veelgestelde vragen beschikbaar.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($faqs as $faq)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                        <button
                            type="button"
                            onclick="toggleFaq({{ $faq->id }})"
                            class="w-full flex items-center justify-between gap-6 px-6 py-5 text-left hover:bg-gray-50 transition"
                        >
                        <span class="font-semibold text-gray-900 text-lg">
                            {{ $faq->question }}
                        </span>

                            <svg
                                id="faq-icon-{{ $faq->id }}"
                                class="w-5 h-5 text-gray-500 flex-shrink-0 transition-transform duration-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <div
                            id="faq-answer-{{ $faq->id }}"
                            class="hidden px-6 pb-6"
                        >
                            <div class="faq-answer text-gray-700 leading-7
                            [&_a]:text-gray-900
                            [&_a]:underline
                            [&_a]:font-semibold
                            [&_a]:hover:text-gray-600
                            [&_a]:transition">
                                {!! $faq->answer !!}
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-center">
            <p class="text-gray-600 mb-4">
                Staat je vraag er niet tussen?
            </p>

            <a
                href="https://wa.me/31644042554"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center bg-gray-900 hover:bg-gray-800 text-white font-semibold px-7 py-3 rounded-full shadow-md transition"
            >
                Neem contact met ons op via WhatsApp
            </a>
        </div>

    </section>

    <script>
        function toggleFaq(id) {
            const answer = document.getElementById('faq-answer-' + id);
            const icon = document.getElementById('faq-icon-' + id);

            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                answer.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>

</x-app-layout>
