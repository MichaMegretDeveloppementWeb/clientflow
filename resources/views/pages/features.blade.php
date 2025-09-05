@extends('layouts.guest')

@section('title', 'Fonctionnalités - Stone')
@section('description', 'Découvrez les fonctionnalités de Stone : gestion client, projets, facturation et tableau de bord. Un outil simple pour organiser votre activité professionnelle.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Fonctionnalités - Stone",
    "description": "Découvrez les fonctionnalités de Stone : gestion client, projets, facturation et tableau de bord. Un outil simple pour organiser votre activité professionnelle.",
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
                "name": "Fonctionnalités"
            }
        ]
    },
    "mainEntity": {
        "@type": "SoftwareApplication",
        "name": "Stone",
        "applicationCategory": "BusinessApplication",
        "featureList": [
            "Gestion des clients",
            "Suivi des projets",
            "Système d'événements dual",
            "Facturation intégrée",
            "Tableau de bord analytics",
            "Timeline automatique"
        ]
    }
}
@endsection

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-b from-gray-50/50 via-white to-white pt-24 pb-0 sm:pt-32 sm:pb-0">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="mx-auto max-w-4xl text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                Des fonctionnalités pensées pour
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    organiser votre activité
                </span>
                simplement
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-xl leading-8 text-gray-600">
                Stone regroupe les essentiels : clients, projets, facturation et suivi.
                Un outil simple pensé pour les freelances et petites agences.
            </p>
        </div>
    </div>
</section>

{{-- Main Features Grid --}}
<section class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Un outil, l'essentiel regroupé
            </h2>
            <p class="mt-4 text-lg leading-8 text-gray-600">
                Découvrez comment Stone simplifie la gestion quotidienne de votre activité
            </p>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            {{-- Gestion Client --}}
            <div class="flex flex-col lg:flex-row gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                        <x-heroicon-o-users class="h-4 w-4" />
                        <span class="text-sm font-medium">Gestion Client</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Centralisez vos contacts</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Stockez toutes les informations importantes de vos clients : coordonnées, historique des projets,
                        revenus générés. Tout est accessible en un clic.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Fiches clients complètes</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Historique des interactions</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-check class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Statistiques de revenus</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-indigo-100 p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="h-3 w-3/4 rounded bg-blue-200"></div>
                            <div class="h-3 w-1/2 rounded bg-blue-200"></div>
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center justify-between rounded-lg bg-white p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100"></div>
                                        <div class="space-y-1">
                                            <div class="h-2 w-20 rounded bg-gray-200"></div>
                                            <div class="h-2 w-16 rounded bg-gray-100"></div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-blue-600">12.500€</div>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-white p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-green-100"></div>
                                        <div class="space-y-1">
                                            <div class="h-2 w-20 rounded bg-gray-200"></div>
                                            <div class="h-2 w-16 rounded bg-gray-100"></div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-green-600">8.750€</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Système d'Événements --}}
            <div class="flex flex-col lg:flex-row-reverse gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-purple-100 px-4 py-2 text-purple-700">
                        <x-heroicon-o-calendar class="h-4 w-4" />
                        <span class="text-sm font-medium">Événements</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Un système d'événements unifié</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Une approche simple : un seul système pour gérer les étapes de projet ET la facturation.
                        Créez des tâches ou des factures avec la même interface.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-list-bullet class="h-5 w-5 text-purple-600" />
                            <span class="text-gray-700">Événements "Étape" pour les tâches</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-receipt-refund class="h-5 w-5 text-purple-600" />
                            <span class="text-gray-700">Événements "Facturation" pour les paiements</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-clock class="h-5 w-5 text-purple-600" />
                            <span class="text-gray-700">Timeline automatique des projets</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-purple-50 to-indigo-100 p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="h-3 w-1/3 rounded bg-purple-200"></div>
                                <div class="rounded-full bg-purple-600 px-3 py-1">
                                    <div class="h-2 w-12 rounded bg-white"></div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="rounded-lg bg-white p-4">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                        <div class="h-2 w-20 rounded bg-gray-200"></div>
                                    </div>
                                    <div class="h-2 w-full rounded bg-gray-100"></div>
                                </div>
                                <div class="rounded-lg bg-white p-4">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                        <div class="h-2 w-24 rounded bg-gray-200"></div>
                                    </div>
                                    <div class="text-right text-sm font-semibold text-blue-600">2.500€</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Facturation --}}
            <div class="flex flex-col lg:flex-row gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-emerald-700">
                        <x-heroicon-o-calculator class="h-4 w-4" />
                        <span class="text-sm font-medium">Facturation</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Facturation intégrée</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Créez vos devis, factures et acomptes directement dans vos projets.
                        Suivez les paiements et gardez un œil sur votre trésorerie.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-document-text class="h-5 w-5 text-emerald-600" />
                            <span class="text-gray-700">Devis et factures professionnels</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-credit-card class="h-5 w-5 text-emerald-600" />
                            <span class="text-gray-700">Suivi des paiements</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-arrow-trending-up class="h-5 w-5 text-emerald-600" />
                            <span class="text-gray-700">Analytics de trésorerie</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-emerald-50 to-green-100 p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="h-3 w-1/4 rounded bg-emerald-200"></div>
                                <div class="text-right">
                                    <div class="h-2 w-16 rounded bg-emerald-200"></div>
                                </div>
                            </div>
                            <div class="rounded-lg bg-white p-4">
                                <div class="mb-3 flex justify-between items-center">
                                    <div class="h-2 w-20 rounded bg-gray-200"></div>
                                    <div class="text-lg font-bold text-emerald-600">5.000€</div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <div class="h-2 w-32 rounded bg-gray-100"></div>
                                        <div class="h-2 w-12 rounded bg-gray-100"></div>
                                    </div>
                                    <div class="flex justify-between">
                                        <div class="h-2 w-28 rounded bg-gray-100"></div>
                                        <div class="h-2 w-16 rounded bg-gray-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics --}}
            <div class="flex flex-col lg:flex-row-reverse gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-orange-100 px-4 py-2 text-orange-700">
                        <x-heroicon-o-chart-bar class="h-4 w-4" />
                        <span class="text-sm font-medium">Analytics</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Pilotez votre activité</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Visualisez vos revenus, identifiez vos meilleurs clients, suivez l'évolution de vos projets.
                        Des insights précieux pour développer votre business.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-presentation-chart-line class="h-5 w-5 text-orange-600" />
                            <span class="text-gray-700">Graphiques revenus facturé vs encaissé</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-banknotes class="h-5 w-5 text-orange-600" />
                            <span class="text-gray-700">Revenus par client et par projet</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-clock class="h-5 w-5 text-orange-600" />
                            <span class="text-gray-700">Suivi des délais et performances</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-orange-50 to-yellow-100 p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="h-3 w-1/3 rounded bg-orange-200"></div>
                                <div class="rounded bg-orange-600 px-3 py-1">
                                    <div class="h-2 w-12 rounded bg-white"></div>
                                </div>
                            </div>
                            
                            <!-- Statistiques principales -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-lg bg-white p-3">
                                    <div class="text-xs text-orange-600 font-medium mb-1">Facturé</div>
                                    <div class="text-lg font-bold text-orange-700">18.750€</div>
                                </div>
                                <div class="rounded-lg bg-white p-3">
                                    <div class="text-xs text-green-600 font-medium mb-1">Encaissé</div>
                                    <div class="text-lg font-bold text-green-700">14.200€</div>
                                </div>
                            </div>
                            
                            <!-- Graphique en barres plus clair -->
                            <div class="rounded-lg bg-white p-4">
                                <div class="flex items-end justify-between h-12 gap-2">
                                    <div class="w-8 bg-orange-300 rounded-t" style="height: 60%;"></div>
                                    <div class="w-8 bg-orange-400 rounded-t" style="height: 80%;"></div>
                                    <div class="w-8 bg-green-400 rounded-t" style="height: 100%;"></div>
                                    <div class="w-8 bg-green-300 rounded-t" style="height: 45%;"></div>
                                    <div class="w-8 bg-orange-200 rounded-t" style="height: 30%;"></div>
                                    <div class="w-8 bg-green-500 rounded-t" style="height: 90%;"></div>
                                </div>
                                <div class="flex justify-between mt-2 text-xs text-gray-400">
                                    <span>Jan</span>
                                    <span>Fév</span>
                                    <span>Mar</span>
                                    <span>Avr</span>
                                    <span>Mai</span>
                                    <span>Juin</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Feature List --}}
<section class="bg-gray-50 py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-4">
                Les fonctionnalités principales
            </h2>
            <p class="text-lg text-gray-600">
                Les fonctionnalités essentielles pour organiser et suivre votre activité professionnelle
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Gestion Client --}}
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100">
                    <x-heroicon-o-users class="h-6 w-6 text-blue-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Gestion des clients</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Fiches clients complètes
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Coordonnées et notes privées
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Historique des projets
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Statistiques de revenus
                    </li>
                </ul>
            </div>

            {{-- Projets --}}
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-100">
                    <x-heroicon-o-briefcase class="h-6 w-6 text-indigo-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Suivi des projets</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Organisation par client
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Gestion des budgets
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Suivi des délais
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Statuts automatiques
                    </li>
                </ul>
            </div>

            {{-- Événements --}}
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100">
                    <x-heroicon-o-calendar class="h-6 w-6 text-purple-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Système d'événements</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Événements "Étape"
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Événements "Facturation"
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Timeline automatique
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Notifications intelligentes
                    </li>
                </ul>
            </div>

            {{-- Facturation --}}
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-100">
                    <x-heroicon-o-calculator class="h-6 w-6 text-emerald-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Facturation</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Devis et factures
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Acomptes et échelonnement
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Suivi des paiements
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Historique des paiements
                    </li>
                </ul>
            </div>

            {{-- Analytics --}}
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-orange-100">
                    <x-heroicon-o-chart-bar class="h-6 w-6 text-orange-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Tableaux de bord
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Graphiques de revenus
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Statistiques clients
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Indicateurs de performance
                    </li>
                </ul>
            </div>

            {{-- Interface --}}
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-gray-100">
                    <x-heroicon-o-computer-desktop class="h-6 w-6 text-gray-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Interface utilisateur</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Design moderne
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Responsive mobile
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Navigation intuitive
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Thème sombre/clair
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-6">
                Prêt à découvrir Stone ?
            </h2>
            <p class="text-xl text-gray-600 mb-8">
                Créez votre compte gratuitement et explorez toutes les fonctionnalités pendant 30 jours.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}"
                   class="group inline-flex items-center gap-3 bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-3 text-lg font-semibold text-white shadow-xl hover:from-gray-800 hover:to-gray-700 transition-all duration-200 rounded-lg">
                    Créer mon compte gratuitement
                    <x-heroicon-o-arrow-right class="h-5 w-5 transition-transform group-hover:translate-x-1" />
                </a>
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
