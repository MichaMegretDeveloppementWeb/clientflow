@extends('layouts.guest')

@section('title', 'Communauté - Stone')
@section('description', 'Rejoignez la communauté Stone. Échangez avec d\'autres utilisateurs, partagez vos expériences et découvrez de nouvelles façons d\'optimiser votre gestion client.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Communauté Stone",
    "description": "Communauté d'utilisateurs pour échanger et partager les meilleures pratiques",
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
                "name": "Communauté"
            }
        ]
    },
    "mainEntity": {
        "@type": "Organization",
        "name": "Stone",
        "description": "Plateforme de gestion client pour freelancers et petites agences"
    }
}
@endsection

@section('content')

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Rejoignez la communauté {{ config('app.name') }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Échangez avec d'autres freelancers et agences, partagez vos expériences et découvrez les meilleures pratiques pour optimiser votre gestion client.</p>
        </div>

        <!-- Main Community Platforms -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- GitHub -->
            <a href="{{ config('social.github') }}" target="_blank" class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 group hover:-translate-y-1">
                <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.30 3.297-1.30.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">GitHub</h3>
                <p class="text-gray-600">Code source ouvert, issues et discussions techniques. Participez au développement de Stone.</p>
            </a>

            <!-- LinkedIn -->
            <a href="{{ config('social.linkedin') }}" target="_blank" class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 group hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-500 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">LinkedIn</h3>
                <p class="text-gray-600">Réseau professionnel, actualités business et networking avec l'équipe Stone.</p>
            </a>
        </div>

        <!-- Social Media -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-16">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 text-center">Suivez-nous sur les réseaux sociaux</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ config('social.twitter') }}" target="_blank" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center mb-2 group-hover:bg-blue-300 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Twitter/X</span>
                </a>

                <a href="{{ config('social.facebook') }}" target="_blank" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mb-2 group-hover:bg-blue-400 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Facebook</span>
                </a>

                <a href="{{ config('social.instagram') }}" target="_blank" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-violet-500 rounded-lg flex items-center justify-center mb-2 group-hover:from-pink-400 group-hover:to-violet-400 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.75 2h8.5C19.8 2 22 4.2 22 7.75v8.5c0 3.55-2.2 5.75-5.75 5.75h-8.5C4.2 22 2 19.8 2 16.25v-8.5C2 4.2 4.2 2 7.75 2zM7.75 4C5.3 4 4 5.3 4 7.75v8.5c0 2.45 1.3 3.75 3.75 3.75h8.5c2.45 0 3.75-1.3 3.75-3.75v-8.5C20 5.3 18.7 4 16.25 4h-8.5zm8.75 2.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5zM12 7.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Instagram</span>
                </a>

                <a href="{{ route('support.contact') }}" class="flex flex-col items-center p-4 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mb-2 group-hover:bg-green-400 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Contact</span>
                </a>
            </div>
        </div>

        <!-- Recent GitHub Discussions -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Discussions récentes</h2>
                <a href="{{ config('social.github') }}/discussions" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Participer aux discussions →
                </a>
            </div>

            <div id="discussions-loading" class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600">Chargement des discussions...</p>
            </div>

            <div id="discussions-content" class="hidden space-y-4">
                <!-- Les discussions seront insérées ici via JavaScript -->
            </div>

            <div id="discussions-error" class="hidden text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-gray-600">Impossible de charger les discussions</p>
                <p class="text-sm text-gray-500 mt-2">Vous pouvez consulter directement les discussions sur GitHub</p>
            </div>
        </div>

        <!-- Community Guidelines -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Règles de bonne conduite</h2>

            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        À faire
                    </h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Soyez respectueux et bienveillant envers tous les membres
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Partagez vos expériences et bonnes pratiques
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Aidez les nouveaux utilisateurs avec leurs questions
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Utilisez des titres descriptifs pour vos messages
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Recherchez avant de poser une question déjà posée
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        À éviter
                    </h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Spam, publicité non autorisée ou contenu hors-sujet
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Langage offensant ou discriminatoire
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Partage d'informations confidentielles ou sensibles
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Demandes de support urgent (utilisez le support officiel)
                        </li>
                        <li class="flex items-start">
                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Discussions politiques ou polémiques non liées
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Open Source Project -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl shadow-lg border border-green-200 p-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ config('app.name') }} est open source</h2>
            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                {{ config('app.name') }} est un projet open source. Rejoignez notre communauté de contributeurs et participez à l'évolution de la plateforme.
            </p>

            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center justify-center">
                    Comment contribuer
                </h3>
                <div class="grid md:grid-cols-2 gap-x-5 gap-y-8 p-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.30 3.297-1.30.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col items-start">
                            <h4 class="font-medium text-gray-900 mb-1">Fork & Pull Request</h4>
                            <p class="text-sm text-gray-600 text-left">Fork le repository et proposez vos améliorations</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col items-start">
                            <h4 class="font-medium text-gray-900 mb-1">Issues & Bugs</h4>
                            <p class="text-sm text-gray-600 text-left">Signalez des bugs ou suggérez des fonctionnalités</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col items-start">
                            <h4 class="font-medium text-gray-900 mb-1">Documentation</h4>
                            <p class="text-sm text-gray-600 text-left">Aidez à améliorer la documentation</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col items-start">
                            <h4 class="font-medium text-gray-900 mb-1">Retours d'expérience</h4>
                            <p class="text-sm text-gray-600 text-left">Partagez vos retours d'expérience</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadGitHubDiscussions();
});

async function loadGitHubDiscussions() {
    const loadingEl = document.getElementById('discussions-loading');
    const contentEl = document.getElementById('discussions-content');
    const errorEl = document.getElementById('discussions-error');

    try {
        const response = await fetch('{{ route('api.github.discussions') }}');
        const data = await response.json();

        if (!response.ok) {
            throw new Error('Failed to fetch discussions');
        }

        // Masquer le loading
        loadingEl.classList.add('hidden');

        if (data.discussions && data.discussions.length > 0) {
            // Afficher les discussions
            contentEl.innerHTML = data.discussions.map(discussion => createDiscussionCard(discussion)).join('');
            contentEl.classList.remove('hidden');
        } else {
            // Pas de discussions
            errorEl.querySelector('p.text-gray-600').textContent = 'Aucune discussion récente';
            errorEl.classList.remove('hidden');
        }

    } catch (error) {
        console.error('Error loading GitHub discussions:', error);
        loadingEl.classList.add('hidden');
        errorEl.classList.remove('hidden');
    }
}

function createDiscussionCard(discussion) {
    const timeAgo = formatTimeAgo(discussion.updated_at);

    return `
        <a href="${discussion.html_url}" target="_blank" class="block bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors">
            <div class="flex items-start space-x-3">
                <img src="${discussion.avatar_url}" alt="${discussion.author}" class="w-8 h-8 rounded-full flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-medium text-gray-900 truncate">${escapeHtml(discussion.title)}</h4>
                        <span class="text-xs text-gray-500 ml-2">${timeAgo}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mb-2">
                        <span>par ${discussion.author}</span>
                        <span class="mx-2">•</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs">${discussion.category}</span>
                        ${discussion.comments_count > 0 ? `<span class="mx-2">•</span><span>${discussion.comments_count} réponse${discussion.comments_count > 1 ? 's' : ''}</span>` : ''}
                    </div>
                    ${discussion.body_preview ? `<p class="text-sm text-gray-600">${escapeHtml(discussion.body_preview)}</p>` : ''}
                </div>
            </div>
        </a>
    `;
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffMins < 60) {
        return diffMins + ' min';
    } else if (diffHours < 24) {
        return diffHours + 'h';
    } else if (diffDays < 7) {
        return diffDays + 'j';
    } else {
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush
