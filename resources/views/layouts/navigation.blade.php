<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 font-light">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="relative flex justify-between items-center h-20">

            <div class="flex justify-center absolute left-1/2 transform -translate-x-1/2 z-10 pointer-events-none" style="width: max-content;">
            <a href="{{ route('home') }}" class="text-2xl font-semibold font-serif text-gray-800 tracking-tight pointer-events-auto">
                    aMayzing
                </a>
            </div>

            {{-- Navigatie links - links uitgelijnd --}}
            <div class="hidden md:flex items-center space-x-6 z-20">
                <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-nav-link>
                <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">Producten</x-nav-link>
                <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')">Winkelwagen</x-nav-link>

                @auth
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                @endauth
            </div>

            {{-- Auth knoppen - rechts uitgelijnd --}}
            <div class="hidden md:flex items-center space-x-6 z-20 text-sm">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Inloggen</a>
{{--                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">Registreren</a>--}}
                @endguest

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 transition">
                                {{ Auth::user()->name }}
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.5 7l4.5 4 4.5-4z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profiel</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">Uitloggen</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            {{-- Auth links op mobiel - rechts van logo --}}
            <div class="md:hidden absolute right-4 top-1/2 transform -translate-y-1/2 flex space-x-4 text-sm z-20">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Inloggen</a>
                @endguest

                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <div class="md:hidden flex items-center z-20">
                <button @click="open = ! open" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobiele navigatie --}}
    <div :class="{ 'block': open, 'hidden': ! open }" class="md:hidden z-20">
        <div class="px-4 pt-4 pb-3 space-y-2 text-base text-gray-700">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">Producten</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')">Winkelwagen</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-gray-900">Inloggen</a>
{{--                <a href="{{ route('register') }}" class="block text-gray-700 hover:text-gray-900">Registreren</a>--}}
            @endguest

            @auth
                <div class="border-t border-gray-200 pt-3">
                    <div class="text-sm text-gray-600 mb-2 px-1">{{ Auth::user()->name }}</div>
                    <x-responsive-nav-link :href="route('profile.edit')">Profiel</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); this.closest('form').submit();">Uitloggen</x-responsive-nav-link>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
