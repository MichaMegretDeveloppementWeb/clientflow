@extends('layouts.guest')

@section('title', 'Centre d\'aide - Stone')
@section('description', 'Centre d\'aide et FAQ pour Stone. Trouvez rapidement les réponses à vos questions sur notre plateforme de gestion client.')

@section('json-ld-schema')
{
    "@type": "FAQPage",
    "name": "Centre d'aide - Stone",
    "description": "Centre d'aide et FAQ pour Stone. Assistance complète pour votre gestion client.",
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
                "name": "Centre d'aide"
            }
        ]
    },
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Comment créer un nouveau client ?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Rendez-vous dans la section Clients et cliquez sur 'Nouveau client'. Remplissez les informations requises et enregistrez."
            }
        },
        {
            "@type": "Question",
            "name": "Comment gérer mes projets ?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "La section Projets vous permet de créer, suivre et gérer vos projets client avec un système complet de suivi des étapes et facturation."
            }
        }
    ]
}
@endsection

@section('content')

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Comment pouvons-nous vous aider ?</h1>
            <p class="text-xl text-gray-600 mb-8">Trouvez rapidement les réponses à vos questions</p>

        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <a href="{{ route('support.documentation') }}" class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 group hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Documentation</h3>
                <p class="text-gray-600">Guides détaillés et tutoriels pour utiliser toutes les fonctionnalités</p>
            </a>

            <a href="{{ route('support.contact') }}" class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 group hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nous contacter</h3>
                <p class="text-gray-600">Besoin d'aide personnalisée ? Notre équipe est là pour vous accompagner</p>
            </a>

            <a href="{{ route('support.community') }}" class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 group hover:-translate-y-1">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Communauté</h3>
                <p class="text-gray-600">Échangez avec d'autres utilisateurs et partagez vos expériences</p>
            </a>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Questions fréquentes</h2>

            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Comment créer un nouveau client ?</h3>
                    <p class="text-gray-600">Rendez-vous dans la section "Clients" depuis le menu principal, puis cliquez sur le bouton "Nouveau client". Remplissez les informations requises comme le nom, l'email et les coordonnées, puis enregistrez. Vous pourrez ensuite créer des projets pour ce client.</p>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Comment gérer mes projets ?</h3>
                    <p class="text-gray-600">La section "Projets" vous permet de créer et suivre vos projets client. Pour chaque projet, vous pouvez définir des étapes, gérer la facturation, et suivre l'avancement. Utilisez le tableau de bord pour avoir une vue d'ensemble de tous vos projets en cours.</p>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Comment fonctionne le système d'événements ?</h3>
                    <p class="text-gray-600">Les événements permettent de suivre les étapes d'un projet (réunions, livrables, etc.) et la facturation (devis, factures, paiements). Chaque événement peut être de type "étape" ou "facturation" avec des informations spécifiques selon le type.</p>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Puis-je exporter mes données ?</h3>
                    <p class="text-gray-600">Oui, vous pouvez exporter vos données clients, projets et événements dans différents formats (PDF, Excel) depuis les sections correspondantes. Utilisez les boutons d'export disponibles dans chaque liste.</p>
                </div>

                <!-- FAQ Item 5 -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Comment sécuriser mon compte ?</h3>
                    <p class="text-gray-600">Utilisez un mot de passe fort et unique pour votre compte. Vous pouvez modifier vos informations de sécurité dans la section "Paramètres" > "Mot de passe". Déconnectez-vous toujours après utilisation sur un ordinateur partagé.</p>
                </div>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="text-center mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Vous ne trouvez pas ce que vous cherchez ?</h2>
            <p class="text-gray-600 mb-8">Notre équipe support est là pour vous aider</p>
            <a href="{{ route('support.contact') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Nous contacter
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </main>

@endsection
