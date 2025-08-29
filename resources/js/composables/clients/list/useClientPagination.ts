import {  computed, reactive } from 'vue'
import type {
    PaginationMeta,
    PaginationState,
    PaginationActions,
} from '@/types/clients/list/pagination'


export function useClientPagination(
    initialMeta: PaginationMeta = {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0
    },
) {

    // État réactif
    const paginationState = reactive<PaginationState>({
        currentPage: initialMeta.current_page,
        perPage: initialMeta.per_page,
        total: initialMeta.total,
        lastPage: initialMeta.last_page,
        isLoading: false,
        hasNextPage: initialMeta.current_page < initialMeta.last_page,
        hasPrevPage: initialMeta.current_page > 1
    })


    // Computeds
    const pageInfo = computed(() => ({
        start: paginationState.total === 0 ? 0 : (paginationState.currentPage - 1) * paginationState.perPage + 1,
        end: Math.min(paginationState.currentPage * paginationState.perPage, paginationState.total),
        total: paginationState.total
    }))

    const visiblePages = computed(() => {
        const { currentPage, lastPage } = paginationState
        const delta = 2 // Nombre de pages à afficher de chaque côté
        const pages: number[] = []

        // Toujours inclure la première page
        pages.push(1)

        // Calculer la plage autour de la page actuelle
        const rangeStart = Math.max(2, currentPage - delta)
        const rangeEnd = Math.min(lastPage - 1, currentPage + delta)

        // Ajouter '...' si nécessaire avant la plage
        if (rangeStart > 2) {
            pages.push(-1) // -1 représente '...'
        }

        // Ajouter la plage
        for (let i = rangeStart; i <= rangeEnd; i++) {
            pages.push(i)
        }

        // Ajouter '...' si nécessaire après la plage
        if (rangeEnd < lastPage - 1) {
            pages.push(-1) // -1 représente '...'
        }

        // Toujours inclure la dernière page (si > 1)
        if (lastPage > 1) {
            pages.push(lastPage)
        }

        return pages
    })

    const canGoNext = computed(() =>
        paginationState.hasNextPage && !paginationState.isLoading
    )

    const canGoPrev = computed(() =>
        paginationState.hasPrevPage && !paginationState.isLoading
    )

    const paginationSummary = computed(() => {
        const { start, end, total } = pageInfo.value
        if (total === 0) return 'Aucun résultat'
        if (start === end) return `Résultat ${start} sur ${total}`
        return `Résultats ${start} à ${end} sur ${total}`
    })

    // Actions
    const updateMeta = (meta: PaginationMeta): void => {
        paginationState.currentPage = meta.current_page
        paginationState.lastPage = meta.last_page
        paginationState.perPage = meta.per_page
        paginationState.total = meta.total
        paginationState.hasNextPage = meta.current_page < meta.last_page
        paginationState.hasPrevPage = meta.current_page > 1
    }

    const updateCurrentPage = (page: number): boolean => {
        if (page === paginationState.currentPage) return false
        if (page < 1 || page > paginationState.lastPage) return false

        paginationState.currentPage = page
        paginationState.hasNextPage = page < paginationState.lastPage
        paginationState.hasPrevPage = page > 1
        return true
    }

    // Actions supprimées - l'orchestrateur useClientListManager gère la pagination via goToPage()
    // et la modification de taille de page via applyFilters()



    // Actions réduites aux méthodes utilisées par l'orchestrateur
    const actions: PaginationActions = {
        updateCurrentPage,
    }


    return {
        // État
        paginationState,

        // Computeds
        pageInfo,
        visiblePages,
        canGoNext,
        canGoPrev,
        paginationSummary,

        // Actions utilisées par l'orchestrateur
        ...actions,
        updateMeta,

    }
}
