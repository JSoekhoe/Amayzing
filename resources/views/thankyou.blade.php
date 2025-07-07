<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-6 py-16">
        <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-10 text-center border border-gray-200">
            @if($status === 'paid')
                <h1 class="text-4xl font-extrabold text-gray-800 mb-4">Bedankt voor je bestelling!</h1>
                <p class="text-lg text-gray-600">
                    {{ $message ?? 'Je betaling is succesvol verwerkt. We gaan meteen voor je aan de slag!' }}
                </p>
            @elseif($status === 'failed')
                <h1 class="text-4xl font-extrabold text-gray-800 mb-4">Betaling mislukt</h1>
                <p class="text-lg text-gray-600">
                    {{ $message ?? 'Er ging iets mis bij het verwerken van je betaling. Probeer het opnieuw.' }}
                </p>
            @elseif($status === 'cancelled')
                <h1 class="text-4xl font-extrabold text-gray-600 mb-4">Betaling geannuleerd</h1>
                <p class="text-lg text-gray-500">
                    {{ $message ?? 'Je hebt de betaling geannuleerd. Je kunt het later opnieuw proberen.' }}
                </p>
            @elseif($status === 'expired')
                <h1 class="text-4xl font-extrabold text-gray-800 mb-4">Betaling verlopen</h1>
                <p class="text-lg text-gray-600">
                    {{ $message ?? 'Je betaling is verlopen. Je kunt een nieuwe bestelling plaatsen.' }}
                </p>
            @else
                <h1 class="text-4xl font-extrabold text-gray-700 mb-4">Onbekende status</h1>
                <p class="text-lg text-gray-600">
                    {{ $message ?? 'Er is iets misgegaan. Neem contact op als dit blijft gebeuren.' }}
                </p>
            @endif

            <a href="{{ route('home') }}"
               class="mt-8 inline-block bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-full shadow-sm transition">
                Terug naar de homepage
            </a>
        </div>
    </div>
</x-app-layout>
