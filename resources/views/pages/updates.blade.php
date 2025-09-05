@extends('layouts.guest')

@section('title', 'Mises à jour - Stone')
@section('description', 'Découvrez les dernières améliorations de Stone, notre cycle de développement et inscrivez-vous à notre liste de diffusion pour ne rien manquer des nouveautés.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Mises à jour - Stone",
    "description": "Découvrez les dernières améliorations de Stone, notre cycle de développement et inscrivez-vous à notre liste de diffusion pour ne rien manquer des nouveautés.",
    "url": "{{ url()->current() }}",
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Accueil",
                "item": "{{ route('home') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Mises à jour"
            }
        ]
    },
    "mainEntity": {
        "@type": "SoftwareApplication",
        "name": "Stone",
        "softwareVersion": "1.0",
        "applicationCategory": "BusinessApplication",
        "releaseNotes": "Améliorations continues basées sur les retours utilisateurs et l'innovation produit"
    }
}
@endsection

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-b from-blue-50/50 via-white to-white pt-24 pb-0 sm:pt-32 sm:pb-0">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                <x-heroicon-o-arrow-path class="h-5 w-5" />
                <span class="text-sm font-medium">Version 1.0</span>
            </div>
            <h1 class="mx-auto max-w-4xl text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                Stone évolue avec
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    vos besoins
                </span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-xl leading-8 text-gray-600">
                Découvrez notre approche du développement produit : améliorations continues,
                écoute utilisateur et innovations pour faire évoluer votre outil de travail.
            </p>
        </div>
    </div>
</section>

{{-- Development Approach --}}
<section class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Notre philosophie de développement
            </h2>
            <p class="mt-4 text-lg leading-8 text-gray-600">
                Stone évolue en permanence grâce à votre feedback et notre vision produit
            </p>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            {{-- User-Driven Updates --}}
            <div class="flex flex-col lg:flex-row gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-green-700">
                        <x-heroicon-o-users class="h-4 w-4" />
                        <span class="text-sm font-medium">Orienté utilisateur</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Vos retours guident nos améliorations</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Chaque suggestion, chaque remontée de bug est prise en compte. Vous utilisez Stone au quotidien,
                        vous savez mieux que quiconque comment l'améliorer.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-chat-bubble-bottom-center class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Feedback utilisateur prioritaire</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-bolt class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Corrections de bugs rapides</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-light-bulb class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Nouvelles fonctionnalités demandées</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-green-50 to-emerald-100 p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center justify-center h-12 w-12 bg-green-600 rounded-full mb-3">
                                    <x-heroicon-o-users class="h-6 w-6 text-white" />
                                </div>
                                <div class="text-sm font-medium text-green-700">Cycle de feedback</div>
                            </div>
                            {{-- Feedback cycle --}}
                            <div class="space-y-3">
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm">1. Remontée utilisateur</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-yellow-500 rounded-full"></div>
                                    <span class="text-sm">2. Analyse & priorisation</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-purple-500 rounded-full"></div>
                                    <span class="text-sm">3. Développement</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm">4. Déploiement</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Regular Improvements --}}
            <div class="flex flex-col lg:flex-row-reverse gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                        <x-heroicon-o-cog-6-tooth class="h-4 w-4" />
                        <span class="text-sm font-medium">Amélioration continue</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Améliorations mensuelles</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        En plus des retours utilisateurs, nous développons régulièrement de nouvelles fonctionnalités
                        et améliorons l'expérience utilisateur selon notre vision produit.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-calendar class="h-5 w-5 text-blue-600" />
                            <span class="text-gray-700">Rythme mensuel de base</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-sparkles class="h-5 w-5 text-blue-600" />
                            <span class="text-gray-700">Nouvelles fonctionnalités</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-arrow-trending-up class="h-5 w-5 text-blue-600" />
                            <span class="text-gray-700">Optimisations de performance</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-indigo-100 p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center justify-center h-12 w-12 bg-blue-600 rounded-full mb-3">
                                    <x-heroicon-o-cog-6-tooth class="h-6 w-6 text-white" />
                                </div>
                                <div class="text-sm font-medium text-blue-700">Planning de développement</div>
                            </div>
                            {{-- Monthly calendar mockup --}}
                            <div class="bg-white rounded-lg p-4">
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">L</div>
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">M</div>
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">M</div>
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">J</div>
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">V</div>
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">S</div>
                                    <div class="text-center text-xs font-medium text-gray-500 p-1">D</div>
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    <div class="h-6 w-6 text-center text-xs p-1 text-gray-400">28</div>
                                    <div class="h-6 w-6 text-center text-xs p-1 text-gray-400">29</div>
                                    <div class="h-6 w-6 text-center text-xs p-1 text-gray-400">30</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">1</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">2</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">3</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">4</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">5</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">6</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">7</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">8</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">9</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">10</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">11</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">12</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">13</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">14</div>
                                    <div class="h-6 w-6 text-center text-xs p-1 bg-blue-100 rounded">15</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">16</div>
                                    <div class="h-6 w-6 text-center text-xs p-1">17</div>
                                </div>
                                <div class="mt-2 text-center text-xs text-blue-600 font-medium">
                                    📅 Mise à jour mensuelle prévue
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Types of Updates --}}
<section class="bg-gray-50 py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-4">
                Types de mises à jour
            </h2>
            <p class="text-lg text-gray-600">
                Découvrez les différentes améliorations que nous apportons régulièrement à Stone
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- New Features --}}
            <div class="bg-white rounded-xl p-8 shadow-sm">
                <div class="mb-6 inline-flex items-center justify-center h-16 w-16 rounded-lg bg-blue-100">
                    <x-heroicon-o-sparkles class="h-8 w-8 text-blue-600" />
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Nouvelles fonctionnalités</h3>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Ajout de nouvelles capacités à Stone pour enrichir votre expérience et répondre
                    à vos besoins métier en évolution.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-plus-circle class="h-5 w-5 text-blue-600 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900">Nouvelles intégrations</div>
                            <div class="text-sm text-gray-600">Connexions avec d'autres outils</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-plus-circle class="h-5 w-5 text-blue-600 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900">Modules supplémentaires</div>
                            <div class="text-sm text-gray-600">Extensions des capacités existantes</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-plus-circle class="h-5 w-5 text-blue-600 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900">Outils d'analyse avancés</div>
                            <div class="text-sm text-gray-600">Nouvelles métriques et rapports</div>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Bug Fixes --}}
            <div class="bg-white rounded-xl p-8 shadow-sm">
                <div class="mb-6 inline-flex items-center justify-center h-16 w-16 rounded-lg bg-green-100">
                    <x-heroicon-o-wrench-screwdriver class="h-8 w-8 text-green-600" />
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Résolution de bugs</h3>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Corrections rapides des problèmes remontés par les utilisateurs pour garantir
                    une expérience fluide et fiable au quotidien.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900">Corrections d'affichage</div>
                            <div class="text-sm text-gray-600">Interface utilisateur optimisée</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900">Problèmes de performance</div>
                            <div class="text-sm text-gray-600">Temps de réponse améliorés</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900">Erreurs fonctionnelles</div>
                            <div class="text-sm text-gray-600">Comportements corrigés</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Newsletter Subscription --}}
<section class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-4">
                Ne manquez aucune mise à jour
            </h2>
            <p class="text-lg text-gray-600">
                Inscrivez-vous à notre liste de diffusion pour être informé en avant-première
                des nouvelles fonctionnalités et améliorations de Stone.
            </p>
        </div>

        <div class="mx-auto max-w-xl">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 border border-gray-200">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center h-12 w-12 bg-blue-600 rounded-full mb-4">
                        <x-heroicon-o-envelope class="h-6 w-6 text-white" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Liste de diffusion des mises à jour</h3>
                    <p class="text-gray-600">
                        Recevez un email à chaque nouvelle version avec le détail des améliorations
                    </p>
                </div>

                {{-- Newsletter Form --}}
                <form id="updates-newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="newsletter-email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse email
                        </label>
                        <input type="email"
                               id="newsletter-email"
                               name="email"
                               required
                               placeholder="votre@email.com"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <x-heroicon-o-paper-airplane class="h-5 w-5" />
                        S'inscrire à la liste de diffusion
                    </button>
                </form>

                {{-- Benefits --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span class="text-gray-600">Informations en avant-première</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span class="text-gray-600">Désinscription en un clic</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span class="text-gray-600">Pas de spam, juste les mises à jour</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span class="text-gray-600">Emails séparés de votre compte</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Maintenance Notice --}}
<section class="bg-gray-50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-yellow-100 px-4 py-2 text-yellow-700">
                <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                <span class="text-sm font-medium">Information</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                Déploiement des mises à jour
            </h3>
            <p class="text-gray-600 leading-relaxed">
                Les mises à jour sont déployées selon les besoins, sans calendrier prédéfini.
                Une page de maintenance sera mise en place prochainement pour vous informer
                en temps réel lors des déploiements.
            </p>
        </div>
    </div>
</section>

{{-- Contact & Feedback --}}
<section class="bg-gray-900 py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl mb-6">
                Votre feedback compte
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Vous avez une idée d'amélioration, un bug à signaler ou une fonctionnalité à proposer ?
                Votre retour est essentiel pour faire évoluer Stone.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="mailto:feedback@stone-app.fr"
                   class="group inline-flex items-center gap-3 bg-white px-6 py-3 text-lg font-semibold text-gray-900 shadow-xl hover:bg-gray-100 transition-all duration-200 rounded-lg">
                    <x-heroicon-o-chat-bubble-bottom-center class="h-5 w-5" />
                    Envoyer mon feedback
                </a>
                <a href="{{ route('home') }}" class="text-gray-300 hover:text-white font-medium">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Simple JavaScript for form feedback (no backend logic yet) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updates-newsletter-form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const emailInput = form.querySelector('input[name="email"]');

    // Store original button content
    const originalBtnContent = submitBtn.innerHTML;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Inscription en cours...
        `;
        submitBtn.disabled = true;

        // Get form data
        const formData = new FormData(form);

        // Send AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success state
                submitBtn.innerHTML = `
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    ${data.type === 'already_subscribed' ? 'Déjà inscrit !' : 
                      data.type === 'reactivated' ? 'Réinscription confirmée !' : 
                      'Inscription confirmée !'}
                `;
                submitBtn.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                submitBtn.classList.add('from-green-600', 'to-green-600');

                // Reset form
                emailInput.value = '';

                // Reset button after 3 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalBtnContent;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('from-green-600', 'to-green-600');
                    submitBtn.classList.add('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                }, 3000);
            } else {
                // Error state
                submitBtn.innerHTML = `
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    ${data.message || 'Erreur'}
                `;
                submitBtn.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                submitBtn.classList.add('from-red-600', 'to-red-600');

                // Reset button after 3 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalBtnContent;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('from-red-600', 'to-red-600');
                    submitBtn.classList.add('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Error state
            submitBtn.innerHTML = `
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Erreur de connexion
            `;
            submitBtn.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
            submitBtn.classList.add('from-red-600', 'to-red-600');

            // Reset button after 3 seconds
            setTimeout(() => {
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.disabled = false;
                submitBtn.classList.remove('from-red-600', 'to-red-600');
                submitBtn.classList.add('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
            }, 3000);
        });
    });
});
</script>
@endsection
