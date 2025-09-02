<template>

    <div v-if="listManager.hasError.value" class="flex flex-col gap-4 p-4">

        <!-- Header avec breadcrumb et actions -->
        <EventsHeader
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.isAnyLoading.value"
            @refresh="handleRefresh"
            @create="handleCreate"
        />

        <!-- Gestion des erreurs -->
        <div class="rounded-lg bg-red-50 p-4">

            <div class="flex">
                <div class="flex-shrink-0">
                    <Icon name="x-circle" class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Une erreur est survenue
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ listManager.globalState.error }}</p>
                    </div>
                    <div class="mt-4">
                        <Button
                            variant="outline"
                            size="sm"
                            @click="handleRetry"
                        >
                            Réessayer
                        </Button>
                    </div>
                </div>
            </div>

        </div>

    </div>


    <div v-else class="relative space-y-8">
        <!-- Overlay de suppression en cours -->
        <div
            v-if="isDeletionInProgress"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        >
            <div class="bg-white rounded-lg p-6 shadow-lg flex items-center gap-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-900 font-medium">Suppression en cours...</span>
            </div>
        </div>

        <!-- Header avec breadcrumb et actions -->
        <EventsHeader
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.isAnyLoading.value"
            @refresh="handleRefresh"
            @create="handleCreate"
        />

        <!-- Grille de statistiques -->
        <EventsStatsCards
            :stats="listManager.globalState.stats"
            :current-filters="listManager.filters.filters"
            :is-loading="listManager.isStatsLoading.value"
            @filter-selected="handleStatsFilterClick"
        />

        <!-- Panneau de filtres -->
        <EventsFilterPanel
            v-model:visible="showFilters"
            :filters="listManager.filters.filters"
            :active-filters="listManager.filters.activeFilters.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.loadingStates.filters"
            :available-projects="listManager.availableProjects.value"
            :available-clients="listManager.availableClients.value"
            @update:filters="handleFiltersUpdate"
            @clear-filter="handleClearFilter"
            @clear-all="handleClearAllFilters"
        />

        <!-- Cartes des événements -->
        <EventsCards
            :events="listManager.displayedEvents.value"
            :is-loading="listManager.isCardsLoading.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            @event-click="handleEventClick"
            @event-delete="handleEventDelete"
        />

        <!-- Pagination -->
        <EventsPagination
            v-if="listManager.pagination.paginationState.lastPage > 1"
            :pagination-state="listManager.pagination.paginationState"
            :page-info="listManager.pagination.pageInfo.value"
            :visible-pages="listManager.pagination.visiblePages.value"
            :can-go-next="listManager.pagination.canGoNext.value"
            :can-go-prev="listManager.pagination.canGoPrev.value"
            @page-change="handlePageChange"
            @page-size-change="handlePageSizeChange"
        />

        <!-- État vide -->
        <EventsEmptyState
            v-if="listManager.isEmpty.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            @clear-filters="handleClearAllFilters"
            @create-event="handleCreate"
        />

    </div>

</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, provide } from 'vue'
import { useEventListManager } from '@/composables/events/list'
import { useEventActions } from '@/composables/events/list/useEventActions'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'

// Import des sous-composants
import EventsHeader from './EventsHeader.vue'
import EventsStatsCards from './EventsStatsCards.vue'
import EventsFilterPanel from './EventsFilterPanel.vue'
import EventsCards from './EventsCards.vue'
import EventsPagination from './EventsPagination.vue'
import EventsEmptyState from './EventsEmptyState.vue'

import type { EventListProps } from '@/types/events/list'
import type { EventDTO } from '@/types/models'

const props = defineProps<{
    skeletonData: EventListProps & {
        clients?: Array<{ id: number; name: string }>
    }
    eventsData?: any
}>()

const emit = defineEmits<{
    'event-deleted': [eventId: number]
    'filters-changed': [filters: any]
}>()

// État global orchestré - Utiliser eventsData si disponible, sinon skeletonData
const initialData = props.eventsData || props.skeletonData
const listManager = useEventListManager(initialData)
const eventActions = useEventActions()

// États locaux UI
const showFilters = ref(false)
const isDeletionInProgress = ref(false)

// Fournir l'état aux composants enfants
provide('listManager', listManager)



// Gestionnaires d'événements
const handleRefresh = async () => {
    await listManager.refreshEvents()
}

const handleCreate = () => {
    eventActions.navigateToCreateEvent()
}

const handleStatsFilterClick = async (filterKey: string, filterValue: any) => {
    let filters: any = {}

    if (filterKey === 'clear_status_filter') {
        filters = {
            status: undefined,
        }
    } else {
        filters = { [filterKey]: filterValue }
    }

    await listManager.applyFilters(filters)
    emit('filters-changed', filters)
}

const handleFiltersUpdate = async (filters: any) => {
    await listManager.applyFilters(filters)
    emit('filters-changed', filters)
}

const handleClearFilter = async (key: string) => {
    await listManager.clearFilter(key as any)
}

const handleClearAllFilters = async () => {
    await listManager.clearFilters()
}

const handleEventClick = (event: EventDTO) => {
    eventActions.navigateToEvent(event.id)
}

const handleEventDelete = (eventId: number) => {
    // Phase 1 : Loader de suppression
    isDeletionInProgress.value = true

    eventActions.deleteEvent(eventId, {
        onSuccess: async () => {
            // Fin phase 1
            isDeletionInProgress.value = false
            // Phase 2 : Skeleton pour le rechargement des données
            listManager.setDeletionLoading(true)
            await listManager.fetchEventsData()
            emit('event-deleted', eventId)
        },
        onFinish: () => {
            // S'assurer que tout est nettoyé
            isDeletionInProgress.value = false
            listManager.setDeletionLoading(false)
        },
        onError: () => {
            // Nettoyer en cas d'erreur
            isDeletionInProgress.value = false
        }
    })
}

const handlePageChange = async (page: number) => {
    await listManager.goToPage(page)
}

const handlePageSizeChange = async (size: number) => {
    await listManager.changePageSize(size)
}

const handleRetry = async () => {
    listManager.clearError()
    await listManager.refreshEvents()
}

// Raccourcis clavier
const handleKeyboardShortcuts = (e: KeyboardEvent) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault()
        showFilters.value = true
    }
}

// Lifecycle - Gestionnaire clavier
onMounted(() => {
    document.addEventListener('keydown', handleKeyboardShortcuts)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyboardShortcuts)
    listManager.cleanup()
})
</script>
