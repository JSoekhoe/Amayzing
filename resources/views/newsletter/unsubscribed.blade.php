<x-app-layout>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8 text-center">

            <div class="text-5xl mb-5">✉️</div>

            <h1 class="text-2xl font-bold text-gray-900 mb-3">
                Je bent uitgeschreven!
            </h1>

            <p class="text-gray-600 mb-6">
                Je ontvangt geen promotionele nieuwsbrieven meer van
                Jamay Tuller Patisserie.
            </p>

            <a href="{{ route('home') }}"
               class="inline-block px-6 py-3 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                Terug naar de website
            </a>

        </div>
    </div>
</x-app-layout>
