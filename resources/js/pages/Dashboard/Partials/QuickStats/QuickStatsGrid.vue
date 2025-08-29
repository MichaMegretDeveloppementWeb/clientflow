<template>
    <Card class="border-0 bg-white shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2">
                <OptimizedIcon name="bar-chart-3" :size="20" class="text-gray-600" preload />
                Statistiques rapides
            </CardTitle>
        </CardHeader>

        <CardContent v-if="isLoading" class="space-y-4">
            <div class="animate-pulse">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-lg bg-gray-200 h-24"></div>
                    <div class="rounded-lg bg-gray-200 h-24"></div>
                </div>
                <div class="grid grid-cols-3 gap-3 pt-6 mt-4 border-t border-gray-100">
                    <div class="text-center space-y-2">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-6 bg-gray-200 rounded"></div>
                    </div>
                    <div class="text-center space-y-2">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-6 bg-gray-200 rounded"></div>
                    </div>
                    <div class="text-center space-y-2">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-6 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </CardContent>

        <CardContent v-else-if="error" class="space-y-4">
            <div class="text-center py-8">
                <OptimizedIcon name="alert-circle" :size="48" class="mx-auto text-red-400 mb-4" />
                <h4 class="text-sm font-medium text-red-900 mb-2">Erreur de chargement</h4>
                <p class="text-sm text-red-600 mb-4">{{ error }}</p>
                <Button @click="refreshQuickStats" variant="outline" size="sm">
                    <OptimizedIcon name="refresh-cw" :size="16" class="mr-2" />
                    Réessayer
                </Button>
            </div>
        </CardContent>

        <CardContent v-else-if="hasQuickStats" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <!-- Taux de completion projets -->
                <div class="rounded-lg bg-gray-50 p-4 text-center transition-colors hover:bg-gray-100">
                    <p class="text-sm text-gray-600 mb-2">
                        Taux de completion projets
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ quickStats.completion_rate.formatted }}
                    </p>

                    <!-- Barre de progression -->
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                        <div
                            class="bg-emerald-500 h-1.5 rounded-full transition-all duration-700"
                            :style="{ width: `${quickStats.completion_rate.value}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Revenus par client -->
                <div class="rounded-lg bg-gray-50 p-4 text-center transition-colors hover:bg-gray-100">
                    <p class="text-sm text-gray-600 mb-2">
                        Revenus par client
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ formatCurrency(quickStats.revenue_per_client.formatted) }}
                    </p>

                    <!-- Indicateur de tendance -->
                    <div class="mt-2 flex items-center justify-center gap-1">
                        <OptimizedIcon
                            :name="quickStats.revenue_growth.trend_icon"
                            :size="12"
                            :class="`text-${quickStats.revenue_growth.trend_color}-500`"
                        />
                        <span
                            class="text-xs font-medium"
                            :class="`text-${quickStats.revenue_growth.trend_color}-600`"
                        >
                            {{ formatPercentage(quickStats.revenue_growth.formatted) }}
                        </span>
                    </div>
                </div>
            </div>
        </CardContent>

        <CardContent v-else class="space-y-4">
            <div class="text-center py-8">
                <OptimizedIcon name="bar-chart-3" :size="48" class="mx-auto text-gray-400 mb-4" />
                <h4 class="text-sm font-medium text-gray-900 mb-2">Aucune donnée</h4>
                <p class="text-sm text-gray-600">Aucune statistique disponible</p>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import OptimizedIcon from '@/components/OptimizedIcon.vue'
import { useQuickStats } from '@/composables/dashboard/useQuickStats'

const {
    quickStats,
    isLoading,
    error,
    hasQuickStats,
    loadQuickStats,
    refreshQuickStats,
} = useQuickStats()

const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount).replace(/[\u202F\u00A0]/g, '\u2004')
}

const formatPercentage = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'percent',
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    }).format(amount/100).replace(/[\u202F\u00A0]/g, '\u2004')
}

onMounted(async () => {
    await loadQuickStats()
})
</script>

<style scoped>
/* Animation de la barre de progression */
.bg-emerald-500 {
    transition: width 0.7s ease-out;
}

/* Hover effects pour les métriques */
.rounded-lg.bg-gray-50 {
    transition: background-color 0.2s ease;
}
</style>
