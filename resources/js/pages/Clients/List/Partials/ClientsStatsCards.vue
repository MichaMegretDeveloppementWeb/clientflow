<template>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <template v-if="isLoading || !hasValidStats">
            <StatsCardSkeleton v-for="i in 4" :key="`skeleton-${i}`" />
        </template>
        <template v-else>
            <StatsCard
                v-for="card in statsCards"
                :key="card.key"
                :title="card.title"
                :value="card.value"
                :icon="card.icon"
                :color="card.color"
                :is-active="card.isActive"
                :is-loading="false"
                :is-clickable="card.isClickable"
                @click="card.isClickable && handleCardClick(card)"
            />
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import StatsCard from './StatsCard.vue'
import StatsCardSkeleton from './StatsCardSkeleton.vue'
import type { ClientListStats } from '@/types/clients/list'
import type { ClientFilters } from '@/types/clients/list'

/**
 * Grille de cartes statistiques
 * Responsabilités :
 * - Affichage des statistiques agrégées
 * - Filtrage rapide par clic sur carte
 * - État de chargement unifié
 */

interface Props {
    stats: ClientListStats
    currentFilters: ClientFilters
    isLoading?: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'filter-selected': [filterKey: string, filterValue: any]
}>()

interface StatsCardData {
    key: string
    title: string
    value: number | string
    icon: string
    color: 'blue' | 'green' | 'amber' | 'purple'
    isActive: boolean
    isClickable: boolean
    filterKey?: string
    filterValue?: any
}

const statsCards = computed((): StatsCardData[] => [
    {
        key: 'total',
        title: 'Total clients',
        value: props.stats.total,
        icon: 'users',
        color: 'blue',
        isActive: !hasProjectFilters.value,
        isClickable: true,
        filterKey: 'clear_project_filters',
        filterValue: true
    },
    {
        key: 'with_projects',
        title: 'Avec projets',
        value: props.stats.with_projects,
        icon: 'folder',
        color: 'green',
        isActive: props.currentFilters.has_projects === 'true',
        isClickable: true,
        filterKey: 'has_projects',
        filterValue: 'true'
    },
    {
        key: 'without_projects',
        title: 'Sans projets',
        value: props.stats.without_projects,
        icon: 'folder-x',
        color: 'amber',
        isActive: props.currentFilters.has_projects === 'false',
        isClickable: true,
        filterKey: 'has_projects',
        filterValue: 'false'
    },
    {
        key: 'with_active_projects',
        title: 'Projets actifs',
        value: props.stats.with_active_projects,
        icon: 'activity',
        color: 'purple',
        isActive: props.currentFilters.has_active_projects === 'true',
        isClickable: true,
        filterKey: 'has_active_projects',
        filterValue: 'true'
    }
])

const hasProjectFilters = computed(() => {
    return props.currentFilters.has_projects !== undefined ||
           props.currentFilters.has_active_projects !== undefined
})

const hasValidStats = computed(() => {
    // Des stats sont valides si l'objet existe avec les propriétés attendues
    // Même si toutes les valeurs sont à 0, ce sont des stats valides
    return props.stats && 
           typeof props.stats.total === 'number' &&
           typeof props.stats.with_projects === 'number' &&
           typeof props.stats.without_projects === 'number' &&
           typeof props.stats.with_active_projects === 'number'
})

const handleCardClick = (card: StatsCardData) => {
    if (!card.isClickable) return

    // Si la carte est déjà active, ne rien faire
    if (card.isActive) return

    // Si c'est pour effacer les filtres projets (carte "Total clients")
    if (card.filterKey === 'clear_project_filters') {
        // Émettre un événement spécial pour effacer les filtres projets
        emit('filter-selected', 'clear_project_filters', null)
    } else if (card.filterKey && card.filterValue !== undefined) {
        // Appliquer le filtre (et ça va automatiquement effacer les autres filtres mutuellement exclusifs)
        emit('filter-selected', card.filterKey, card.filterValue)
    }
}
</script>
