<template>
    <ModernFilterPanel
        :visible="props.visible"
        :search-value="props.filters?.search || ''"
        search-placeholder="Nom, email, entreprise..."
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
import { computed } from 'vue'
import ModernFilterPanel from '@/components/ui/ModernFilterPanel.vue'
import type { FilterSection } from '@/components/ui/ModernFilterPanel.vue'
import type { ClientFilters, ActiveFilter } from '@/types/clients/list'

/**
 * Panneau de filtres pour la liste des clients
 * Responsabilités :
 * - Interface de saisie des filtres
 * - Affichage des filtres actifs
 * - Debouncing de la recherche
 * - Émission des changements
 */

interface Props {
    visible?: boolean
    filters: ClientFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    isLoading: false
})

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'update:filters': [filters: Partial<ClientFilters>]
    'clear-filter': [key: keyof ClientFilters]
    'clear-all': []
}>()

// Configuration des sections de filtres
const filterSections = computed<FilterSection[]>(() => [
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
                value: props.filters?.sort_by || 'created_at',
                options: [
                    { value: 'name', label: 'Nom' },
                    { value: 'email', label: 'Email' },
                    { value: 'company', label: 'Entreprise' },
                    { value: 'projects_count', label: 'Nombre de projets' },
                    { value: 'total_revenue', label: 'Montant facturé' },
                    { value: 'created_at', label: 'Ancienneté' }
                ]
            },
            {
                key: 'sort_order',
                type: 'select',
                label: 'Ordre',
                placeholder: 'Sélectionner',
                value: props.filters?.sort_order || 'desc',
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
        icon: 'alert-triangle',
        layout: 'single',
        filters: [
            {
                key: 'has_overdue_payments',
                type: 'checkbox',
                label: 'Clients en retard de paiement',
                value: props.filters?.has_overdue_payments === 'true' || props.filters?.has_overdue_payments === true,
                icon: 'alert-triangle',
                color: 'red'
            }
        ]
    }
])

// Methods
const handleSearchInput = (value: string) => {
    emit('update:filters', { search: value })
}

const handleSearchEnter = (value: string) => {
    emit('update:filters', { search: value })
}

const clearSearch = () => {
    emit('update:filters', { search: '' })
}

const handleFilterChange = (key: string, value: any) => {
    emit('update:filters', { [key]: value })
}
</script>