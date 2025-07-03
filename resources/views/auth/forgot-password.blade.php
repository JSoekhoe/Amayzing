<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f0f0f0] px-6 py-16">
        <div class="bg-white rounded-3xl shadow max-w-md w-full p-10">
            <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6 text-center">
                Wachtwoord vergeten?
            </h1>

            <p class="text-sm text-gray-600 text-center mb-6">
                Geen probleem. Vul je e-mailadres in en we sturen je een link om je wachtwoord opnieuw in te stellen.
            </p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-green-600 text-sm text-center" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <x-input-label for="email" :value="__('E-mailadres')" class="text-gray-700"/>
                    <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                                  type="email" name="email" :value="old('email')"
                                  required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                            class="bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-full shadow transition">
                        Verstuur resetlink
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
