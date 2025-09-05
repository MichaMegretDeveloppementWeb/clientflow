@extends('layouts.guest')

@section('title', 'Mentions légales - Stone')
@section('description', 'Mentions légales de Stone. Informations sur l\'éditeur, l\'hébergement et les conditions d\'utilisation de notre plateforme.')

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Mentions légales - Stone",
    "description": "Informations légales et mentions obligatoires",
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
                "name": "Mentions légales"
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
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Mentions légales</h1>
                <p class="text-gray-600">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
            </div>

            <div class="prose prose-blue max-w-none">
                <!-- Éditeur du site -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Éditeur du site</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="mb-2"><strong>Raison sociale :</strong> {{ config('contact.company.owner') }}</p>
                        <p class="mb-2"><strong>Forme juridique :</strong> Auto-entreprise</p>
                        <p class="mb-2"><strong>Adresse du siège social :</strong><br>
                        {{ config('contact.address.street') }}<br>
                        {{ config('contact.address.postal_code') }} {{ config('contact.address.city') }}, {{ config('contact.address.country') }}</p>
                        <p class="mb-2"><strong>Numéro SIRET :</strong> {{ config('contact.company.siret') }}</p>
                        <p class="mb-2"><strong>Email :</strong> {{ config('contact.emails.contact') }}</p>
                        <p class="mb-2"><strong>Directeur de la publication :</strong> {{ config('contact.company.owner') }}</p>
                    </div>
                </section>

                <!-- Hébergement -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Hébergement</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="mb-2"><strong>Hébergeur :</strong> HOSTINGER INTERNATIONAL LTD</p>
                        <p class="mb-2"><strong>Adresse :</strong><br>
                        61 Lordou Vironos Street<br>
                        6023 Larnaca, Chypre</p>
                        <p class="mb-2"><strong>Contact :</strong> <a href="https://www.hostinger.fr/contact" class="text-blue-600 hover:text-blue-800">https://www.hostinger.fr/contact</a></p>
                        <p class="mb-2"><strong>Site web :</strong> <a href="https://www.hostinger.com" class="text-blue-600 hover:text-blue-800">www.hostinger.com</a></p>
                    </div>
                </section>

                <!-- Propriété intellectuelle -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Propriété intellectuelle</h2>
                    <p class="text-gray-700 mb-4">
                        L'ensemble du site {{ config('app.name') }}, y compris tous les textes, images, sons, vidéos,
                        logos, icônes, ainsi que leur mise en forme, sont la propriété exclusive de {{ config('app.name') }},
                        à l'exception des marques, logos ou contenus appartenant à d'autres sociétés partenaires ou auteurs.
                    </p>
                    <p class="text-gray-700 mb-4">
                        Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des
                        éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation
                        écrite préalable de {{ config('app.name') }}.
                    </p>
                    <p class="text-gray-700">
                        Toute exploitation non autorisée du site ou de l'un quelconque des éléments qu'il contient sera
                        considérée comme constitutive d'une contrefaçon et poursuivie conformément aux dispositions des
                        articles L.335-2 et suivants du Code de Propriété Intellectuelle.
                    </p>
                </section>

                <!-- Responsabilité -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Limitation de responsabilité</h2>
                    <p class="text-gray-700 mb-4">
                        Les informations contenues sur ce site sont aussi précises que possible et le site remis à jour à
                        différentes périodes de l'année, mais peut toutefois contenir des inexactitudes ou des omissions.
                    </p>
                    <p class="text-gray-700 mb-4">
                        Si vous constatez une lacune, erreur ou ce qui paraît être un dysfonctionnement, merci de bien vouloir
                        le signaler par email à {{ config('contact.emails.contact') }} en décrivant le problème
                        de la manière la plus précise possible.
                    </p>
                    <p class="text-gray-700">
                        {{ config('app.name') }} ne pourra être tenu responsable des dommages directs et indirects causés au
                        matériel de l'utilisateur, lors de l'accès au site, et résultant soit de l'utilisation d'un matériel
                        ne répondant pas aux spécifications indiquées, soit de l'apparition d'un bug ou d'une incompatibilité.
                    </p>
                </section>

                <!-- Litiges -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Litiges</h2>
                    <p class="text-gray-700 mb-4">
                        Les présentes conditions du site {{ config('app.name') }} sont régies par les lois françaises et
                        toute contestation ou litiges qui pourraient naître de l'interprétation ou de l'exécution de celles-ci
                        seront de la compétence exclusive des tribunaux dont dépend le siège social de la société.
                    </p>
                    <p class="text-gray-700">
                        La langue de référence, pour le règlement de contentieux éventuels, est le français.
                    </p>
                </section>

                <!-- Données personnelles -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Données personnelles</h2>
                    <p class="text-gray-700 mb-4">
                        De manière générale, vous n'êtes pas tenu de nous communiquer vos données personnelles lorsque vous
                        visitez notre site Internet {{ config('app.name') }}.
                    </p>
                    <p class="text-gray-700 mb-4">
                        Cependant, certaines informations peuvent être collectées lors de l'utilisation de nos services.
                        Pour plus d'informations sur le traitement de vos données personnelles, consultez notre
                        <a href="{{ route('legal.privacy') }}" class="text-blue-600 hover:text-blue-800">politique de confidentialité</a>.
                    </p>
                    <p class="text-gray-700">
                        Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos
                        données personnelles. Pour exercer ce droit, contactez-nous à l'adresse :
                        <a href="mailto:{{ config('contact.emails.legal') }}" class="text-blue-600 hover:text-blue-800">
                            {{ config('contact.emails.legal') }}
                        </a>
                    </p>
                </section>

                <!-- Cookies -->
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">7. Cookies</h2>
                    <p class="text-gray-700 mb-4">
                        Le site {{ config('app.name') }} utilise uniquement des cookies strictement nécessaires au
                        fonctionnement de l'application (authentification, session utilisateur, préférences).
                    </p>
                    <p class="text-gray-700 mb-4">
                        Nous ne collectons aucune donnée d'analyse ou de tracking via des cookies. Les cookies utilisés
                        sont essentiels pour assurer la sécurité et le bon fonctionnement de votre session.
                    </p>
                    <p class="text-gray-700">
                        La désactivation de ces cookies dans votre navigateur peut affecter le fonctionnement normal
                        de l'application et empêcher certaines fonctionnalités de fonctionner correctement.
                    </p>
                </section>

                <!-- Contact -->
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">8. Contact</h2>
                    <p class="text-gray-700 mb-4">
                        Pour toute question concernant ces mentions légales, vous pouvez nous contacter :
                    </p>
                    <ul class="text-gray-700 space-y-1">
                        <li><strong>Email :</strong> {{ config('contact.emails.contact') }}</li>
                        <li><strong>Formulaire de contact :</strong> <a href="{{ route('support.contact') }}" class="text-blue-600 hover:text-blue-800">Page de contact</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </main>

@endsection
