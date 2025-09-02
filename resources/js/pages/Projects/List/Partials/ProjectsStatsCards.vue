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
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import StatsCard from './StatsCard.vue'
import StatsCardSkeleton from './StatsCardSkeleton.vue'
import type { ProjectListStats, ProjectFilters } from '@/types/projects/list'

/**
 * Grille de cartes statistiques pour les projets
 * Responsabilités :
 * - Affichage des statistiques agrégées
 * - Filtrage rapide par clic sur carte
 * - État de chargement unifié
 */

interface Props {
    stats: ProjectListStats
    currentFilters: ProjectFilters
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
    color: 'purple' | 'emerald' | 'blue' | 'orange'
    isActive: boolean
    isClickable: boolean
    filterKey?: string
    filterValue?: any
}

const statsCards = computed((): StatsCardData[] => [
    {
        key: 'total',
        title: 'Total projets',
        value: props.stats.total,
        icon: 'folder',
        color: 'purple',
        isActive: !props.currentFilters.status || props.currentFilters.status === 'all',
        isClickable: true,
        filterKey: 'clear_status_filter',
        filterValue: null
    },
    {
        key: 'active',
        title: 'Actifs',
        value: props.stats.active,
        icon: 'play',
        color: 'emerald',
        isActive: props.currentFilters.status === 'active',
        isClickable: true,
        filterKey: 'status',
        filterValue: 'active'
    },
    {
        key: 'completed',
        title: 'Terminés',
        value: props.stats.completed,
        icon: 'check',
        color: 'blue',
        isActive: props.currentFilters.status === 'completed',
        isClickable: true,
        filterKey: 'status',
        filterValue: 'completed'
    },
    {
        key: 'on_hold',
        title: 'En pause',
        value: props.stats.on_hold,
        icon: 'circle-pause',
        color: 'orange',
        isActive: props.currentFilters.status === 'on_hold',
        isClickable: true,
        filterKey: 'status',
        filterValue: 'on_hold'
    }
])

const hasValidStats = computed(() => {
    // Des stats sont valides si l'objet existe avec les propriétés attendues
    // Même si toutes les valeurs sont à 0, ce sont des stats valides
    return props.stats &&
           typeof props.stats.total === 'number' &&
           typeof props.stats.active === 'number' &&
           typeof props.stats.completed === 'number'
})

const handleCardClick = (card: StatsCardData) => {
    if (!card.isClickable || card.isActive) return

    // Si c'est pour effacer les filtres de status (carte "Total projets")
    if (card.filterKey === 'clear_status_filter') {
        const { status, ...otherFilters } = props.currentFilters
        router.get(route('projects.index', otherFilters), {}, {
            only: ['projects', 'stats', 'filters'],
            preserveState: true,
            preserveScroll: true
        })
        emit('filter-selected', 'clear_status_filter', null)
    } else if (card.filterKey && card.filterValue !== undefined) {
        // Appliquer le filtre de status
        const newFilters = { ...props.currentFilters, [card.filterKey]: card.filterValue }
        router.get(route('projects.index', newFilters), {}, {
            only: ['projects', 'stats', 'filters'],
            preserveState: true,
            preserveScroll: true
        })
        emit('filter-selected', card.filterKey, card.filterValue)
    }
}
</script>
