<template>
    <div class="relative space-y-8">
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
            :is-loading="listManager.isStatsLoading.value"
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
            :is-loading="listManager.isCardsLoading.value"
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
            v-if="listManager.isEmpty.value"
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
import { ref, provide } from 'vue'
import { useClientListManager } from '@/composables/clients/list'
import { useClientActions } from '@/composables/clients/list/useClientActions'
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
    skeletonData: ClientListProps
    clientsData?: any
}>()

const emit = defineEmits<{
    'client-deleted': [clientId: number]
    'filters-changed': [filters: any]
}>()


// État global orchestré - Utiliser clientsData si disponible, sinon skeletonData
const initialData = props.clientsData || props.skeletonData
const listManager = useClientListManager(initialData)
const clientActions = useClientActions()

// États locaux UI
const showFilters = ref(false)
const isDeletionInProgress = ref(false)

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
            has_active_projects: undefined,
            has_overdue_payments: undefined
        }
    } else {
        // Chaque filtre de stats efface tous les autres filtres de stats
        filters = {
            has_projects: undefined,
            has_active_projects: undefined,
            has_overdue_payments: undefined,
            [filterKey]: filterValue // Appliquer uniquement le filtre sélectionné
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
    // Phase 1 : Loader de suppression
    isDeletionInProgress.value = true

    clientActions.deleteClient(clientId, {
        onSuccess: async () => {
            // Fin phase 1
            isDeletionInProgress.value = false
            // Phase 2 : Skeleton pour le rechargement des données
            listManager.setDeletionLoading(true)
            await listManager.fetchClientsData()
            emit('client-deleted', clientId)
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
    await listManager.refreshClients()
}

</script>
