import { reactive, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import type {
    ClientDetailData,
    ClientDetailState,
    ClientDetailActions
} from '@/types/clients/detail'

export function useClientDetailManager(clientId: number, skeletonData: ClientDetailData) {
    // État réactif
    const globalState = reactive<ClientDetailState>({
        client: skeletonData.client,
        projects: skeletonData.projects || [],
        events: skeletonData.events || [],
        financialStats: skeletonData.financialStats,
        isLoading: false,
        error: null
    })

    // Computed
    const isLoading = computed(() => globalState.isLoading)
    const hasError = computed(() => !!globalState.error)

    // Récupération des données
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => {
                globalState.isLoading = true
            },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData) {
                    // Vérifier s'il y a une erreur personnalisée
                    if (pageData.error) {
                        globalState.error = pageData.error
                        // En cas d'erreur, utiliser les données skeleton
                        globalState.client = skeletonData.client
                        globalState.projects = skeletonData.projects || []
                        globalState.events = skeletonData.events || []
                        globalState.financialStats = skeletonData.financialStats || null
                    } else {
                        // Structure aplatie : client/projects/events/financialStats
                        globalState.client = pageData.client || skeletonData.client
                        globalState.projects = pageData.projects || skeletonData.projects || []
                        globalState.events = pageData.events || skeletonData.events || []
                        globalState.financialStats = pageData.financialStats || skeletonData.financialStats || null
                        globalState.error = null
                    }
                } else {
                    globalState.error = {
                        type: 'general_error',
                        title: 'Erreur de chargement',
                        message: 'Erreur lors du chargement des données',
                        code: 500
                    }
                }

                globalState.isLoading = false
            },
            onError: (errors) => {
                globalState.error = errors
                globalState.isLoading = false
            }
        })
    }

    const clearError = (): void => {
        globalState.error = null
    }

    const loadRealData = async (): Promise<void> => {
        try {
            await fetchData()
        } catch (error) {
            console.error('Load real data error:', error)
        }
    }

    // Méthode pour recharger avec filtres
    const reloadWithFilters = async (filters: Record<string, any>) => {
        router.visit(route('clients.show', clientId), {
            method: 'get',
            data: filters,
            only: ['data'],
            preserveState: true,
            replace: false, // Force la création d'une nouvelle entrée historique
            preserveScroll: true,
            onStart: () => {
                globalState.isLoading = true
            },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData) {
                    // Vérifier s'il y a une erreur personnalisée
                    if (pageData.error) {
                        globalState.error = pageData.error
                        // En cas d'erreur, utiliser les données skeleton
                        globalState.client = skeletonData.client
                        globalState.projects = skeletonData.projects || []
                        globalState.events = skeletonData.events || []
                        globalState.financialStats = skeletonData.financialStats || null
                    } else {
                        // Structure aplatie : client/projects/events/financialStats
                        globalState.client = pageData.client || skeletonData.client
                        globalState.projects = pageData.projects || skeletonData.projects || []
                        globalState.events = pageData.events || skeletonData.events || []
                        globalState.financialStats = pageData.financialStats || skeletonData.financialStats || null
                        globalState.error = null
                    }
                } else {
                    globalState.error = {
                        type: 'general_error',
                        title: 'Erreur de chargement',
                        message: 'Erreur lors du chargement des données',
                        code: 500
                    }
                }

                globalState.isLoading = false
            },
            onError: (errors) => {
                globalState.error = errors
                globalState.isLoading = false
            }
        })
    }

    // Auto-fetch au montage
    onMounted(() => {
        fetchData()
    })

    const actions: ClientDetailActions = {
        fetchData,
        clearError,
        loadRealData
    }

    return {
        // State
        globalState,
        isLoading,
        hasError,

        // Actions
        ...actions,
        reloadWithFilters
    }
}
