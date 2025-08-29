/**
 * Types pour les composants de la liste projets
 */

import type { ProjectDTO } from '../../models'
import type { ProjectFilters, ActiveFilter } from './filters'
import type { PaginatedData } from './pagination'

export interface ProjectListProps {
    projects: PaginatedData<ProjectDTO>
    stats: ProjectListStats
    filters: ProjectFilters
    skeleton_mode?: boolean
    clients?: Array<{ id: number; name: string }>
}

export interface ProjectListStats {
    active: number
    completed: number
    on_hold: number
    total: number
}

export interface ProjectListState {
    projects: ProjectDTO[]
    stats: ProjectListStats
    filters: any
    pagination: any
    isLoading: boolean
    error: Record<string, string> | null
}

export interface ProjectListActions {
    refreshProjects: () => Promise<void>
    applyFilters: (filters: Partial<ProjectFilters>) => Promise<void>
    clearFilters: () => Promise<void>
    clearFilter: (key: keyof ProjectFilters) => Promise<void>
    goToPage: (page: number) => Promise<void>
    changePageSize: (size: number) => Promise<void>
}

export interface ProjectCardProps {
    project: ProjectDTO
    showActions?: boolean
    isLoading?: boolean
}

export interface ProjectStatsCardProps {
    title: string
    value: number | string
    icon: string
    color: string
    isActive?: boolean
    isLoading?: boolean
    href?: string
}

export interface ProjectFilterPanelProps {
    filters: ProjectFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading: boolean
}

export interface ProjectFilterPanelEmits {
    'update:filters': [filters: Partial<ProjectFilters>]
    'clear-filter': [key: keyof ProjectFilters]
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
