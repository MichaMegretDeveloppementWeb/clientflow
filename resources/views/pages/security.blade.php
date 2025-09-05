@extends('layouts.guest')

@section('title', 'Sécurité - Stone')
@section('description', 'Découvrez comment Stone protège vos données et celles de vos clients. Sécurité renforcée, conformité RGPD, serveurs français et chiffrement de bout en bout.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Sécurité - Stone",
    "description": "Découvrez comment Stone protège vos données et celles de vos clients. Sécurité renforcée, conformité RGPD, serveurs français et chiffrement de bout en bout.",
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
                "name": "Sécurité"
            }
        ]
    },
    "mainEntity": {
        "@type": "SecurityAndPrivacyPolicy",
        "name": "Politique de sécurité Stone",
        "description": "Stone garantit la protection de vos données avec des serveurs français, une conformité RGPD stricte et un chiffrement de bout en bout.",
        "securityCredentials": [
            "Serveurs hébergés en France",
            "Conformité RGPD",
            "Chiffrement AES-256",
            "Authentification à deux facteurs",
            "Sauvegardes automatiques chiffrées",
            "Monitoring 24/7"
        ]
    }
}
@endsection

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-b from-green-50/50 via-white to-white pt-24 pb-0 sm:pt-32 sm:pb-0">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-green-700">
                <x-heroicon-o-shield-check class="h-5 w-5" />
                <span class="text-sm font-medium">Sécurité de niveau entreprise</span>
            </div>
            <h1 class="mx-auto max-w-4xl text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                Vos données en
                <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    sécurité maximale
                </span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-xl leading-8 text-gray-600">
                Stone applique les standards de sécurité les plus exigeants pour protéger vos données professionnelles
                et celles de vos clients. Conformité RGPD, serveurs français, chiffrement de bout en bout.
            </p>
        </div>
    </div>
</section>

{{-- Security Features --}}
<section class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Protection multicouche
            </h2>
            <p class="mt-4 text-lg leading-8 text-gray-600">
                Chaque aspect de Stone est pensé pour garantir la confidentialité et l'intégrité de vos données
            </p>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            {{-- Données & Hébergement --}}
            <div class="flex flex-col lg:flex-row gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                        <x-heroicon-o-server class="h-4 w-4" />
                        <span class="text-sm font-medium">Hébergement français</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Serveurs 100% français</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Vos données ne quittent jamais le territoire français. Hébergement dans des datacenters
                        certifiés ISO 27001 avec une disponibilité garantie de 99,9%.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-map-pin class="h-5 w-5 text-blue-600" />
                            <span class="text-gray-700">Serveurs hébergés en France</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-shield-check class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Conformité juridique française</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-clock class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Uptime 99,9% garanti</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl p-8 shadow-xl">
                        <div class="space-y-6">
                            {{-- Server representation --}}
                            <div class="flex items-center justify-center">
                                <div class="relative">
                                    <div class="h-32 w-24 bg-gray-800 rounded-lg shadow-lg">
                                        <div class="absolute top-2 left-2 right-2 h-1 bg-green-400 rounded"></div>
                                        <div class="absolute top-4 left-2 right-2 h-1 bg-blue-400 rounded"></div>
                                        <div class="absolute top-6 left-2 right-2 h-1 bg-green-400 rounded"></div>
                                        <div class="space-y-2 p-3 pt-10">
                                            <div class="h-2 bg-gray-600 rounded"></div>
                                            <div class="h-2 bg-gray-600 rounded"></div>
                                            <div class="h-2 bg-gray-600 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="absolute -top-2 -right-2 h-6 w-6 bg-green-500 rounded-full flex items-center justify-center">
                                        <div class="h-2 w-2 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- French flag --}}
                            <div class="flex items-center justify-center gap-2">
                                <div class="flex rounded overflow-hidden shadow-sm">
                                    <div class="h-6 w-4 bg-blue-600"></div>
                                    <div class="h-6 w-4 bg-white"></div>
                                    <div class="h-6 w-4 bg-red-600"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700">100% français</span>
                            </div>
                            {{-- Stats --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="text-center p-3 bg-white rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">99,9%</div>
                                    <div class="text-xs text-gray-600">Uptime</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">24/7</div>
                                    <div class="text-xs text-gray-600">Monitoring</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chiffrement --}}
            <div class="flex flex-col lg:flex-row-reverse gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-purple-100 px-4 py-2 text-purple-700">
                        <x-heroicon-o-lock-closed class="h-4 w-4" />
                        <span class="text-sm font-medium">Chiffrement avancé</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Connexions sécurisées</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Vos échanges avec Stone sont protégés par SSL/TLS.
                        Les mots de passe sont hachés et les sessions sécurisées.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-lock-closed class="h-5 w-5 text-purple-600" />
                            <span class="text-gray-700">Certificat SSL/TLS</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-key class="h-5 w-5 text-purple-600" />
                            <span class="text-gray-700">Mots de passe hachés</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-shield-check class="h-5 w-5 text-purple-600" />
                            <span class="text-gray-700">Sessions sécurisées</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center h-16 w-16 bg-purple-600 rounded-full mb-4">
                                    <x-heroicon-o-lock-closed class="h-8 w-8 text-white" />
                                </div>
                                <div class="text-sm font-medium text-purple-700">Connexions SSL/TLS</div>
                            </div>
                            {{-- SSL visualization --}}
                            <div class="space-y-3">
                                <div class="bg-white rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-2">Navigateur</div>
                                    <div class="font-mono text-sm flex items-center gap-2">
                                        <x-heroicon-o-computer-desktop class="h-4 w-4 text-blue-600" />
                                        <span>Votre ordinateur</span>
                                    </div>
                                </div>
                                <div class="flex justify-center">
                                    <div class="flex items-center gap-2 text-xs text-purple-600 font-medium">
                                        <x-heroicon-o-lock-closed class="h-4 w-4" />
                                        <span>SSL/TLS</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-2">Serveur</div>
                                    <div class="font-mono text-sm flex items-center gap-2">
                                        <x-heroicon-o-server class="h-4 w-4 text-green-600" />
                                        <span>{{ parse_url(config('app.url'), PHP_URL_HOST) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center text-xs text-purple-600 font-medium">
                                🔒 Connexion sécurisée
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RGPD --}}
            <div class="flex flex-col lg:flex-row gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-green-700">
                        <x-heroicon-o-document-check class="h-4 w-4" />
                        <span class="text-sm font-medium">Conformité RGPD</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">100% conforme RGPD</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Stone respecte scrupuleusement le Règlement Général sur la Protection des Données.
                        Vos droits et ceux de vos clients sont garantis en toute transparence.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-eye class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Transparence totale sur vos données</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-trash class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Droit à l'effacement respecté</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-arrow-down-tray class="h-5 w-5 text-green-600" />
                            <span class="text-gray-700">Export de vos données à tout moment</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center justify-center h-12 w-12 bg-green-600 rounded-full mb-3">
                                    <x-heroicon-o-shield-check class="h-6 w-6 text-white" />
                                </div>
                                <div class="text-sm font-medium text-green-700">Conformité RGPD</div>
                            </div>
                            {{-- RGPD rights --}}
                            <div class="space-y-3">
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm">Droit d'accès</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm">Droit de rectification</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm">Droit à l'effacement</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                    <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm">Droit à la portabilité</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Authentification --}}
            <div class="flex flex-col lg:flex-row-reverse gap-8 items-center">
                <div class="lg:w-1/2">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-orange-100 px-4 py-2 text-orange-700">
                        <x-heroicon-o-finger-print class="h-4 w-4" />
                        <span class="text-sm font-medium">Authentification sécurisée</span>
                    </div>
                    <h3 class="mb-4 text-2xl font-bold text-gray-900">Accès sécurisé</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Connexion sécurisée avec email et mot de passe.
                        Déconnexion automatique et protection contre les attaques basiques.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-at-symbol class="h-5 w-5 text-orange-600" />
                            <span class="text-gray-700">Connexion par email/mot de passe</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-clock class="h-5 w-5 text-orange-600" />
                            <span class="text-gray-700">Déconnexion automatique</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-heroicon-o-shield-exclamation class="h-5 w-5 text-orange-600" />
                            <span class="text-gray-700">Protection CSRF</span>
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2">
                    <div class="rounded-2xl p-8 shadow-xl">
                        <div class="space-y-4">
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center justify-center h-12 w-12 bg-orange-600 rounded-full mb-3">
                                    <x-heroicon-o-at-symbol class="h-6 w-6 text-white" />
                                </div>
                                <div class="text-sm font-medium text-orange-700">Authentification simple</div>
                            </div>
                            {{-- Login flow --}}
                            <div class="space-y-3">
                                <div class="bg-white rounded-lg p-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <x-heroicon-o-envelope class="h-4 w-4 text-blue-600" />
                                        <span class="text-sm">Email</span>
                                    </div>
                                </div>
                                <div class="flex justify-center">
                                    <x-heroicon-o-plus class="h-4 w-4 text-orange-600" />
                                </div>
                                <div class="bg-white rounded-lg p-3">
                                    <div class="flex items-center  justify-center gap-3">
                                        <x-heroicon-o-key class="h-4 w-4 text-orange-600" />
                                        <span class="text-sm">Mot de passe</span>
                                    </div>
                                </div>
                                <div class="flex justify-center">
                                    <x-heroicon-o-arrow-down class="h-4 w-4 text-orange-600" />
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <span class="text-sm font-medium text-green-700">✅ Connexion réussie</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Security Certifications --}}
<section class="bg-gray-50 py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-4">
                Certifications et standards
            </h2>
            <p class="text-lg text-gray-600">
                Stone répond aux exigences de sécurité les plus strictes du marché
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            {{-- RGPD --}}
            <div class="bg-white rounded-xl p-6 shadow-sm text-center">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-green-100">
                    <x-heroicon-o-document-check class="h-6 w-6 text-green-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">RGPD</h3>
                <p class="text-sm text-gray-600">
                    Respect du Règlement Général sur la Protection des Données
                </p>
            </div>

            {{-- SSL --}}
            <div class="bg-white rounded-xl p-6 shadow-sm text-center">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100">
                    <x-heroicon-o-lock-closed class="h-6 w-6 text-blue-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Certificat SSL</h3>
                <p class="text-sm text-gray-600">
                    Connexions chiffrées et sécurisées pour tous les échanges
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Security Best Practices --}}
<section class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-4">
                Vos données, nos engagements
            </h2>
            <p class="text-lg text-gray-600">
                Stone s'engage à maintenir le plus haut niveau de sécurité pour vos données professionnelles
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Monitoring --}}
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100">
                    <x-heroicon-o-eye class="h-6 w-6 text-blue-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Surveillance 24/7</h3>
                <p class="text-gray-600 mb-4">
                    Monitoring continu de nos systèmes avec alertes automatiques en cas d'anomalie.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Détection des intrusions
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Analyse comportementale
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Logs d'activité complets
                    </li>
                </ul>
            </div>

            {{-- Backups --}}
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-green-100">
                    <x-heroicon-o-cloud-arrow-up class="h-6 w-6 text-green-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Sauvegardes chiffrées</h3>
                <p class="text-gray-600 mb-4">
                    Sauvegardes automatiques quotidiennes, chiffrées et stockées sur plusieurs sites.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Sauvegarde quotidienne
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Réplication géographique
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Restauration rapide
                    </li>
                </ul>
            </div>

            {{-- Updates --}}
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100">
                    <x-heroicon-o-arrow-path class="h-6 w-6 text-purple-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Mises à jour sécurisées</h3>
                <p class="text-gray-600 mb-4">
                    Patches de sécurité appliqués automatiquement sans interruption de service.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Patches automatiques
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Tests de régression
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Zero-downtime deployment
                    </li>
                </ul>
            </div>

            {{-- Incident Response --}}
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-red-100">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Réponse aux incidents</h3>
                <p class="text-gray-600 mb-4">
                    Procédures d'urgence et équipe de réponse dédiée pour traiter tout incident de sécurité.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Équipe d'astreinte 24/7
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Plan de continuité
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Communication transparente
                    </li>
                </ul>
            </div>

            {{-- Audit --}}
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-orange-100">
                    <x-heroicon-o-magnifying-glass class="h-6 w-6 text-orange-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Audits réguliers</h3>
                <p class="text-gray-600 mb-4">
                    Audits de sécurité internes et externes pour maintenir nos standards de protection.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Pentests trimestriels
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Code review systématique
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Certification continue
                    </li>
                </ul>
            </div>

            {{-- Privacy --}}
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-100">
                    <x-heroicon-o-user-circle class="h-6 w-6 text-indigo-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Respect de la vie privée</h3>
                <p class="text-gray-600 mb-4">
                    Minimisation des données, anonymisation et respect strict de vos droits digitaux.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Collecte minimale
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Consentement éclairé
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-green-500" />
                        Anonymisation avancée
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Contact Security Team --}}
<section class="bg-gray-900 py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl mb-6">
                Questions sur la sécurité ?
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Notre équipe sécurité est disponible pour répondre à toutes vos questions techniques
                et vous accompagner dans l'évaluation de nos mesures de protection.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="mailto:security@stone-app.fr"
                   class="group inline-flex items-center gap-3 bg-white px-6 py-3 text-lg font-semibold text-gray-900 shadow-xl hover:bg-gray-100 transition-all duration-200 rounded-lg">
                    <x-heroicon-o-envelope class="h-5 w-5" />
                    Contacter l'équipe sécurité
                </a>
                <a href="{{ route('home') }}" class="text-gray-300 hover:text-white font-medium">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
