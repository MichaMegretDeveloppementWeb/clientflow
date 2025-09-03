import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useAppState } from '@/composables/useAppState'

export function useEventActions() {
    const appState = useAppState()

    const navigateToEvent = (eventId: number): void => {
        router.visit(route('events.show', { event: eventId }))
    }

    const navigateToCreateEvent = (projectId?: number): void => {
        const params = projectId ? { project_id: projectId } : {}
        router.visit(route('events.create', params))
    }

    const navigateToEditEvent = (eventId: number): void => {
        router.visit(route('events.edit', { event: eventId }))
    }

    const deleteEvent = (
        eventId: number,
        options: {
            onSuccess?: () => void,
            onFinish?: () => void,
            onError?: () => void,
            eventName?: string
        } = {}
    ): void => {
        const confirmMessage = options.eventName
            ? `Êtes-vous sûr de vouloir supprimer l'événement "${options.eventName}" ?`
            : 'Êtes-vous sûr de vouloir supprimer cet événement ?'

        if (!confirm(confirmMessage)) {
            options.onFinish?.()
            return
        }

        router.delete(route('events.destroy', { event: eventId }), {
            preserveState: true,
            preserveScroll: true,
            only: ['eventsData'], // Ne recharge que les données différées, ignore la redirection
            onSuccess: () => {
                appState.notifySuccess(
                    'Événement supprimé',
                    options.eventName ? `L'événement "${options.eventName}" a été supprimé avec succès.` : 'L\'événement a été supprimé avec succès.'
                )
                options.onSuccess?.()
            },
            onError: (errors) => {
                console.error('Delete event error:', errors)
                appState.notifyError(
                    'Erreur',
                    'Une erreur est survenue lors de la suppression de l\'événement.'
                )
            },
            onFinish: () => {
                options.onFinish?.()
            }
        })
    }

    const markEventAsDone = (eventId: number, eventName?: string): void => {
        router.patch(
            route('events.update', { event: eventId }),
            { status: 'done' },
            {
                preserveScroll: true,
                onSuccess: () => {
                    appState.notifySuccess(
                        'Événement terminé',
                        eventName ? `L'événement "${eventName}" a été marqué comme terminé.` : 'L\'événement a été marqué comme terminé.'
                    )
                },
                onError: (errors) => {
                    console.error('Mark event as done error:', errors)
                    appState.notifyError(
                        'Erreur',
                        'Une erreur est survenue lors de la mise à jour de l\'événement.'
                    )
                }
            }
        )
    }

    const markEventAsSent = (eventId: number, eventName?: string): void => {
        router.patch(
            route('events.update', { event: eventId }),
            { status: 'sent' },
            {
                preserveScroll: true,
                onSuccess: () => {
                    appState.notifySuccess(
                        'Événement envoyé',
                        eventName ? `L'événement "${eventName}" a été marqué comme envoyé.` : 'L\'événement a été marqué comme envoyé.'
                    )
                },
                onError: (errors) => {
                    console.error('Mark event as sent error:', errors)
                    appState.notifyError(
                        'Erreur',
                        'Une erreur est survenue lors de la mise à jour de l\'événement.'
                    )
                }
            }
        )
    }

    const cancelEvent = (eventId: number, eventName?: string): void => {
        const confirmMessage = eventName
            ? `Êtes-vous sûr de vouloir annuler l'événement "${eventName}" ?`
            : 'Êtes-vous sûr de vouloir annuler cet événement ?'

        if (!confirm(confirmMessage)) {
            return
        }

        router.patch(
            route('events.update', { event: eventId }),
            { status: 'cancelled' },
            {
                preserveScroll: true,
                onSuccess: () => {
                    appState.notifySuccess(
                        'Événement annulé',
                        eventName ? `L'événement "${eventName}" a été annulé.` : 'L\'événement a été annulé.'
                    )
                },
                onError: (errors) => {
                    console.error('Cancel event error:', errors)
                    appState.notifyError(
                        'Erreur',
                        'Une erreur est survenue lors de l\'annulation de l\'événement.'
                    )
                }
            }
        )
    }

    const duplicateEvent = (eventId: number, eventName?: string): void => {
        router.post(
            route('events.duplicate', { event: eventId }),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    appState.notifySuccess(
                        'Événement dupliqué',
                        eventName ? `L'événement "${eventName}" a été dupliqué avec succès.` : 'L\'événement a été dupliqué avec succès.'
                    )
                },
                onError: (errors) => {
                    console.error('Duplicate event error:', errors)
                    appState.notifyError(
                        'Erreur',
                        'Une erreur est survenue lors de la duplication de l\'événement.'
                    )
                }
            }
        )
    }

    return {
        // Navigation
        navigateToEvent,
        navigateToCreateEvent,
        navigateToEditEvent,

        // Actions CRUD
        deleteEvent,

        // Actions de statut
        markEventAsDone,
        markEventAsSent,
        cancelEvent,

        // Actions utilitaires
        duplicateEvent
    }
}
