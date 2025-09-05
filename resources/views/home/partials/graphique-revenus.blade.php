{{-- Mini Feature: Graphique revenus --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
            {{-- Content --}}
            <div class="lg:pr-8">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
                    <x-heroicon-o-arrow-trending-up class="h-3 w-3" />
                    <span class="text-xs font-medium">Analytics</span>
                </div>
                <h3 class="mb-4 text-2xl font-bold text-gray-900">
                    Visualisez facturé vs encaissé
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    Comparez d'un coup d'œil vos revenus facturés et réellement encaissés.
                    Identifiez les décalages et optimisez votre trésorerie sur la période de votre choix.
                </p>
            </div>

            {{-- Mockup --}}
            <div>
                <div class="relative mx-auto w-[85%] max-w-md">
                    {{-- Revenue Chart mockup --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                        {{-- Title --}}
                        <div class="mb-4 flex items-center gap-2">
                            <div class="h-3 w-3 rounded bg-gray-100"></div>
                            <div class="h-3 w-32 rounded bg-gray-100"></div>
                        </div>

                        {{-- Period selector --}}
                        <div class="mb-4 h-8 w-24 rounded bg-gray-100"></div>

                        {{-- Stats (detailed) --}}
                        <div class="mb-6 grid grid-cols-2 gap-3">
                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                <div class="text-xs text-blue-600 font-medium mb-1">Total facturé</div>
                                <div class="text-xl font-bold text-blue-700">18.750€</div>
                            </div>
                            <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                                <div class="text-xs text-green-600 font-medium mb-1">Total encaissé</div>
                                <div class="text-xl font-bold text-green-700">14.200€</div>
                            </div>
                        </div>

                        {{-- Legend --}}
                        <div class="mb-3 flex justify-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                <span class="text-xs text-gray-600">Revenus facturé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                <span class="text-xs text-gray-600">Revenus perçus</span>
                            </div>
                        </div>

                        {{-- Chart with dual lines --}}
                        <div class="h-24 w-full rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <svg viewBox="0 0 200 60" class="h-full w-full">
                                {{-- Grid lines --}}
                                <defs>
                                    <pattern id="grid" width="20" height="15" patternUnits="userSpaceOnUse">
                                        <path d="M 20 0 L 0 0 0 15" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                                    </pattern>
                                </defs>
                                <rect width="200" height="60" fill="url(#grid)" />

                                {{-- Facturé line (blue) --}}
                                <polyline fill="none" stroke="#3b82f6" stroke-width="2" points="10,45 35,42 60,38 85,35 110,28 135,25 160,20 185,15"/>
                                <polygon fill="#3b82f6" fill-opacity="0.1" points="10,45 35,42 60,38 85,35 110,28 135,25 160,20 185,15 185,50 10,50"/>

                                {{-- Encaissé line (green) --}}
                                <polyline fill="none" stroke="#10b981" stroke-width="2" points="10,50 35,48 60,45 85,43 110,40 135,38 160,30 185,25"/>
                                <polygon fill="#10b981" fill-opacity="0.1" points="10,50 35,48 60,45 85,43 110,40 135,38 160,30 185,25 185,55 10,55"/>

                                {{-- Data points --}}
                                <circle cx="185" cy="15" r="2" fill="#3b82f6"/>
                                <circle cx="185" cy="25" r="2" fill="#10b981"/>
                            </svg>
                        </div>

                        {{-- Gap indicator --}}
                        <div class="mt-3 flex justify-between text-xs">
                            <span class="text-gray-500">Jan</span>
                            <div class="text-orange-600 font-medium">
                                Écart: 4.550€
                            </div>
                            <span class="text-gray-500">Juin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
