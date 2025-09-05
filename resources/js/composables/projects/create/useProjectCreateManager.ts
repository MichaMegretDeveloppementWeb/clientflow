import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { 
    ProjectCreateData, 
    ProjectCreateState, 
    ProjectCreateSkeletonData
} from '@/types/projects/create'

export function useProjectCreateManager(
    skeletonData: ProjectCreateSkeletonData, 
    initialData?: ProjectCreateData
) {
    // État réactif
    const state = reactive<ProjectCreateState>({
        isLoading: !initialData,
        data: initialData || skeletonData,
        error: null
    })

    // Computed properties
    const clients = computed(() => {
        if (state.isLoading || !state.data) return []
        const data = state.data as ProjectCreateData
        return data.clients || []
    })

    const selectedClientId = computed(() => {
        if (state.isLoading || !state.data) return null
        const data = state.data as ProjectCreateData
        return data.selectedClientId || null
    })

    const selectedClient = computed(() => {
        if (state.isLoading || !state.data || !selectedClientId.value) return null
        const data = state.data as ProjectCreateData
        return data.clients?.find(client => client.id.toString() === selectedClientId.value.toString()) || null
    })

    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => !!state.error)

    // Récupération des données complètes si pas de données initiales
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => { state.isLoading = true },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData) {
                    state.data = pageData
                    state.error = null
                } else {
                    state.error = { general: 'Erreur lors du chargement des données' }
                }

                state.isLoading = false
            },
            onError: (errors) => {
                state.error = errors
                state.isLoading = false
            }
        })
    }

    // Auto-fetch au montage si pas de données initiales
    onMounted(() => {
        if (!initialData) {
            fetchData()
        }
    })

    return {
        // State
        state,
        clients,
        selectedClientId,
        selectedClient,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
    }
}