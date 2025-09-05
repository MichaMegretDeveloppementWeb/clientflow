{{-- Guest Navbar --}}
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <img src="{{ asset('assets/images/stone_logo.webp') }}" alt="Stone Logo" class="h-10 w-10" />
                <span class="text-2xl font-bold text-gray-900">Stone</span>
            </a>

            {{-- Auth Actions (Desktop) --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                        Accéder au tableau de bord
                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-gray-600 hover:text-gray-900 transition-colors">
                        Se connecter
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                        Créer un compte
                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <div class="md:hidden relative" x-data="{ open: false }">
                <button type="button" 
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-500" 
                        @click="open = !open">
                    <span class="sr-only">Ouvrir le menu</span>
                    <x-heroicon-o-bars-3 class="h-6 w-6" x-show="!open" />
                    <x-heroicon-o-x-mark class="h-6 w-6" x-show="open" style="display: none" />
                </button>
            
                {{-- Mobile menu --}}
                <div class="absolute right-0 top-full mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                     x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 scale-95" 
                     x-transition:enter-end="opacity-100 scale-100" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="opacity-100 scale-100" 
                     x-transition:leave-end="opacity-0 scale-95" 
                     style="display: none">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-900 font-medium hover:bg-gray-100">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Se connecter</a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-900 font-medium hover:bg-gray-100">Créer un compte</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
