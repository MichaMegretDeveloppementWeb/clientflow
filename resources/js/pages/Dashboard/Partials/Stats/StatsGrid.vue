<template>
    <div v-if="!isLoading && displayStats.length > 0" class="grid gap-4 grid-cols-2 lg:grid-cols-4 mt-10">
        <StatsCard
            v-for="stat in displayStats"
            :key="stat.title"
            :title="stat.title"
            :value="stat.value"
            :icon="stat.icon"
            :color="stat.color"
            :href="stat.href"
            :growth="stat.growth"
            :growth-label="stat.growthLabel"
            :format-as-number="false"
        />
    </div>

    <div v-if="isLoading" class="grid gap-4 grid-cols-2 lg:grid-cols-4 mt-10">
        <Card v-for="i in 4" :key="i">
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <div class="h-4 w-24 bg-gray-200 animate-pulse rounded"></div>
                <div class="h-4 w-4 bg-gray-200 animate-pulse rounded"></div>
            </CardHeader>
            <CardContent>
                <div class="h-8 w-20 bg-gray-200 animate-pulse rounded mb-2"></div>
                <div class="h-3 w-32 bg-gray-200 animate-pulse rounded"></div>
            </CardContent>
        </Card>
    </div>

    <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-600">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import StatsCard from './StatsCard.vue'
import { useStatistics } from '@/composables/dashboard/useStatistics'
import { route } from 'ziggy-js'

const {
    statistics,
    isLoading,
    error,
    loadStatistics,
    formatCurrency,
    formatPercentage,
    getGrowthIndicator,
} = useStatistics()


const displayStats = computed(() => {
    if (!statistics.value) return []

    const growthIndicator = getGrowthIndicator(statistics.value.revenue.growth)

    return [
        {
            title: 'Clients',
            value: statistics.value.clients.total.toString(),
            icon: 'users',
            color: 'blue',
            href: route('clients.index', { from: 'dashboard' }),
            growth: statistics.value.clients.this_month,
            growthLabel: (() => {
                const thisMonth = statistics.value.clients.this_month
                if (thisMonth === 0) {
                    return 'aucun ce mois'
                } else if (thisMonth === 1) {
                    return 'client ce mois'
                } else {
                    return 'clients ce mois'
                }
            })(),
        },
        {
            title: 'Projets actifs',
            value: statistics.value.projects.active.toString(),
            icon: 'folder-open',
            color: 'emerald',
            href: route('projects.index', { status: 'active', from: 'dashboard' }),
        },
        {
            title: 'Projets terminés',
            value: statistics.value.projects.completed.toString(),
            icon: 'check-circle',
            color: 'green',
            href: route('projects.index', { status: 'completed', from: 'dashboard' }),
        },
        {
            title: 'Projets en pause',
            value: statistics.value.projects.on_hold.toString(),
            icon: 'pause',
            color: 'orange',
            href: route('projects.index', { status: 'on_hold', from: 'dashboard' }),
        },
    ]
})

onMounted( () => {
    loadStatistics();
})
</script>
