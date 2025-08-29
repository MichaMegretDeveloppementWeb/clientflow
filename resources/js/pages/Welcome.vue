<template>
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav class="relative sticky top-0 z-50 border-b border-gray-100 bg-white/95 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600">
                                <Icon name="users" class="h-5 w-5 text-white" />
                            </div>
                            <div class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-emerald-400"></div>
                        </div>
                        <span class="bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-xl font-bold text-transparent">ClientFlow</span>
                    </div>
                    <div class="hidden items-center space-x-1 md:flex">
                        <Button variant="ghost" class="font-medium text-gray-600 hover:text-gray-900" as-child>
                            <Link :href="route('login')"> Se connecter </Link>
                        </Button>
                        <Button
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 font-medium text-white shadow-lg shadow-blue-500/25 transition-all duration-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-blue-500/40"
                            as-child
                        >
                            <Link :href="route('register')"> Commencer gratuitement </Link>
                        </Button>
                    </div>
                    <div class="md:hidden">
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="route('login')"> Connexion </Link>
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

            <div class="relative mx-auto max-w-7xl px-4 pt-20 pb-24 sm:px-6 sm:pt-32 sm:pb-32 lg:px-8">
                <div class="text-center">
                    <!-- Badge -->
                    <div
                        class="mb-8 inline-flex items-center gap-2 rounded-full border border-blue-200/50 bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-2"
                    >
                        <div class="h-2 w-2 animate-pulse rounded-full bg-gradient-to-r from-green-400 to-emerald-400"></div>
                        <span class="text-sm font-medium text-blue-700">Nouveau : Suivi temps reel des projets</span>
                        <Icon name="arrow-right" class="h-3 w-3 text-blue-600" />
                    </div>

                    <!-- Main Heading -->
                    <h1 class="mx-auto max-w-4xl text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                        Transformez votre
                        <span class="relative">
                            <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent"
                                >gestion client</span
                            >
                            <svg class="absolute -bottom-2 left-0 h-3 w-full text-blue-200" viewBox="0 0 300 12" fill="none">
                                <path d="M1 6C50 2 100 2 150 6C200 10 250 10 299 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </span>
                        <br />en solution intelligente
                    </h1>

                    <!-- Subtitle -->
                    <p class="mx-auto mt-8 max-w-2xl text-xl leading-8 font-light text-gray-600">
                        La plateforme tout-en-un qui simplifie la gestion de vos clients, projets et revenus. Conçue par des freelances, pour des
                        freelances.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Button
                            size="lg"
                            class="group w-full bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-4 text-lg font-semibold text-white shadow-lg shadow-blue-500/25 transition-all duration-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-blue-500/40 sm:w-auto"
                            as-child
                        >
                            <Link :href="route('register')">
                                <Icon name="rocket" class="mr-2 h-5 w-5 transition-transform group-hover:scale-110" />
                                Commencer gratuitement
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            size="lg"
                            class="group w-full border-gray-300 px-8 py-4 text-lg font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
                            as-child
                        >
                            <Link :href="route('login')">
                                <Icon name="play" class="mr-2 h-5 w-5 transition-transform group-hover:translate-x-1" />
                                Voir la demo
                            </Link>
                        </Button>
                    </div>

                    <!-- Social Proof -->
                    <div class="mt-16 flex flex-col items-center">
                        <p class="mb-6 text-sm font-medium text-gray-500">Fait confiance par 1000+ freelances</p>
                        <div class="flex items-center space-x-8 opacity-60">
                            <div class="flex items-center space-x-2">
                                <div class="h-8 w-8 rounded bg-gray-200"></div>
                                <span class="text-sm font-medium text-gray-400">Company A</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="h-8 w-8 rounded bg-gray-200"></div>
                                <span class="text-sm font-medium text-gray-400">Company B</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="h-8 w-8 rounded bg-gray-200"></div>
                                <span class="text-sm font-medium text-gray-400">Company C</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-gray-50 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="mb-20 text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                        <Icon name="zap" class="h-4 w-4" />
                        <span class="text-sm font-medium">Fonctionnalites</span>
                    </div>
                    <h2 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Tout ce dont vous avez besoin</h2>
                    <p class="mx-auto max-w-2xl text-xl text-gray-600">
                        Une suite complete d'outils pour gerer votre activite freelance efficacement
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div
                        class="group relative rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-900/10"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 transition-transform duration-300 group-hover:scale-110"
                        >
                            <Icon name="users" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">Gestion des clients</h3>
                        <p class="mb-4 leading-relaxed text-gray-600">
                            Centralisez toutes les informations de vos clients. Contacts, historique, notes et documents en un seul endroit.
                        </p>
                        <div class="flex items-center text-sm font-medium text-blue-600">
                            En savoir plus
                            <Icon name="arrow-right" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="group relative rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-900/10"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 transition-transform duration-300 group-hover:scale-110"
                        >
                            <Icon name="folder-kanban" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">Suivi des projets</h3>
                        <p class="mb-4 leading-relaxed text-gray-600">
                            Organisez vos projets par statut, definissez des budgets et suivez l'avancement en temps reel avec des tableaux de bord
                            intuitifs.
                        </p>
                        <div class="flex items-center text-sm font-medium text-emerald-600">
                            En savoir plus
                            <Icon name="arrow-right" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="group relative rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-900/10"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 transition-transform duration-300 group-hover:scale-110"
                        >
                            <Icon name="clock" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">Timeline intelligente</h3>
                        <p class="mb-4 leading-relaxed text-gray-600">
                            Gardez un historique complet de tous vos echanges et etapes importantes. Ne perdez plus jamais le fil.
                        </p>
                        <div class="flex items-center text-sm font-medium text-purple-600">
                            En savoir plus
                            <Icon name="arrow-right" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div
                        class="group relative rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-900/10"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 transition-transform duration-300 group-hover:scale-110"
                        >
                            <Icon name="bar-chart-3" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">Analyses avancees</h3>
                        <p class="mb-4 leading-relaxed text-gray-600">
                            Obtenez des insights sur votre activite avec des rapports detailles et des metriques en temps reel.
                        </p>
                        <div class="flex items-center text-sm font-medium text-amber-600">
                            En savoir plus
                            <Icon name="arrow-right" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div
                        class="group relative rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-900/10"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 transition-transform duration-300 group-hover:scale-110"
                        >
                            <Icon name="shield-check" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">Securite enterprise</h3>
                        <p class="mb-4 leading-relaxed text-gray-600">
                            Vos donnees sont protegees avec un chiffrement de niveau bancaire et des sauvegardes automatiques.
                        </p>
                        <div class="flex items-center text-sm font-medium text-rose-600">
                            En savoir plus
                            <Icon name="arrow-right" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div
                        class="group relative rounded-2xl border border-gray-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-900/10"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 transition-transform duration-300 group-hover:scale-110"
                        >
                            <Icon name="smartphone" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">Mobile-first</h3>
                        <p class="mb-4 leading-relaxed text-gray-600">
                            Acces complet depuis votre mobile. Gerer vos clients et projets ou que vous soyez, quand vous voulez.
                        </p>
                        <div class="flex items-center text-sm font-medium text-indigo-600">
                            En savoir plus
                            <Icon name="arrow-right" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Des resultats qui parlent</h2>
                    <p class="text-xl text-gray-600">Rejoignez des milliers de freelances qui ont transforme leur activite</p>
                </div>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center">
                        <div class="mb-2 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-4xl font-bold text-transparent">98%</div>
                        <div class="font-medium text-gray-600">Satisfaction client</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-4xl font-bold text-transparent">2.5x</div>
                        <div class="font-medium text-gray-600">Gain de productivite</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-4xl font-bold text-transparent">50h</div>
                        <div class="font-medium text-gray-600">Economisees par mois</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-4xl font-bold text-transparent">24/7</div>
                        <div class="font-medium text-gray-600">Support disponible</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 py-24 sm:py-32">
            <!-- Background Pattern -->
            <div class="bg-grid-pattern absolute inset-0 opacity-10"></div>
            <div class="absolute top-0 left-1/4 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="absolute right-1/4 bottom-0 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>

            <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <h2 class="mb-6 text-4xl font-bold tracking-tight text-white sm:text-5xl">Pret a transformer votre activite ?</h2>
                <p class="mx-auto mb-12 max-w-2xl text-xl text-blue-100">
                    Rejoignez des milliers de freelances qui ont choisi ClientFlow pour simplifier leur gestion quotidienne et accelerer leur
                    croissance.
                </p>

                <div class="mb-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Button
                        size="lg"
                        variant="secondary"
                        class="group w-full bg-white px-8 py-4 text-lg font-semibold text-gray-900 shadow-xl hover:bg-gray-100 sm:w-auto"
                        as-child
                    >
                        <Link :href="route('register')">
                            <Icon name="rocket" class="mr-2 h-5 w-5 transition-transform group-hover:scale-110" />
                            Commencer gratuitement
                        </Link>
                    </Button>
                    <Button
                        variant="outline"
                        size="lg"
                        class="w-full border-white/30 px-8 py-4 text-lg font-medium text-white hover:bg-white/10 sm:w-auto"
                        as-child
                    >
                        <Link :href="route('login')">
                            <Icon name="log-in" class="mr-2 h-5 w-5" />
                            Se connecter
                        </Link>
                    </Button>
                </div>

                <div class="flex items-center justify-center space-x-6 text-sm text-blue-200">
                    <div class="flex items-center">
                        <Icon name="check" class="mr-2 h-4 w-4 text-emerald-400" />
                        Essai gratuit 14 jours
                    </div>
                    <div class="flex items-center">
                        <Icon name="check" class="mr-2 h-4 w-4 text-emerald-400" />
                        Aucune carte requise
                    </div>
                    <div class="flex items-center">
                        <Icon name="check" class="mr-2 h-4 w-4 text-emerald-400" />
                        Annulation facile
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-800 bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between md:flex-row">
                    <div class="mb-4 flex items-center space-x-3 md:mb-0">
                        <div class="relative">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600">
                                <Icon name="users" class="h-5 w-5 text-white" />
                            </div>
                            <div class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-emerald-400"></div>
                        </div>
                        <span class="text-xl font-bold text-white">ClientFlow</span>
                    </div>
                    <div class="flex items-center space-x-6 text-sm text-gray-400">
                        <span>2025 ClientFlow. Cree avec passion pour les freelances.</span>
                        <div class="flex items-center space-x-4">
                            <a href="#" class="transition-colors hover:text-white">Confidentialite</a>
                            <a href="#" class="transition-colors hover:text-white">Conditions</a>
                            <a href="#" class="transition-colors hover:text-white">Support</a>
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
