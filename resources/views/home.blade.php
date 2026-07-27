<x-app-layout>

    {{-- ================= HERO ================= --}}
    <section class="relative min-h-[70vh] md:min-h-screen">

        <img
            src="{{ asset('images/home.jpeg') }}"
            alt="Jamay Tuller Patisserie"
            class="absolute inset-0 w-full h-full object-cover"
        >

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 flex items-center justify-center min-h-[70vh] md:min-h-screen px-6">

            <div class="max-w-4xl text-center text-white">

                <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-light uppercase tracking-[2px] sm:tracking-[5px] md:tracking-[8px] leading-tight">
                    Jamay Tuller Patisserie
                </h1>

                <p class="mt-6 md:mt-10 text-base sm:text-lg md:text-xl leading-7 md:leading-9 max-w-2xl mx-auto">
                    Een plek waar ik mijn passie voor luxe patisserie volledig tot leven breng.
                </p>

            </div>

        </div>

    </section>

    {{-- ================= VAN DROOM ================= --}}
    <section class="bg-white py-16 md:py-28">

        <div class="max-w-7xl mx-auto px-5 md:px-8">

            <div class="max-w-4xl mx-auto text-center">

                <h2 class="text-3xl md:text-5xl font-light uppercase tracking-[3px] md:tracking-[5px] mb-8 md:mb-10">
                    Van droom naar werkelijkheid
                </h2>

                <p class="text-gray-700 text-base md:text-lg leading-8 md:leading-9 mb-8">

                    Mijn liefde voor bakken begon toen ik veertien jaar was en mijn eerste
                    bijbaan in een bakkerij vond. Na tien jaar ervaring in luxe hotels in
                    Amsterdam en een inspirerende tijd in Dubai ben ik klaar om die passie
                    met jullie te delen.

                </p>

                <p class="text-gray-700 text-base md:text-lg leading-8 md:leading-9 mb-12 md:mb-16">

                    Bij Jamay Tuller Patisserie draait het om exclusieve, luxe creaties
                    die je verwachtingen overtreffen. Elke pastry maak ik met zorg,
                    vakmanschap en oog voor detail.

                </p>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">

                <img
                    src="{{ asset('images/about-1.jpeg') }}"
                    alt="Jamay Tuller"
                    class="rounded-xl shadow-xl w-full h-64 sm:h-80 md:h-96 object-cover transition duration-300 hover:scale-[1.02]"
                >

                <img
                    src="{{ asset('images/about-2.jpeg') }}"
                    alt="Luxe patisserie"
                    class="rounded-xl shadow-xl w-full h-64 sm:h-80 md:h-96 object-cover transition duration-300 hover:scale-[1.02]"
                >

                <img
                    src="{{ asset('images/about-3.jpeg') }}"
                    alt="Patisserie creaties"
                    class="rounded-xl shadow-xl w-full h-64 sm:h-80 md:h-96 object-cover transition duration-300 hover:scale-[1.02]"
                >

            </div>

        </div>

    </section>

    {{-- ================= PATISSERIE ================= --}}
    <section class="bg-[#faf8f5] py-16 md:py-28">

        <div class="max-w-7xl mx-auto px-5 md:px-8">

            <div class="text-center mb-12 md:mb-20">

                <h2 class="text-3xl md:text-5xl font-light uppercase tracking-[3px] md:tracking-[5px] mb-5">
                    Patisserie voor ieder moment
                </h2>

                <div class="w-24 md:w-32 h-px bg-gray-300 mx-auto"></div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- ================= BEZORGING ================= --}}
                <div class="bg-[#fdfd96] rounded-2xl shadow-lg p-6 md:p-10 transition duration-300 hover:-translate-y-1 hover:shadow-2xl">

                    <h3 class="uppercase tracking-[2px] text-xl md:text-2xl mb-6">
                        Bezorging
                    </h3>

                    <p class="text-gray-700 text-base leading-7 md:leading-8">
                        Waar je ook woont, wij bezorgen onze verse desserts graag
                        bij jou thuis. Onze bezorgplanning wisselt wekelijks per
                        regio. Volg ons op social media om te zien wanneer wij bij
                        jou in de buurt bezorgen.
                    </p>

                    <div class="my-8 border-t border-gray-300"></div>

                    <h4 class="uppercase tracking-[2px] font-semibold mb-4">
                        Eenvoudig bestellen
                    </h4>

                    <p class="text-gray-700 text-base leading-7 md:leading-8">
                        Alle bestellingen voor bezorging plaats je eenvoudig via
                        deze website. Vul je adres in, kies je favoriete
                        producten en wij zorgen ervoor dat jouw bestelling vers
                        bij je wordt bezorgd.
                    </p>

                </div>

                {{-- ================= AFHALEN ================= --}}
                <div class="bg-[#fdfd96] rounded-2xl shadow-lg p-6 md:p-10 transition duration-300 hover:-translate-y-1 hover:shadow-2xl">

                    <h3 class="uppercase tracking-[2px] text-xl md:text-2xl mb-6">
                        Afhalen
                    </h3>

                    <h4 class="uppercase tracking-[2px] font-semibold mb-4">
                        Afhalen in onze bakkerij
                    </h4>

                    <p class="text-gray-700 text-base leading-7 md:leading-8 mb-8">
                        Liever zelf ophalen? In Wormerveer kun je jouw bestelling
                        afhalen aan de Industrieweg 19, van woensdag tot en met
                        zondag tussen 10:00 en 14:30 uur.
                    </p>

                    <div class="my-8 border-t border-gray-300"></div>

                    <h4 class="uppercase tracking-[2px] font-semibold mb-4">
                        Afhalen in bezorgsteden
                    </h4>

                    <p class="text-gray-700 text-base leading-7 md:leading-8 mb-5">
                        Wil je afhalen in één van onze bezorgsteden? Ook dat is
                        mogelijk. Dit kan uitsluitend op de bezorgdag zelf,
                        tussen 10:00 en 14:00 uur.
                    </p>

                    <p class="text-gray-700 text-base leading-7 md:leading-8">
                        App ons op
                        <a href="tel:+31644042554" class="font-semibold hover:underline">
                            +31 6 44042554
                        </a>
                        en wij laten je weten waar je jouw bestelling kunt
                        ophalen.
                    </p>

                </div>

                {{-- ================= EVENEMENTEN ================= --}}
                <div class="bg-[#fdfd96] rounded-2xl shadow-lg p-6 md:p-10 transition duration-300 hover:-translate-y-1 hover:shadow-2xl">

                    <h3 class="uppercase tracking-[2px] text-xl md:text-2xl mb-6">
                        Evenementen
                    </h3>

                    <p class="text-gray-700 text-base leading-7 md:leading-8 mb-8">
                        Verjaardag, bruiloft, babyshower of een ander bijzonder
                        moment? Wij maken jouw gelegenheid extra feestelijk met
                        verse, luxe patisserie die volledig aansluit bij jouw
                        wensen.
                    </p>

                    <div class="my-8 border-t border-gray-300"></div>

                    <p class="text-gray-700 text-base leading-7 md:leading-8">
                        Bekijk ons assortiment of vraag een creatie op maat aan.
                        Van kleur en smaak tot thema: alles wordt met zorg en
                        vakmanschap speciaal voor jou gemaakt.
                    </p>

                </div>

            </div>

        </div>

    </section>
    {{-- ================= CONTACT ================= --}}
    <section class="py-16 md:py-28 bg-white">

        <div class="max-w-4xl mx-auto px-5 md:px-8 text-center">

            <h2 class="text-3xl md:text-5xl font-light uppercase tracking-[3px] md:tracking-[5px] mb-8 md:mb-10">
                Contact
            </h2>

            <p class="text-gray-600 text-base md:text-lg leading-8 md:leading-9 mb-10 max-w-3xl mx-auto">
                Heb je vragen over onze producten, de bezorging of
                afhaalmogelijkheden? Of wil je direct een bestelling plaatsen
                voor een verjaardag, bruiloft of een ander bijzonder moment?
                Neem dan gerust contact met ons op.
            </p>

            <div class="space-y-8">

                <div>
                    <h3 class="uppercase tracking-[2px] font-semibold text-lg mb-2">
                        Telefoon
                    </h3>

                    <a
                        href="tel:+31644042554"
                        class="text-gray-700 hover:text-black hover:underline transition"
                    >
                        +31 6 44042554
                    </a>
                </div>

                <div>
                    <h3 class="uppercase tracking-[2px] font-semibold text-lg mb-2">
                        E-mail
                    </h3>

                    <a
                        href="mailto:amayzingpastry@gmail.com"
                        class="text-gray-700 hover:text-black hover:underline transition"
                    >
                        amayzingpastry@gmail.com
                    </a>
                </div>

            </div>

        </div>

    </section>

</x-app-layout>
