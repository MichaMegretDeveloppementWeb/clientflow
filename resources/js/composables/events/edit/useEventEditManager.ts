import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { EventEditData, EventEditState, EventEditSkeletonData, EventEditFormData } from '@/types/events/edit'

export function useEventEditManager(eventId: number, skeletonData: EventEditSkeletonData, initialData?: EventEditData) {
    // État réactif
    const state = reactive<EventEditState>({
        isLoading: true,
        data: initialData || skeletonData,
        error: null
    })

    // Computed
    const event = computed(() => {
        if (state.isLoading || !state.data) return null
        const data = state.data as EventEditData
        if (data.errors && Object.keys(data.errors).length > 0) return null
        return (data.event && Object.keys(data.event).length > 0) ? data.event as EventEditFormData : null
    })

    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => {
        if (state.error) return true
        const data = state.data as EventEditData
        return data?.errors && Object.keys(data.errors).length > 0
    })

    // Récupération des données
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => { state.isLoading = true },
            onFinish: () => {
                state.data = usePage().props.data as EventEditData

                // Vérifier s'il y a des erreurs
                if (state.data && (state.data as EventEditData).errors &&
                    typeof (state.data as EventEditData).errors === 'object' &&
                    Object.keys((state.data as EventEditData).errors).length > 0) {
                    state.error = (state.data as EventEditData).errors
                } else {
                    // Réinitialiser les erreurs si tout va bien
                    state.error = null
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
        } else {
            // Si on a des données initiales, on n'est plus en loading
            state.isLoading = false
        }
    })

    return {
        // State
        state,
        event,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
    }
}
