<x-app-layout>
    {{-- Hero Sectie --}}
    <section class="relative bg-cover bg-center h-screen" style="background-image: url('/images/home.jpeg');">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6">
            <h1 class="text-5xl font-extrabold text-white mb-4">Luxe patisserie</h1>
            <p class="text-xl text-white max-w-2xl mb-8">
                Welkom op de website van aMayzing, een plek waar ik mijn passie voor luxe patisserie volledig tot leven breng.
            </p>
            <a href="#about" class="bg-white text-gray-800 px-6 py-3 font-semibold rounded shadow hover:bg-gray-100 transition">
                Ontdek meer
            </a>
        </div>
    </section>

    {{-- Over ons --}}
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-24">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">aMayzing</h2>
            <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                Mijn liefde voor bakken begon toen ik veertien jaar was en mijn eerste bijbaan in een bakkerij vond. Na zeven jaar ervaring in luxe hotels in Amsterdam en een inspirerende tijd in Dubai, ben ik klaar om die passie met u te delen.
            <p class="text-lg text-gray-700 leading-relaxed">
                Bij aMayzing draait het om exclusieve, luxe creaties die uw verwachtingen overtreffen. Elke pastry maak ik met zorg, vakmanschap en oog voor detail. Hieronder vertel ik graag wat meer over de mogelijkheden.
            </p>
        </div>
    </section>

    {{-- Diensten --}}
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-24">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Creëer bijzondere momenten met luxe patisserie</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Bouquet & Pastries</h3>
                    <p class="text-gray-700">
                        Heeft u een speciale gelegenheid? Een bruiloft, een jubileum, of gewoon een dag waarop u iemand (of uzelf!) wilt verrassen met iets bijzonders? Bij aMayzing kunt u terecht voor prachtige pastries, bouquets en petit fours die echt indruk maakt. Elk item is uniek, afgestemd op uw wensen en gemaakt met de fijnste ingrediënten.
                    </p>
                </div>
                <div class="bg-white rounded shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Leermeesterschap</h3>
                    <p class="text-gray-700">
                        Wilt u zelf aan de slag in de wereld van luxe patisserie? Ik ben leermeester en sta voor iedereen klaar die meer wil leren over dit prachtige vak. Of u nu beginner bent of al ervaring heeft, ik begeleid u stap voor stap, zodat u zelf met vertrouwen de heerlijkste creaties kunt maken.
                    </p>
                </div>
                <div class="bg-white rounded shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Evenementenservice & Foodbox</h3>
                    <p class="text-gray-700">
                        aMayzing Pastry biedt evenementenservice en maakt van elk evenement een smakelijke ervaring met unieke, op maat gemaakte patisserie. Wij nodigen u ook uit om de unieke ervaring van onze Foodbox te ontdekken. Twee automaten vol met verrukkelijke gerechten en verfrissende drankjes wachten op u!
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Beleving --}}
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-24 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Passie en vakmanschap in elke hap</h2>
            <p class="text-lg text-gray-700 leading-relaxed max-w-3xl mx-auto">
                Voor mij is patisserie niet zomaar een product – het is een ervaring die alle zintuigen prikkelt. Mijn ervaring heeft mij geïnspireerd om technieken en smaken te combineren die nét even anders zijn. Elk gebakje dat ik maak, is met liefde en aandacht bereid, zodat het niet alleen mooi is om te zien, maar ook een smaaksensatie biedt. Ik wil dat u bij elke hap verrast wordt door de luxe en passie die erin verwerkt zit.
            </p>
        </div>
    </section>

    {{-- Contact --}}
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-24 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Laat u verleiden door aMayzing</h2>
            <p class="text-lg text-gray-700 mb-6">
                Of u nu op zoek bent naar een prachtig dessert voor een speciale gelegenheid, een uniek bouquet, of bij een stage wilt volgen – ik help graag. Bij aMayzing bent u verzekerd van luxe en kwaliteit, en ik hoop dat u net zo veel geniet van mijn creaties als ik van het maken ervan.
            </p>
            <p class="text-lg text-gray-700 mb-8">
                Heeft u nog vragen of wilt u een bestelling plaatsen? Neem dan contact met mij op door te bellen naar
                <a href="tel:+31858882901" class="text-gray-800 font-semibold hover:underline">085-8882901</a> of te mailen naar
                <a href="mailto:jamaytuller@gmail.com" class="text-gray-800 font-semibold hover:underline">amayzingpastry@gmail.com</a>.
            </p>
        </div>
    </section>

    {{-- Footer met info --}}
    <footer class="bg-gray-100 py-8">
        <div class="container mx-auto px-6 lg:px-24 text-center">
            <p class="text-gray-700 text-lg mb-2">
                aMayzing Pastry - KvK 95042032
            </p>
            <p class="text-gray-700">
                Contact:
                <a href="tel:+31858882901" class="font-semibold text-gray-800 hover:underline">085-8882901</a> &middot;
               </p>
        </div>
    </footer>
</x-app-layout>
