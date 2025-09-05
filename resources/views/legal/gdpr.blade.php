@extends('layouts.guest')

@section('title', 'RGPD - Stone')
@section('description', 'Informations RGPD de Stone. Conformité au Règlement Général sur la Protection des Données et gestion de vos droits.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "RGPD - Stone",
    "description": "Informations sur la conformité RGPD et la protection des données personnelles",
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
                "name": "RGPD"
            }
        ]
    },
    "isPartOf": {
        "@type": "WebSite",
        "name": "Stone",
        "url": "{{ url('/') }}"
    }
}
@endsection

@section('content')
    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-0 lg:px-4 py-12 mb-16">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">RGPD - Protection des données</h1>
                <p class="text-gray-600">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
            </div>

            <!-- Introduction RGPD -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Conformité RGPD</h2>
                        <p class="text-gray-700">
                            {{ config('app.name') }} respecte le Règlement Général sur la Protection des Données (RGPD) 
                            et s'engage à protéger vos données personnelles avec le plus grand soin.
                        </p>
                    </div>
                </div>
            </div>

            <div class="prose prose-blue max-w-none">
                <!-- Qu'est-ce que le RGPD -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Qu'est-ce que le RGPD ?</h2>
                    <p class="text-gray-700 mb-4">
                        Le Règlement Général sur la Protection des Données (RGPD) est un règlement européen 
                        entré en vigueur le 25 mai 2018. Il harmonise et renforce la protection des données 
                        personnelles des résidents de l'Union Européenne.
                    </p>
                    <p class="text-gray-700 mb-4">
                        Ce règlement vous donne plus de contrôle sur vos données personnelles et impose 
                        aux organisations des obligations strictes en matière de protection des données.
                    </p>
                </section>

                <!-- Vos droits -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Vos droits fondamentaux</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Droit à l'information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Droit à l'information</h3>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Être informé de manière claire et transparente sur l'utilisation de vos données personnelles.
                            </p>
                        </div>

                        <!-- Droit d'accès -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Droit d'accès</h3>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Obtenir la confirmation que vos données sont traitées et accéder à ces données.
                            </p>
                        </div>

                        <!-- Droit de rectification -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Droit de rectification</h3>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Faire corriger des données inexactes ou compléter des données incomplètes.
                            </p>
                        </div>

                        <!-- Droit à l'effacement -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Droit à l'effacement</h3>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Demander la suppression de vos données personnelles sous certaines conditions.
                            </p>
                        </div>

                        <!-- Droit à la limitation -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Droit à la limitation</h3>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Obtenir la limitation du traitement de vos données dans certains cas.
                            </p>
                        </div>

                        <!-- Droit à la portabilité -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Droit à la portabilité</h3>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Récupérer vos données dans un format structuré et lisible par machine.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Comment exercer vos droits -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Comment exercer vos droits ?</h2>
                    
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Moyens de contact</h3>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 mb-1">Email</h4>
                                <p class="text-sm text-gray-600">{{ config('contact.emails.legal') }}</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 mb-1">Paramètres</h4>
                                <p class="text-sm text-gray-600">Depuis votre compte</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 mb-1">Contact</h4>
                                <p class="text-sm text-gray-600">
                                    <a href="{{ route('support.contact') }}" class="text-blue-600 hover:text-blue-800">Formulaire</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="font-medium text-gray-900 mb-2">Délai de réponse</h4>
                        <p class="text-gray-700 text-sm mb-2">
                            Nous nous engageons à répondre à votre demande dans un délai maximum d'<strong>un mois</strong> 
                            à compter de sa réception.
                        </p>
                        <p class="text-gray-700 text-sm">
                            Dans certains cas complexes, ce délai peut être prolongé de deux mois supplémentaires. 
                            Nous vous en informerions alors dans le mois suivant la réception de votre demande.
                        </p>
                    </div>
                </section>

                <!-- Nos engagements -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Nos engagements RGPD</h2>
                    
                    <div class="space-y-6">
                        <!-- Privacy by Design -->
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Protection dès la conception</h3>
                                <p class="text-gray-700">
                                    Nous intégrons la protection des données dès la conception de nos fonctionnalités 
                                    et appliquons le principe de minimisation des données.
                                </p>
                            </div>
                        </div>

                        <!-- Sécurité -->
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Sécurité renforcée</h3>
                                <p class="text-gray-700">
                                    Chiffrement des données, authentification forte, surveillance continue 
                                    et plan de réponse aux incidents de sécurité.
                                </p>
                            </div>
                        </div>

                        <!-- Transparence -->
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Transparence totale</h3>
                                <p class="text-gray-700">
                                    Information claire sur nos pratiques, finalités du traitement et partage 
                                    de données dans notre politique de confidentialité.
                                </p>
                            </div>
                        </div>

                        <!-- Formation -->
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Formation continue</h3>
                                <p class="text-gray-700">
                                    Notre équipe est régulièrement formée aux enjeux de protection des données 
                                    et aux évolutions réglementaires.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Violation de données -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Gestion des violations de données</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-3">En cas de violation de données</h3>
                        <div class="space-y-3 text-gray-700">
                            <div class="flex items-start space-x-2">
                                <span class="w-5 h-5 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs text-gray-600">1</span>
                                </span>
                                <p class="text-sm">Notification à la CNIL dans les 72 heures si nécessaire</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="w-5 h-5 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs text-gray-600">2</span>
                                </span>
                                <p class="text-sm">Information des utilisateurs concernés si le risque est élevé</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="w-5 h-5 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs text-gray-600">3</span>
                                </span>
                                <p class="text-sm">Mesures correctives immédiates pour limiter l'impact</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- DPO -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Contact pour vos données</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Contact</h3>
                                <div class="space-y-1 text-gray-700">
                                    <p><strong>Responsable :</strong> {{ config('contact.company.owner') }}</p>
                                    <p><strong>Email :</strong> <a href="mailto:{{ config('contact.emails.legal') }}" class="text-blue-600 hover:text-blue-800">{{ config('contact.emails.legal') }}</a></p>
                                    <p><strong>Mission :</strong> Veiller au respect du RGPD et traiter vos demandes relatives aux données</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Autorité de contrôle -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">7. Autorité de contrôle</h2>
                    <p class="text-gray-700 mb-4">
                        Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire 
                        une réclamation auprès de la Commission Nationale de l'Informatique et des Libertés (CNIL).
                    </p>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Coordonnées CNIL</h4>
                                <div class="space-y-1 text-gray-700 text-sm">
                                    <p><strong>Adresse :</strong> 3 Place de Fontenoy, 75007 Paris</p>
                                    <p><strong>Téléphone :</strong> 01 53 73 22 22</p>
                                    <p><strong>Site web :</strong> <a href="https://www.cnil.fr" class="text-blue-600 hover:text-blue-800">www.cnil.fr</a></p>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Réclamation en ligne</h4>
                                <p class="text-gray-700 text-sm mb-2">
                                    Vous pouvez déposer une plainte directement en ligne sur le site de la CNIL.
                                </p>
                                <a href="https://www.cnil.fr/fr/plaintes" target="_blank" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                    Déposer une plainte
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Mises à jour -->
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">8. Mises à jour de cette page</h2>
                    <p class="text-gray-700 mb-4">
                        Cette page RGPD peut être mise à jour pour refléter les changements dans nos 
                        pratiques ou les évolutions réglementaires.
                    </p>
                    <p class="text-gray-700">
                        Nous vous encourageons à consulter régulièrement cette page. La date de dernière 
                        mise à jour est indiquée en haut de la page.
                    </p>
                </section>
            </div>
        </div>
    </main>
@endsection