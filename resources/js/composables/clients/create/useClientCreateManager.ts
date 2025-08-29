import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { ClientCreateData, ClientCreateState, ClientCreateSkeletonData, ClientCreateFormData } from '@/types/clients/create'

export function useClientCreateManager(
    skeletonData: ClientCreateSkeletonData,
    initialData?: ClientCreateData
) {
    // État réactif
    const state = reactive<ClientCreateState>({
        isLoading: !initialData,
        data: initialData || skeletonData,
        error: null
    })

    // Computed
    const client = computed(() => {
        if (state.isLoading || !state.data) return null
        const data = state.data as ClientCreateData
        if (data.errors && Object.keys(data.errors).length > 0) return null
        return (data.client && Object.keys(data.client).length > 0) ? data.client as ClientCreateFormData : null
    })

    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => {

        if (state.error) return true

        const data = state.data as ClientCreateData
        // errors est un array vide [] et non un objet {}, donc on vérifie différemment
        return data?.errors && Array.isArray(data.errors)
            ? data.errors.length > 0
            : data?.errors && typeof data.errors === 'object' && Object.keys(data.errors).length > 0
    })

    // Récupération des données réelles
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => {
                state.isLoading = true
            },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData) {
                    state.data = pageData
                } else {
                    state.error = 'Erreur lors du chargement des données'
                }

                state.isLoading = false
            },
            onError: (errors) => {
                state.error = errors
                state.isLoading = false
            }
        })
    }

    const clearError = (): void => {
        state.error = null
    }

    const loadRealData = async (): Promise<void> => {
        try {
            await fetchData()
        } catch (error) {
            console.error('Load real data error:', error)
        }
    }

    // Auto-fetch au montage si pas de données initiales
    onMounted(() => {
        if (!initialData) {
            fetchData()
        }
    })

    return {
        // State
        client,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
        clearError,
        loadRealData
    }
}
