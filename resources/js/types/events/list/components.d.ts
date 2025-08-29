/**
 * Types pour les composants de la liste événements
 */

import type { EventDTO } from '../../models'
import type { EventFilters, ActiveFilter } from './filters'
import type { PaginatedData } from './pagination'

export interface EventListProps {
    events: PaginatedData<EventDTO>
    stats: EventListStats
    filters: EventFilters
    skeleton_mode?: boolean
    projects?: Array<{ id: number; name: string }>
    clients?: Array<{ id: number; name: string }>
}

export interface EventListStats {
    total: number
    todo: number
    done: number
    overdue: number
    step_events: number
    billing_events: number
}

export interface EventListState {
    events: EventDTO[]
    stats: EventListStats
    filters: any
    pagination: any
    isLoading: boolean
    error: Record<string, string> | null
}

export interface EventListActions {
    refreshEvents: () => Promise<void>
    applyFilters: (filters: Partial<EventFilters>) => Promise<void>
    clearFilters: () => Promise<void>
    clearFilter: (key: keyof EventFilters) => Promise<void>
    goToPage: (page: number) => Promise<void>
    changePageSize: (size: number) => Promise<void>
}

export interface EventCardProps {
    event: EventDTO
    showActions?: boolean
    isLoading?: boolean
}

export interface EventStatsCardProps {
    title: string
    value: number | string
    icon: string
    color: string
    isActive?: boolean
    isLoading?: boolean
    href?: string
}

export interface EventFilterPanelProps {
    filters: EventFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading: boolean
    availableProjects?: Array<{ id: number; name: string }>
    availableClients?: Array<{ id: number; name: string }>
}

export interface EventFilterPanelEmits {
    'update:filters': [filters: Partial<EventFilters>]
    'clear-filter': [key: keyof EventFilters]
    'clear-all': []
}

export interface EmptyStateProps {
    hasActiveFilters: boolean
    isLoading: boolean
    title?: string
    description?: string
    actionLabel?: string
    actionHref?: string
}
