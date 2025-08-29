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
        onSuccess?: () => void,
        onError?: () => void
    ): void => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
            return
        }

        router.delete(route('projects.destroy', projectId), {
            onSuccess: () => {
                appState.notifySuccess('Projet supprimé', 'Le projet a été supprimé avec succès')
                onSuccess?.()
            },
            onError: () => {
                appState.notifyError('Erreur', 'Impossible de supprimer le projet')
                onError?.()
            }
        })
    }

    return {
        navigateToProject,
        navigateToCreateProject,
        deleteProject
    }
}