<template>
    <div class="space-y-8">
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
            :is-loading="skeletonLoader.showSkeleton(true, listManager.loadingStates.stats)"
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
            :is-loading="skeletonLoader.showSkeleton(true, listManager.loadingStates.projects)"
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
            v-if="listManager.isEmpty.value && !skeletonLoader.showSkeleton(true, listManager.loadingStates.projects)"
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
import { useSkeletonLoader } from '@/composables/useSkeletonLoader'
import { useConnectionDetection } from '@/composables/useConnectionDetection'
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
        skeleton_mode?: boolean
        clients?: Array<{ id: number; name: string }>
    }
    data?: any
}>()

const emit = defineEmits<{
    'project-deleted': [projectId: number]
    'filters-changed': [filters: any]
}>()

// État global orchestré
const listManager = useProjectListManager(props.skeletonData)
const projectActions = useProjectActions()
const connectionDetection = useConnectionDetection()
const skeletonLoader = useSkeletonLoader(
    props.skeletonData.skeleton_mode ?? false,
    connectionDetection.getOptimalSkeletonDelay()
)

// États locaux UI
const showFilters = ref(false)

// Fournir l'état aux composants enfants
provide('listManager', listManager)

// Vérifier si les données initiales contiennent une erreur
onMounted(() => {
    if (props.data && props.data.error) {
        listManager.globalState.error = props.data.debug_message || props.data.message || 'Une erreur est survenue lors du chargement des données'
    }
})

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
    projectActions.deleteProject(
        projectId,
        () => {
            emit('project-deleted', projectId)
            listManager.refreshProjects()
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
