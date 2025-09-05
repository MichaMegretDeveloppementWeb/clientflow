@extends('layouts.guest')

@section('title', 'Documentation - Stone')
@section('description', 'Documentation complète de Stone. Guides d\'utilisation, tutoriels et manuels pour maîtriser votre gestion client.')

@section('json-ld-schema')
{
    "@type": "TechArticle",
    "name": "Documentation - Stone",
    "headline": "Documentation Stone",
    "description": "Guide complet d'utilisation de la plateforme Stone",
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
                "name": "Documentation"
            }
        ]
    },
    "author": {
        "@type": "Organization",
        "name": "Stone"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Stone"
    },
    "dateModified": "{{ now()->format('Y-m-d') }}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ url()->current() }}"
    }
}
@endsection

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:flex gap-8">
            <!-- Sidebar -->
            <aside class="lg:w-64 lg:flex-shrink-0 mb-8 lg:mb-0">
                <nav class="bg-white rounded-2xl shadow-lg p-6 lg:sticky lg:top-20">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sommaire</h3>
                    <ul class="space-y-3">
                        <li><a href="#getting-started" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Premiers pas</a></li>
                        <li><a href="#clients" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Gestion des clients</a></li>
                        <li><a href="#projects" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Projets</a></li>
                        <li><a href="#events" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Événements</a></li>
                        <li><a href="#billing" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Facturation</a></li>
                        <li><a href="#dashboard" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Tableau de bord</a></li>
                        <li><a href="#settings" class="block py-2 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Paramètres</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <!-- Introduction -->
                <div class="mb-12">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Documentation {{ config('app.name') }}</h1>
                    <p class="text-lg text-gray-600">Guide complet pour utiliser efficacement votre plateforme de gestion client</p>
                </div>

                <!-- Getting Started -->
                <section id="getting-started" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Premiers pas</h2>

                    <div class="prose prose-blue max-w-none">
                        <p class="text-gray-600 mb-4">{{ config('app.name') }} est une plateforme complète de gestion client conçue pour les freelancers et petites agences. Elle vous permet de gérer vos clients, projets et facturation dans une interface unifiée.</p>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Architecture de l'application</h3>
                        <p class="text-gray-600 mb-4">L'application suit une hiérarchie à trois niveaux :</p>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li><strong>Clients</strong> : Vos contacts et entreprises</li>
                            <li><strong>Projets</strong> : Les missions pour chaque client</li>
                            <li><strong>Événements</strong> : Les étapes et éléments de facturation de chaque projet</li>
                        </ul>
                    </div>
                </section>

                <!-- Clients -->
                <section id="clients" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Gestion des clients</h2>

                    <div class="prose prose-blue max-w-none">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Créer un client</h3>
                        <ol class="list-decimal list-inside text-gray-600 mb-6 space-y-2">
                            <li>Accédez à la section "Clients" depuis le menu principal</li>
                            <li>Cliquez sur "Nouveau client"</li>
                            <li>Remplissez les informations requises : nom, email, téléphone</li>
                            <li>Ajoutez optionnellement l'adresse et des notes</li>
                            <li>Enregistrez le client</li>
                        </ol>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Fonctionnalités avancées</h3>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li>Filtrage et recherche dans la liste des clients</li>
                            <li>Statistiques financières par client</li>
                            <li>Historique complet des interactions</li>
                            <li>Suppression sécurisée (soft delete)</li>
                        </ul>
                    </div>
                </section>

                <!-- Projects -->
                <section id="projects" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Projets</h2>

                    <div class="prose prose-blue max-w-none">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Créer un projet</h3>
                        <ol class="list-decimal list-inside text-gray-600 mb-6 space-y-2">
                            <li>Sélectionnez un client existant</li>
                            <li>Définissez le nom et la description du projet</li>
                            <li>Fixez le budget prévisionnel</li>
                            <li>Définissez les dates de début et fin</li>
                            <li>Choisissez le statut initial (généralement "Actif")</li>
                        </ol>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Statuts de projet</h3>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li><strong>Actif</strong> : Projet en cours de réalisation</li>
                            <li><strong>Terminé</strong> : Projet livré et validé</li>
                            <li><strong>En pause</strong> : Projet temporairement suspendu</li>
                            <li><strong>Annulé</strong> : Projet arrêté définitivement</li>
                        </ul>
                    </div>
                </section>

                <!-- Events -->
                <section id="events" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Système d'événements</h2>

                    <div class="prose prose-blue max-w-none">
                        <p class="text-gray-600 mb-4">Le système d'événements est au cœur de {{ config('app.name') }}. Chaque événement peut être de deux types :</p>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Événements "Étape"</h3>
                        <p class="text-gray-600 mb-4">Pour le suivi des jalons du projet :</p>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li>Réunions client</li>
                            <li>Livrables intermédiaires</li>
                            <li>Validations</li>
                            <li>Formations</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Événements "Facturation"</h3>
                        <p class="text-gray-600 mb-4">Pour la gestion financière :</p>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li>Devis</li>
                            <li>Factures</li>
                            <li>Acomptes</li>
                            <li>Paiements</li>
                        </ul>
                    </div>
                </section>

                <!-- Billing -->
                <section id="billing" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Facturation</h2>

                    <div class="prose prose-blue max-w-none">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Créer une facture</h3>
                        <ol class="list-decimal list-inside text-gray-600 mb-6 space-y-2">
                            <li>Depuis un projet, créez un nouvel événement</li>
                            <li>Sélectionnez le type "Facturation"</li>
                            <li>Choisissez "Facture" comme sous-type</li>
                            <li>Définissez le montant et les dates</li>
                            <li>Suivez le statut de paiement</li>
                        </ol>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Suivi des paiements</h3>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li><strong>En attente</strong> : Facture envoyée, paiement attendu</li>
                            <li><strong>Payé</strong> : Paiement reçu et confirmé</li>
                            <li><strong>En retard</strong> : Date d'échéance dépassée</li>
                        </ul>
                    </div>
                </section>

                <!-- Dashboard -->
                <section id="dashboard" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Tableau de bord</h2>

                    <div class="prose prose-blue max-w-none">
                        <p class="text-gray-600 mb-4">Le tableau de bord offre une vue d'ensemble de votre activité :</p>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Statistiques principales</h3>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li>Nombre de clients actifs</li>
                            <li>Projets en cours</li>
                            <li>Chiffre d'affaires mensuel</li>
                            <li>Taux de paiement</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Activités récentes</h3>
                        <p class="text-gray-600 mb-4">Suivez en temps réel les dernières actions sur vos projets et la facturation.</p>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Tâches urgentes</h3>
                        <p class="text-gray-600 mb-4">Identifiez rapidement les événements en retard et les actions prioritaires.</p>
                    </div>
                </section>

                <!-- Settings -->
                <section id="settings" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Paramètres</h2>

                    <div class="prose prose-blue max-w-none">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Profil utilisateur</h3>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li>Modification des informations personnelles</li>
                            <li>Changement de mot de passe</li>
                            <li>Configuration des notifications</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Apparence</h3>
                        <ul class="list-disc list-inside text-gray-600 mb-6 space-y-2">
                            <li>Thème clair/sombre</li>
                            <li>Personnalisation de l'interface</li>
                            <li>Langue et région</li>
                        </ul>
                    </div>
                </section>

                <!-- Contact CTA -->
                <div class="bg-blue-50 rounded-2xl p-6 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Besoin d'aide supplémentaire ?</h3>
                    <p class="text-gray-600 mb-4">Notre équipe support est disponible pour répondre à vos questions</p>
                    <a href="{{ route('support.contact') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                        Nous contacter
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript pour la navigation du sommaire -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du scroll avec offset pour la navbar
        const navbarHeight = 80; // Hauteur approximative de la navbar
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('nav a[href^="#"]');

        // Fonction pour faire défiler vers une section avec offset
        function scrollToSection(targetId) {
            const target = document.getElementById(targetId);
            if (target) {
                const targetPosition = target.offsetTop - navbarHeight;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        }

        // Gestion des clics sur les liens du sommaire
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                scrollToSection(targetId);
                
                // Mise à jour immédiate de l'état actif
                updateActiveLink(this);
            });
        });

        // Fonction pour mettre à jour le lien actif
        function updateActiveLink(activeLink) {
            navLinks.forEach(link => {
                link.classList.remove('text-blue-600', 'bg-blue-50', 'font-medium');
                link.classList.add('text-gray-600');
            });
            
            if (activeLink) {
                activeLink.classList.remove('text-gray-600');
                activeLink.classList.add('text-blue-600', 'bg-blue-50', 'font-medium');
            }
        }

        // Fonction pour détecter la section courante lors du scroll
        function getCurrentSection() {
            const scrollPosition = window.scrollY + navbarHeight + 50; // +50 pour un peu de marge
            
            let currentSection = null;
            sections.forEach(section => {
                if (section.offsetTop <= scrollPosition) {
                    currentSection = section;
                }
            });
            
            return currentSection;
        }

        // Mise à jour du sommaire pendant le scroll
        function updateSummaryOnScroll() {
            const currentSection = getCurrentSection();
            if (currentSection) {
                const activeLink = document.querySelector(`nav a[href="#${currentSection.id}"]`);
                updateActiveLink(activeLink);
            }
        }

        // Écouter les événements de scroll avec throttling
        let isScrolling = false;
        window.addEventListener('scroll', function() {
            if (!isScrolling) {
                window.requestAnimationFrame(function() {
                    updateSummaryOnScroll();
                    isScrolling = false;
                });
                isScrolling = true;
            }
        });

        // Initialiser l'état au chargement
        updateSummaryOnScroll();
    });
    </script>

@endsection
