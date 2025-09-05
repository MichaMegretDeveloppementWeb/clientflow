{{-- Client Management Section --}}
<section class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
            <div class="order-2 lg:order-1">
                {{-- IMAGE: Screenshot de la liste des clients avec cartes stats --}}
                {{-- Montrer la page de liste des clients avec les cartes de statistiques en haut et le tableau des clients en dessous --}}
                <img
                    src="{{ asset("assets/images/homepage/clients_list.webp") }}"
                    class="aspect-[16/10] w-full rounded-2xl shadow-2xl"
                    alt="Capture d'écran de la page liste des clients. Vu de l'interface urilisateur, backoffice de l'application web Stone."
                    preload="lazy"
                >
            </div>
            <div class="order-1 lg:order-2 lg:pl-8">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                    <x-heroicon-o-users class="h-4 w-4" />
                    <span class="text-sm font-medium">Gestion Client</span>
                </div>
                <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    Centralisez vos relations client
                </h2>
                <p class="mb-8 text-xl leading-8 text-gray-600">
                    Gardez toutes les informations importantes de vos clients à portée de main. Contacts, historique des projets, notes personnelles et statistiques financières en un coup d'œil.
                </p>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                            <x-heroicon-o-user-plus class="h-4 w-4 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Fiches client complètes</h3>
                            <p class="text-gray-600">Nom, société, coordonnées, notes personnalisées et historique des interactions</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                            <x-heroicon-o-chart-bar-square class="h-4 w-4 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Statistiques en temps réel</h3>
                            <p class="text-gray-600">Chiffre d'affaires total, nombre de projets et vue d'ensemble de votre portefeuille</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                            <x-heroicon-o-magnifying-glass class="h-4 w-4 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Recherche et filtrage avancés</h3>
                            <p class="text-gray-600">Trouvez rapidement le bon client grâce aux filtres par statut, société ou période</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
