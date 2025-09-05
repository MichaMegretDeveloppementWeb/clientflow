<template>
    <ModernFilterPanel
        :visible="props.visible"
        :search-value="localFilters.search || ''"
        search-placeholder="Nom, description, projet..."
        :filter-sections="filterSections"
        :active-filters="props.activeFilters"
        :has-active-filters="props.hasActiveFilters"
        :is-loading="props.isLoading"
        @update:visible="(value) => emit('update:visible', value)"
        @search-input="handleSearchInput"
        @search-enter="handleSearchEnter"
        @clear-search="clearSearch"
        @filter-change="handleFilterChange"
        @clear-filter="(key) => emit('clear-filter', key)"
        @clear-all="emit('clear-all')"
    />
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import ModernFilterPanel from '@/components/ui/ModernFilterPanel.vue'
import type { FilterSection } from '@/components/ui/ModernFilterPanel.vue'
import type { EventFilters, ActiveFilter } from '@/types/events/list'

interface Props {
    visible?: boolean
    filters: EventFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading?: boolean
    availableProjects?: Array<{ id: number; name: string; client_id?: number }>
    availableClients?: Array<{ id: number; name: string }>
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    isLoading: false,
    availableProjects: () => [],
    availableClients: () => []
})

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'update:filters': [filters: Partial<EventFilters>]
    'clear-filter': [key: keyof EventFilters]
    'clear-all': []
}>()

// État local
const localFilters = ref<EventFilters>({ ...props.filters })
let searchTimeout: NodeJS.Timeout | null = null

// Computed
const showPaymentOverdueFilter = computed(() => {
    return !localFilters.value.event_type ||
           localFilters.value.event_type === 'all' ||
           localFilters.value.event_type === 'billing'
})

const showPaymentStatusFilter = computed(() => {
    return !localFilters.value.event_type ||
           localFilters.value.event_type === 'all' ||
           localFilters.value.event_type === 'billing'
})

// Projets filtrés selon le client sélectionné
const filteredProjects = computed(() => {
    if (!localFilters.value.client_id || localFilters.value.client_id === 'all') {
        return props.availableProjects
    }

    const clientId = parseInt(localFilters.value.client_id)
    return props.availableProjects.filter(project =>
        project.client_id === clientId
    )
})

// Clients filtrés selon le projet sélectionné
const filteredClients = computed(() => {
    if (!localFilters.value.project_id || localFilters.value.project_id === 'all') {
        return props.availableClients
    }

    const projectId = parseInt(localFilters.value.project_id)
    const selectedProject = props.availableProjects.find(project =>
        project.id === projectId
    )

    if (selectedProject && selectedProject.client_id) {
        return props.availableClients.filter(client =>
            client.id === selectedProject.client_id
        )
    }

    return props.availableClients
})

// Configuration des sections de filtres - dynamique selon les conditions
const filterSections = computed<FilterSection[]>(() => {
    const sections: FilterSection[] = [
        {
            key: 'filters',
            title: 'Filtres',
            icon: 'filter',
            layout: 'double',
            filters: [
                {
                    key: 'event_type',
                    type: 'select',
                    label: 'Type d\'événement',
                    placeholder: 'Tous les types',
                    value: localFilters.value.event_type || 'all',
                    options: [
                        { value: 'all', label: 'Tous les types' },
                        { value: 'step', label: 'Étapes' },
                        { value: 'billing', label: 'Facturations' }
                    ]
                },
                {
                    key: 'project_id',
                    type: 'select',
                    label: 'Projet',
                    placeholder: 'Tous les projets',
                    value: localFilters.value.project_id || 'all',
                    options: [
                        { value: 'all', label: 'Tous les projets' },
                        ...filteredProjects.value.map(project => ({
                            value: project.id.toString(),
                            label: project.name
                        }))
                    ]
                },
                {
                    key: 'client_id',
                    type: 'select',
                    label: 'Client',
                    placeholder: 'Tous les clients',
                    value: localFilters.value.client_id || 'all',
                    options: [
                        { value: 'all', label: 'Tous les clients' },
                        ...filteredClients.value.map(client => ({
                            value: client.id.toString(),
                            label: client.name
                        }))
                    ]
                }
            ]
        }
    ]

    // Ajouter le filtre de statut de paiement si applicable
    if (showPaymentStatusFilter.value) {
        sections[0].filters.push({
            key: 'payment_status',
            type: 'select',
            label: 'Statut de paiement',
            placeholder: 'Tous les statuts',
            value: localFilters.value.payment_status || 'all',
            options: [
                { value: 'all', label: 'Tous les statuts' },
                { value: 'pending', label: 'En attente' },
                { value: 'paid', label: 'Payé' }
            ]
        })
    }

    // Section tri
    sections.push({
        key: 'sort',
        title: 'Tri et ordre',
        icon: 'arrow-up-down',
        layout: 'double',
        filters: [
            {
                key: 'sort',
                type: 'select',
                label: 'Trier par',
                placeholder: 'Sélectionner',
                value: localFilters.value.sort || 'created_date',
                options: [
                    { value: 'name', label: 'Nom' },
                    { value: 'created_date', label: 'Date de création' },
                    { value: 'due_date', label: 'Date de réalisation prévue' },
                    ...(showPaymentStatusFilter.value ? [{ value: 'amount', label: 'Montant' }] : [])
                ]
            },
            {
                key: 'direction',
                type: 'select',
                label: 'Ordre',
                placeholder: 'Sélectionner',
                value: localFilters.value.direction || 'desc',
                options: [
                    { value: 'asc', label: 'Croissant' },
                    { value: 'desc', label: 'Décroissant' }
                ]
            }
        ]
    })

    // Section alertes (seulement si applicable)
    if (showPaymentOverdueFilter.value) {
        sections.push({
            key: 'alerts',
            title: 'Alertes',
            icon: 'alert-circle',
            layout: 'single',
            filters: [
                {
                    key: 'payment_overdue',
                    type: 'checkbox',
                    label: 'Paiements en retard',
                    value: localFilters.value.payment_overdue || false,
                    icon: 'credit-card',
                    color: 'red'
                }
            ]
        })
    }

    return sections
})

// Watchers
watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters }
}, { deep: true })

// Watcher pour réinitialiser le projet quand le client change
watch(() => localFilters.value.client_id, (newClientId, oldClientId) => {
    if (newClientId !== oldClientId && newClientId && newClientId !== 'all') {
        const currentProjectId = localFilters.value.project_id
        if (currentProjectId && currentProjectId !== 'all') {
            const currentProject = props.availableProjects.find(p => p.id === parseInt(currentProjectId))
            const newClientIdNum = parseInt(newClientId)

            if (!currentProject || currentProject.client_id !== newClientIdNum) {
                localFilters.value.project_id = 'all'
                applyFilters()
            }
        }
    }
})

// Watcher pour réinitialiser le client quand le projet change
watch(() => localFilters.value.project_id, (newProjectId, oldProjectId) => {
    if (newProjectId !== oldProjectId && newProjectId && newProjectId !== 'all') {
        const selectedProject = props.availableProjects.find(p => p.id === parseInt(newProjectId))
        if (selectedProject && selectedProject.client_id) {
            localFilters.value.client_id = selectedProject.client_id.toString()
            applyFilters()
        }
    }
    else if (newProjectId === 'all' && localFilters.value.client_id !== 'all') {
        localFilters.value.client_id = 'all'
        applyFilters()
    }
})

// Methods
const handleSearchInput = (value: string) => {
    localFilters.value.search = value
    
    if (searchTimeout) clearTimeout(searchTimeout)
    
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 300)
}

const handleSearchEnter = (value: string) => {
    localFilters.value.search = value
    
    if (searchTimeout) clearTimeout(searchTimeout)
    applyFilters()
}

const clearSearch = () => {
    localFilters.value.search = ''
    applyFilters()
}

const handleFilterChange = (key: string, value: any) => {
    localFilters.value[key as keyof EventFilters] = value
    
    // Si on change vers 'step', effacer les filtres de paiement
    if (key === 'event_type' && value === 'step') {
        if (localFilters.value.payment_overdue) {
            localFilters.value.payment_overdue = false
        }
        if (localFilters.value.payment_status) {
            localFilters.value.payment_status = undefined
        }
    }
    
    applyFilters()
}

const applyFilters = () => {
    const filtersToApply = { ...localFilters.value }

    // Si on change vers 'step', effacer les filtres de paiement AVANT la requête
    if (filtersToApply.event_type === 'step') {
        if (filtersToApply.payment_overdue) {
            filtersToApply.payment_overdue = false
            localFilters.value.payment_overdue = false
        }
        if (filtersToApply.payment_status) {
            filtersToApply.payment_status = undefined
            localFilters.value.payment_status = undefined
        }
    }

    emit('update:filters', filtersToApply)
}
</script>