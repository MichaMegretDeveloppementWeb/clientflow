<template>
    <Card class="border border-gray-200 bg-white shadow-sm">
        <CardHeader class="pb-4  border-b border-gray-200/70">
            <div class="flex items-center justify-between">
                <!-- Onglets -->
                <div class="m-auto flex items-center rounded-lg bg-gray-100 p-1">
                    <button
                        @click="handleTabChange('projects')"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-all"
                        :class="
                            activeTab === 'projects' ? 'bg-white text-gray-900 shadow-sm' : 'cursor-pointer text-gray-500 hover:text-gray-700'
                        "
                    >
                        Projets
                    </button>
                    <button
                        @click="handleTabChange('events')"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-all"
                        :class="
                            activeTab === 'events' ? 'bg-white text-gray-900 shadow-sm' : 'cursor-pointer text-gray-500 hover:text-gray-700'
                        "
                    >
                        Événements
                    </button>
                </div>
            </div>

            <!-- Filtres pour les événements -->
            <div v-if="activeTab === 'events'" class="px-4 py-4 sm:px-6 mt-6">
                <div class="space-y-4 flex flex-col lg:flex-row lg:items-center lg:justify-start gap-12">
                    <!-- Ligne 1: Label + Filtres de statut -->
                    <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4 mb-0">
                        <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Filtres :</span>
                        <div class="flex flex-wrap items-center gap-1 rounded-lg bg-white p-1 shadow-sm sm:gap-0">
                            <button
                                v-for="filter in availableEventFilters"
                                :key="filter.key"
                                @click="handleFilterChange(filter.key)"
                                class="flex-1 rounded-md px-2 py-1.5 text-xs font-medium transition-all sm:flex-none sm:px-3 sm:text-sm"
                                :class="
                                    currentEventFilter === filter.key
                                        ? 'bg-blue-500 text-white shadow-sm'
                                        : 'cursor-pointer text-gray-600 hover:text-gray-900'
                                "
                                :title="filter.description"
                            >
                                {{ filter.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Ligne 2: Checkbox en retard (seulement si visible) -->
                    <div v-if="canShowOverdueFilter" class="flex lg:justify-start justify-center">
                        <label class="flex items-center cursor-pointer group">
                            <input
                                type="checkbox"
                                :checked="showOverdueOnly"
                                @change="handleOverdueFilterChange"
                                class="h-4 w-4 text-red-600 border-gray-300 rounded"
                            />
                            <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900 flex items-center">
                                <Icon name="clock" class="inline h-3 w-3 mr-1 text-red-500" />
                                En retard uniquement
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </CardHeader>
        <CardContent class="p-0">
            <!-- Loading state -->
            <div v-if="isLoading" class="animate-pulse p-6">
                <div class="space-y-4">
                    <div v-for="i in 3" :key="i" class="flex items-center space-x-4">
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-1/4 bg-gray-200 rounded"></div>
                            <div class="h-3 w-1/2 bg-gray-200 rounded"></div>
                        </div>
                        <div class="h-4 w-4 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Vue Projets -->
            <div v-else-if="activeTab === 'projects' && client">
                <div v-if="!showProjects || showProjects.length === 0" class="p-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                        <Icon name="folder-plus" class="h-8 w-8 text-gray-400" />
                    </div>
                    <h3 class="mb-1 text-lg font-medium text-gray-900">Aucun projet</h3>
                    <p class="mb-4 text-sm text-gray-500">Ce client n'a pas encore de projet</p>
                    <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                        <Link :href="route('projects.create', { client_id: client?.id || 0 })">
                            <Icon name="plus" class="mr-2 h-4 w-4" />
                            Créer le premier projet
                        </Link>
                    </Button>
                </div>
                <div v-else class="divide-y divide-slate-100">
                    <div
                        v-for="project in showProjects"
                        :key="project.id"
                        class="group cursor-pointer transition-all duration-200 hover:bg-slate-50/40 hover:shadow-sm relative min-h-[140px]"
                        @click="() => router.visit(route('projects.show', project.id))"
                    >
                        <!-- Trait fin gauche élégant -->
                        <div class="absolute left-0 top-0 h-full w-px bg-transparent group-hover:bg-slate-300/60 transition-all duration-200"></div>

                        <div class="flex flex-col gap-4 p-6 py-8 lg:flex-row lg:items-center lg:justify-between relative">
                            <!-- Contenu principal -->
                            <div class="flex-1 space-y-2.5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-1.5">
                                        <!-- Nom du projet -->
                                        <div class="flex items-center gap-4">
                                            <h3 class="text-base font-medium transition-colors group-hover:text-emerald-700 text-slate-900">
                                                {{ project.name }}
                                            </h3>
                                            <!-- Badge de statut -->
                                            <span
                                                class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                                :class="getProjectStatusClasses(project.status)"
                                            >
                                                {{ getStatusLabel(project.status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations secondaires -->
                                <div v-if="project.description" class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5">
                                    <span class="flex items-center gap-1.5">
                                        <Icon name="file-text" class="h-3.5 w-3.5 text-slate-400" />
                                        {{ truncateText(project.description, 50) }}
                                    </span>
                                </div>

                                <!-- Métriques business -->
                                <div class="flex flex-wrap items-center gap-3">
                                    <!-- Date de début -->
                                    <span
                                        v-if="project.start_date"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700"
                                    >
                                        <Icon name="calendar" class="h-3 w-3" />
                                        {{ formatDate(project.start_date) }}
                                    </span>

                                    <!-- Budget -->
                                    <span
                                        v-if="project.budget"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                                    >
                                        <Icon name="banknote" class="h-3 w-3" />
                                        {{ formatCurrencyCompact(project.budget) }}
                                    </span>

                                    <!-- Montant facturé -->
                                    <span
                                        v-if="project.billing_total > 0"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-purple-700"
                                    >
                                        <Icon name="receipt" class="h-3 w-3" />
                                        {{ formatCurrencyCompact(project.billing_total) }} facturé
                                    </span>

                                    <!-- Nombre d'événements -->
                                    <span
                                        v-if="project.events_count > 0"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"
                                    >
                                        <Icon name="activity" class="h-3 w-3" />
                                        {{ project.events_count }} événement{{ project.events_count > 1 ? 's' : '' }}
                                    </span>
                                </div>

                                <!-- Indicateurs d'alerte -->
                                <div v-if="hasAnyAlerts(project)" class="flex flex-wrap items-center gap-2 mt-2">
                                    <!-- Projet en retard -->
                                    <span
                                        v-if="isProjectOverdue(project)"
                                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                                    >
                                        <Icon name="clock" class="h-3 w-3" />
                                        Projet en retard
                                    </span>

                                    <!-- Événements en retard -->
                                    <span
                                        v-if="project.has_overdue_events"
                                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-orange-700 bg-orange-50 rounded-md ring-1 ring-orange-200"
                                    >
                                        <Icon name="alert-triangle" class="h-3 w-3" />
                                        Tâches en retard
                                    </span>

                                    <!-- Retards de paiement -->
                                    <span
                                        v-if="project.has_payment_overdue"
                                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                                    >
                                        <Icon name="credit-card" class="h-3 w-3" />
                                        Paiements en retard
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue Événements -->
            <div v-else-if="activeTab === 'events'">
                <div v-if="!showEvents || showEvents.length === 0" class="p-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                        <Icon name="calendar" class="h-8 w-8 text-gray-400" />
                    </div>
                    <h3 class="mb-1 text-lg font-medium text-gray-900">
                        {{ currentEventFilter === 'all' ? 'Aucun événement' : `Aucun événement ${activeEventFilter.label.toLowerCase()}` }}
                    </h3>
                    <p class="mb-4 text-sm text-gray-500">
                        {{ currentEventFilter === 'all'
                            ? "Ce client n'a pas encore d'événement"
                            : `Aucun événement ne correspond au filtre "${activeEventFilter.label}"`
                        }}
                    </p>
                </div>
                <div v-else class="divide-y divide-slate-100">
                    <div
                        v-for="event in showEvents"
                        :key="event.id"
                        class="group cursor-pointer transition-all duration-200 hover:bg-slate-50/40 hover:shadow-sm relative min-h-[140px]"
                        @click="() => router.visit(route('events.show', event.id))"
                    >
                        <!-- Trait fin gauche élégant -->
                        <div class="absolute left-0 top-0 h-full w-px bg-transparent group-hover:bg-slate-300/60 transition-all duration-200"></div>

                        <div class="flex flex-col gap-4 p-6 py-8 lg:flex-row lg:items-center lg:justify-between relative">
                            <!-- Contenu principal -->
                            <div class="flex-1 space-y-2.5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-1.5">
                                        <!-- Nom de l'événement -->
                                        <div class="flex items-center gap-4">
                                            <h3 class="text-base font-medium transition-colors group-hover:text-emerald-700 text-slate-900">
                                                {{ event.name }}
                                            </h3>
                                            <!-- Badge de type -->
                                            <span
                                                class="inline-flex w-fit items-center rounded-full text-sm font-medium gap-1"
                                                :class="getEventTypeClasses(event.event_type)"
                                            >
                                                <Icon v-if="event.event_type === 'billing'" name="banknote" class="h-3.5 w-3.5 text-emerald-600" />
                                                <Icon v-else name="flag" class="h-3.5 w-3.5 text-blue-600" />
                                                {{ event.event_type_label }}
                                            </span>
                                            <!-- Badge de statut -->
                                            <span
                                                class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-[0.8em] font-medium ring-1 ring-inset"
                                                :class="getEventStatusClasses(event.status)"
                                            >
                                                {{ event.status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5 mt-3">
                                    <!-- Date d'éxécution -->
                                    <span
                                        v-if="event.execution_date || event.send_date"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"
                                    >
                                        <Icon name="calendar" class="h-3 w-3" />
                                        {{ formatEventDate(event.execution_date || event.send_date) }}
                                    </span>
                                    <!-- Événement en retard -->
                                    <span
                                        v-if="event.is_overdue"
                                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200 w-max"
                                    >
                                        <Icon name="clock" class="h-3 w-3" />
                                        {{ event.event_type === 'billing' ? "Envoi" : "Exécution" }} en retard
                                    </span>
                                </div>

                                <!-- Informations secondaires -->
                                <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5 mt-3">
                                    <span v-if="event.project?.name" class="flex items-center gap-1.5">
                                        <Icon name="folder" class="h-3.5 w-3.5 text-slate-400" />
                                        {{ event.project.name }}
                                    </span>
                                    <span v-if="event.description" class="flex items-center gap-1.5">
                                        <Icon name="file-text" class="h-3.5 w-3.5 text-slate-400" />
                                        {{ truncateText(event.description, 50) }}
                                    </span>
                                </div>

                                <!-- Métriques business -->
                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                    <!-- Montant (pour les facturations) -->
                                    <span
                                        v-if="event.event_type === 'billing' && event.amount"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                                    >
                                        <Icon name="banknote" class="h-3 w-3" />
                                        {{ formatEventAmount(event.amount) }}
                                    </span>

                                    <!-- Statut de paiement (pour les facturations) -->
                                    <span
                                        v-if="event.event_type === 'billing'"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium"
                                        :class="getPaymentStatusClasses(event.payment_status)"
                                    >
                                        <Icon name="credit-card" class="h-3 w-3" />
                                        {{ getPaymentStatusLabel(event.payment_status) }}
                                    </span>
                                    <!--Date échéance-->
                                    <span
                                        v-if="event.event_type === 'billing' && (event.paid_at || event.payment_due_date)"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium"
                                        :class="(event.is_payment_overdue ?? false) ? 'text-orange-700' : 'text-blue-600'"
                                    >
                                        <Icon name="calendar" class="h-3 w-3" />
                                        {{ formatEventDate(event.paid_at || event.payment_due_date) }}
                                    </span>
                                </div>

                                <!-- Indicateurs d'alerte -->
                                <div v-if="hasAnyEventAlerts(event)" class="flex flex-wrap items-center gap-2 mt-2">
                                    <!-- Paiement en retard -->
                                    <span
                                        v-if="event.is_payment_overdue ?? false"
                                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                                    >
                                        <Icon name="credit-card" class="h-3 w-3" />
                                        Paiement en retard
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="shouldShowPagination" class="border-t border-gray-200/70 bg-gradient-to-r from-gray-50/50 to-white px-6 py-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Pagination info -->
                    <div class="text-center text-sm text-gray-600 sm:text-left">
                        <span class="inline-flex items-center gap-1">
                            <span class="font-semibold text-gray-900">{{ activePagination.start }}</span>
                            <span class="text-gray-400">à</span>
                            <span class="font-semibold text-gray-900">{{ activePagination.end }}</span>
                            <span class="text-gray-400">sur</span>
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">
                                {{ activePagination.total }}
                            </span>
                            <span class="text-gray-500">{{ activeTab === 'projects' ? 'projets' : 'événements' }}</span>
                        </span>
                    </div>

                    <!-- Pagination controls -->
                    <div class="flex items-center justify-center gap-3">
                        <!-- Previous button -->
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="activePagination.currentPage <= 1"
                            @click="goToPage(activePagination.currentPage - 1)"
                            class="group rounded-lg border-gray-300 bg-white px-3 py-2 text-gray-700 shadow-sm transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Icon name="chevron-left" class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                            <span class="ml-1.5 hidden font-medium sm:inline">Précédent</span>
                        </Button>

                        <!-- Page numbers -->
                        <div class="hidden items-center gap-1.5 sm:flex">
                            <template v-for="(page, index) in visiblePages" :key="index">
                                <Button
                                    v-if="page === -1"
                                    variant="ghost"
                                    size="sm"
                                    disabled
                                    class="h-9 w-9 cursor-default text-gray-400"
                                >
                                    <Icon name="more-horizontal" class="h-4 w-4" />
                                </Button>
                                <Button
                                    v-else
                                    :variant="page === activePagination.currentPage ? 'default' : 'outline'"
                                    size="sm"
                                    class="h-9 w-9 rounded-lg font-medium shadow-sm transition-all duration-200"
                                    :class="page === activePagination.currentPage
                                        ? 'border-blue-500 bg-gradient-to-b from-blue-500 to-blue-600 text-white shadow-blue-500/25'
                                        : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700'"
                                    @click="goToPage(page)"
                                >
                                    {{ page }}
                                </Button>
                            </template>
                        </div>

                        <!-- Mobile page indicator -->
                        <div class="flex items-center gap-2.5 text-sm font-medium text-gray-600 sm:hidden">
                            <span class="text-gray-500">Page {{ activePagination.currentPage }} sur {{ activePagination.lastPage }}</span>
                        </div>

                        <!-- Next button -->
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="activePagination.currentPage >= activePagination.lastPage"
                            @click="goToPage(activePagination.currentPage + 1)"
                            class="group rounded-lg border-gray-300 bg-white px-3 py-2 text-gray-700 shadow-sm transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span class="mr-1.5 hidden font-medium sm:inline">Suivant</span>
                            <Icon name="chevron-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                        </Button>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { ref, computed, watch, toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader } from '@/components/ui/card'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ClientDetailProjectsProps, Event } from '@/types/clients/detail/index'
import { useClientDetailPagination } from '@/composables/clients/detail/useClientDetailPagination'
import { useClientDetailFilters, type EventFilterType } from '@/composables/clients/detail/useClientDetailFilters'

const props = defineProps<ClientDetailProjectsProps>()

// État pour l'onglet actif
const activeTab = ref<'projects' | 'events'>('projects')

// Utilisation du composable de filtres
const {
    currentEventFilter,
    showOverdueOnly,
    availableEventFilters,
    activeEventFilter,
    canShowOverdueFilter,
    setEventFilter,
    setShowOverdueOnly,
    filterEvents
} = useClientDetailFilters()

// Événements filtrés côté client AVANT pagination
const filteredEvents = computed(() => {
    return filterEvents(props.events, currentEventFilter.value, showOverdueOnly.value)
})

// Utilisation du composable de pagination avec les événements filtrés
const {
    paginatedProjects,
    paginatedEvents,
    projectsPagination,
    eventsPagination,
    projectsVisiblePages,
    eventsVisiblePages,
    goToProjectPage,
    goToEventPage,
    resetPages,
    shouldShowProjectsPagination,
    shouldShowEventsPagination
} = useClientDetailPagination(
    toRef(props, 'projects'),
    filteredEvents,
    toRef(props, 'isLoading'),
    5 // 5 éléments par page
)

// Computed pour les données actives selon l'onglet
const showProjects = computed(() => paginatedProjects.value)
const showEvents = computed(() => paginatedEvents.value)

// Pagination active selon l'onglet
const activePagination = computed(() => {
    return activeTab.value === 'projects' ? projectsPagination.value : eventsPagination.value
})

// Pages visibles selon l'onglet
const visiblePages = computed(() => {
    return activeTab.value === 'projects' ? projectsVisiblePages.value : eventsVisiblePages.value
})

// Affichage de la pagination selon l'onglet
const shouldShowPagination = computed(() => {
    return activeTab.value === 'projects' ? shouldShowProjectsPagination.value : shouldShowEventsPagination.value
})

// Actions de pagination
const goToPage = (page: number) => {
    if (activeTab.value === 'projects') {
        goToProjectPage(page)
    } else {
        goToEventPage(page)
    }
}

// Handlers pour les changements
const handleTabChange = (tab: 'projects' | 'events') => {
    activeTab.value = tab
    resetPages()
    // Plus besoin de recharger - les données sont maintenant toujours présentes
}

const handleFilterChange = (filter: EventFilterType) => {
    setEventFilter(filter)
    resetPages() // Reset pagination quand on change de filtre
    // Plus besoin d'émettre vers le backend - filtrage côté client
}

const handleOverdueFilterChange = (inputEvent: InputEvent) => {
    const target = inputEvent.target as HTMLInputElement
    setShowOverdueOnly(target.checked)
    resetPages() // Reset pagination quand on change le filtre
}


// Reset pagination when changing tabs
watch(activeTab, () => {
    resetPages()
})

// Fonctions utilitaires - seulement celles utilisées
function getStatusLabel(status: string): string {
    const labels: Record<string, string> = {
        'active': 'Actif',
        'completed': 'Terminé',
        'on_hold': 'En pause',
        'cancelled': 'Annulé'
    }
    return labels[status] || status
}

function getProjectStatusClasses(status: string): string {
    const classes: Record<string, string> = {
        'active': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'completed': 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'on_hold': 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'cancelled': 'bg-red-50 text-red-700 ring-red-600/20'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

function truncateText(text: string, maxLength: number): string {
    return text.length > maxLength ? `${text.slice(0, maxLength)}...` : text
}

function formatCurrencyCompact(amount: number): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
        notation: amount >= 1000 ? 'compact' : 'standard'
    }).format(amount)
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('fr-FR')
}

function getEventStatusClasses(status: string): string {
    const classes: Record<string, string> = {
        'todo': 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'done': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'to_send': 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'sent': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'cancelled': 'bg-red-50 text-red-700 ring-red-600/20'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

function getEventTypeClasses(eventType: string): string {
    const classes: Record<string, string> = {
        'step': 'text-blue-700',
        'billing': 'text-emerald-700'
    }
    return classes[eventType] || 'text-gray-700'
}

function getPaymentStatusClasses(paymentStatus: string | undefined): string {
    if (paymentStatus === 'paid') {
        return 'text-emerald-700'
    }
    return 'text-orange-700'
}

function hasAnyEventAlerts(event: Event): boolean {
    return event.is_overdue || (event.is_payment_overdue ?? false)
}

function formatEventDate(dateString: string | undefined): string {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'numeric',
        year: 'numeric'
    })
}

function formatEventAmount(amount: number | undefined): string {
    if (!amount) return '0€'

    if (amount >= 1000000) {
        return `${(amount / 1000000).toFixed(1).replace('.0', '')}M€`
    } else if (amount >= 1000) {
        return `${(amount / 1000).toFixed(1).replace('.0', '')}k€`
    } else {
        return `${amount}€`
    }
}

function getPaymentStatusLabel(paymentStatus: string | undefined): string {
    return paymentStatus === 'paid' ? 'Payé' : 'À payer'
}

// Fonctions utilitaires pour les alertes projet
function isProjectOverdue(project: any): boolean {
    if (!project.end_date) return false
    const endDate = new Date(project.end_date)
    const now = new Date()
    return endDate < now && project.status === 'active'
}

function hasAnyAlerts(project: any): boolean {
    return isProjectOverdue(project) || 
           project.has_overdue_events || 
           project.has_payment_overdue
}
</script>
