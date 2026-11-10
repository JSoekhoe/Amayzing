<x-app-layout>

    <section class="bg-[#f4f0e9] min-h-[75vh] flex flex-col items-center px-5 py-12 md:py-16">

        <div class="max-w-3xl mx-auto w-full flex flex-col items-center text-center">

            {{-- INTRODUCTIE --}}
            <div class="mb-9 md:mb-11">

                <p class="uppercase tracking-[4px] text-xs text-[#a18464] mb-5">
                    Jamay Tuller Patisserie
                </p>

                <h1 class="text-4xl md:text-6xl font-light leading-tight text-[#30251f]">
                    Een wereld vol
                    <span class="block italic text-[#a18464]">zoete verrassingen.</span>
                </h1>

                <p class="text-[#88776a] mt-5 max-w-xl mx-auto leading-relaxed">
                    Ontdek onze nieuwste creaties, bijzondere smaken,
                    seizoensspecials en exclusieve aanbiedingen.
                </p>

            </div>

            {{-- FORMULIER --}}
            <div class="bg-white rounded-2xl shadow-lg p-7 sm:p-9 md:p-11 max-w-lg w-full">

                <div class="text-center mb-7">

                    <h2 class="text-2xl font-light text-[#30251f]">
                        Schrijf je in
                    </h2>

                    <p class="text-[#88776a] text-sm mt-2">
                        Laat je inspireren door alles wat onze patisserie te bieden heeft.
                    </p>

                </div>

                {{-- GIVEAWAY --}}
                <div class="relative bg-[#f9f6f1] border border-[#e8ddcf] rounded-xl px-5 py-6 mb-7 text-center">

                    <p class="uppercase tracking-[3px] text-[10px] text-[#a18464] mb-3">
                        Een extra zoete kans
                    </p>

                    <h3 class="text-2xl font-light text-[#30251f]">
                        Een jaar lang
                        <span class="italic text-[#a18464]">genieten.</span>
                    </h3>

                    <p class="text-sm text-[#88776a] leading-relaxed mt-3">
                        Onder onze nieuwsbriefabonnees verloten we een jaar lang
                        iedere maand een doosje desserts ter waarde van €50.
                    </p>

                    <div class="flex items-center justify-center gap-3 mt-5 pt-4 border-t border-[#e8ddcf]">

                        <div>
                            <p class="text-xl font-light text-[#a18464]">12</p>
                            <p class="text-[10px] uppercase tracking-wider text-[#88776a]">Maanden</p>
                        </div>

                        <div class="h-8 w-px bg-[#e0d2c2]"></div>

                        <div>
                            <p class="text-xl font-light text-[#a18464]">€600</p>
                            <p class="text-[10px] uppercase tracking-wider text-[#88776a]">Dessertwaarde</p>
                        </div>

                        <div class="h-8 w-px bg-[#e0d2c2]"></div>

                        <div>
                            <p class="text-xl font-light text-[#a18464]">1</p>
                            <p class="text-[10px] uppercase tracking-wider text-[#88776a]">Winnaar</p>
                        </div>

                    </div>

                    <p class="text-[11px] text-[#a39487] mt-4 leading-relaxed">
                        De doosjes zijn iedere maand af te halen bij Jamay Tuller Patisserie.
                    </p>

                </div>

                @if (session('success'))
                    <div class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-center text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('newsletter.subscribe.store') }}">
                    @csrf

                    {{-- NAAM --}}
                    <div class="mb-5">

                        <label for="name" class="block text-sm text-[#30251f] mb-2 text-left">
                            Naam
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            placeholder="Je naam"
                            required
                            autocomplete="name"
                            class="w-full bg-[#faf8f5] border border-[#e5dbd0] rounded-lg px-4 py-3 text-[#30251f] focus:border-[#a18464] focus:ring-0"
                        >

                        @error('name')
                        <p class="text-red-600 text-sm mt-2 text-left">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- E-MAIL --}}
                    <div class="mb-5">

                        <label for="email" class="block text-sm text-[#30251f] mb-2 text-left">
                            E-mailadres
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="naam@voorbeeld.nl"
                            required
                            autocomplete="email"
                            class="w-full bg-[#faf8f5] border border-[#e5dbd0] rounded-lg px-4 py-3 text-[#30251f] focus:border-[#a18464] focus:ring-0"
                        >

                        @error('email')
                        <p class="text-red-600 text-sm mt-2 text-left">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- TOESTEMMING --}}
                    <div class="mb-6">

                        <label class="flex items-start gap-3 cursor-pointer text-left">

                            <input
                                type="checkbox"
                                name="consent"
                                value="1"
                                required
                                class="mt-1 rounded border-[#d8cabb] text-[#30251f] focus:ring-[#a18464]"
                                {{ old('consent') ? 'checked' : '' }}
                            >

                            <span class="text-xs text-[#88776a] leading-5">
                                Ik geef toestemming om nieuwsbrieven, aanbiedingen
                                en updates per e-mail te ontvangen. Mijn inschrijving
                                betekent automatisch deelname aan de giveaway.
                            </span>

                        </label>

                        @error('consent')
                        <p class="text-red-600 text-sm mt-2 text-left">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- VERZENDKNOP --}}
                    <button
                        type="submit"
                        class="w-full bg-[#30251f] hover:bg-[#49382e] text-white uppercase tracking-[2px] text-xs font-semibold py-4 rounded-lg transition duration-300"
                    >
                        Aanmelden & maak kans
                    </button>

                </form>

                <p class="text-center text-xs text-[#a39487] mt-5">
                    Je kunt je op ieder moment uitschrijven.
                </p>

            </div>

        </div>

    </section>

</x-app-layout>
