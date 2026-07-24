<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 font-light">

    <div class="max-w-7xl mx-auto px-8 lg:px-12">

        <div class="flex justify-between items-center h-32">

            {{-- Logo --}}
            <div class="flex items-center">

                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.svg') }}"
                         alt="Jamay Tuller Patisserie Logo"
                         class="w-32 h-32 object-contain">
                </a>

            </div>


            {{-- Desktop navigatie --}}
            <div class="hidden md:flex items-center gap-12 text-sm uppercase tracking-[3px] text-gray-700">

                <x-nav-link :href="route('home')"
                            :active="request()->routeIs('home')">
                    Home
                </x-nav-link>

                <x-nav-link :href="route('products.index')"
                            :active="request()->routeIs('products.index')">
                    Producten & Bestellen
                </x-nav-link>

                <x-nav-link :href="route('cart.index')"
                            :active="request()->routeIs('cart.index')">
                    Winkelwagen
                </x-nav-link>

                @auth
                    <x-nav-link :href="route('dashboard')"
                                :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                @endauth

            </div>


            {{-- Account --}}
            <div class="hidden md:flex items-center">

                @guest

                    <a href="{{ route('login') }}"
                       class="text-gray-600 hover:text-black transition"
                       title="Inloggen">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 64 64"
                             class="w-9 h-9"
                             fill="currentColor">

                            <path d="M20 32c-5 0-9-4-9-9s4-9 9-9c1.5 0 3 .4 4.2 1.1C25.5 10.4 28.6 8 32 8s6.5 2.4 7.8 6.1C41 13.4 42.5 13 44 13c5 0 9 4 9 9s-4 9-9 9H20z"/>

                            <path d="M18 34h28v6H18z"/>

                            <path d="M20 40h24v12H20z"/>

                        </svg>
                    </a>

                @endguest


                @auth

                    <x-dropdown align="right" width="48">

                        <x-slot name="trigger">

                            <button class="uppercase tracking-[2px] text-sm text-gray-600 hover:text-black transition">
                                {{ Auth::user()->name }}

                                <svg class="inline ml-2 h-3 w-3"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">

                                    <path fill-rule="evenodd"
                                          d="M5.5 7l4.5 4 4.5-4z"
                                          clip-rule="evenodd" />

                                </svg>

                            </button>

                        </x-slot>


                        <x-slot name="content">

                            <x-dropdown-link :href="route('profile.edit')">
                                Profiel
                            </x-dropdown-link>


                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    Uitloggen
                                </x-dropdown-link>

                            </form>

                        </x-slot>

                    </x-dropdown>

                @endauth

            </div>


            {{-- Mobile knop --}}
            <div class="md:hidden">

                <button @click="open = !open"
                        class="text-gray-600">

                    <svg class="h-7 w-7"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path x-show="!open"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M4 6h16M4 12h16M4 18h16"/>

                        <path x-show="open"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M6 18L18 6M6 6l12 12"/>

                    </svg>

                </button>

            </div>

        </div>

    </div>


    {{-- Mobile menu --}}
    <div :class="{ 'block': open, 'hidden': !open }"
         class="md:hidden border-t border-gray-100">

        <div class="px-8 py-6 space-y-4 uppercase tracking-[2px] text-sm">

            <x-responsive-nav-link :href="route('home')">
                Home
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('products.index')">
                Producten & Bestellen
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('cart.index')">
                Winkelwagen
            </x-responsive-nav-link>

            @guest
                <a href="{{ route('login') }}">
                    Inloggen
                </a>
            @endguest

        </div>

    </div>

</nav>
