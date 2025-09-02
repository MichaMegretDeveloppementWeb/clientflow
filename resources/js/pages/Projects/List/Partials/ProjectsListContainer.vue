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
        <ProjectsHeader
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.isAnyLoading.value"
            @refresh="handleRefresh"
            @create="handleCreate"
        />

        <!-- Grille de statistiques -->
        <ProjectsStatsCards
            :stats="listManager.globalState.stats"
            :current-filters="listManager.filters.filters"
            :is-loading="listManager.isStatsLoading.value"
            @filter-selected="handleStatsFilterClick"
        />

        <!-- Panneau de filtres -->
        <ProjectsFilterPanel
            v-model:visible="showFilters"
            :filters="listManager.filters.filters"
            :active-filters="listManager.filters.activeFilters.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            :is-loading="listManager.loadingStates.filters"
            :available-clients="listManager.availableClients.value"
            @update:filters="handleFiltersUpdate"
            @clear-filter="handleClearFilter"
            @clear-all="handleClearAllFilters"
        />

        <!-- Cartes des projets -->
        <ProjectsCards
            :projects="listManager.displayedProjects.value"
            :is-loading="listManager.isCardsLoading.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            @project-click="handleProjectClick"
            @project-delete="handleProjectDelete"
        />

        <!-- Pagination -->
        <ProjectsPagination
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
        <ProjectsEmptyState
            v-if="listManager.isEmpty.value"
            :has-active-filters="listManager.filters.hasActiveFilters.value"
            @clear-filters="handleClearAllFilters"
            @create-project="handleCreate"
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
import { useProjectListManager } from '@/composables/projects/list'
import { useProjectActions } from '@/composables/projects/list/useProjectActions'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'

// Import des sous-composants
import ProjectsHeader from './ProjectsHeader.vue'
import ProjectsStatsCards from './ProjectsStatsCards.vue'
import ProjectsFilterPanel from './ProjectsFilterPanel.vue'
import ProjectsCards from './ProjectsCards.vue'
import ProjectsPagination from './ProjectsPagination.vue'
import ProjectsEmptyState from './ProjectsEmptyState.vue'

import type { ProjectListProps } from '@/types/projects/list'
import type { ProjectDTO } from '@/types/models'

const props = defineProps<{
    skeletonData: ProjectListProps & {
        clients?: Array<{ id: number; name: string }>
    }
    projectsData?: any
}>()

const emit = defineEmits<{
    'project-deleted': [projectId: number]
    'filters-changed': [filters: any]
}>()

// État global orchestré - Utiliser projectsData si disponible, sinon skeletonData
const initialData = props.projectsData || props.skeletonData
const listManager = useProjectListManager(initialData)
const projectActions = useProjectActions()

// États locaux UI
const showFilters = ref(false)
const isDeletionInProgress = ref(false)

// Fournir l'état aux composants enfants
provide('listManager', listManager)

// Gestionnaires d'événements
const handleRefresh = async () => {
    await listManager.refreshProjects()
}

const handleCreate = () => {
    projectActions.navigateToCreateProject()
}

const handleStatsFilterClick = async (filterKey: string, filterValue: any) => {
    let filters: any = {}

    if (filterKey === 'clear_status_filter') {
        filters = {
            status: undefined
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

const handleProjectClick = (project: ProjectDTO) => {
    projectActions.navigateToProject(project.id)
}

const handleProjectDelete = (projectId: number) => {
    // Phase 1 : Loader de suppression
    isDeletionInProgress.value = true

    projectActions.deleteProject(projectId, {
        onSuccess: async () => {
            // Fin phase 1
            isDeletionInProgress.value = false
            // Phase 2 : Skeleton pour le rechargement des données
            listManager.setDeletionLoading(true)
            await listManager.fetchProjectsData()
            emit('project-deleted', projectId)
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
    await listManager.refreshProjects()
}

// Raccourcis clavier
const handleKeyboardShortcuts = (e: KeyboardEvent) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault()
        showFilters.value = true
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
