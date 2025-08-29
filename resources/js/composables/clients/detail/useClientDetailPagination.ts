import { ref, computed, watch, type Ref } from 'vue'
import type { Project, Event } from '@/types/clients/detail/index'

interface PaginationInfo {
    currentPage: number
    lastPage: number
    total: number
    start: number
    end: number
}

export function useClientDetailPagination(
    projects: Ref<Project[]>,
    events: Ref<Event[]>,
    isLoading: Ref<boolean>,
    itemsPerPage: number = 5
) {
    // États de pagination
    const projectsCurrentPage = ref(1)
    const eventsCurrentPage = ref(1)

    // Listes complètes
    const allProjects = computed(() => projects.value || [])
    const allEvents = computed(() => events.value || [])

    // Calcul de la pagination pour les projets
    const projectsPagination = computed((): PaginationInfo => {
        const total = allProjects.value.length
        const lastPage = Math.max(1, Math.ceil(total / itemsPerPage))
        const currentPage = projectsCurrentPage.value
        
        return {
            currentPage,
            lastPage,
            total,
            start: total === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1,
            end: Math.min(currentPage * itemsPerPage, total)
        }
    })

    // Calcul de la pagination pour les événements
    const eventsPagination = computed((): PaginationInfo => {
        const total = allEvents.value.length
        const lastPage = Math.max(1, Math.ceil(total / itemsPerPage))
        const currentPage = eventsCurrentPage.value
        
        return {
            currentPage,
            lastPage,
            total,
            start: total === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1,
            end: Math.min(currentPage * itemsPerPage, total)
        }
    })

    // Listes paginées
    const paginatedProjects = computed(() => {
        if (isLoading.value || allProjects.value.length <= itemsPerPage) {
            return allProjects.value
        }
        
        const start = (projectsCurrentPage.value - 1) * itemsPerPage
        const end = start + itemsPerPage
        return allProjects.value.slice(start, end)
    })

    const paginatedEvents = computed(() => {
        if (isLoading.value || allEvents.value.length <= itemsPerPage) {
            return allEvents.value
        }
        
        const start = (eventsCurrentPage.value - 1) * itemsPerPage
        const end = start + itemsPerPage
        return allEvents.value.slice(start, end)
    })

    // Calcul des pages visibles
    const getVisiblePages = (pagination: PaginationInfo): number[] => {
        const { currentPage, lastPage } = pagination
        const pages: number[] = []
        
        if (lastPage <= 5) {
            // Si 5 pages ou moins, afficher toutes
            for (let i = 1; i <= lastPage; i++) {
                pages.push(i)
            }
        } else {
            // Logique pour plus de 5 pages avec ellipses
            pages.push(1)
            
            if (currentPage > 3) {
                pages.push(-1) // ellipse
            }
            
            const start = Math.max(2, currentPage - 1)
            const end = Math.min(lastPage - 1, currentPage + 1)
            
            for (let i = start; i <= end; i++) {
                if (!pages.includes(i)) {
                    pages.push(i)
                }
            }
            
            if (currentPage < lastPage - 2) {
                pages.push(-1) // ellipse
            }
            
            if (lastPage > 1) {
                pages.push(lastPage)
            }
        }
        
        return pages
    }

    // Pages visibles pour les projets
    const projectsVisiblePages = computed(() => getVisiblePages(projectsPagination.value))

    // Pages visibles pour les événements
    const eventsVisiblePages = computed(() => getVisiblePages(eventsPagination.value))

    // Actions de navigation
    const goToProjectPage = (page: number) => {
        if (page >= 1 && page <= projectsPagination.value.lastPage) {
            projectsCurrentPage.value = page
        }
    }

    const goToEventPage = (page: number) => {
        if (page >= 1 && page <= eventsPagination.value.lastPage) {
            eventsCurrentPage.value = page
        }
    }

    // Reset des pages à 1 (utile lors du changement d'onglet)
    const resetPages = () => {
        projectsCurrentPage.value = 1
        eventsCurrentPage.value = 1
    }

    // Correction automatique si la page actuelle dépasse le nombre de pages disponibles
    watch(projectsPagination, (newPagination) => {
        if (projectsCurrentPage.value > newPagination.lastPage) {
            projectsCurrentPage.value = Math.max(1, newPagination.lastPage)
        }
    })

    watch(eventsPagination, (newPagination) => {
        if (eventsCurrentPage.value > newPagination.lastPage) {
            eventsCurrentPage.value = Math.max(1, newPagination.lastPage)
        }
    })

    // Vérification si la pagination doit être affichée
    const shouldShowProjectsPagination = computed(() => 
        !isLoading.value && projectsPagination.value.lastPage > 1
    )

    const shouldShowEventsPagination = computed(() => 
        !isLoading.value && eventsPagination.value.lastPage > 1
    )

    return {
        // États
        projectsCurrentPage,
        eventsCurrentPage,

        // Données paginées
        paginatedProjects,
        paginatedEvents,

        // Informations de pagination
        projectsPagination,
        eventsPagination,

        // Pages visibles
        projectsVisiblePages,
        eventsVisiblePages,

        // Actions
        goToProjectPage,
        goToEventPage,
        resetPages,

        // Utilitaires
        shouldShowProjectsPagination,
        shouldShowEventsPagination
    }
}