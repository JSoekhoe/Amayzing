<x-app-layout>
    {{-- ================= HERO ================= --}}
    <section class="relative h-screen">

        <img src="{{ asset('images/home.jpeg') }}"
             class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/35"></div>

        <div class="relative z-10 flex items-center justify-center h-full">

            <div class="text-center text-white">

                <h1 class="text-6xl font-light tracking-[8px] uppercase">
                    Jamay Tuller Patisserie
                </h1>

                <p class="mt-10 text-l max-w-2xl mx-auto leading-8">
                    Een plek waar ik mijn passie voor luxe patisserie
                    volledig tot leven breng.
                </p>

            </div>

        </div>

    </section>

    {{-- ================= VAN DROOM ================= --}}
    <section class="bg-white py-28">

        <div class="max-w-7xl mx-auto px-8">

            <div class="max-w-4xl mx-auto text-center">

                <h2 class="text-5xl font-light uppercase tracking-[5px] mb-10">
                    Van droom naar werkelijkheid
                </h2>

                <p class="text-gray-700 leading-9 text-lg mb-8">

                    Mijn liefde voor bakken begon toen ik veertien jaar
                    was en mijn eerste bijbaan in een bakkerij vond.
                    Na tien jaar ervaring in luxe hotels in Amsterdam
                    en een inspirerende tijd in Dubai, ben ik klaar
                    om die passie met jullie te delen.

                </p>

                <p class="text-gray-700 leading-9 text-lg mb-16">

                    Bij Jamay Tuller Patisserie draait het om
                    exclusieve, luxe creaties die je verwachtingen
                    overtreffen. Elke pastry maak ik met zorg,
                    vakmanschap en oog voor detail.

                </p>

            </div>


            {{-- Foto's --}}
            <div class="grid md:grid-cols-3 gap-8">

                <img src="{{ asset('images/about-1.jpg') }}"
                     class="rounded-lg shadow-xl w-full h-96 object-cover"
                     alt="Jamay Tuller Patisserie">

                <img src="{{ asset('images/about-2.jpg') }}"
                     class="rounded-lg shadow-xl w-full h-96 object-cover"
                     alt="Luxe patisserie">

                <img src="{{ asset('images/about-3.jpg') }}"
                     class="rounded-lg shadow-xl w-full h-96 object-cover"
                     alt="Patisserie creaties">

            </div>

        </div>

    </section>
    {{-- ================= PATISSERIE VOOR IEDER MOMENT ================= --}}
    <section class="py-28 bg-[#faf8f5]">

        <div class="max-w-7xl mx-auto px-8">

            <div class="text-center mb-20">
                <h2 class="text-5xl font-light uppercase tracking-[5px] mb-5">
                    Patisserie voor ieder moment
                </h2>

                <div class="w-32 h-px bg-gray-300 mx-auto"></div>
            </div>

            <div class="grid lg:grid-cols-3 gap-12 text-center">

                {{-- Bezorging --}}
                <div class="bg-[#fdfd96] rounded-xl shadow-lg p-10">
                    <h3 class="uppercase tracking-[3px] text-2xl mb-6">
                        Bezorging
                    </h3>

                    <p class="text-gray-600 leading-8">
                        Waar je ook woont, wij bezorgen onze verse desserts graag
                        bij jou thuis. Onze bezorgplanning wisselt wekelijks per
                        regio. Volg ons op social media om te zien wanneer wij bij
                        jou in de buurt bezorgen.
                    </p>

                    <div class="my-8"></div>

                    <h4 class="uppercase tracking-[2px] font-medium mb-4">
                        Eenvoudig bestellen
                    </h4>

                    <p class="text-gray-600 leading-8">
                        Alle bestellingen voor bezorging plaats je gemakkelijk via
                        deze website. Geef je adres op, kies je producten en wij
                        zorgen dat je bestelling vers bij je aankomt.
                    </p>
                </div>

                {{-- Afhalen --}}
                <div class="bg-[#fdfd96] rounded-xl shadow-lg p-10">
                    <h3 class="uppercase tracking-[3px] text-2xl mb-6">
                        Afhalen
                    </h3>

                    <h4 class="uppercase tracking-[2px] font-medium mb-4">
                        Afhalen in onze bakkerij
                    </h4>

                    <p class="text-gray-600 leading-8 mb-8">
                        Liever zelf ophalen? In Wormerveer kun je jouw bestelling
                        afhalen bij Industieweg 19, van woensdag tot en met zondag tussen
                        10:00 en 14:30 uur.
                    </p>

                    <div class="my-8"></div>

                    <h4 class="uppercase tracking-[2px] font-medium mb-4">
                        Afhalen in bezorgsteden
                    </h4>

                    <p class="text-gray-600 leading-8">
                        Wil je afhalen in één van onze berzorgsteden? Ook dat
                        is mogelijk! Let op: alleen op de dag van de bezorging zelf,
                        tussen 10:00 en 14:00.
                    </p>

                    <p class="text-gray-600 leading-8">
                        App ons op +31 6 44042554 en wij laten je weten waar je
                        jouw bestelling kunt ophalen.
                    </p>
                </div>

                {{-- Evenementen --}}
                <div class="bg-[#fdfd96] rounded-xl shadow-lg p-10">
                    <h3 class="uppercase tracking-[3px] text-2xl mb-6">
                        Evenementen
                    </h3>

                    <p class="text-gray-600 leading-8 mb-8">
                        Verjaardag, bruiloft, babyshower of een ander bijzonder
                        moment? Wij maken het extra feestelijk met heerlijke,
                        verse patisserie.
                    </p>

                    <div class="my-8"></div>

                    <p class="text-gray-600 leading-8">
                        Bekijk ons assortiment of vraag een creatie op maat aan.
                        Van kleur en smaak tot thema: alles wordt volledig naar
                        jouw wensen gemaakt.
                    </p>
                </div>

            </div>

        </div>

    </section>
    {{-- ================= CONTACT ================= --}}
    <section class="py-28 bg-white">

        <div class="max-w-4xl mx-auto px-8 text-center">

            <h2 class="text-5xl font-light uppercase tracking-[5px] mb-10">
                Contact
            </h2>

            <p class="text-gray-600 text-lg leading-9 mb-10">
                Heb je vragen over onze producten, de bezorging of
                afhaalmogelijkheden? Of wil je direct een bestelling
                plaatsen voor een verjaardag, bruiloft of een ander
                bijzonder moment? Neem dan gerust contact met ons op.
            </p>

            <div class="space-y-4 text-lg">

                <p>
                    <span class="font-semibold">Telefoon</span><br>
                    <a href="tel:+31644042554"
                       class="hover:underline">
                        +31 6 44042554
                    </a>
                </p>

                <p>
                    <span class="font-semibold">E-mail</span><br>
                    <a href="mailto:amayzingpastry@gmail.com"
                       class="hover:underline">
                        amayzingpastry@gmail.com
                    </a>
                </p>

            </div>

        </div>

    </section>

</x-app-layout>
