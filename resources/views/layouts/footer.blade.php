{{-- ================= FOOTER ================= --}}
<footer class="bg-gray-900 text-white">

    <div class="max-w-7xl mx-auto px-6 md:px-8 py-14 md:py-16">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-16">

            {{-- ================= BEDRIJF ================= --}}
            <div>
                <h3 class="text-xl md:text-2xl font-light uppercase tracking-[3px] mb-5">
                    Jamay Tuller Patisserie
                </h3>

                <p class="text-gray-300 leading-7">
                    Luxe patisserie, met zorg en vakmanschap gemaakt.
                    Ontdek onze creaties voor ieder bijzonder moment.
                </p>
            </div>

            {{-- ================= CONTACT ================= --}}
            <div>
                <h3 class="uppercase tracking-[2px] font-semibold text-sm mb-5">
                    Contact
                </h3>

                <div class="space-y-3 text-gray-300">

                    <a
                        href="tel:+31644042554"
                        class="block hover:text-white hover:underline transition"
                    >
                        +31 6 44042554
                    </a>

                    <a
                        href="mailto:amayzingpastry@gmail.com"
                        class="block hover:text-white hover:underline transition"
                    >
                        amayzingpastry@gmail.com
                    </a>

                </div>
            </div>

            {{-- ================= NAVIGATIE ================= --}}
            <div>
                <h3 class="uppercase tracking-[2px] font-semibold text-sm mb-5">
                    Informatie
                </h3>

                <nav class="space-y-3">

                    <a
                        href="{{ route('products.index') }}"
                        class="block text-gray-300 hover:text-white transition"
                    >
                        Producten & Bestellen
                    </a>

                    <a
                        href="{{ route('faq') }}"
                        class="block text-gray-300 hover:text-white transition"
                    >
                        Veelgestelde vragen
                    </a>

                </nav>
            </div>

        </div>

        {{-- ================= ONDERSTE DEEL ================= --}}
        <div class="border-t border-gray-700 mt-12 pt-6">

            <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                <p class="text-sm text-gray-400 text-center md:text-left">
                    © {{ date('Y') }} Jamay Tuller Patisserie. Alle rechten voorbehouden.
                </p>

                <div class="flex items-center gap-6 text-sm">

                    <a
                        href="{{ route('home') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        Home
                    </a>

                    <a
                        href="{{ route('faq') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        FAQ
                    </a>

                </div>

            </div>

        </div>

    </div>

</footer>
