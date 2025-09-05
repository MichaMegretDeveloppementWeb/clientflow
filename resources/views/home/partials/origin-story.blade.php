{{-- Origin Story Section --}}
<section class="bg-gradient-to-b from-gray-50 to-white py-16 sm:py-24">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            {{-- Badge --}}
            <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-emerald-700">
                <x-heroicon-o-heart class="h-4 w-4" />
                <span class="text-sm font-medium">Né d'un besoin réel</span>
            </div>

            {{-- Title --}}
            <h2 class="mb-8 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Une solution créée par un freelance, pour les freelances
            </h2>

            {{-- Story --}}
            <div class="mx-auto max-w-3xl">
                <p class="mb-6 text-lg leading-relaxed text-gray-600">
                    Salut ! Je suis <strong>Micha</strong>, développeur web freelance basé à <strong>Évian-les-Bains</strong>. 
                    Comme vous, j'ai longtemps cherché un outil de gestion simple pour mes clients et projets. 
                    Las des CRM complexes avec des milliers d'options que je n'utilisais jamais, j'ai décidé de créer <strong>Stone</strong>.
                </p>

                <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-100">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                            <x-heroicon-o-gift class="h-5 w-5 text-green-600" />
                        </div>
                        <h3 class="mb-2 font-semibold text-gray-900">100% Gratuit</h3>
                        <p class="text-sm text-gray-600">
                            Pas de version premium cachée, pas d'abonnement. Stone sera toujours entièrement gratuit.
                        </p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-100">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                            <x-heroicon-o-document-text class="h-5 w-5 text-blue-600" />
                        </div>
                        <h3 class="mb-2 font-semibold text-gray-900">Open Source</h3>
                        <p class="text-sm text-gray-600">
                            Le code est disponible sur <a href="https://github.com/MichaMegretDeveloppementWeb/stone" target="_blank" class="text-blue-600 hover:text-blue-700 underline">GitHub</a>. 
                            Transparence totale sur le développement.
                        </p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-100 sm:col-span-2 lg:col-span-1">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                            <x-heroicon-o-bolt class="h-5 w-5 text-purple-600" />
                        </div>
                        <h3 class="mb-2 font-semibold text-gray-900">Simple par design</h3>
                        <p class="text-sm text-gray-600">
                            Volontairement épuré. Juste les fonctionnalités essentielles, sans la complexité.
                        </p>
                    </div>
                </div>

                {{-- Call to collaboration --}}
                <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-emerald-50 p-8 border border-blue-100">
                    <div class="text-center">
                        <div class="mb-4 flex justify-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white shadow-sm">
                                <x-heroicon-o-users class="h-6 w-6 text-blue-600" />
                            </div>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold text-gray-900">
                            Construisons ensemble l'outil parfait
                        </h3>
                        <p class="mb-6 text-gray-600 leading-relaxed">
                            Stone est conçu pour évoluer avec vos besoins. Rencontrez-vous un bug ? 
                            Une fonctionnalité vous manque ? Partagez votre expérience ! 
                            Vos retours façonnent directement l'avenir de l'application.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="https://github.com/MichaMegretDeveloppementWeb/stone/issues/new" target="_blank" 
                               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                <x-heroicon-o-chat-bubble-left class="h-4 w-4" />
                                Signaler un problème
                            </a>
                            <a href="https://github.com/MichaMegretDeveloppementWeb/stone/discussions" target="_blank" 
                               class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                <x-heroicon-o-light-bulb class="h-4 w-4" />
                                Proposer une amélioration
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>