/**
 * Types pour la gestion de la pagination de la liste projets
 */

export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from?: number
    to?: number
}

export interface PaginationLinks {
    first?: string
    last?: string
    prev?: string
    next?: string
}

export interface PaginatedData<T> {
    data: T[]
    links: PaginationLinks
    meta: PaginationMeta
}

export interface PaginationState {
    currentPage: number
    perPage: number
    total: number
    lastPage: number
    isLoading: boolean
    hasNextPage: boolean
    hasPrevPage: boolean
}

export interface PaginationActions {
    updateCurrentPage: (page: number) => boolean
}
