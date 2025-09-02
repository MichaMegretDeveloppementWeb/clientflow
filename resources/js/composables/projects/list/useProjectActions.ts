import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useAppState } from '@/composables/useAppState'

export function useProjectActions() {
    const appState = useAppState()

    const navigateToProject = (projectId: number): void => {
        router.get(route('projects.show', { project: projectId }))
    }

    const navigateToCreateProject = (): void => {
        router.get(route('projects.create'))
    }

    const deleteProject = (
        projectId: number,
        options: { 
            onSuccess?: () => void,
            onError?: () => void,
            onFinish?: () => void 
        } = {}
    ): void => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
            options.onFinish?.()
            return
        }

        router.delete(route('projects.destroy', projectId), {
            preserveState: true,
            preserveScroll: true,
            only: ['projectsData'], // Ne recharge que les données différées, ignore la redirection
            onSuccess: () => {
                appState.notifySuccess('Projet supprimé', 'Le projet a été supprimé avec succès')
                options.onSuccess?.()
            },
            onError: () => {
                appState.notifyError('Erreur', 'Impossible de supprimer le projet')
                options.onError?.()
            },
            onFinish: () => {
                options.onFinish?.()
            }
        })
    }

    return {
        navigateToProject,
        navigateToCreateProject,
        deleteProject
    }
}