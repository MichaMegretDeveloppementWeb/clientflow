import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { EventCreateData, EventCreateState, EventCreateSkeletonData, EventCreateFormData } from '@/types/events/create'

export function useEventCreateManager(
    projectId: number | null, 
    skeletonData: EventCreateSkeletonData, 
    initialData?: EventCreateData
) {
    // État réactif
    const state = reactive<EventCreateState>({
        isLoading: !initialData,
        data: initialData || skeletonData,
        error: null
    })

    // Computed
    const event = computed(() => {
        if (state.isLoading || !state.data) return null
        const data = state.data as EventCreateData
        if (data.errors && Object.keys(data.errors).length > 0) return null
        return (data.event && Object.keys(data.event).length > 0) ? data.event as EventCreateFormData : null
    })

    const projects = computed(() => {
        if (!state.data) return []
        const data = state.data as EventCreateData
        return data.projects || []
    })

    const selectedProject = computed(() => {
        if (!state.data) return null
        const data = state.data as EventCreateData
        return data.selectedProject || null
    })

    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => {
        if (state.error) return true
        
        const data = state.data as EventCreateData
        // errors est un array vide [] et non un objet {}, donc on vérifie différemment
        return data?.errors && Array.isArray(data.errors) 
            ? data.errors.length > 0 
            : data?.errors && typeof data.errors === 'object' && Object.keys(data.errors).length > 0
    })

    // Récupération des données
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => { state.isLoading = true },
            onFinish: () => {
                state.data = usePage().props.data as EventCreateData

                // Vérifier s'il y a des erreurs
                if (state.data) {
                    const data = state.data as EventCreateData
                    const hasDataErrors = Array.isArray(data.errors) 
                        ? data.errors.length > 0 
                        : data.errors && typeof data.errors === 'object' && Object.keys(data.errors).length > 0
                        
                    if (hasDataErrors) {
                        state.error = data.errors
                    } else {
                        // Réinitialiser les erreurs si tout va bien
                        state.error = null
                    }
                } else {
                    state.error = { general: 'Aucune donnée reçue' }
                }

                state.isLoading = false
            },
            onError: (errors) => {
                state.error = errors
                state.isLoading = false
            }
        })
    }

    // Pas d'actions de soumission ici - c'est le rôle du Form composable

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
        projects,
        selectedProject,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData
    }
}