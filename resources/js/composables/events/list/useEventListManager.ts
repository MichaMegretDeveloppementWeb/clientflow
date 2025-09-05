import { computed, reactive, ref, onMounted, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
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
    const filters = useEventFilters(initialProps.filters || {})
    const pagination = useEventPagination(initialProps.events?.meta || { current_page: 1, per_page: 15, total: 0, last_page: 1 })
    const appState = useAppState()

    // État global
    const globalState = reactive<EventListState>({
        events: initialProps.events?.data || [],
        stats: initialProps.stats || {},
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
        pagination: false,
        deletion: false
    })

    // Flag pour savoir si les stats ont été chargées au moins une fois
    const hasStatsLoadedOnce = ref(false)


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

    const getInertiaVersion = (): string | null => {
        const p: any = usePage() // suivant la version, c'est un objet ou un ref
        return p?.version ?? p?.value?.version ?? null
    }

    // Méthode de chargement initial via fetch (comme le dashboard)
    const loadInitialData = async (force: boolean = false): Promise<void> => {
        // Charger seulement si pas déjà de données (éviter double chargement)
        if (!force && globalState.events.length > 0 && Object.keys(globalState.stats).length > 0) {
            return
        }

        loadingStates.events = true
        loadingStates.stats = true
        globalState.isLoading = true
        globalState.error = null

        try {
            const params = buildQueryParams(filters.filters, pagination.paginationState)
            const queryString = new URLSearchParams()

            Object.entries(params).forEach(([key, value]) => {
                if (value !== undefined) {
                    queryString.append(key, String(value))
                }
            })

            const url = route('events.index') + (queryString.toString() ? '?' + queryString.toString() : '')

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Inertia': 'true',
                    'X-Inertia-Partial-Component': 'Events/List/Index',
                    'X-Inertia-Partial-Data': 'eventsData',
                    'X-Inertia-Version': getInertiaVersion(),
                }
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data = await response.json()

            if (data.props?.eventsData) {
                const eventsData = data.props.eventsData

                globalState.events = eventsData.events?.data || []
                globalState.stats = eventsData.stats || {}

                if (eventsData.events?.meta) {
                    pagination.updateMeta(eventsData.events.meta)
                }

                if (eventsData.projects) {
                    availableProjects.value = eventsData.projects
                }
                if (eventsData.clients) {
                    availableClients.value = eventsData.clients
                }

                hasStatsLoadedOnce.value = true
            }
        } catch (error) {
            globalState.error = 'Une erreur est survenue lors du chargement des données'
            console.error('Erreur lors du chargement initial:', error)
        } finally {
            globalState.isLoading = false
            loadingStates.events = false
            loadingStates.stats = false
        }
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

    // Computed pour le skeleton des cartes (indépendant des stats)
    const isCardsLoading = computed(() =>
        loadingStates.deletion ||
        loadingStates.events ||
        globalState.isLoading
    )

    const isStatsLoading = computed(() =>
        loadingStates.deletion ||
        loadingStates.stats
    )

    // Méthode centralisée pour charger les données événements
    // Méthode pour les navigations réelles (filtres, pagination)
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

        // Navigation complète pour mettre à jour l'URL dans l'historique
        router.get(route('events.index'), params, {
            only: ['eventsData'],
            preserveState: true,
            replace: true,
            preserveScroll: true,
            onStart: () => {
                globalState.isLoading = true
                loadingStates.stats = true
            },
            onFinish: () => {
                const pageData = usePage().props.eventsData as any

                if (pageData && pageData.events && pageData.stats) {
                    // Mise à jour centralisée de l'état
                    globalState.events = pageData.events.data || []
                    globalState.stats = pageData.stats
                    hasStatsLoadedOnce.value = true

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
                loadingStates.stats = false
            },
            onError: (errors) => {
                globalState.error = errors
                globalState.isLoading = false
                loadingStates.events = false
                loadingStates.stats = false
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
        // Effacer tous les filtres via le composable dédié
        filters.clearAllFilters()
        // Recharger les données (URL mise à jour automatiquement)
        await fetchEventsData({ resetPagination: true })
    }

    const clearFilter = async (key: keyof EventFilters): Promise<void> => {
        // Supprimer le filtre spécifique via le composable dédié
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

    // Fonctions pour gérer l'état de suppression
    const setDeletionLoading = (isLoading: boolean): void => {
        loadingStates.deletion = isLoading
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

    // Lifecycle - Charger les données au premier rendu seulement si pas de données dans les props
    onMounted(() => {
        loadInitialData(true)
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
        hasStatsLoadedOnce,

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
        isCardsLoading,
        isStatsLoading,

        // Actions principales
        ...actions,
        loadRealData,
        loadInitialData,
        fetchEventsData,
        clearError,
        setDeletionLoading,

        // Nettoyage
        cleanup
    }
}
