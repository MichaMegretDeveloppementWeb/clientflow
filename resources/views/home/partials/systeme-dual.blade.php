{{-- Mini Feature: Système dual --}}
<section class="bg-gradient-to-b from-white to-gray-50 py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
            {{-- Mockup --}}
            <div class="order-2 lg:order-1">
                <div class="relative mx-auto w-[85%] max-w-md">
                    {{-- Toggle interface --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                        {{-- Type selector (detailed) --}}
                        <div class="mb-6">
                            <div class="mb-3 text-sm font-medium text-gray-700">Type d'événement</div>
                            <div class="grid grid-cols-2 gap-3">
                                <button class="rounded-lg border border-gray-200 bg-white p-4 text-left transition-all hover:border-gray-300">
                                    <div class="mb-2 flex items-center space-x-2">
                                        <x-heroicon-o-list-bullet class="h-4 w-4 text-purple-600" />
                                        <span class="text-sm font-medium text-gray-700">Étape</span>
                                    </div>
                                    <div class="text-xs text-gray-500">Tâche, réunion</div>
                                </button>
                                {{-- Selected option --}}
                                <button class="rounded-lg border-2 border-emerald-500 bg-emerald-50 p-4 text-left">
                                    <div class="mb-2 flex items-center space-x-2">
                                        <x-heroicon-o-receipt-refund class="h-4 w-4 text-emerald-600" />
                                        <span class="text-sm font-medium text-emerald-800">Facturation</span>
                                    </div>
                                    <div class="text-xs text-emerald-600">Facture, acompte</div>
                                </button>
                            </div>
                        </div>

                        {{-- Dynamic fields (detailed for billing) --}}
                        <div class="space-y-4">
                            <div>
                                <div class="mb-2 text-xs font-medium text-gray-700">Montant HT</div>
                                <div class="flex items-center rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2">
                                    <x-heroicon-o-currency-euro class="mr-2 h-4 w-4 text-emerald-600" />
                                    <span class="text-sm font-medium text-emerald-800">2.500,00</span>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-2 text-xs font-medium text-gray-700">Date d'échéance</div>
                                <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2">
                                    <span class="text-sm font-medium text-emerald-800">15 mars 2025</span>
                                </div>
                            </div>

                            {{-- Toggle indicator --}}
                            <div class="mt-4 flex items-center justify-center">
                                <div class="flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">
                                    <x-heroicon-s-bolt class="h-3 w-3" />
                                    <span>Champs dynamiques actifs</span>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden step fields skeleton --}}
                        <div class="mt-4 space-y-2 opacity-30">
                            <div class="h-2 w-full rounded bg-gray-100"></div>
                            <div class="h-2 w-3/4 rounded bg-gray-100"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="order-1 lg:order-2 lg:pl-8">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-indigo-100 px-3 py-1 text-indigo-700">
                    <x-heroicon-o-arrow-path class="h-3 w-3" />
                    <span class="text-xs font-medium">Innovation</span>
                </div>
                <h3 class="mb-4 text-2xl font-bold text-gray-900">
                    Un formulaire, deux univers
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    Plus besoin de jongler entre différents outils. Un simple toggle transforme votre formulaire 
                    d'événement : étape de projet ou élément de facturation, les champs s'adaptent automatiquement.
                </p>
            </div>
        </div>
    </div>
</section>