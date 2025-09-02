import { ref, computed, reactive } from 'vue'
import type { 
    EventFilters, 
    EventFilterState, 
    ActiveFilter
} from '@/types/events/list/filters'

export function useEventFilters(initialFilters: EventFilters = {}) {
    const debounceTimeout = ref<NodeJS.Timeout | null>(null)

    // État réactif simplifié
    const filters = reactive<EventFilterState>({
        search: initialFilters.search || '',
        event_type: initialFilters.event_type || 'all',
        status: initialFilters.status || 'all',
        project_id: initialFilters.project_id || 'all',
        client_id: initialFilters.client_id || 'all',
        payment_status: initialFilters.payment_status || 'all',
        overdue: initialFilters.overdue,
        payment_overdue: initialFilters.payment_overdue,
        sort: initialFilters.sort || 'date',
        direction: initialFilters.direction || 'desc',
    })

    // Utilitaires pour les labels
    const getFilterLabel = (key: keyof EventFilters, value: any): string => {
        switch (key) {
            case 'search':
                return `"${value}"`
            case 'event_type':
                const eventTypeLabels = {
                    'step': 'Étape',
                    'billing': 'Facturation'
                }
                return eventTypeLabels[value as keyof typeof eventTypeLabels] || value
            case 'status':
                const statusLabels = {
                    'todo': 'À faire',
                    'done': 'Terminé',
                    'to_send': 'À envoyer',
                    'sent': 'Envoyé',
                    'cancelled': 'Annulé'
                }
                return statusLabels[value as keyof typeof statusLabels] || value
            case 'payment_status':
                const paymentLabels = {
                    'paid': 'Payé',
                    'pending': 'En attente',
                    'overdue': 'En retard'
                }
                return paymentLabels[value as keyof typeof paymentLabels] || value
            case 'project_id':
                return `Projet: ${value}`
            case 'client_id':
                return `Client: ${value}`
            case 'overdue':
                return 'En retard'
            case 'payment_overdue':
                return 'Paiements en retard'
            default:
                return String(value)
        }
    }

    const isFilterActive = (key: keyof EventFilters, value: any): boolean => {
        switch (key) {
            case 'search':
                return Boolean(value && value.trim().length > 0)
            case 'event_type':
            case 'status':
            case 'project_id':
            case 'client_id':
            case 'payment_status':
                return Boolean(value && value !== '' && value !== 'all')
            case 'overdue':
            case 'payment_overdue':
                return value === 'true' || value === true || value === '1'
            default:
                return Boolean(value)
        }
    }

    // Computeds simplifiés - Export direct
    const activeFilters = computed((): ActiveFilter[] => {
        const active: ActiveFilter[] = []

        if (isFilterActive('search', filters.search)) {
            active.push({
                key: 'search',
                value: filters.search,
                label: getFilterLabel('search', filters.search),
                type: 'search'
            })
        }

        if (isFilterActive('event_type', filters.event_type)) {
            active.push({
                key: 'event_type',
                value: filters.event_type,
                label: getFilterLabel('event_type', filters.event_type),
                type: 'select'
            })
        }

        if (isFilterActive('status', filters.status)) {
            active.push({
                key: 'status',
                value: filters.status,
                label: getFilterLabel('status', filters.status),
                type: 'select'
            })
        }

        if (isFilterActive('project_id', filters.project_id)) {
            active.push({
                key: 'project_id',
                value: filters.project_id,
                label: getFilterLabel('project_id', filters.project_id),
                type: 'select'
            })
        }

        if (isFilterActive('client_id', filters.client_id)) {
            active.push({
                key: 'client_id',
                value: filters.client_id,
                label: getFilterLabel('client_id', filters.client_id),
                type: 'select'
            })
        }

        if (isFilterActive('payment_status', filters.payment_status)) {
            active.push({
                key: 'payment_status',
                value: filters.payment_status,
                label: getFilterLabel('payment_status', filters.payment_status),
                type: 'select'
            })
        }

        if (isFilterActive('overdue', filters.overdue)) {
            active.push({
                key: 'overdue',
                value: filters.overdue,
                label: getFilterLabel('overdue', filters.overdue),
                type: 'boolean'
            })
        }

        if (isFilterActive('payment_overdue', filters.payment_overdue)) {
            active.push({
                key: 'payment_overdue',
                value: filters.payment_overdue,
                label: getFilterLabel('payment_overdue', filters.payment_overdue),
                type: 'boolean'
            })
        }

        return active
    })

    const hasActiveFilters = computed(() => activeFilters.value.length > 0)
    const activeFiltersCount = computed(() => activeFilters.value.length)

    // Actions simplifiées
    const updateFilters = (newFilters: Partial<EventFilters>): void => {
        Object.assign(filters, newFilters)
    }

    const clearFilter = (key: keyof EventFilters): void => {
        const clearedValue = key === 'search' ? '' : 
                           key === 'event_type' ? 'all' :
                           key === 'status' ? 'all' :
                           key === 'client_id' ? 'all' :
                           key === 'project_id' ? 'all' :
                           key === 'payment_status' ? 'all' :
                           undefined
        updateFilters({ [key]: clearedValue })
    }

    const clearAllFilters = (): void => {
        updateFilters({
            search: '',
            event_type: 'all',
            status: 'all',
            client_id: 'all',
            project_id: 'all',
            payment_status: 'all',
            overdue: undefined,
            payment_overdue: undefined
        })
    }

    // Nettoyage
    const cleanup = (): void => {
        if (debounceTimeout.value) {
            clearTimeout(debounceTimeout.value)
        }
    }

    return {
        // État
        filters,

        // Computeds
        activeFilters,
        hasActiveFilters,
        activeFiltersCount,

        // Actions pour modification d'état réactif
        updateFilters,
        clearFilter,
        clearAllFilters,

        // Utilitaire
        cleanup
    }
}