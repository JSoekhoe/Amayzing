<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-6 py-16">
        <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-10 text-center border border-gray-200">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-4">Bedankt voor je bestelling!</h1>
            <p class="text-lg text-gray-600">
                Je betaling is ontvangen. We hebben je bestelling verwerkt en je ontvangt een bevestiging per e-mail.
            </p>

            <a href="{{ route('home') }}"
               class="mt-8 inline-block bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-full shadow-sm transition">
                Terug naar de homepage
            </a>
        </div>
    </div>
</x-app-layout>
