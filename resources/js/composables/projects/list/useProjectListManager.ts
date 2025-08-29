import { computed, reactive, onMounted, onUnmounted, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useProjectFilters } from './useProjectFilters'
import { useProjectPagination } from './useProjectPagination'
import { useAppState } from '@/composables/useAppState'
import type {
    ProjectListProps,
    ProjectListState,
    ProjectListActions
} from '@/types/projects/list/components'
import type { ProjectFilters } from '@/types/projects/list/filters'

export function useProjectListManager(initialProps: ProjectListProps & { skeleton_mode?: boolean; clients?: Array<{ id: number; name: string }> }) {

    // Clients disponibles pour les filtres
    const availableClients = ref(initialProps.clients || [])

    // Sous-composables spécialisés
    const filters = useProjectFilters(initialProps.filters, availableClients)
    const pagination = useProjectPagination(initialProps.projects?.meta || { current_page: 1, per_page: 15, total: 0, last_page: 1 })
    const appState = useAppState()

    // État global
    const globalState = reactive<ProjectListState>({
        projects: initialProps.projects?.data || [],
        stats: initialProps.stats || { active: 0, completed: 0, on_hold: 0, total: 0 },
        filters: filters.filters,
        pagination: pagination.paginationState,
        isLoading: false,
        error: null
    })

    // États de chargement granulaires
    const loadingStates = reactive({
        projects: false,
        stats: false,
        filters: false,
        search: false,
        sort: false,
        pagination: false
    })


    // Fonction utilitaire pour construire les paramètres de requête
    const buildQueryParams = (filtersState: typeof filters.filters, paginationState?: any) => {
        const params: Record<string, any> = {}

        // Strings - inclure même si vides pour supprimer de l'URL
        params.search = filtersState.search?.trim() || undefined

        // Tri - inclure même si valeurs par défaut pour supprimer de l'URL
        params.sort_by = (filtersState.sort_by && filtersState.sort_by !== 'created_at')
            ? filtersState.sort_by
            : undefined
        params.sort_order = (filtersState.sort_order && filtersState.sort_order !== 'desc')
            ? filtersState.sort_order
            : undefined

        // Statut - undefined pour le supprimer de l'URL
        params.status = (filtersState.status && filtersState.status !== 'all')
            ? filtersState.status
            : undefined

        // Client - undefined pour le supprimer de l'URL
        params.client_id = (filtersState.client_id && filtersState.client_id !== 'all')
            ? filtersState.client_id
            : undefined

        // Booléens - undefined pour les supprimer de l'URL
        params.has_overdue_tasks = (filtersState.has_overdue_tasks !== undefined && filtersState.has_overdue_tasks !== '')
            ? filtersState.has_overdue_tasks
            : undefined
        params.has_payment_overdue = (filtersState.has_payment_overdue !== undefined && filtersState.has_payment_overdue !== '')
            ? filtersState.has_payment_overdue
            : undefined

        // Pagination
        params.page = (paginationState?.currentPage > 1)
            ? paginationState.currentPage.toString()
            : undefined

        params.per_page = paginationState?.perPage
            ? paginationState.perPage.toString()
            : undefined

        return params
    }

    // Computeds
    const isAnyLoading = computed(() =>
        Object.values(loadingStates).some(loading => loading) || globalState.isLoading
    )

    const hasData = computed(() =>
        globalState.projects.length > 0
    )

    const isEmpty = computed(() =>
        !hasData.value && !globalState.error
    )

    const hasError = computed(() =>
        Boolean(globalState.error)
    )

    const hasResults = computed(() =>
        hasData.value
    )

    const displayedProjects = computed(() => {
        // Les données sont déjà filtrées côté serveur (y compris la recherche)
        return globalState.projects
    })


    // Méthode centralisée pour charger les données projets
    const fetchProjectsData = async (options: {
        showLoading?: boolean
        resetPagination?: boolean
    } = {}): Promise<void> => {
        const { showLoading = true, resetPagination = false } = options

        if (showLoading) {
            loadingStates.projects = true
            globalState.isLoading = true
        }

        const params = buildQueryParams(
            filters.filters,
            resetPagination ? null : pagination.paginationState
        )

        router.reload({
            only: ['data'],
            data: params,
            onStart: () => {
                globalState.isLoading = true
                loadingStates.projects = true
            },
            onFinish: () => {
                const pageData = usePage().props.data as any

                // Vérifier si c'est une erreur retournée par le contrôleur
                if (pageData && pageData.error) {
                    globalState.error = pageData.debug_message || pageData.message || 'Une erreur est survenue'
                    globalState.isLoading = false
                    loadingStates.projects = false
                    return
                }

                if (pageData && pageData.projects && pageData.statistics) {
                    // Mise à jour centralisée de l'état
                    globalState.projects = pageData.projects.data || []
                    globalState.stats = pageData.statistics
                    
                    // Vérifier que les métadonnées de pagination existent
                    if (pageData.projects.meta) {
                        pagination.updateMeta(pageData.projects.meta)
                    }
                    
                    globalState.error = null

                    // Mise à jour des clients disponibles si fournis
                    if (pageData.clients) {
                        availableClients.value = pageData.clients
                    }
                }

                globalState.isLoading = false
                loadingStates.projects = false
            },
            onError: (errors) => {
                globalState.error = errors
                globalState.isLoading = false
                loadingStates.projects = false
            }
        })
    }


    const refreshProjects = async (): Promise<void> => {
        await fetchProjectsData()
        appState.notifySuccess('Projets actualisés', 'La liste des projets a été mise à jour')
    }

    const loadRealData = async (): Promise<void> => {
        try {
            await fetchProjectsData({ showLoading: true })
        } catch (error) {
            console.error('Load real data error:', error)
        }
    }

    const applyFilters = async (newFilters: Partial<ProjectFilters>): Promise<void> => {
        // Déléguer la modification d'état au composable dédié
        filters.updateFilters(newFilters)

        // Recharger les données (URL mise à jour automatiquement)
        await fetchProjectsData({ resetPagination: true })
    }

    const clearFilters = async (): Promise<void> => {
        // Déléguer la remise à zéro au composable dédié
        filters.clearAllFilters()

        // Recharger les données (URL mise à jour automatiquement)
        await fetchProjectsData({ resetPagination: true })
    }

    const clearFilter = async (key: keyof ProjectFilters): Promise<void> => {
        // Déléguer l'effacement au composable dédié
        filters.clearFilter(key)

        // Recharger les données (URL mise à jour automatiquement)
        await fetchProjectsData({ resetPagination: true })
    }

    const goToPage = async (page: number): Promise<void> => {
        // Déléguer la mise à jour de page au composable spécialisé
        if(pagination.updateCurrentPage(page)){
            // Recharger les données pour la nouvelle page si page differente
            await fetchProjectsData()
        }

    }

    const changePageSize = async (newSize: number): Promise<void> => {
        // Changer la taille de page et revenir à la page 1
        pagination.paginationState.perPage = newSize
        pagination.paginationState.currentPage = 1

        // Recharger les données
        await fetchProjectsData({ resetPagination: true })
    }


    // clearSearch supprimé - la recherche se gère via clearFilter('search')


    const clearError = (): void => {
        globalState.error = null
    }

    // API complète des actions
    const actions: ProjectListActions = {
        refreshProjects,
        applyFilters,
        clearFilters,
        clearFilter,
        goToPage,
        changePageSize
    }

    // Nettoyage
    const cleanup = (): void => {
        filters.cleanup()
        // Autres nettoyages si nécessaire
    }

    // Lifecycle
    onMounted(() => {
        fetchProjectsData()
    })

    onUnmounted(() => {
        cleanup()
    })

    return {
        // État global
        globalState,
        loadingStates,
        availableClients,

        // Sous-états spécialisés
        filters,
        pagination,

        // Computeds
        isAnyLoading,
        hasData,
        isEmpty,
        hasError,
        hasResults,
        displayedProjects,

        // Actions principales
        ...actions,
        loadRealData,
        clearError,

        // Nettoyage
        cleanup
    }
}
