import { ref, computed } from 'vue'

export type EventFilterType = 'all' | 'todo' | 'done'

interface EventFilter {
    key: EventFilterType
    label: string
    description: string
}

export function useClientDetailFilters() {
    // État du filtre actuel
    const currentEventFilter = ref<EventFilterType>('todo')
    const showOverdueOnly = ref<boolean>(false)

    // Définition des filtres disponibles
    const availableEventFilters: EventFilter[] = [
        {
            key: 'all',
            label: 'Tous',
            description: 'Tous les événements'
        },
        {
            key: 'todo',
            label: 'À faire',
            description: 'Événements à faire (todo, to_send)'
        },
        {
            key: 'done',
            label: 'Faits',
            description: 'Événements terminés (done, sent)'
        }
    ]

    // Computed pour le filtre actuel
    const activeEventFilter = computed(() =>
        availableEventFilters.find(f => f.key === currentEventFilter.value) || availableEventFilters[0]
    )

    // Fonction pour changer le filtre
    const setEventFilter = (filter: EventFilterType) => {
        currentEventFilter.value = filter
        // Reset la checkbox en retard quand on change de filtre
        if (filter !== 'todo') {
            showOverdueOnly.value = false
        }
    }

    // Setter pour la checkbox en retard
    const setShowOverdueOnly = (value: boolean) => {
        showOverdueOnly.value = value
    }

    // Fonction pour vérifier si un événement correspond au filtre
    const eventMatchesFilter = (event: { status: string }, filter: EventFilterType): boolean => {
        switch (filter) {
            case 'all':
                return true
            case 'todo':
                return ['todo', 'to_send'].includes(event.status)
            case 'done':
                return ['done', 'sent'].includes(event.status)
            default:
                return true
        }
    }

    // Fonction pour vérifier si un événement est en retard
    const isEventOverdue = (event: { status: string, execution_date?: string, send_date?: string, is_overdue?: boolean }): boolean => {
        // Si l'événement a déjà une propriété is_overdue, l'utiliser
        if (event.is_overdue !== undefined) {
            return event.is_overdue
        }

        // Sinon, calculer manuellement
        const now = new Date()
        
        if (['todo'].includes(event.status) && event.execution_date) {
            const executionDate = new Date(event.execution_date)
            return executionDate < now
        }
        
        if (['to_send'].includes(event.status) && event.send_date) {
            const sendDate = new Date(event.send_date)
            return sendDate < now
        }
        
        return false
    }

    // Fonction pour filtrer une liste d'événements (côté client, pour le fallback)
    const filterEvents = <T extends { status: string, execution_date?: string, send_date?: string, is_overdue?: boolean }>(
        events: T[], 
        filter: EventFilterType = currentEventFilter.value,
        overdueOnly: boolean = showOverdueOnly.value
    ): T[] => {
        let filteredEvents = events

        // Appliquer le filtre de statut
        if (filter !== 'all') {
            filteredEvents = filteredEvents.filter(event => eventMatchesFilter(event, filter))
        }

        // Appliquer le filtre en retard si activé et sur le filtre "todo"
        if (overdueOnly && filter === 'todo') {
            filteredEvents = filteredEvents.filter(event => isEventOverdue(event))
        }

        return filteredEvents
    }

    // Fonction pour générer les paramètres de requête backend
    const getBackendFilterParams = () => {
        const params: Record<string, any> = {}

        if (currentEventFilter.value !== 'all') {
            params.event_status_filter = currentEventFilter.value
        }

        return params
    }

    // Reset des filtres
    const resetFilters = () => {
        currentEventFilter.value = 'all'
        showOverdueOnly.value = false
    }

    // Computed pour savoir si on peut afficher la checkbox en retard
    const canShowOverdueFilter = computed(() => currentEventFilter.value === 'todo')

    return {
        // État
        currentEventFilter,
        showOverdueOnly,

        // Données
        availableEventFilters,
        activeEventFilter,
        canShowOverdueFilter,

        // Actions
        setEventFilter,
        setShowOverdueOnly,
        resetFilters,

        // Utilitaires
        eventMatchesFilter,
        filterEvents,
        getBackendFilterParams,
        isEventOverdue
    }
}
