<template>
    <div class="space-y-8">
        <!-- Header avec breadcrumb et actions -->
        <ClientsHeader
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.isAnyLoading.value"
            @refresh="handleRefresh"
            @create="handleCreate"
        />

        <!-- Grille de statistiques -->
        <ClientsStatsCards
            :stats="listManager.globalState.stats"
            :current-filters="listManager.filters.filters"
            :is-loading="skeletonLoader.showSkeleton(true, listManager.loadingStates.stats)"
            @filter-selected="handleStatsFilterClick"
        />

        <!-- Panneau de filtres -->
        <ClientsFilterPanel
            v-model:visible="showFilters"
            :filters="listManager.filters.filters"
            :active-filters="listManager.filters.activeFilters.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.loadingStates.filters"
            @update:filters="handleFiltersUpdate"
            @clear-filter="handleClearFilter"
            @clear-all="handleClearAllFilters"
        />

        <!-- Cartes des clients -->
        <ClientsCards
            :clients="listManager.displayedClients.value"
            :is-loading="skeletonLoader.showSkeleton(true, listManager.loadingStates.clients)"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            @client-click="handleClientClick"
            @client-delete="handleClientDelete"
        />

        <!-- Pagination -->
        <ClientsPagination
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
        <ClientsEmptyState
            v-if="listManager.isEmpty.value && !listManager.isAnyLoading.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            @clear-filters="handleClearAllFilters"
            @create-client="handleCreate"
        />

        <!-- Gestion des erreurs -->
        <div v-if="listManager.hasError.value" class="rounded-lg bg-red-50 p-4">
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
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, provide } from 'vue'
import { useClientListManager } from '@/composables/clients/list'
import { useClientActions } from '@/composables/clients/list/useClientActions'
import { useSkeletonLoader } from '@/composables/useSkeletonLoader'
import { useConnectionDetection } from '@/composables/useConnectionDetection'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'

// Import des sous-composants
import ClientsHeader from './ClientsHeader.vue'
import ClientsStatsCards from './ClientsStatsCards.vue'
import ClientsFilterPanel from './ClientsFilterPanel.vue'
import ClientsCards from './ClientsCards.vue'
import ClientsPagination from './ClientsPagination.vue'
import ClientsEmptyState from './ClientsEmptyState.vue'

import type { ClientListProps } from '@/types/clients/list'
import type { ClientDTO } from '@/types/models'

/**
 * Container principal pour la liste des clients
 * Responsabilités :
 * - Orchestration de l'état global via useClientListState
 * - Communication entre composants enfants
 * - Gestion des commandes avec historique
 */

const props = defineProps<{
    skeletonData: ClientListProps & {
        skeleton_mode?: boolean
    }
    data?: any
}>()

const emit = defineEmits<{
    'client-deleted': [clientId: number]
    'filters-changed': [filters: any]
}>()

// État global orchestré
const listManager = useClientListManager(props.skeletonData)
const clientActions = useClientActions()
const connectionDetection = useConnectionDetection()
const skeletonLoader = useSkeletonLoader(
    props.skeletonData.skeleton_mode ?? false,
    connectionDetection.getOptimalSkeletonDelay(),
)

// États locaux UI
const showFilters = ref(false)

// Fournir l'état aux composants enfants
provide('listManager', listManager)

// Gestionnaires d'événements
const handleRefresh = async () => {
    await listManager.refreshClients()
}

const handleCreate = () => {
    clientActions.navigateToCreateClient()
}

const handleStatsFilterClick = async (filterKey: string, filterValue: any) => {
    let filters: any = {}

    // Gestion du cas spécial pour effacer les filtres projets
    if (filterKey === 'clear_project_filters') {
        filters = {
            has_projects: undefined,
            has_active_projects: undefined
        }
    } else {
        // Pour les filtres mutuellement exclusifs, on efface les autres
        if (filterKey === 'has_projects') {
            filters = {
                has_projects: filterValue,
                has_active_projects: undefined // Effacer le filtre projets actifs
            }
        } else if (filterKey === 'has_active_projects') {
            filters = {
                has_projects: undefined, // Effacer le filtre has_projects
                has_active_projects: filterValue
            }
        } else {
            filters = { [filterKey]: filterValue }
        }
    }

    // Appel direct - simple et efficace !
    await listManager.applyFilters(filters)
    emit('filters-changed', filters)
}

const handleFiltersUpdate = async (filters: any) => {
    // Appel direct - simple et efficace !
    await listManager.applyFilters(filters)
    emit('filters-changed', filters)
}

const handleClearFilter = async (key: string) => {
    await listManager.clearFilter(key as any)
}

const handleClearAllFilters = async () => {
    // Appel direct - simple et efficace !
    await listManager.clearFilters()
}


const handleClientClick = (client: ClientDTO) => {
    clientActions.navigateToClient(client.id)
}

const handleClientDelete = (clientId: number) => {
    clientActions.deleteClient(
        clientId,
        () => {
            emit('client-deleted', clientId)
            listManager.refreshClients()
        }
    )
}

const handlePageChange = async (page: number) => {
    await listManager.goToPage(page)
}

const handlePageSizeChange = async (size: number) => {
    await listManager.changePageSize(size)
}

const handleRetry = async () => {
    listManager.clearError()
    await listManager.refreshClients()
}

// Raccourcis clavier
const handleKeyboardShortcuts = (e: KeyboardEvent) => {
    // Ctrl/Cmd + F pour focus sur recherche
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault()
        showFilters.value = true
        // Focus sera géré par le composant ClientsFilterPanel
    }
}

// Lifecycle
onMounted(() => {
    document.addEventListener('keydown', handleKeyboardShortcuts)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyboardShortcuts)
    listManager.cleanup()
})
</script>
