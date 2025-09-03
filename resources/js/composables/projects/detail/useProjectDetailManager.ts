import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ProjectDetailData, ProjectDetailState } from '@/types/projects/detail'

export function useProjectDetailManager(projectId: number, skeletonData: ProjectDetailData) {
    // État réactif
    const state = reactive<ProjectDetailState>({
        isLoading: true,
        data: skeletonData,
        error: null
    })

    // Computed
    const project = computed(() => state.data?.project ?? null)
    const events = computed(() => state.data?.events ?? [])
    const financialStats = computed(() => state.data?.financialStats ?? null)
    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => !!state.error)

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

    // Actions de projet
    const updateStatus = (newStatus: string) => {
        if (!project.value) return

        router.patch(route('projects.update', project.value.id), {
            status: newStatus
        }, {
            onSuccess: () => {
                // Recharger les données après succès
                fetchData()
            }
        })
    }

    // Helpers computed
    const canChangeStatus = computed(() => {
        return project.value && ['active', 'on_hold'].includes(project.value.status)
    })

    // Auto-fetch au montage
    onMounted(() => {
        fetchData()
    })

    return {
        // State
        state,
        project,
        events,
        financialStats,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
        updateStatus,

        // Computed
        canChangeStatus
    }
}
