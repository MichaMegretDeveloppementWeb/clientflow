{{-- Mini Feature: Suivi budgétaire --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
            {{-- Content --}}
            <div class="lg:pr-8">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
                    <x-heroicon-o-arrow-trending-up class="h-3 w-3" />
                    <span class="text-xs font-medium">Suivi budget</span>
                </div>
                <h3 class="mb-4 text-2xl font-bold text-gray-900">
                    Visualisez la santé financière de vos projets
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    Chaque projet affiche automatiquement son avancement budgétaire avec des alertes visuelles. 
                    Identifiez en un coup d'œil les dépassements et optimisez vos marges.
                </p>
            </div>

            {{-- Mockup --}}
            <div>
                <div class="relative mx-auto w-[85%] max-w-md">
                    {{-- Project card mockup --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                        {{-- Project header (skeleton) --}}
                        <div class="mb-6 flex items-center justify-between">
                            <div class="space-y-2">
                                <div class="h-4 w-32 rounded bg-gray-200"></div>
                                <div class="h-3 w-24 rounded bg-gray-100"></div>
                            </div>
                            <div class="h-6 w-16 rounded bg-gray-100"></div>
                        </div>

                        {{-- Budget section (detailed) --}}
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-sm font-medium text-emerald-800">Budget projet</span>
                                <div class="text-xs text-emerald-600">73% utilisé</div>
                            </div>

                            {{-- Progress bar --}}
                            <div class="mb-3 h-3 rounded-full bg-emerald-200">
                                <div class="h-3 w-[73%] rounded-full bg-emerald-500"></div>
                            </div>

                            {{-- Budget details --}}
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-emerald-700">Dépensé</span>
                                    <span class="font-medium text-emerald-800">2.190€</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-emerald-700">Budget total</span>
                                    <span class="font-medium text-emerald-800">3.000€</span>
                                </div>
                                <div class="flex justify-between border-t border-emerald-200 pt-2">
                                    <span class="text-emerald-700">Reste</span>
                                    <span class="font-bold text-emerald-900">810€</span>
                                </div>
                            </div>

                            {{-- Alert badge --}}
                            <div class="mt-3 inline-flex items-center gap-1 rounded-full bg-orange-100 px-2 py-1 text-xs text-orange-700">
                                <x-heroicon-s-exclamation-triangle class="h-3 w-3" />
                                <span>Attention budget</span>
                            </div>
                        </div>

                        {{-- Tasks skeleton --}}
                        <div class="mt-4 space-y-2">
                            <div class="h-2 w-full rounded bg-gray-100"></div>
                            <div class="h-2 w-3/4 rounded bg-gray-100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>