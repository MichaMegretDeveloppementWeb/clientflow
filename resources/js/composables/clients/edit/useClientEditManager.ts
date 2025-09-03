import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { ClientEditData, ClientEditState, ClientEditSkeletonData, ClientEditFormData } from '@/types/clients/edit'

export function useClientEditManager(clientId: number, skeletonData: ClientEditSkeletonData, initialData?: ClientEditData) {
    // État réactif
    const state = reactive<ClientEditState>({
        isLoading: true,
        data: initialData || skeletonData,
        error: null
    })

    // Computed
    const client = computed(() => {
        if (state.isLoading || !state.data) return null
        const data = state.data as ClientEditData
        if (data.errors && Object.keys(data.errors).length > 0) return null
        return (data.client && Object.keys(data.client).length > 0) ? data.client as ClientEditFormData : null
    })

    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => {
        if (state.error) return true
        const data = state.data as ClientEditData
        return data?.errors && Object.keys(data.errors).length > 0
    })

    // Récupération des données
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => { state.isLoading = true },
            onFinish: () => {
                state.data = usePage().props.data as ClientEditData

                // Vérifier s'il y a des erreurs
                if (state.data && (state.data as ClientEditData).errors &&
                    typeof (state.data as ClientEditData).errors === 'object' &&
                    Object.keys((state.data as ClientEditData).errors).length > 0) {
                    state.error = (state.data as ClientEditData).errors
                } else {
                    // Réinitialiser les erreurs si tout va bien
                    state.error = null
                }
                state.isLoading = false
            },
            onError: (errors) => {
                console.log(errors);
                state.error = errors
                state.isLoading = false
            }
        })
    }

    // Auto-fetch au montage si pas de données initiales
    onMounted(() => {
        if (!initialData) {
            fetchData()
        } else {
            // Si on a des données initiales, on n'est plus en loading
            state.isLoading = false
        }
    })

    return {
        // State
        state,
        client,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
    }
}
