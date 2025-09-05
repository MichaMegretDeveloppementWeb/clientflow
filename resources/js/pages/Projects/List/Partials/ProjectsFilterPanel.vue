<template>
    <ModernFilterPanel
        :visible="props.visible"
        :search-value="localFilters.search || ''"
        search-placeholder="Nom, description, client..."
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
import type { ProjectFilters, ActiveFilter } from '@/types/projects/list'

interface Props {
    visible?: boolean
    filters: ProjectFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading?: boolean
    availableClients?: Array<{ id: number; name: string }>
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    isLoading: false,
    availableClients: () => []
})

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'update:filters': [filters: Partial<ProjectFilters>]
    'clear-filter': [key: keyof ProjectFilters]
    'clear-all': []
}>()

// État local pour la gestion des filtres
const localFilters = ref<ProjectFilters>({ ...props.filters })
let searchTimeout: NodeJS.Timeout | null = null

// Configuration des sections de filtres
const filterSections = computed<FilterSection[]>(() => [
    {
        key: 'filters',
        title: 'Filtres',
        icon: 'filter',
        layout: 'single',
        filters: [
            {
                key: 'client_id',
                type: 'select',
                label: 'Client',
                placeholder: 'Tous les clients',
                value: localFilters.value.client_id || 'all',
                options: [
                    { value: 'all', label: 'Tous les clients' },
                    ...props.availableClients.map(client => ({
                        value: client.id.toString(),
                        label: client.name
                    }))
                ]
            }
        ]
    },
    {
        key: 'sort',
        title: 'Tri et ordre',
        icon: 'arrow-up-down',
        layout: 'double',
        filters: [
            {
                key: 'sort_by',
                type: 'select',
                label: 'Trier par',
                placeholder: 'Sélectionner',
                value: localFilters.value.sort_by || 'created_at',
                options: [
                    { value: 'name', label: 'Nom' },
                    { value: 'created_at', label: 'Date de création' },
                    { value: 'start_date', label: 'Date de début' },
                    { value: 'budget', label: 'Budget' },
                    { value: 'status', label: 'Statut' },
                    { value: 'events_count', label: 'Nombre d\'événements' },
                    { value: 'total_billed', label: 'Montant facturé' }
                ]
            },
            {
                key: 'sort_order',
                type: 'select',
                label: 'Ordre',
                placeholder: 'Sélectionner',
                value: localFilters.value.sort_order || 'desc',
                options: [
                    { value: 'asc', label: 'Croissant' },
                    { value: 'desc', label: 'Décroissant' }
                ]
            }
        ]
    },
    {
        key: 'alerts',
        title: 'Alertes',
        icon: 'alert-circle',
        layout: 'single',
        filters: [
            {
                key: 'has_overdue_tasks',
                type: 'checkbox',
                label: 'Projets avec tâches en retard',
                value: localFilters.value.has_overdue_tasks || false,
                icon: 'alert-triangle',
                color: 'orange'
            },
            {
                key: 'has_payment_overdue',
                type: 'checkbox',
                label: 'Projets avec paiements en retard',
                value: localFilters.value.has_payment_overdue || false,
                icon: 'credit-card',
                color: 'red'
            }
        ]
    }
])

// Watchers
watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters }
}, { deep: true })

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
    localFilters.value[key as keyof ProjectFilters] = value
    applyFilters()
}

const applyFilters = () => {
    emit('update:filters', { ...localFilters.value })
}
</script>