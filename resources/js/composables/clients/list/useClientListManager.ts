import { computed, reactive, onMounted, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useClientFilters } from './useClientFilters'
import { useClientPagination } from './useClientPagination'
import { useAppState } from '@/composables/useAppState'
import type {
    ClientListProps,
    ClientListState,
    ClientListActions
} from '@/types/clients/list/components'
import type { ClientFilters } from '@/types/clients/list/filters'

export function useClientListManager(initialProps: ClientListProps) {

    // Sous-composables spécialisés
    const filters = useClientFilters(initialProps.filters)
    const pagination = useClientPagination(initialProps.clients.meta)
    const appState = useAppState()

    // État global
    const globalState = reactive<ClientListState>({
        clients: initialProps.clients.data,
        stats: initialProps.stats,
        filters: filters.filters,
        pagination: pagination.paginationState,
        isLoading: false,
        error: null
    })

    // États de chargement granulaires
    const loadingStates = reactive({
        clients: false,
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

        // Booléens - undefined pour les supprimer de l'URL
        params.has_projects = (filtersState.has_projects !== undefined && filtersState.has_projects !== '')
            ? filtersState.has_projects
            : undefined
        params.has_active_projects = (filtersState.has_active_projects !== undefined && filtersState.has_active_projects !== '')
            ? filtersState.has_active_projects
            : undefined
        params.has_overdue_payments = (filtersState.has_overdue_payments !== undefined && filtersState.has_overdue_payments !== '')
            ? filtersState.has_overdue_payments
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
        globalState.clients.length > 0
    )

    const isEmpty = computed(() =>
        !hasData.value && !isAnyLoading.value && !globalState.error
    )

    const hasError = computed(() =>
        Boolean(globalState.error)
    )

    const hasResults = computed(() =>
        hasData.value
    )

    const displayedClients = computed(() => {
        // Les données sont déjà filtrées côté serveur (y compris la recherche)
        return globalState.clients
    })


    // Méthode centralisée pour charger les données clients
    const fetchClientsData = async (options: {
        showLoading?: boolean
        resetPagination?: boolean
    } = {}): Promise<void> => {
        const { showLoading = true, resetPagination = false } = options

        if (showLoading) {
            loadingStates.clients = true
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
                loadingStates.clients = true
            },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData && pageData.clients && pageData.stats) {
                    // Mise à jour centralisée de l'état
                    globalState.clients = pageData.clients.data || []
                    globalState.stats = pageData.stats
                    pagination.updateMeta(pageData.clients.meta)
                    globalState.error = null
                }

                globalState.isLoading = false
                loadingStates.clients = false
            },
            onError: (errors) => {
                globalState.error = errors
                globalState.isLoading = false
                loadingStates.clients = false
            }
        })
    }


    const refreshClients = async (): Promise<void> => {
        await fetchClientsData()
        appState.notifySuccess('Clients actualisés', 'La liste des clients a été mise à jour')
    }

    const loadRealData = async (): Promise<void> => {
        try {
            await fetchClientsData({ showLoading: true })
        } catch (error) {
            console.error('Load real data error:', error)
        }
    }

    const applyFilters = async (newFilters: Partial<ClientFilters>): Promise<void> => {
        // Déléguer la modification d'état au composable dédié
        filters.updateFilters(newFilters)

        // Recharger les données (URL mise à jour automatiquement)
        await fetchClientsData({ resetPagination: true })
    }

    const clearFilters = async (): Promise<void> => {
        // Déléguer la remise à zéro au composable dédié
        filters.clearAllFilters()

        // Recharger les données (URL mise à jour automatiquement)
        await fetchClientsData({ resetPagination: true })
    }

    const clearFilter = async (key: keyof ClientFilters): Promise<void> => {
        // Déléguer l'effacement au composable dédié
        filters.clearFilter(key)

        // Recharger les données (URL mise à jour automatiquement)
        await fetchClientsData({ resetPagination: true })
    }


    const goToPage = async (page: number): Promise<void> => {
        // Déléguer la mise à jour de page au composable spécialisé
        if(pagination.updateCurrentPage(page)){
            // Recharger les données pour la nouvelle page si page différente
            await fetchClientsData()
        }
    }

    const changePageSize = async (newSize: number): Promise<void> => {
        // Changer la taille de page et revenir à la page 1
        pagination.paginationState.perPage = newSize
        pagination.paginationState.currentPage = 1

        // Recharger les données
        await fetchClientsData({ resetPagination: true })
    }


    // clearSearch supprimé - la recherche se gère via clearFilter('search')


    const clearError = (): void => {
        globalState.error = null
    }

    // Watcher supprimé - plus besoin de synchronisation avec useClientSearch

    // API complète des actions
    const actions: ClientListActions = {
        refreshClients,
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
        fetchClientsData()
    })

    onUnmounted(() => {
        cleanup()
    })

    return {
        // État global
        globalState,
        loadingStates,

        // Sous-états spécialisés
        filters,
        pagination,

        // Computeds
        isAnyLoading,
        hasData,
        isEmpty,
        hasError,
        hasResults,
        displayedClients,

        // Actions principales
        ...actions,
        loadRealData,
        clearError,

        // Nettoyage
        cleanup
    }
}
