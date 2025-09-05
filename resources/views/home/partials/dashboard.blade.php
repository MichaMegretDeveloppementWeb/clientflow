{{-- Dashboard Section --}}
<section class="bg-gradient-to-b from-gray-50 to-white py-24 sm:py-32">
    <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
            <div class="lg:pr-8">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2 text-gray-700">
                    <x-heroicon-o-chart-bar class="h-4 w-4" />
                    <span class="text-sm font-medium">Tableau de bord</span>
                </div>
                <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    Votre activité en un coup d'œil
                </h2>
                <p class="mb-8 text-lg leading-8 text-gray-600">
                    Statistiques, graphiques, tâches urgentes : tout ce dont vous avez besoin pour
                    prendre les bonnes décisions et rester focus sur l'essentiel.
                </p>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                            <x-heroicon-o-chart-pie class="h-4 w-4 text-gray-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Métriques essentielles</h3>
                            <p class="text-gray-600">Revenus du mois, taux de conversion, projets actifs</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                            <x-heroicon-o-exclamation-triangle class="h-4 w-4 text-gray-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Alertes intelligentes</h3>
                            <p class="text-gray-600">Factures en retard, deadlines approchantes, budgets dépassés</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                            <x-heroicon-o-clock class="h-4 w-4 text-gray-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Activité récente</h3>
                            <p class="text-gray-600">Feed temps réel de toutes vos actions importantes</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                {{-- IMAGE: Screenshot du tableau de bord avec graphiques, stats et activités récentes --}}
                {{-- Montrer le dashboard principal avec le graphique des revenus, les cartes de statistiques, les tâches urgentes et l'activité récente --}}
                <img
                    src="{{ asset("assets/images/homepage/dashboard_graph.webp") }}"
                    alt="Capture d'écran du graphique sur le tableua de bord. Vu de l'interface urilisateur, backoffice de l'application web Stone."
                    class="aspect-[16/10] w-full rounded-2xl shadow-2xl"
                >
            </div>
        </div>
    </div>
</section>
