import { ref, computed, reactive } from 'vue'
import type {
    ClientFilters,
    FilterState,
    ActiveFilter
} from '@/types/clients/list/filters'

export function useClientFilters(initialFilters: ClientFilters = {}) {
    const debounceTimeout = ref<NodeJS.Timeout | null>(null)

    // État réactif simplifié
    const filters = reactive<FilterState>({
        search: initialFilters.search || '',
        sort_by: initialFilters.sort_by || 'created_at',
        sort_order: initialFilters.sort_order || 'desc',
        has_projects: initialFilters.has_projects?.toString(),
        has_active_projects: initialFilters.has_active_projects?.toString(),
        has_overdue_payments: initialFilters.has_overdue_payments?.toString()
    })

    // Utilitaires pour les labels (remplace les Strategy classes)
    const getFilterLabel = (key: keyof ClientFilters, value: any): string => {
        switch (key) {
            case 'search':
                return `"${value}"`
            case 'has_projects':
                return value === 'true' ? 'Avec projets' : 'Sans projets'
            case 'has_active_projects':
                return 'Projets actifs'
            case 'has_overdue_payments':
                return 'Paiements en retard'
            default:
                return String(value)
        }
    }

    const isFilterActive = (key: keyof ClientFilters, value: any): boolean => {
        switch (key) {
            case 'search':
                return Boolean(value && value.trim().length > 0)
            case 'has_projects':
                return Boolean(value && ['true', 'false'].includes(value))
            case 'has_active_projects':
                return value === 'true'
            case 'has_overdue_payments':
                return value === true || value === 'true'
            default:
                return Boolean(value)
        }
    }

    // Computeds simplifiés - Export direct (plus de wrapper state)
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

        if (isFilterActive('has_projects', filters.has_projects)) {
            active.push({
                key: 'has_projects',
                value: filters.has_projects,
                label: getFilterLabel('has_projects', filters.has_projects),
                type: 'boolean'
            })
        }

        if (isFilterActive('has_active_projects', filters.has_active_projects)) {
            active.push({
                key: 'has_active_projects',
                value: filters.has_active_projects,
                label: getFilterLabel('has_active_projects', filters.has_active_projects),
                type: 'boolean'
            })
        }

        if (isFilterActive('has_overdue_payments', filters.has_overdue_payments)) {
            active.push({
                key: 'has_overdue_payments',
                value: filters.has_overdue_payments,
                label: getFilterLabel('has_overdue_payments', filters.has_overdue_payments),
                type: 'boolean'
            })
        }

        return active
    })

    const hasActiveFilters = computed(() => activeFilters.value.length > 0)
    const activeFiltersCount = computed(() => activeFilters.value.length)

    // Actions simplifiées
    const updateFilters = (newFilters: Partial<ClientFilters>): void => {
        Object.assign(filters, newFilters)
    }

    const clearFilter = (key: keyof ClientFilters): void => {
        const clearedValue = key === 'search' ? '' : undefined
        updateFilters({ [key]: clearedValue })
    }

    const clearAllFilters = (): void => {
        updateFilters({
            search: '',
            has_projects: undefined,
            has_active_projects: undefined,
            has_overdue_payments: undefined
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

        // Computeds - Export direct (plus de wrapper)
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
