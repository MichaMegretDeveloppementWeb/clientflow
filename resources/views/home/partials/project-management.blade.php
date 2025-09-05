{{-- Project Management Section --}}
<section class="bg-gradient-to-b from-gray-50 to-white py-24 sm:py-32">
    <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
            <div class="lg:pr-8">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-emerald-700">
                    <x-heroicon-o-folder class="h-4 w-4" />
                    <span class="text-sm font-medium">Gestion Projet</span>
                </div>
                <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    Pilotez vos projets de A à Z
                </h2>
                <p class="mb-8 text-lg leading-8 text-gray-600">
                    De la conception à la livraison, gardez le contrôle total sur vos projets.
                    Budgets, délais, progression : tout est transparent et organisé.
                </p>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                            <x-heroicon-o-calendar class="h-4 w-4 text-emerald-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Timeline claire</h3>
                            <p class="text-gray-600">Visualisez dates de début, échéances et jalons importants</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                            <x-heroicon-o-banknotes class="h-4 w-4 text-emerald-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Suivi budgétaire</h3>
                            <p class="text-gray-600">Gardez l'œil sur vos coûts et marges en temps réel</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                            <x-heroicon-o-flag class="h-4 w-4 text-emerald-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">États personnalisables</h3>
                            <p class="text-gray-600">Actif, en attente, terminé - adaptez les statuts à votre workflow</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                {{-- IMAGE: Screenshot de la liste des projets avec interface de gestion --}}
                {{-- Montrer la page projets avec cartes colorées selon le statut, barres de progression et budgets --}}
                <img
                    src="{{ asset("assets/images/homepage/projects_list.webp") }}"
                    alt="Capture d'écran de la page liste des projets. Vu de l'interface urilisateur, backoffice de l'application web Stone."
                    class="aspect-[16/10] w-full rounded-2xl shadow-2xl"
                >
            </div>
        </div>
    </div>
</section>
