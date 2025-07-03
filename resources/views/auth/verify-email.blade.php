<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f7f6f4] px-6 py-16">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full p-10 text-center">
            <h1 class="text-2xl font-serif font-bold text-gray-800 mb-4">
                Verifieer je e-mailadres
            </h1>

            <p class="text-sm text-gray-600 mb-6">
                Bedankt voor je registratie! Klik op de link in de e-mail die we je gestuurd hebben om je account te activeren.<br>
                Geen mail ontvangen? We sturen je graag opnieuw een link toe.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600">
                    Er is een nieuwe verificatielink verzonden naar het opgegeven e-mailadres.
                </div>
            @endif

            <div class="flex flex-col gap-4 items-center">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-full shadow transition">
                        Stuur opnieuw
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline transition">
                        Uitloggen
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
