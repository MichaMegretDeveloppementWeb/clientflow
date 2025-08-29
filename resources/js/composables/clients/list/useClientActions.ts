import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useAppState } from '@/composables/useAppState'
import type { ClientDTO } from '@/types/models'

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
        onSuccess?: () => void,
        onError?: () => void
    ): void => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
            return
        }

        router.delete(route('clients.destroy', clientId), {
            onSuccess: () => {
                appState.notifySuccess('Client supprimé', 'Le client a été supprimé avec succès')
                onSuccess?.()
            },
            onError: () => {
                appState.notifyError('Erreur', 'Impossible de supprimer le client')
                onError?.()
            }
        })
    }

    return {
        navigateToClient,
        navigateToCreateClient,
        deleteClient
    }
}