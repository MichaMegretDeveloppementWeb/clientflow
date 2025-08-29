/**
 * Types pour la gestion des filtres de la liste événements
 */

export interface EventFilters {
    search?: string
    event_type?: string
    status?: string
    project_id?: string
    client_id?: string
    payment_status?: string
    overdue?: string | boolean
    payment_overdue?: string | boolean
    sort?: string
    direction?: 'asc' | 'desc'
}

export interface EventFilterState {
    search: string
    event_type: string
    status: string
    project_id: string
    client_id: string
    payment_status: string
    overdue?: boolean | string
    payment_overdue?: boolean | string
    sort: string
    direction: 'asc' | 'desc'
}

export interface ActiveFilter {
    key: keyof EventFilters
    value: any
    label: string
    type: 'search' | 'select' | 'boolean'
}