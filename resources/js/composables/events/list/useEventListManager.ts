import { computed, reactive, onMounted, onUnmounted, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useEventFilters } from './useEventFilters'
import { useEventPagination } from './useEventPagination'
import { useAppState } from '@/composables/useAppState'
import type {
    EventListProps,
    EventListState,
    EventListActions
} from '@/types/events/list/components'
import type { EventFilters } from '@/types/events/list/filters'

export function useEventListManager(initialProps: EventListProps) {

    // Sous-composables spécialisés
    const filters = useEventFilters(initialProps.filters)
    const pagination = useEventPagination(initialProps.events.meta)
    const appState = useAppState()

    // État global
    const globalState = reactive<EventListState>({
        events: initialProps.events.data,
        stats: initialProps.stats,
        filters: filters.filters,
        pagination: pagination.paginationState,
        isLoading: false,
        error: null
    })

    // Projets et clients disponibles pour les filtres
    const availableProjects = ref(initialProps.projects || [])
    const availableClients = ref(initialProps.clients || [])

    // États de chargement granulaires
    const loadingStates = reactive({
        events: false,
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

        // Type d'événement
        params.event_type = (filtersState.event_type && filtersState.event_type !== 'all')
            ? filtersState.event_type
            : undefined

        // Statut
        params.status = (filtersState.status && filtersState.status !== 'all')
            ? filtersState.status
            : undefined

        // Projet
        params.project_id = (filtersState.project_id && filtersState.project_id !== 'all')
            ? filtersState.project_id
            : undefined

        // Client
        params.client_id = (filtersState.client_id && filtersState.client_id !== 'all')
            ? filtersState.client_id
            : undefined

        // Statut de paiement
        params.payment_status = (filtersState.payment_status && filtersState.payment_status !== 'all')
            ? filtersState.payment_status
            : undefined

        // Tri - inclure même si valeurs par défaut pour supprimer de l'URL
        params.sort = (filtersState.sort && filtersState.sort !== 'date')
            ? filtersState.sort
            : undefined
        params.direction = (filtersState.direction && filtersState.direction !== 'desc')
            ? filtersState.direction
            : undefined

        // Booléens - undefined pour les supprimer de l'URL
        params.overdue = (filtersState.overdue === true || filtersState.overdue === 'true')
            ? 'true'
            : undefined
        params.payment_overdue = (filtersState.payment_overdue === true || filtersState.payment_overdue === 'true')
            ? 'true'
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
        globalState.events.length > 0
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

    const displayedEvents = computed(() => {
        // Les données sont déjà filtrées côté serveur (y compris la recherche)
        return globalState.events
    })

    // Méthode centralisée pour charger les données événements
    const fetchEventsData = async (options: {
        showLoading?: boolean
        resetPagination?: boolean
    } = {}): Promise<any> => {
        const { showLoading = true, resetPagination = false } = options

        if (showLoading) {
            loadingStates.events = true
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
                loadingStates.events = true
            },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData && pageData.events && pageData.stats) {
                    // Mise à jour centralisée de l'état
                    globalState.events = pageData.events.data || []
                    globalState.stats = pageData.stats
                    pagination.updateMeta(pageData.events.meta)
                    globalState.error = null

                    // Mise à jour des listes disponibles pour les filtres si fournies
                    if (pageData.projects) {
                        availableProjects.value = pageData.projects
                    }
                    if (pageData.clients) {
                        availableClients.value = pageData.clients
                    }
                }

                globalState.isLoading = false
                loadingStates.events = false
            },
            onError: (errors) => {
                globalState.error = errors
                globalState.isLoading = false
                loadingStates.events = false
            }
        })
    }

    const refreshEvents = async (): Promise<void> => {
        await fetchEventsData()
        appState.notifySuccess('Événements actualisés', 'La liste des événements a été mise à jour')
    }

    const loadRealData = async (): Promise<void> => {
        try {
            await fetchEventsData({ showLoading: true })
        } catch (error) {
            console.error('Load real data error:', error)
        }
    }

    const applyFilters = async (newFilters: Partial<EventFilters>): Promise<void> => {
        // Déléguer la modification d'état au composable dédié
        filters.updateFilters(newFilters)

        // Recharger les données (URL mise à jour automatiquement)
        await fetchEventsData({ resetPagination: true })
    }

    const clearFilters = async (): Promise<void> => {
        // Déléguer la remise à zéro au composable dédié
        filters.clearAllFilters()

        // Recharger les données (URL mise à jour automatiquement)
        await fetchEventsData({ resetPagination: true })
    }

    const clearFilter = async (key: keyof EventFilters): Promise<void> => {
        // Déléguer l'effacement au composable dédié
        filters.clearFilter(key)

        // Recharger les données (URL mise à jour automatiquement)
        await fetchEventsData({ resetPagination: true })
    }

    const goToPage = async (page: number): Promise<void> => {
        // Déléguer la mise à jour de page au composable spécialisé
        if(pagination.updateCurrentPage(page)){
            // Recharger les données pour la nouvelle page si page différente
            await fetchEventsData()
        }
    }

    const changePageSize = async (newSize: number): Promise<void> => {
        // Changer la taille de page et revenir à la page 1
        pagination.paginationState.perPage = newSize
        pagination.paginationState.currentPage = 1

        // Recharger les données
        await fetchEventsData({ resetPagination: true })
    }

    const clearError = (): void => {
        globalState.error = null
    }

    // API complète des actions
    const actions: EventListActions = {
        refreshEvents,
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
        fetchEventsData()
    })

    onUnmounted(() => {
        cleanup()
    })

    return {
        // État global
        globalState,
        loadingStates,
        availableProjects,
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
        displayedEvents,

        // Actions principales
        ...actions,
        loadRealData,
        clearError,

        // Nettoyage
        cleanup
    }
}
