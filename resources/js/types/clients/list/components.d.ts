/**
 * Types pour les composants de la liste clients
 */

import type { ClientDTO } from '../../models'
import type { ClientFilters, ActiveFilter } from './filters'
import type { PaginatedData } from './pagination'

export interface ClientListProps {
    clients: PaginatedData<ClientDTO>
    stats: ClientListStats
    filters: ClientFilters
    skeleton_mode?: boolean
}

export interface ClientListStats {
    total: number
    with_projects: number
    without_projects: number
    with_active_projects: number
}

export interface ClientListState {
    clients: ClientDTO[]
    stats: ClientListStats
    filters: any
    pagination: any
    isLoading: boolean
    error: Record<string, string> | null
}


export interface ClientListActions {
    refreshClients: () => Promise<void>
    applyFilters: (filters: Partial<ClientFilters>) => Promise<void>
    clearFilters: () => Promise<void>
    clearFilter: (key: keyof ClientFilters) => Promise<void>
    goToPage: (page: number) => Promise<void>
    changePageSize: (size: number) => Promise<void>
}

export interface ClientCardProps {
    client: ClientDTO
    showActions?: boolean
    isLoading?: boolean
}

export interface ClientStatsCardProps {
    title: string
    value: number | string
    icon: string
    color: string
    isActive?: boolean
    isLoading?: boolean
    href?: string
}

export interface ClientFilterPanelProps {
    filters: ClientFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading: boolean
}

export interface ClientFilterPanelEmits {
    'update:filters': [filters: Partial<ClientFilters>]
    'clear-filter': [key: keyof ClientFilters]
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
