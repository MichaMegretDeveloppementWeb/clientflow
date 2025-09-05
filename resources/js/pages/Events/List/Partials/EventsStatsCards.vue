<template>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
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
                @click="handleCardClick(card)"
            />
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import StatsCard from './StatsCard.vue'
import StatsCardSkeleton from './StatsCardSkeleton.vue'
import type { EventListStats, EventFilters } from '@/types/events/list'

/**
 * Grille de cartes statistiques pour les événements
 * Responsabilités :
 * - Affichage des statistiques agrégées (total, todo, done, overdue)
 * - Filtrage rapide par clic sur carte
 * - État de chargement unifié
 */

interface Props {
    stats: EventListStats
    currentFilters: EventFilters
    isLoading: boolean
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
    color: 'purple' | 'emerald' | 'blue' | 'orange' | 'red'
    isActive: boolean
    isClickable: boolean
    filterKey?: string
    filterValue?: any
}

const statsCards = computed((): StatsCardData[] => [
    {
        key: 'total',
        title: 'Total événements',
        value: props.stats.total,
        icon: 'calendar',
        color: 'purple',
        isActive: !props.currentFilters.status || props.currentFilters.status === 'all',
        isClickable: true,
        filterKey: 'clear_status_filter',
        filterValue: null
    },
    {
        key: 'todo',
        title: 'À faire',
        value: props.stats.todo,
        icon: 'circle-dot',
        color: 'blue',
        isActive: props.currentFilters.status === 'todo',
        isClickable: true,
        filterKey: 'status',
        filterValue: 'todo'
    },
    {
        key: 'done',
        title: 'Terminés',
        value: props.stats.done,
        icon: 'check-circle',
        color: 'emerald',
        isActive: props.currentFilters.status === 'done',
        isClickable: true,
        filterKey: 'status',
        filterValue: 'done'
    },
    {
        key: 'overdue',
        title: 'En retard',
        value: props.stats.overdue,
        icon: 'clock',
        color: 'red',
        isActive: props.currentFilters.status === 'overdue',
        isClickable: true,
        filterKey: 'status',
        filterValue: 'overdue'
    }
])

const hasValidStats = computed(() => {
    // Des stats sont valides si l'objet existe avec les propriétés attendues
    // Même si toutes les valeurs sont à 0, ce sont des stats valides
    const isValid = props.stats &&
           typeof props.stats.total === 'number' &&
           typeof props.stats.todo === 'number' &&
           typeof props.stats.done === 'number' &&
           typeof props.stats.overdue === 'number'

    console.log('EventsStatsCards - hasValidStats:', isValid, 'stats:', props.stats)
    return isValid
})

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const hasStatusFilter = computed(() => {
    return props.currentFilters.status && props.currentFilters.status !== 'all'
})

const handleCardClick = (card: StatsCardData) => {
    if (!card.isClickable || card.isActive) return

    // Si c'est pour effacer les filtres de status (carte "Total événements")
    if (card.filterKey === 'clear_status_filter') {
        emit('filter-selected', 'clear_status_filter', null)
    } else if (card.filterKey && card.filterValue !== undefined) {
        // Appliquer le filtre
        emit('filter-selected', card.filterKey, card.filterValue)
    }
}
</script>
