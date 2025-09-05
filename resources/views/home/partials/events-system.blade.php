{{-- Events System Section --}}
<section class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
            <div class="order-2 lg:order-1">
                {{-- IMAGE: Screenshot du formulaire de création d'événement --}}
                {{-- Montrer le formulaire avec le toggle Étape/Facturation et les champs qui changent dynamiquement --}}
                <img
                    src="{{ asset("assets/images/homepage/event_create.webp") }}"
                    alt="Capture d'écran du formulaire de création d'un événement. Vu de l'interface urilisateur, backoffice de l'application web Stone."
                    class="aspect-[16/10] w-full rounded-2xl shadow-2xl"
                >
            </div>
            <div class="order-1 lg:order-2 lg:pl-8">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-purple-100 px-4 py-2 text-purple-700">
                    <x-heroicon-o-calendar class="h-4 w-4" />
                    <span class="text-sm font-medium">Système d'Événements</span>
                </div>
                <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    Un système unique pour tout gérer
                </h2>
                <p class="mb-8 text-xl leading-8 text-gray-600">
                    Notre système d'événements révolutionnaire réunit la gestion des tâches et la facturation en un seul outil. Créez des étapes de projet ou des factures avec la même simplicité.
                </p>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                            <x-heroicon-o-list-bullet class="h-4 w-4 text-purple-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Événements "Étape"</h3>
                            <p class="text-gray-600">Tâches, réunions, livrables - organisez toutes les étapes de vos projets</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                            <x-heroicon-o-receipt-refund class="h-4 w-4 text-purple-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Événements "Facturation"</h3>
                            <p class="text-gray-600">Factures, acomptes, paiements - gérez toute votre facturation dans le même système</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                            <x-heroicon-o-clock class="h-4 w-4 text-purple-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Timeline automatique</h3>
                            <p class="text-gray-600">Visualisez l'historique complet de chaque projet avec toutes les interactions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
