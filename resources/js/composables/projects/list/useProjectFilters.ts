import { ref, computed, reactive, type Ref } from 'vue'
import type { 
    ProjectFilters, 
    FilterState, 
    ActiveFilter
} from '@/types/projects/list/filters'

export function useProjectFilters(
    initialFilters: ProjectFilters = {}, 
    availableClients: Ref<Array<{ id: number; name: string }>> = ref([])
) {
    const debounceTimeout = ref<NodeJS.Timeout | null>(null)

    // État réactif simplifié
    const filters = reactive<FilterState>({
        search: initialFilters.search || '',
        status: initialFilters.status || 'all',
        client_id: initialFilters.client_id || 'all',
        sort_by: initialFilters.sort_by || 'created_at',
        sort_order: initialFilters.sort_order || 'desc',
        has_overdue_tasks: initialFilters.has_overdue_tasks,
        has_payment_overdue: initialFilters.has_payment_overdue
    })

    // Utilitaires pour les labels
    const getFilterLabel = (key: keyof ProjectFilters, value: any): string => {
        switch (key) {
            case 'search':
                return `"${value}"`
            case 'status':
                const statusLabels = {
                    'active': 'Actif',
                    'completed': 'Terminé', 
                    'on_hold': 'En pause'
                }
                return statusLabels[value as keyof typeof statusLabels] || value
            case 'client_id':
                const client = availableClients.value.find(c => c.id.toString() === value.toString())
                return client ? client.name : `Client ID: ${value}`
            case 'has_overdue_tasks':
                return 'Tâches en retard'
            case 'has_payment_overdue':
                return 'Paiements en retard'
            default:
                return String(value)
        }
    }

    const isFilterActive = (key: keyof ProjectFilters, value: any): boolean => {
        switch (key) {
            case 'search':
                return Boolean(value && value.trim().length > 0)
            case 'status':
                return Boolean(value && value !== '' && value !== 'all')
            case 'client_id':
                return Boolean(value && value !== '' && value !== 'all')
            case 'has_overdue_tasks':
                return value === 'true' || value === true || value === '1'
            case 'has_payment_overdue':
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

        if (isFilterActive('status', filters.status)) {
            active.push({
                key: 'status',
                value: filters.status,
                label: getFilterLabel('status', filters.status),
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

        if (isFilterActive('has_overdue_tasks', filters.has_overdue_tasks)) {
            active.push({
                key: 'has_overdue_tasks',
                value: filters.has_overdue_tasks,
                label: getFilterLabel('has_overdue_tasks', filters.has_overdue_tasks),
                type: 'boolean'
            })
        }

        if (isFilterActive('has_payment_overdue', filters.has_payment_overdue)) {
            active.push({
                key: 'has_payment_overdue',
                value: filters.has_payment_overdue,
                label: getFilterLabel('has_payment_overdue', filters.has_payment_overdue),
                type: 'boolean'
            })
        }

        return active
    })

    const hasActiveFilters = computed(() => activeFilters.value.length > 0)
    const activeFiltersCount = computed(() => activeFilters.value.length)

    // Actions simplifiées
    const updateFilters = (newFilters: Partial<ProjectFilters>): void => {
        Object.assign(filters, newFilters)
    }

    const clearFilter = (key: keyof ProjectFilters): void => {
        let clearedValue: any
        if (key === 'search') {
            clearedValue = ''
        } else if (key === 'status' || key === 'client_id') {
            clearedValue = 'all'
        } else {
            clearedValue = undefined
        }
        updateFilters({ [key]: clearedValue })
    }

    const clearAllFilters = (): void => {
        updateFilters({
            search: '',
            status: 'all',
            client_id: 'all',
            has_overdue_tasks: undefined,
            has_payment_overdue: undefined
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

        // Computeds - Export direct
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