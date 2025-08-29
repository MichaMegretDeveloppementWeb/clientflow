import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { ProjectEditData, ProjectEditState, ProjectEditSkeletonData, ProjectEditFormData } from '@/types/projects/edit'

export function useProjectEditManager(projectId: number, skeletonData: ProjectEditSkeletonData, initialData?: ProjectEditData) {
    // État réactif
    const state = reactive<ProjectEditState>({
        isLoading: true,
        data: initialData || skeletonData,
        error: null
    })

    // Computed
    const project = computed(() => {
        if (state.isLoading || !state.data) return null
        const data = state.data as ProjectEditData
        if (data.errors && Object.keys(data.errors).length > 0) return null
        return (data.project && Object.keys(data.project).length > 0) ? data.project as ProjectEditFormData : null
    })

    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => {
        if (state.error) return true
        const data = state.data as ProjectEditData
        return data?.errors && Object.keys(data.errors).length > 0
    })

    // Récupération des données
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => { state.isLoading = true },
            onFinish: () => {
                const pageData = usePage().props.data as any

                // Vérifier si c'est une erreur retournée par le contrôleur
                if (pageData && pageData.error) {
                    state.error = { message: pageData.debug_message || pageData.message || 'Une erreur est survenue' }
                    state.isLoading = false
                    return
                }

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
        } else {
            // Si on a des données initiales, on n'est plus en loading
            state.isLoading = false
        }
    })

    return {
        // State
        state,
        project,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
    }
}
