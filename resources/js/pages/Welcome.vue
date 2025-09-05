<template>
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav class="relative sticky top-0 z-50 border-b border-gray-100 bg-white/95 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="/resources/images/stone_logo.webp" alt="Stone" class="h-8 w-8" />
                        <span class="text-xl font-bold text-gray-900">Stone</span>
                    </div>
                    <div class="hidden items-center space-x-4 md:flex">
                        <Button variant="ghost" class="font-medium text-gray-600 hover:text-gray-900" as-child>
                            <Link :href="route('login')">Se connecter</Link>
                        </Button>
                        <Button class="bg-gray-900 font-medium text-white hover:bg-gray-800" as-child>
                            <Link :href="route('register')">Commencer gratuitement</Link>
                        </Button>
                    </div>
                    <div class="md:hidden">
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="route('login')">Connexion</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-b from-gray-50/50 via-white to-white">
            <!-- Background Pattern -->
            <div class="bg-grid-pattern absolute inset-0 opacity-[0.02]"></div>
            <div
                class="absolute top-0 left-1/2 h-[800px] w-[800px] -translate-x-1/2 rounded-full bg-gradient-to-r from-blue-400/10 to-indigo-400/10 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-[100em] px-4 pt-24 pb-32 sm:px-6 sm:pt-32 sm:pb-40 lg:px-8">
                <div class="text-center">
                    <!-- Main Heading -->
                    <h1 class="mx-auto max-w-5xl text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                        La solution de gestion
                        <span class="relative inline-block">
                            <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                tout-en-un
                            </span>
                        </span>
                        <br />pour freelances ambitieux
                    </h1>

                    <!-- Subtitle -->
                    <p class="mx-auto mt-8 max-w-3xl text-xl leading-8 text-gray-600">
                        Centralisez vos clients, pilotez vos projets et optimisez vos revenus avec une plateforme pensée pour votre succès.
                        <span class="font-medium text-gray-900">Simple, puissante, française.</span>
                    </p>

                    <!-- Main CTA -->
                    <div class="mt-12">
                        <Button
                            size="lg"
                            class="group bg-gradient-to-r from-gray-900 to-gray-800 px-12 py-5 text-xl font-semibold text-white shadow-xl hover:from-gray-800 hover:to-gray-700 transition-all duration-200"
                            as-child
                        >
                            <Link :href="route('register')">
                                Créer mon compte gratuitement
                                <Icon name="arrow-right" class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" />
                            </Link>
                        </Button>
                    </div>

                    <!-- Trust indicators -->
                    <div class="mt-16 flex flex-col items-center space-y-6">
                        <p class="text-sm font-medium text-gray-500">Faites confiance par 1000+ freelances français</p>
                        <div class="flex items-center space-x-8">
                            <div class="flex items-center space-x-1 text-yellow-400">
                                <Icon name="star" class="h-5 w-5 fill-current" />
                                <Icon name="star" class="h-5 w-5 fill-current" />
                                <Icon name="star" class="h-5 w-5 fill-current" />
                                <Icon name="star" class="h-5 w-5 fill-current" />
                                <Icon name="star" class="h-5 w-5 fill-current" />
                                <span class="ml-2 text-sm font-medium text-gray-700">4.9/5</span>
                            </div>
                            <div class="h-4 w-px bg-gray-300"></div>
                            <div class="text-sm text-gray-600">
                                <span class="font-medium text-gray-900">98%</span> de satisfaction
                            </div>
                            <div class="h-4 w-px bg-gray-300"></div>
                            <div class="text-sm text-gray-600">
                                <span class="font-medium text-gray-900">30 jours</span> d'essai gratuit
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Origin Story Section -->
        <section class="bg-gradient-to-b from-gray-50 to-white py-16 sm:py-24">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Badge -->
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-emerald-700">
                        <Icon name="heart" class="h-4 w-4" />
                        <span class="text-sm font-medium">Né d'un besoin réel</span>
                    </div>

                    <!-- Title -->
                    <h2 class="mb-8 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Une solution créée par un freelance, pour les freelances
                    </h2>

                    <!-- Story -->
                    <div class="mx-auto max-w-3xl">
                        <p class="mb-6 text-lg leading-relaxed text-gray-600">
                            Salut ! Je suis <strong>Micha</strong>, développeur web freelance basé à <strong>Évian-les-Bains</strong>.
                            Comme vous, j'ai longtemps cherché un outil de gestion simple pour mes clients et projets.
                            Las des CRM complexes avec des milliers d'options que je n'utilisais jamais, j'ai décidé de créer <strong>Stone</strong>.
                        </p>

                        <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-100">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                    <Icon name="gift" class="h-5 w-5 text-green-600" />
                                </div>
                                <h3 class="mb-2 font-semibold text-gray-900">100% Gratuit</h3>
                                <p class="text-sm text-gray-600">
                                    Pas de version premium cachée, pas d'abonnement. Stone sera toujours entièrement gratuit.
                                </p>
                            </div>

                            <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-100">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                    <Icon name="github" class="h-5 w-5 text-blue-600" />
                                </div>
                                <h3 class="mb-2 font-semibold text-gray-900">Open Source</h3>
                                <p class="text-sm text-gray-600">
                                    Le code est disponible sur <a href="https://github.com/MichaMegretDeveloppementWeb/stone" target="_blank" class="text-blue-600 hover:text-blue-700 underline">GitHub</a>.
                                    Transparence totale sur le développement.
                                </p>
                            </div>

                            <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-100 sm:col-span-2 lg:col-span-1">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                    <Icon name="zap" class="h-5 w-5 text-purple-600" />
                                </div>
                                <h3 class="mb-2 font-semibold text-gray-900">Simple par design</h3>
                                <p class="text-sm text-gray-600">
                                    Volontairement épuré. Juste les fonctionnalités essentielles, sans la complexité.
                                </p>
                            </div>
                        </div>

                        <!-- Call to collaboration -->
                        <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-emerald-50 p-8 border border-blue-100">
                            <div class="text-center">
                                <div class="mb-4 flex justify-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white shadow-sm">
                                        <Icon name="users" class="h-6 w-6 text-blue-600" />
                                    </div>
                                </div>
                                <h3 class="mb-4 text-xl font-semibold text-gray-900">
                                    Construisons ensemble l'outil parfait
                                </h3>
                                <p class="mb-6 text-gray-600 leading-relaxed">
                                    Stone est conçu pour évoluer avec vos besoins. Rencontrez-vous un bug ?
                                    Une fonctionnalité vous manque ? Partagez votre expérience !
                                    Vos retours façonnent directement l'avenir de l'application.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="https://github.com/MichaMegretDeveloppementWeb/stone/issues/new" target="_blank"
                                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                        <Icon name="message-circle" class="h-4 w-4" />
                                        Signaler un problème
                                    </a>
                                    <a href="https://github.com/MichaMegretDeveloppementWeb/stone/discussions" target="_blank"
                                       class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                        <Icon name="lightbulb" class="h-4 w-4" />
                                        Proposer une amélioration
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Client Management Section -->
        <section class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
                    <div class="order-2 lg:order-1">
                        <!-- IMAGE: Screenshot de la liste des clients avec cartes stats -->
                        <!-- Montrer la page de liste des clients avec les cartes de statistiques en haut et le tableau des clients en dessous -->
                        <div class="aspect-[16/10] w-full rounded-2xl border border-gray-200 bg-gray-100 shadow-2xl"></div>
                    </div>
                    <div class="order-1 lg:order-2 lg:pl-8">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                            <Icon name="users" class="h-4 w-4" />
                            <span class="text-sm font-medium">Gestion Client</span>
                        </div>
                        <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                            Centralisez vos relations client
                        </h2>
                        <p class="mb-8 text-xl leading-8 text-gray-600">
                            Gardez toutes les informations importantes de vos clients à portée de main. Contacts, historique des projets, notes personnelles et statistiques financières en un coup d'œil.
                        </p>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                    <Icon name="user-check" class="h-4 w-4 text-blue-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Fiches client complètes</h3>
                                    <p class="text-gray-600">Nom, société, coordonnées, notes personnalisées et historique des interactions</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                    <Icon name="bar-chart-3" class="h-4 w-4 text-blue-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Statistiques en temps réel</h3>
                                    <p class="text-gray-600">Chiffre d'affaires total, nombre de projets et vue d'ensemble de votre portefeuille</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                    <Icon name="search" class="h-4 w-4 text-blue-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Recherche et filtrage avancés</h3>
                                    <p class="text-gray-600">Trouvez rapidement le bon client grâce aux filtres par statut, société ou période</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mini Feature: Actions rapides -->
        <section class="bg-gradient-to-b from-white to-gray-50 py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
                    <!-- Mockup -->
                    <div class="order-2 lg:order-1">
                        <div class="relative mx-auto w-[85%] max-w-md">
                            <!-- Interface mockup -->
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                                <!-- Client rows in skeleton -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between rounded-lg border border-gray-100 p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                            <div class="space-y-2">
                                                <div class="h-3 w-24 rounded bg-gray-200"></div>
                                                <div class="h-2 w-16 rounded bg-gray-100"></div>
                                            </div>
                                        </div>
                                        <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                                    </div>

                                    <!-- Featured row with dropdown -->
                                    <div class="relative flex items-center justify-between rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-full bg-blue-200"></div>
                                            <div class="space-y-2">
                                                <div class="h-3 w-24 rounded bg-blue-300"></div>
                                                <div class="h-2 w-16 rounded bg-blue-200"></div>
                                            </div>
                                        </div>
                                        <!-- Dropdown menu (detailed) -->
                                        <div class="absolute right-4 top-16 z-10 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                                            <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <Icon name="eye" class="mr-3 h-4 w-4 text-gray-400" />
                                                Voir le détail
                                            </button>
                                            <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <Icon name="edit" class="mr-3 h-4 w-4 text-gray-400" />
                                                Modifier
                                            </button>
                                            <button class="flex w-full items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">
                                                <Icon name="plus" class="mr-3 h-4 w-4 text-blue-500" />
                                                Nouveau projet
                                            </button>
                                            <button class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <Icon name="trash" class="mr-3 h-4 w-4 text-red-500" />
                                                Supprimer
                                            </button>
                                        </div>
                                        <!-- Cursor -->
                                        <div class="absolute right-8 top-12">
                                            <Icon name="mouse-pointer" class="h-5 w-5 text-blue-600" />
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between rounded-lg border border-gray-100 p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                            <div class="space-y-2">
                                                <div class="h-3 w-24 rounded bg-gray-200"></div>
                                                <div class="h-2 w-16 rounded bg-gray-100"></div>
                                            </div>
                                        </div>
                                        <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="order-1 lg:order-2 lg:pl-8">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-blue-700">
                            <Icon name="zap" class="h-3 w-3" />
                            <span class="text-xs font-medium">Actions rapides</span>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-gray-900">
                            Gagnez du temps sur chaque interaction
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Directement depuis votre liste clients, accédez à toutes les actions importantes : consulter les détails, créer un nouveau projet, modifier ou supprimer votre client en un clic.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Project Management Section -->
        <section class="bg-gray-50 py-24 sm:py-32">
            <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
                    <div class="lg:pr-8">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-emerald-700">
                            <Icon name="folder-kanban" class="h-4 w-4" />
                            <span class="text-sm font-medium">Gestion Projet</span>
                        </div>
                        <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                            Pilotez vos projets avec précision
                        </h2>
                        <p class="mb-8 text-xl leading-8 text-gray-600">
                            Organisez vos projets par statut, définissez des budgets et suivez l'avancement en temps réel. Ne laissez plus rien au hasard dans la gestion de vos missions.
                        </p>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                                    <Icon name="target" class="h-4 w-4 text-emerald-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Suivi budgétaire intelligent</h3>
                                    <p class="text-gray-600">Définissez des budgets et suivez automatiquement vos dépenses et revenus par projet</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                                    <Icon name="calendar-days" class="h-4 w-4 text-emerald-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Planning intégré</h3>
                                    <p class="text-gray-600">Dates de début et fin, échéances importantes et alertes pour les projets en retard</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                                    <Icon name="activity" class="h-4 w-4 text-emerald-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Statuts personnalisables</h3>
                                    <p class="text-gray-600">Actif, terminé, en pause ou annulé - adaptez les statuts à votre façon de travailler</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <!-- IMAGE: Screenshot de la liste des projets avec cartes stats et tableau -->
                        <!-- Montrer la page de liste des projets avec les statistiques (projets actifs, terminés, etc.) et le tableau des projets -->
                        <div class="aspect-[16/10] w-full rounded-2xl border border-gray-200 bg-gray-100 shadow-2xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mini Feature: Suivi budgétaire -->
        <section class="bg-white py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
                    <!-- Content -->
                    <div class="lg:pr-8">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
                            <Icon name="trending-up" class="h-3 w-3" />
                            <span class="text-xs font-medium">Suivi budget</span>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-gray-900">
                            Visualisez la santé financière de vos projets
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Chaque projet affiche automatiquement son avancement budgétaire avec des alertes visuelles. Identifiez en un coup d'œil les dépassements et optimisez vos marges.
                        </p>
                    </div>

                    <!-- Mockup -->
                    <div>
                        <div class="relative mx-auto w-[85%] max-w-md">
                            <!-- Project card mockup -->
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                                <!-- Project header (skeleton) -->
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="space-y-2">
                                        <div class="h-4 w-32 rounded bg-gray-200"></div>
                                        <div class="h-3 w-24 rounded bg-gray-100"></div>
                                    </div>
                                    <div class="h-6 w-16 rounded-full bg-gray-100"></div>
                                </div>

                                <!-- Budget section (detailed) -->
                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="text-sm font-medium text-emerald-800">Budget du projet</span>
                                        <Icon name="trending-up" class="h-4 w-4 text-emerald-600" />
                                    </div>
                                    <div class="mb-4 flex items-baseline space-x-2">
                                        <span class="text-2xl font-bold text-emerald-900">8.750€</span>
                                        <span class="text-sm text-emerald-600">/ 12.000€</span>
                                    </div>
                                    <!-- Progress bar -->
                                    <div class="mb-2 h-2 overflow-hidden rounded-full bg-emerald-200">
                                        <div class="h-full w-3/4 bg-gradient-to-r from-emerald-500 to-emerald-400"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-emerald-700">
                                        <span>73% utilisé</span>
                                        <span>3.250€ restant</span>
                                    </div>
                                </div>

                                <!-- Other sections (skeleton) -->
                                <div class="mt-4 space-y-3">
                                    <div class="h-3 w-full rounded bg-gray-100"></div>
                                    <div class="h-3 w-4/5 rounded bg-gray-100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events System Section -->
        <section class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
                    <div class="order-2 lg:order-1">
                        <!-- IMAGE: Screenshot de la page des événements avec le formulaire de création -->
                        <!-- Montrer la page de création d'événement avec les deux types (étape/facturation) et les champs conditionnels -->
                        <div class="aspect-[16/10] w-full rounded-2xl border border-gray-200 bg-gray-100 shadow-2xl"></div>
                    </div>
                    <div class="order-1 lg:order-2 lg:pl-8">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-purple-100 px-4 py-2 text-purple-700">
                            <Icon name="calendar-clock" class="h-4 w-4" />
                            <span class="text-sm font-medium">Système d'Événements</span>
                        </div>
                        <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                            Un système unique pour tout gérer
                        </h2>
                        <p class="mb-8 text-xl leading-8 text-gray-600">
                            Notre système d'événements révolutionnaire réunit la gestion des tâches et la facturation en un seul outil. Créez des étapes de projet ou des factures avec la même simplicité.
                        </p>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                                    <Icon name="list-checks" class="h-4 w-4 text-purple-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Événements "Étape"</h3>
                                    <p class="text-gray-600">Tâches, réunions, livrables - organisez toutes les étapes de vos projets</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                                    <Icon name="receipt" class="h-4 w-4 text-purple-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Événements "Facturation"</h3>
                                    <p class="text-gray-600">Factures, acomptes, paiements - gérez toute votre facturation dans le même système</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                                    <Icon name="clock" class="h-4 w-4 text-purple-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Timeline automatique</h3>
                                    <p class="text-gray-600">Visualisez l'historique complet de chaque projet avec toutes les interactions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mini Feature: Système dual -->
        <section class="bg-gradient-to-b from-white to-gray-50 py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
                    <!-- Mockup -->
                    <div class="order-2 lg:order-1">
                        <div class="relative mx-auto w-[85%] max-w-md">
                            <!-- Toggle interface -->
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                                <!-- Type selector (detailed) -->
                                <div class="mb-6">
                                    <div class="mb-3 text-sm font-medium text-gray-700">Type d'événement</div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button class="rounded-lg border border-gray-200 bg-white p-4 text-left transition-all hover:border-gray-300">
                                            <div class="mb-2 flex items-center space-x-2">
                                                <Icon name="list-checks" class="h-4 w-4 text-purple-600" />
                                                <span class="text-sm font-medium text-gray-700">Étape</span>
                                            </div>
                                            <div class="text-xs text-gray-500">Tâche, réunion</div>
                                        </button>
                                        <!-- Selected option -->
                                        <button class="rounded-lg border-2 border-emerald-500 bg-emerald-50 p-4 text-left">
                                            <div class="mb-2 flex items-center space-x-2">
                                                <Icon name="receipt" class="h-4 w-4 text-emerald-600" />
                                                <span class="text-sm font-medium text-emerald-800">Facturation</span>
                                            </div>
                                            <div class="text-xs text-emerald-600">Facture, acompte</div>
                                        </button>
                                    </div>
                                </div>

                                <!-- Dynamic fields (detailed for billing) -->
                                <div class="space-y-4">
                                    <div>
                                        <div class="mb-2 text-xs font-medium text-gray-700">Montant HT</div>
                                        <div class="flex items-center rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2">
                                            <Icon name="euro" class="mr-2 h-4 w-4 text-emerald-600" />
                                            <span class="text-sm font-medium text-emerald-800">2.500,00</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-2 text-xs font-medium text-gray-700">Date d'échéance</div>
                                        <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2">
                                            <span class="text-sm font-medium text-emerald-800">15 mars 2025</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other fields skeleton -->
                                <div class="mt-6 space-y-3">
                                    <div class="h-3 w-full rounded bg-gray-100"></div>
                                    <div class="h-3 w-3/4 rounded bg-gray-100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="order-1 lg:order-2 lg:pl-8">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-purple-100 px-3 py-1 text-purple-700">
                            <Icon name="layers" class="h-3 w-3" />
                            <span class="text-xs font-medium">Système dual</span>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-gray-900">
                            Un formulaire, deux univers
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Selon le type choisi, le formulaire s'adapte automatiquement. Créez une étape de projet ou une facture avec la même simplicité, mais des champs parfaitement adaptés à chaque usage.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard Section -->
        <section class="bg-gradient-to-b from-gray-50 to-white py-24 sm:py-32">
            <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20">
                    <div class="lg:pr-8">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-amber-700">
                            <Icon name="layout-dashboard" class="h-4 w-4" />
                            <span class="text-sm font-medium">Tableau de Bord</span>
                        </div>
                        <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                            Votre activité en un coup d'œil
                        </h2>
                        <p class="mb-8 text-xl leading-8 text-gray-600">
                            Prenez de meilleures décisions grâce à un tableau de bord qui synthétise toutes vos données importantes. Revenus, projets en cours, tâches urgentes - tout est centralisé.
                        </p>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100">
                                    <Icon name="trending-up" class="h-4 w-4 text-amber-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Analyse des revenus</h3>
                                    <p class="text-gray-600">Graphiques interactifs pour suivre l'évolution de votre chiffre d'affaires</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100">
                                    <Icon name="bell" class="h-4 w-4 text-amber-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Tâches urgentes</h3>
                                    <p class="text-gray-600">Ne ratez plus jamais une échéance importante avec les alertes automatiques</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100">
                                    <Icon name="activity" class="h-4 w-4 text-amber-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Activité récente</h3>
                                    <p class="text-gray-600">Suivez en temps réel toutes les actions importantes sur vos projets</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <!-- IMAGE: Screenshot du tableau de bord avec graphiques, stats et activités récentes -->
                        <!-- Montrer le dashboard principal avec le graphique des revenus, les cartes de statistiques, les tâches urgentes et l'activité récente -->
                        <div class="aspect-[16/10] w-full rounded-2xl border border-gray-200 bg-gray-100 shadow-2xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mini Feature: Graphique revenus -->
        <section class="bg-white py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
                    <!-- Content -->
                    <div class="lg:pr-8">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
                            <Icon name="trending-up" class="h-3 w-3" />
                            <span class="text-xs font-medium">Analytics</span>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-gray-900">
                            Visualisez facturé vs encaissé
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Comparez d'un coup d'œil vos revenus facturés et réellement encaissés. Identifiez les décalages et optimisez votre trésorerie sur la période de votre choix.
                        </p>
                    </div>

                    <!-- Mockup -->
                    <div>
                        <div class="relative mx-auto w-[85%] max-w-md">
                            <!-- Revenue Chart mockup -->
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                                <!-- Title -->
                                <div class="mb-4 flex items-center gap-2">
                                    <div class="h-3 w-3 rounded bg-gray-100"></div>
                                    <div class="h-3 w-32 rounded bg-gray-100"></div>
                                </div>

                                <!-- Period selector -->
                                <div class="mb-4 h-8 w-24 rounded bg-gray-100"></div>

                                <!-- Stats (detailed) -->
                                <div class="mb-6 grid grid-cols-2 gap-3">
                                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                        <div class="text-xs text-blue-600 font-medium mb-1">Total facturé</div>
                                        <div class="text-xl font-bold text-blue-700">18.750€</div>
                                    </div>
                                    <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                                        <div class="text-xs text-green-600 font-medium mb-1">Total encaissé</div>
                                        <div class="text-xl font-bold text-green-700">14.200€</div>
                                    </div>
                                </div>

                                <!-- Legend -->
                                <div class="mb-3 flex justify-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                        <span class="text-xs text-gray-600">Revenus facturé</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                        <span class="text-xs text-gray-600">Revenus perçus</span>
                                    </div>
                                </div>

                                <!-- Chart with dual lines -->
                                <div class="h-24 w-full rounded-lg border border-gray-100 bg-gray-50 p-3">
                                    <svg viewBox="0 0 200 60" class="h-full w-full">
                                        <!-- Grid lines -->
                                        <defs>
                                            <pattern id="grid" width="20" height="15" patternUnits="userSpaceOnUse">
                                                <path d="M 20 0 L 0 0 0 15" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                                            </pattern>
                                        </defs>
                                        <rect width="200" height="60" fill="url(#grid)" />

                                        <!-- Facturé line (blue) -->
                                        <polyline fill="none" stroke="#3b82f6" stroke-width="2" points="10,45 35,42 60,38 85,35 110,28 135,25 160,20 185,15"/>
                                        <polygon fill="#3b82f6" fill-opacity="0.1" points="10,45 35,42 60,38 85,35 110,28 135,25 160,20 185,15 185,50 10,50"/>

                                        <!-- Encaissé line (green) -->
                                        <polyline fill="none" stroke="#10b981" stroke-width="2" points="10,50 35,48 60,45 85,43 110,40 135,38 160,30 185,25"/>
                                        <polygon fill="#10b981" fill-opacity="0.1" points="10,50 35,48 60,45 85,43 110,40 135,38 160,30 185,25 185,55 10,55"/>

                                        <!-- Data points -->
                                        <circle cx="185" cy="15" r="2" fill="#3b82f6"/>
                                        <circle cx="185" cy="25" r="2" fill="#10b981"/>
                                    </svg>
                                </div>

                                <!-- Gap indicator -->
                                <div class="mt-3 flex justify-between text-xs">
                                    <span class="text-gray-500">Jan</span>
                                    <div class="text-orange-600 font-medium">
                                        Écart: 4.550€
                                    </div>
                                    <span class="text-gray-500">Juin</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-[100em] px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gray-50 px-8 py-20 sm:px-16 lg:px-24">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-50 via-white to-gray-50"></div>

                    <div class="relative mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            Prêt à simplifier votre gestion ?
                        </h2>
                        <p class="mt-6 text-lg leading-8 text-gray-600">
                            Rejoignez les freelances qui ont choisi l'efficacité.
                        </p>

                        <div class="mt-10">
                            <Button
                                size="lg"
                                class="bg-gray-900 px-10 py-4 text-lg font-semibold text-white hover:bg-gray-800 transition-colors"
                                as-child
                            >
                                <Link :href="route('register')">
                                    Commencer maintenant
                                </Link>
                            </Button>
                        </div>

                        <div class="mt-8 flex items-center justify-center space-x-8 text-sm text-gray-500">
                            <div>Essai gratuit 30 jours</div>
                            <div class="h-4 w-px bg-gray-300"></div>
                            <div>Sans engagement</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100">
            <div class="mx-auto max-w-[100em] px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-4">
                    <!-- Brand -->
                    <div class="lg:col-span-1">
                        <div class="flex items-center space-x-3 mb-6">
                            <img src="/resources/images/stone_logo.webp" alt="Stone" class="h-10 w-10" />
                            <span class="text-2xl font-bold text-gray-900">Stone</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            La solution de gestion tout-en-un conçue par des freelances français pour simplifier votre quotidien professionnel.
                        </p>
                        <div class="flex items-center space-x-4">
                            <a href="#" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                                <Icon name="mail" class="h-5 w-5 text-gray-600" />
                            </a>
                            <a href="#" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                                <Icon name="phone" class="h-5 w-5 text-gray-600" />
                            </a>
                        </div>
                    </div>

                    <!-- Product -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Produit</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Fonctionnalités</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Tarifs</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Sécurité</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Mises à jour</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Support</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Centre d'aide</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Documentation</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Contact</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Communauté</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Légal</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Mentions légales</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">Confidentialité</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">CGU</a></li>
                            <li><a href="#" class="hover:text-gray-900 transition-colors">RGPD</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom bar -->
                <div class="border-t border-gray-100 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-600 text-sm">
                        © 2025 Stone. Conçu avec ❤️ à Evian-les-bains pour les freelances français.
                    </p>
                    <div class="flex items-center space-x-6 mt-4 md:mt-0">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                            <span>Serveurs français</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <Icon name="shield-check" class="h-4 w-4 text-green-600" />
                            <span>RGPD conforme</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.bg-grid-pattern {
    background-image: linear-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>
