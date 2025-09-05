@extends('layouts.guest')

@section('title', 'Conditions générales d\'utilisation - Stone')
@section('description', 'Conditions générales d\'utilisation de Stone. Règles et conditions d\'utilisation de notre plateforme de gestion client.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Conditions générales d'utilisation - Stone",
    "description": "Règles et conditions d'utilisation de la plateforme Stone",
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
                "name": "Conditions générales d'utilisation"
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
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Conditions générales d'utilisation</h1>
                <p class="text-gray-600">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
            </div>

            <div class="prose prose-blue max-w-none">
                <!-- Introduction -->
                <section class="mb-8">
                    <p class="text-gray-700 mb-4">
                        Les présentes Conditions Générales d'Utilisation (CGU) régissent l'utilisation de la plateforme 
                        {{ config('app.name') }}, accessible à l'adresse {{ url('/') }}.
                    </p>
                    <p class="text-gray-700 mb-4">
                        En accédant et en utilisant {{ config('app.name') }}, vous acceptez pleinement et sans réserve 
                        les présentes CGU. Si vous n'acceptez pas ces conditions, vous devez cesser immédiatement 
                        d'utiliser nos services.
                    </p>
                    <p class="text-gray-700">
                        Ces CGU peuvent être modifiées à tout moment. Les modifications prennent effet dès leur 
                        publication sur le site. Il vous incombe de consulter régulièrement cette page.
                    </p>
                </section>

                <!-- Définitions -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Définitions</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="font-medium text-gray-900">Service</dt>
                                <dd class="text-gray-700">La plateforme {{ config('app.name') }} et l'ensemble de ses fonctionnalités</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Utilisateur</dt>
                                <dd class="text-gray-700">Toute personne physique ou morale utilisant le Service</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Compte</dt>
                                <dd class="text-gray-700">Espace personnel permettant d'accéder aux fonctionnalités du Service</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Contenu</dt>
                                <dd class="text-gray-700">Toute information, donnée ou fichier fourni par l'Utilisateur</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <!-- Objet -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Objet du service</h2>
                    <p class="text-gray-700 mb-4">
                        {{ config('app.name') }} est une plateforme de gestion client conçue pour les freelancers et 
                        petites agences. Elle permet de :
                    </p>
                    <ul class="text-gray-700 mb-4 space-y-2">
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Gérer ses clients et leurs informations
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Organiser et suivre ses projets
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Planifier et gérer les événements (étapes et facturation)
                        </li>
                        <li class="flex items-start">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            Générer des statistiques et rapports
                        </li>
                    </ul>
                </section>

                <!-- Accès au service -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Accès au service</h2>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">3.1 Création de compte</h3>
                    <p class="text-gray-700 mb-4">
                        L'utilisation de {{ config('app.name') }} nécessite la création d'un compte utilisateur. 
                        Vous devez fournir des informations exactes et complètes lors de l'inscription.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">3.2 Responsabilité du compte</h3>
                    <p class="text-gray-700 mb-4">
                        Vous êtes responsable de la confidentialité de vos identifiants de connexion. 
                        Toute utilisation de votre compte est présumée effectuée par vous.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">3.3 Accès technique</h3>
                    <p class="text-gray-700 mb-4">
                        L'accès au service nécessite une connexion internet et un navigateur compatible. 
                        Les coûts de connexion restent à votre charge.
                    </p>
                </section>

                <!-- Utilisation du service -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Utilisation du service</h2>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">4.1 Usage autorisé</h3>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-800 text-sm mb-2"><strong>Vous pouvez :</strong></p>
                        <ul class="text-gray-700 text-sm space-y-1">
                            <li>• Utiliser le service pour vos activités professionnelles légitimes</li>
                            <li>• Sauvegarder et gérer vos données client</li>
                            <li>• Générer des rapports et statistiques</li>
                            <li>• Inviter des collaborateurs (selon votre forfait)</li>
                        </ul>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">4.2 Usage interdit</h3>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-800 text-sm mb-2"><strong>Il est strictement interdit de :</strong></p>
                        <ul class="text-gray-700 text-sm space-y-1">
                            <li>• Utiliser le service à des fins illégales ou frauduleuses</li>
                            <li>• Tenter de contourner les mesures de sécurité</li>
                            <li>• Partager vos identifiants avec des tiers non autorisés</li>
                            <li>• Surcharger l'infrastructure avec un usage abusif</li>
                            <li>• Copier, reproduire ou redistribuer le service</li>
                            <li>• Utiliser des robots ou scripts automatisés non autorisés</li>
                        </ul>
                    </div>
                </section>

                <!-- Données et contenu -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Données et contenu utilisateur</h2>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">5.1 Propriété des données</h3>
                    <p class="text-gray-700 mb-4">
                        Vous conservez tous les droits de propriété sur les données que vous saisissez dans 
                        {{ config('app.name') }}. Nous ne revendiquons aucun droit sur votre contenu.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">5.2 Licence d'utilisation</h3>
                    <p class="text-gray-700 mb-4">
                        En utilisant le service, vous nous accordez une licence limitée pour stocker, traiter 
                        et afficher vos données uniquement dans le but de fournir le service.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">5.3 Sauvegarde et export</h3>
                    <p class="text-gray-700 mb-4">
                        Vous pouvez exporter vos données à tout moment. Nous recommandons d'effectuer 
                        régulièrement des sauvegardes de vos données importantes.
                    </p>
                </section>

                <!-- Disponibilité -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Disponibilité du service</h2>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">6.1 Engagement de disponibilité</h3>
                    <p class="text-gray-700 mb-4">
                        Nous nous efforçons de maintenir le service accessible 24h/24 et 7j/7. Cependant, 
                        nous ne pouvons garantir une disponibilité absolue.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">6.2 Maintenance</h3>
                    <p class="text-gray-700 mb-4">
                        Des interruptions peuvent survenir pour maintenance. Nous nous efforçons de les 
                        programmer aux heures de faible affluence et de vous en informer à l'avance.
                    </p>
                </section>

                <!-- Responsabilité -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">7. Limitation de responsabilité</h2>
                    
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="font-medium text-gray-900 mb-3">Important</h4>
                        <p class="text-gray-700 text-sm mb-4">
                            Le service est fourni "en l'état" sans garantie d'aucune sorte. Notre responsabilité 
                            est limitée au montant des sommes versées au cours des 12 derniers mois.
                        </p>
                        <p class="text-gray-700 text-sm">
                            Nous ne pourrons être tenus responsables des dommages indirects, pertes de profits, 
                            ou interruptions d'activité.
                        </p>
                    </div>
                </section>

                <!-- Protection des données -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">8. Protection des données personnelles</h2>
                    <p class="text-gray-700 mb-4">
                        Le traitement de vos données personnelles est régi par notre 
                        <a href="{{ route('legal.privacy') }}" class="text-blue-600 hover:text-blue-800">politique de confidentialité</a>, 
                        qui fait partie intégrante des présentes CGU.
                    </p>
                    <p class="text-gray-700">
                        Nous nous engageons à respecter le RGPD et à protéger vos données personnelles. 
                        Pour plus de détails, consultez notre <a href="{{ route('legal.gdpr') }}" class="text-blue-600 hover:text-blue-800">page RGPD</a>.
                    </p>
                </section>

                <!-- Durée et résiliation -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">9. Durée et résiliation</h2>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">9.1 Durée</h3>
                    <p class="text-gray-700 mb-4">
                        Ces CGU s'appliquent dès votre première utilisation du service et restent en vigueur 
                        tant que vous utilisez {{ config('app.name') }}.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">9.2 Résiliation par l'utilisateur</h3>
                    <p class="text-gray-700 mb-4">
                        Vous pouvez supprimer votre compte à tout moment depuis les paramètres de votre compte. 
                        Cette action est irréversible.
                    </p>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">9.3 Résiliation par Stone</h3>
                    <p class="text-gray-700 mb-4">
                        Nous pouvons suspendre ou supprimer votre compte en cas de violation des présentes CGU, 
                        après vous avoir notifié et donné la possibilité de corriger la situation.
                    </p>
                </section>

                <!-- Droit applicable -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">10. Droit applicable et juridictions</h2>
                    <p class="text-gray-700 mb-4">
                        Les présentes CGU sont régies par le droit français. En cas de litige, et après échec 
                        de toute tentative de règlement amiable, les tribunaux français seront seuls compétents.
                    </p>
                    <p class="text-gray-700">
                        Pour les utilisateurs résidant dans l'Union Européenne, rien dans ces CGU n'affecte 
                        vos droits en tant que consommateur prévus par la loi applicable.
                    </p>
                </section>

                <!-- Modifications -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">11. Modifications des CGU</h2>
                    <p class="text-gray-700 mb-4">
                        Nous nous réservons le droit de modifier ces CGU à tout moment. Les modifications 
                        substantielles vous seront notifiées par email au moins 30 jours avant leur entrée en vigueur.
                    </p>
                    <p class="text-gray-700">
                        Votre utilisation continue du service après modification des CGU constitue votre 
                        acceptation des nouvelles conditions.
                    </p>
                </section>

                <!-- Contact -->
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">12. Contact</h2>
                    <p class="text-gray-700 mb-4">
                        Pour toute question concernant ces CGU, vous pouvez nous contacter :
                    </p>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <ul class="text-gray-700 space-y-2">
                            <li><strong>Email :</strong> <a href="mailto:{{ config('contact.emails.legal') }}" class="text-blue-600 hover:text-blue-800">{{ config('contact.emails.legal') }}</a></li>
                            <li><strong>Formulaire :</strong> <a href="{{ route('support.contact') }}" class="text-blue-600 hover:text-blue-800">Page de contact</a></li>
                            <li><strong>Courrier :</strong> {{ config('contact.company.owner') }}, {{ config('contact.address.street') }}, {{ config('contact.address.postal_code') }} {{ config('contact.address.city') }}</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection