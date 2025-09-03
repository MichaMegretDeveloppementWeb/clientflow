<template>
    <Card class="bg-white shadow-sm border border-gray-200 my-16">
        <CardHeader class="pb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-900 text-white">
                        <Icon name="trending-up" class="w-5 h-5" />
                    </div>
                    <div>
                        <CardTitle class="text-xl font-semibold text-gray-900">
                            Évolution du chiffre d'affaires
                        </CardTitle>
                        <p class="text-sm text-gray-500">
                            Analyse des revenus et facturation
                        </p>
                    </div>
                </div>
            </div>
        </CardHeader>

        <CardContent class="space-y-6">
            <!-- Contrôles simplifiés -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Période :</label>
                    <select
                        :value="selectedPeriod"
                        @change="handlePeriodChange"
                        :disabled="isLoading"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 disabled:opacity-50"
                    >
                        <option v-for="period in availablePeriods" :key="period.value" :value="period.value">
                            {{ period.label }}
                        </option>
                    </select>
                </div>

                <!-- Sélecteur de type de ligne simplifié -->
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Style :</label>
                    <div class="flex bg-white rounded-lg border border-gray-300 p-1">
                        <button
                            @click="isLineSmooth = false"
                            :class="[
                                'inline-flex items-center justify-center p-2 rounded-md transition-all duration-200 text-sm',
                                !isLineSmooth
                                    ? 'bg-gray-900 text-white'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                            title="Ligne brisée"
                        >
                            <Icon name="chart-line" class="w-4 h-4" />
                        </button>

                        <button
                            @click="isLineSmooth = true"
                            :class="[
                                'inline-flex items-center justify-center p-2 rounded-md transition-all duration-200 text-sm',
                                isLineSmooth
                                    ? 'bg-gray-900 text-white'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                            title="Ligne arrondie"
                        >
                            <Icon name="chart-spline" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistiques simplifiées -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="bg-white border border-gray-200 rounded-lg p-6 transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total facturé</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(getTotalBilled) }}</p>
                        </div>
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <Icon name="calculator" class="h-5 w-5 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total revenus</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(getTotalRevenue) }}</p>
                        </div>
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <Icon name="trending-up" class="h-5 w-5 text-green-600" />
                        </div>
                    </div>
                </div>

<!--                <div class="bg-white border border-gray-200 rounded-lg p-6 transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Moyenne période</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(getAverageMonthlyRevenue) }}</p>
                        </div>
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <Icon name="calendar" class="h-5 w-5 text-purple-600" />
                        </div>
                    </div>
                </div>-->
            </div>

            <!-- Container du graphique -->
            <div class="bg-white border border-gray-200 rounded-lg">
                <!-- Légende avec bonnes couleurs -->
                <div class="flex items-center justify-center gap-4 p-4 border-b border-gray-200">

                    <button
                        @click="toggleDatasetVisibility('facture')"
                        :class="[
                            'inline-flex items-center gap-2 px-4 py-2 rounded-lg border transition-all duration-200 text-sm font-medium',
                            datasetVisibility.facture
                                ? 'bg-blue-50 border-blue-200 text-blue-700 shadow-sm hover:bg-blue-100'
                                : 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100'
                        ]"
                    >
                        <div
                            :class="[
                                'w-3 h-3 rounded-full transition-all duration-200',
                                datasetVisibility.facture ? 'bg-blue-500 ring-2 ring-blue-200' : 'bg-gray-300'
                            ]"
                        ></div>
                        <span>Revenus facturé</span>
                        <Icon
                            :name="datasetVisibility.facture ? 'eye' : 'eye-off'"
                            class="w-4 h-4 opacity-70"
                        />
                    </button>

                    <button
                        @click="toggleDatasetVisibility('revenus')"
                        :class="[
                            'inline-flex items-center gap-2 px-4 py-2 rounded-lg border transition-all duration-200 text-sm font-medium',
                            datasetVisibility.revenus
                                ? 'bg-green-50 border-green-200 text-green-700 shadow-sm hover:bg-green-100'
                                : 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100'
                        ]"
                    >
                        <div
                            :class="[
                                'w-3 h-3 rounded-full transition-all duration-200',
                                datasetVisibility.revenus ? 'bg-green-500 ring-2 ring-green-200' : 'bg-gray-300'
                            ]"
                        ></div>
                        <span>Revenus réels</span>
                        <Icon
                            :name="datasetVisibility.revenus ? 'eye' : 'eye-off'"
                            class="w-4 h-4 opacity-70"
                        />
                    </button>

                </div>

                <div class="relative h-80 p-4">
                    <!-- Canvas pour le graphique -->
                    <canvas
                        v-if="hasChartData && !isLoading"
                        ref="chartCanvas"
                        class="h-full w-full"
                    ></canvas>

                    <!-- Loading State simple -->
                    <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/95">
                        <div class="text-center space-y-3">
                            <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-gray-300 border-t-gray-900"></div>
                            <p class="text-sm text-gray-600">Chargement du graphique...</p>
                        </div>
                    </div>

                    <!-- Error State simple -->
                    <div v-if="error" class="absolute inset-0 flex items-center justify-center bg-white/95">
                        <div class="text-center space-y-4">
                            <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                <Icon name="alert-circle" class="h-6 w-6 text-red-600" />
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-900">Erreur de chargement</h4>
                                <p class="text-sm text-gray-600 max-w-xs">{{ error }}</p>
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="refreshChartData"
                            >
                                <Icon name="refresh-cw" class="h-4 w-4 mr-2" />
                                Réessayer
                            </Button>
                        </div>
                    </div>

                    <!-- Empty State simple -->
                    <div v-if="!hasChartData && !isLoading && !error" class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center space-y-4">
                            <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                                <Icon name="chart-line" class="h-6 w-6 text-gray-500" />
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-900">Aucune donnée</h4>
                                <p class="text-sm text-gray-600 max-w-xs">Aucune donnée disponible pour cette période</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch, nextTick } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import Icon from '@/components/Icon.vue'
import { useRevenueChart } from '@/composables/dashboard/useRevenueChart'

// Lazy loading Chart.js
let Chart: any = null

const loadChartJs = async () => {
    if (Chart) return Chart

    const { Chart: ChartClass, registerables } = await import('chart.js')
    ChartClass.register(...registerables)
    Chart = ChartClass
    return Chart
}

// Composable
const {
    isLoading,
    error,
    selectedPeriod,
    hasChartData,
    labels,
    datasets,
    availablePeriods,
    getTotalRevenue,
    getTotalBilled,
    getTooltipTitle,
    loadChartData,
    refreshChartData,
    changePeriod,
    formatCurrency,
} = useRevenueChart()

// Refs
const chartCanvas = ref<HTMLCanvasElement | null>(null)
let chartInstance: any = null

// Legend state
const datasetVisibility = ref<Record<string, boolean>>({
    'revenus': true,
    'facture': true
})

// Line type state (false = brisée, true = arrondie)
const isLineSmooth = ref(false)

// Handlers
const handlePeriodChange = async (event: Event) => {
    const target = event.target as HTMLSelectElement
    const newPeriod = target.value
    console.log('Changing period to:', newPeriod)

    await changePeriod(newPeriod)
    if (hasChartData.value) {
        await recreateChart()
    }
}

const toggleDatasetVisibility = (datasetKey: string) => {
    datasetVisibility.value[datasetKey] = !datasetVisibility.value[datasetKey]

    if (chartInstance) {
        const datasetIndex = datasetKey === 'revenus' ? 0 : 1

        if (datasetVisibility.value[datasetKey]) {
            // Afficher avec animation
            chartInstance.show(datasetIndex)
        } else {
            // Masquer avec animation
            chartInstance.hide(datasetIndex)
        }
    }
}


// Chart functions
const createChart = async (): Promise<void> => {
    if (!chartCanvas.value || !Chart || !hasChartData.value) return

    const config = {
        type: 'line' as const,
        data: {
            labels: labels.value,
            datasets: datasets.value.map((dataset, index) => ({
                ...dataset,
                // Couleurs modernes avec remplissage
                borderColor: index === 0 ? '#10b981' : '#3b82f6', // Emerald-500 et Blue-500
                backgroundColor: index === 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(59, 130, 246, 0.1)', // Avec transparence
                borderWidth: 2, // Un peu moins épais
                pointRadius: 0, // Pas de points visibles
                pointHoverRadius: 0, // Pas de points au hover non plus
                stepped: false,
                cubicInterpolationMode: 'default',
                tension: isLineSmooth.value ? 0.4 : 0,
                fill: true // Activer le remplissage sous la ligne
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 0
            },
            interaction: {
                intersect: false,
                mode: 'index' as const,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937', // Gray-800
                    titleColor: '#f9fafb', // Gray-50
                    bodyColor: '#f9fafb',
                    borderColor: '#374151', // Gray-700
                    borderWidth: 1,
                    cornerRadius: 12,
                    displayColors: true,
                    padding: 12,
                    titleFont: { size: 14, weight: '600' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        title: (context: any) => {
                            if (context.length > 0) {
                                return getTooltipTitle(context[0].dataIndex)
                            }
                            return ''
                        },
                        label: (context: any) => {
                            return `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280', // Gray-500
                        font: { size: 12, weight: '500' },
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: false,
                        padding: 8
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6', // Gray-100
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280', // Gray-500
                        font: { size: 12, weight: '500' },
                        padding: 8,
                        callback: function(value: any) {
                            const numValue = Number(value)
                            if (numValue >= 1000000) {
                                return (numValue / 1000000).toFixed(1).replace('.0', '') + 'M €'
                            } else if (numValue >= 1000) {
                                return (numValue / 1000).toFixed(1).replace('.0', '') + 'k €'
                            }
                            return numValue + ' €'
                        }
                    }
                }
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
            },
            elements: {
                line: {
                    tension: 0.3
                },
                point: {
                    hoverRadius: 8
                }
            },
            datasets: {
                line: {
                    spanGaps: true
                }
            }
        }
    }

    chartInstance = new Chart(chartCanvas.value, config)

    // Initialiser la visibilité des datasets avec animations
    if (!datasetVisibility.value.revenus) {
        chartInstance.hide(0)
    }
    if (!datasetVisibility.value.facture) {
        chartInstance.hide(1)
    }
}

const updateChart = (): void => {
    if (!chartInstance || !hasChartData.value) return

    chartInstance.data.labels = labels.value
    chartInstance.data.datasets = datasets.value.map(dataset => ({
        ...dataset,
        borderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: dataset.borderColor,
        pointBorderColor: 'white',
        pointBorderWidth: 2,
    }))

    chartInstance.update('active')
}

const recreateChart = async (): Promise<void> => {
    if (chartInstance) {
        chartInstance.destroy()
        chartInstance = null
    }

    if (hasChartData.value) {
        await nextTick()
        await createChart()
    }
}

// Watchers
watch([labels, datasets], () => {
    if (chartInstance && hasChartData.value) {
        updateChart()
    }
}, { deep: true })

watch(isLineSmooth, () => {
    if (chartInstance) {
        const tension = isLineSmooth.value ? 0.4 : 0
        chartInstance.data.datasets.forEach((dataset: any) => {
            dataset.tension = tension
        })
        chartInstance.update('active')
    }
})

// Lifecycle
onMounted(async () => {
    try {
        await loadChartJs()
        await loadChartData()

        if (hasChartData.value) {
            await createChart()
        }
    } catch (err) {
        console.error('Erreur lors de l\'initialisation du graphique:', err)
    }
})

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy()
        chartInstance = null
    }
})
</script>
