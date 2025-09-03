import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useAppState } from '@/composables/useAppState'

export function useClientActions() {
    const appState = useAppState()

    const navigateToClient = (clientId: number): void => {
        router.get(route('clients.show', { client: clientId }))
    }

    const navigateToCreateClient = (): void => {
        router.get(route('clients.create'))
    }

    const deleteClient = (
        clientId: number,
        options: { 
            onSuccess?: () => void,
            onError?: () => void,
            onFinish?: () => void 
        } = {}
    ): void => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
            options.onFinish?.()
            return
        }

        router.delete(route('clients.destroy', clientId), {
            preserveState: true,
            preserveScroll: true,
            only: ['clientsData'], // Recharge automatiquement les données
            onSuccess: () => {
                appState.notifySuccess('Client supprimé', 'Le client a été supprimé avec succès')
                options.onSuccess?.()
            },
            onError: () => {
                appState.notifyError('Erreur', 'Impossible de supprimer le client')
                options.onError?.()
            },
            onFinish: () => {
                options.onFinish?.()
            }
        })
    }

    return {
        navigateToClient,
        navigateToCreateClient,
        deleteClient
    }
}
