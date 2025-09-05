@extends('layouts.guest')

@section('title', 'Politique de confidentialité - Stone')
@section('description', 'Politique de confidentialité de Stone. Comment nous collectons, utilisons et protégeons vos données personnelles en conformité avec le RGPD.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Politique de confidentialité - Stone",
    "description": "Politique de confidentialité et protection des données personnelles",
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
                "name": "Politique de confidentialité"
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
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Politique de confidentialité</h1>
                <p class="text-gray-600">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
            </div>

            <div class="prose prose-blue max-w-none">
                <!-- Introduction -->
                <section class="mb-8">
                    <p class="text-gray-700 mb-4">
                        Chez {{ config('app.name') }}, nous attachons une grande importance à la protection de vos données personnelles. 
                        Cette politique de confidentialité explique comment nous collectons, utilisons, stockons et protégeons vos 
                        informations personnelles conformément au Règlement Général sur la Protection des Données (RGPD).
                    </p>
                    <p class="text-gray-700">
                        En utilisant nos services, vous acceptez les pratiques décrites dans cette politique de confidentialité.
                    </p>
                </section>

                <!-- Responsable du traitement -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Responsable du traitement</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="mb-2"><strong>Responsable du traitement :</strong> {{ config('contact.company.owner') }}</p>
                        <p class="mb-2"><strong>Adresse :</strong><br>
                        {{ config('contact.address.street') }}<br>
                        {{ config('contact.address.postal_code') }} {{ config('contact.address.city') }}, {{ config('contact.address.country') }}</p>
                        <p class="mb-2"><strong>Email de contact :</strong> {{ config('contact.emails.legal') }}</p>
                    </div>
                </section>

                <!-- Données collectées -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Données que nous collectons</h2>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">2.1 Données de compte utilisateur</h3>
                    <ul class="text-gray-700 mb-4 space-y-1">
                        <li>• Nom et prénom</li>
                        <li>• Adresse email</li>
                        <li>• Mot de passe (chiffré)</li>
                        <li>• Date de création du compte</li>
                        <li>• Préférences d'interface</li>
                    </ul>

                    <h3 class="text-lg font-medium text-gray-900 mb-3">2.2 Données professionnelles</h3>
                    <ul class="text-gray-700 mb-4 space-y-1">
                        <li>• Informations sur vos clients (nom, email, téléphone, adresse)</li>
                        <li>• Données de projets (nom, description, budget, dates)</li>
                        <li>• Événements et facturation</li>
                        <li>• Documents et fichiers uploadés</li>
                    </ul>

                    <h3 class="text-lg font-medium text-gray-900 mb-3">2.3 Données techniques</h3>
                    <ul class="text-gray-700 mb-4 space-y-1">
                        <li>• Adresse IP (pour la sécurité)</li>
                        <li>• Type de navigateur et version</li>
                        <li>• Cookies techniques strictement nécessaires</li>
                    </ul>

                    <h3 class="text-lg font-medium text-gray-900 mb-3">2.4 Données de communication</h3>
                    <ul class="text-gray-700 space-y-1">
                        <li>• Messages envoyés via le formulaire de contact</li>
                        <li>• Correspondance par email</li>
                    </ul>
                </section>

                <!-- Finalités du traitement -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Pourquoi nous utilisons vos données</h2>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-1">Fourniture du service</h4>
                            <p class="text-gray-700 text-sm">Permettre l'utilisation de la plateforme de gestion client, sauvegarde de vos données, synchronisation multi-appareils.</p>
                            <p class="text-xs text-gray-600 mt-1"><strong>Base légale :</strong> Exécution du contrat</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-1">Support client</h4>
                            <p class="text-gray-700 text-sm">Répondre à vos questions, résoudre les problèmes techniques, assistance personnalisée.</p>
                            <p class="text-xs text-gray-600 mt-1"><strong>Base légale :</strong> Exécution du contrat</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-1">Obligations légales</h4>
                            <p class="text-gray-700 text-sm">Facturation, comptabilité, réponse aux demandes des autorités compétentes.</p>
                            <p class="text-xs text-gray-600 mt-1"><strong>Base légale :</strong> Obligation légale</p>
                        </div>
                    </div>
                </section>

                <!-- Partage des données -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Partage de vos données</h2>
                    <p class="text-gray-700 mb-4">
                        Nous ne vendons jamais vos données personnelles. Nous ne les partageons qu'avec des partenaires de confiance 
                        et uniquement dans les cas suivants :
                    </p>
                    
                    <div class="space-y-3">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Hébergeur (HOSTINGER INTERNATIONAL LTD)</h4>
                            <p class="text-gray-700 text-sm">Pour le stockage sécurisé de vos données.</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Autorités légales</h4>
                            <p class="text-gray-700 text-sm">Uniquement si requis par la loi ou décision de justice.</p>
                        </div>
                    </div>
                </section>

                <!-- Durée de conservation -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Durée de conservation</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Données de compte actif</h4>
                                <p class="text-gray-700 text-sm">Conservées tant que le compte est actif + 3 ans après la dernière connexion</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Données de facturation</h4>
                                <p class="text-gray-700 text-sm">10 ans (obligation comptable et fiscale)</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Cookies techniques</h4>
                                <p class="text-gray-700 text-sm">Maximum 13 mois</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Logs de sécurité</h4>
                                <p class="text-gray-700 text-sm">1 an maximum</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sécurité -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Sécurité de vos données</h2>
                    <p class="text-gray-700 mb-4">
                        Nous mettons en place des mesures techniques et organisationnelles appropriées pour protéger vos données :
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Chiffrement</h4>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• HTTPS/TLS pour tous les échanges</li>
                                <li>• Chiffrement des données sensibles</li>
                                <li>• Hachage des mots de passe</li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Accès contrôlé</h4>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Authentification forte</li>
                                <li>• Accès limité aux données</li>
                                <li>• Audit des accès</li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Sauvegardes</h4>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Sauvegardes quotidiennes</li>
                                <li>• Réplication sécurisée</li>
                                <li>• Test de restauration</li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Monitoring</h4>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Surveillance de sécurité</li>
                                <li>• Détection d'intrusion</li>
                                <li>• Plan de réponse aux incidents</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Vos droits -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">7. Vos droits</h2>
                    <p class="text-gray-700 mb-4">
                        Conformément au RGPD, vous disposez des droits suivants :
                    </p>
                    
                    <div class="space-y-3">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Droit d'accès</h4>
                            <p class="text-gray-700 text-sm">Obtenir une copie de toutes les données personnelles que nous détenons sur vous</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Droit de rectification</h4>
                            <p class="text-gray-700 text-sm">Corriger ou mettre à jour vos données personnelles inexactes</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Droit à l'effacement</h4>
                            <p class="text-gray-700 text-sm">Demander la suppression de vos données personnelles</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Droit à la limitation</h4>
                            <p class="text-gray-700 text-sm">Limiter le traitement de vos données dans certains cas</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Droit à la portabilité</h4>
                            <p class="text-gray-700 text-sm">Récupérer vos données dans un format lisible par machine</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                        <p class="text-gray-700 mb-2">
                            <strong>Pour exercer vos droits :</strong>
                        </p>
                        <ul class="text-gray-700 space-y-1">
                            <li>• Email : <a href="mailto:{{ config('contact.emails.legal') }}" class="text-blue-600 hover:text-blue-800">{{ config('contact.emails.legal') }}</a></li>
                            <li>• Depuis votre compte : Section "Paramètres" > "Confidentialité"</li>
                            <li>• Délai de réponse : Maximum 1 mois</li>
                        </ul>
                    </div>
                </section>

                <!-- Cookies -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">8. Cookies</h2>
                    <p class="text-gray-700 mb-4">
                        Nous utilisons uniquement des cookies strictement nécessaires sur {{ config('app.name') }} :
                    </p>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Cookies essentiels</h4>
                        <p class="text-gray-700 text-sm">Nécessaires au fonctionnement de l'application (authentification, session utilisateur, préférences). Ces cookies sont indispensables et ne peuvent être désactivés.</p>
                    </div>
                    
                    <p class="text-gray-700 text-sm mt-4">
                        La désactivation de ces cookies dans votre navigateur peut affecter le fonctionnement normal de l'application.
                    </p>
                </section>

                <!-- Contact et réclamations -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">9. Contact et réclamations</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-medium text-gray-900 mb-3">Contact</h4>
                            <p class="text-gray-700 text-sm mb-2">Pour toute question sur cette politique :</p>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Email : {{ config('contact.emails.legal') }}</li>
                                <li>• Courrier : {{ config('contact.company.owner') }}, {{ config('contact.address.street') }}, {{ config('contact.address.postal_code') }} {{ config('contact.address.city') }}</li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="font-medium text-gray-900 mb-3">Réclamation CNIL</h4>
                            <p class="text-gray-700 text-sm mb-2">En cas de désaccord :</p>
                            <ul class="text-gray-700 text-sm space-y-1">
                                <li>• Site web : <a href="https://www.cnil.fr" class="text-blue-600 hover:text-blue-800 underline">www.cnil.fr</a></li>
                                <li>• Adresse : 3 Place de Fontenoy, 75007 Paris</li>
                                <li>• Téléphone : 01 53 73 22 22</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Modifications -->
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">10. Modifications de cette politique</h2>
                    <p class="text-gray-700 mb-4">
                        Nous pouvons modifier cette politique de confidentialité pour refléter les changements de nos pratiques 
                        ou pour des raisons légales, réglementaires ou opérationnelles.
                    </p>
                    <p class="text-gray-700">
                        En cas de modification significative, nous vous informerons par email au moins 30 jours avant l'entrée 
                        en vigueur des changements. La version en vigueur est toujours disponible sur cette page avec sa date de mise à jour.
                    </p>
                </section>
            </div>
        </div>
    </main>

@endsection