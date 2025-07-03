<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f7f6f4] px-6 py-16">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full p-10">
            <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6 text-center">
                Inloggen
            </h1>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-green-600 text-sm text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('E-mailadres')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                  :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Wachtwoord')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password"
                                  name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <label for="remember_me" class="inline-flex items-center text-sm text-gray-600">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-400"
                               name="remember">
                        <span class="ml-2">Onthoud mij</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-500 hover:text-gray-700 transition" href="{{ route('password.request') }}">
                            Wachtwoord vergeten?
                        </a>
                    @endif

                    <button type="submit"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-full shadow transition">
                        Inloggen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
