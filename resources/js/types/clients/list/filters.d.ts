/**
 * Types pour la gestion des filtres de la liste clients
 */

export interface ClientFilters {
    search?: string
    sort_by?: string
    sort_order?: 'asc' | 'desc'
    has_projects?: string | boolean
    has_active_projects?: string | boolean
    has_overdue_payments?: string | boolean
}

export interface FilterState {
    search: string
    sort_by: string
    sort_order: 'asc' | 'desc'
    has_projects?: string
    has_active_projects?: string
    has_overdue_payments?: string
}

export interface ActiveFilter {
    key: keyof ClientFilters
    value: any
    label: string
    type: 'search' | 'boolean' | 'select'
}



