/**
 * Types pour la pagination de la liste événements
 */

export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
}

export interface PaginatedData<T> {
    data: T[]
    meta: PaginationMeta
}

export interface PaginationState {
    currentPage: number
    lastPage: number
    perPage: number
    total: number
    isLoading: boolean
    hasNextPage: boolean
    hasPrevPage: boolean
}

export interface PaginationInfo {
    start: number
    end: number
    total: number
}

export interface PaginationActions {
    updateCurrentPage: (page: number) => boolean
}