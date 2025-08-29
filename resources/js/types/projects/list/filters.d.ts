/**
 * Types pour la gestion des filtres de la liste projets
 */

export interface ProjectFilters {
    search?: string
    status?: string
    client_id?: string
    sort_by?: string
    sort_order?: 'asc' | 'desc'
    has_overdue_tasks?: string | boolean
    has_payment_overdue?: string | boolean
}

export interface FilterState {
    search: string
    status: string
    client_id: string
    sort_by: string
    sort_order: 'asc' | 'desc'
    has_overdue_tasks?: boolean | string
    has_payment_overdue?: boolean | string
}

export interface ActiveFilter {
    key: keyof ProjectFilters
    value: any
    label: string
    type: 'search' | 'boolean' | 'select'
}