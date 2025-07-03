<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f7f6f4] px-6 py-16">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full p-10">
            <h1 class="text-3xl font-serif font-bold text-[#386641] mb-6 text-center">
                Bevestig je wachtwoord
            </h1>

            <p class="text-sm text-gray-600 text-center mb-6">
                Dit is een beveiligd gedeelte van de site. Bevestig je wachtwoord om verder te gaan.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-4">
                    <x-input-label for="password" :value="__('Wachtwoord')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password"
                                  name="password"
                                  required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                            class="bg-[#9bd5cb] hover:bg-[#78b9aa] text-[#1a433d] font-semibold py-3 px-6 rounded-full shadow transition">
                        Bevestig
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
