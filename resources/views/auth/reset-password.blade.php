<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f7f6f4] px-6 py-16">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full p-10">
            <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6 text-center">
                Wachtwoord opnieuw instellen
            </h1>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('E-mailadres')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email"
                                  name="email" :value="old('email', $request->email)" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Nieuw wachtwoord')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password"
                                  name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <x-input-label for="password_confirmation" :value="__('Bevestig wachtwoord')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                  type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-full shadow transition">
                        Wachtwoord resetten
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
