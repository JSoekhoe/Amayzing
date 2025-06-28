<x-app-layout>
    {{-- Hero sectie met achtergrond en CTA --}}
    <section class="relative bg-cover bg-center h-screen" style="background-image: url('/images/hero-patisserie.jpg');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6">
            <h1 class="text-5xl font-extrabold text-white mb-4">Luxe patisserie</h1>
            <p class="text-xl text-white max-w-2xl mb-8">
                Exquise creaties, vakmanschap en passie – op maat gemaakt voor uw mooiste momenten.
            </p>
            <a href="#about" class="bg-white text-gray-800 px-6 py-3 font-semibold rounded shadow hover:bg-gray-100 transition">
                Ontdek meer
            </a>
        </div>
    </section>

    {{-- Over ons --}}
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-24">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Welkom bij aMayzing</h2>
            <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                Bij aMayzing draait alles om passie voor luxe patisserie. Opgegroeid met bakken
                en vrouwenaroma’s van de hotelkeuken, breng ik mijn vak tot leven in elk gebakje.
                Met zorg, oog voor detail en de fijnste ingrediënten overstijgt elk dessert uw verwachtingen.
            </p>
            <p class="text-lg text-gray-700 leading-relaxed">
                Ideaal voor bruiloften, jubilea of als leermeester in patisserie. Samen creëren we
                unieke smaken en ervaringen, of het nu voor pro’s is of beginnende bakkers.
            </p>
        </div>
    </section>

    {{-- Diensten --}}
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-24">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Wat ik aanbied</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Exclusieve creaties</h3>
                    <p class="text-gray-700">Pastries, bouquets en petit fours op maat voor elke gelegenheid.</p>
                </div>
                <div class="bg-white rounded shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Workshops</h3>
                    <p class="text-gray-700">Leer de kneepjes van het vak, van beginner tot gevorderde.</p>
                </div>
                <div class="bg-white rounded shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Catering & bestellingen</h3>
                    <p class="text-gray-700">Unieke, luxe creaties direct bij u thuis of op locatie.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footersctie contact --}}
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-24 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Laat u verleiden door aMayzing</h2>
            <p class="text-lg text-gray-700 mb-8">
                Voor vragen of bestellingen: bel <a href="tel:0858882901" class="text-blue-600 hover:underline">085‑8882901</a>
                of mail ons gerust.
            </p>
            <a href="mailto:info@amayzingpastry.nl" class="bg-blue-600 text-white px-6 py-3 font-semibold rounded hover:bg-blue-700 transition">
                Neem contact op
            </a>
        </div>
    </section>
</x-app-layout>
