<template>
    <div class="space-y-4 my-16">
        <!-- Header avec contrôles -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Évolution du chiffre d'affaires</h3>
        </div>

        <!-- Contrôles -->
        <div class="flex gap-6 items-center justify-start flex-wrap mb-6">
            <!-- Sélecteur de période -->
            <select
                :value="selectedPeriod"
                @change="handlePeriodChange"
                :disabled="isLoading"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium shadow-sm transition-colors focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 disabled:opacity-50"
            >
                <option v-for="period in availablePeriods" :key="period.value" :value="period.value">
                    {{ period.label }}
                </option>
            </select>

            <!-- Sélecteur de type de ligne -->
            <div class="flex items-center gap-3">
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button
                        @click="isLineSmooth = false"
                        :class="[
                            'inline-flex items-center justify-center p-2 rounded-md transition-all duration-200',
                            !isLineSmooth
                                ? 'bg-white shadow-sm text-purple-700 border border-purple-200'
                                : 'text-gray-600 hover:text-gray-900 cursor-pointer'
                        ]"
                        title="Ligne brisée"
                    >
                        <Icon name="chart-line" class="w-4 h-4" />
                    </button>

                    <button
                        @click="isLineSmooth = true"
                        :class="[
                            'inline-flex items-center justify-center p-2 rounded-md transition-all duration-200',
                            isLineSmooth
                                ? 'bg-white shadow-sm text-purple-700 border border-purple-200'
                                : 'text-gray-600 hover:text-gray-900 cursor-pointer'
                        ]"
                        title="Ligne arrondie"
                    >
                        <Icon name="chart-spline" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-3">

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-green-100 p-2">
                        <Icon name="trending-up" class="h-4 w-4 text-green-600" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total revenus</p>
                        <p class="md:text-lg text-sm font-bold text-gray-900">{{ formatCurrency(getTotalRevenue) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-blue-100 p-2">
                        <Icon name="calculator" class="h-4 w-4 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total facturé</p>
                        <p class="md:text-lg text-sm font-bold text-gray-900">{{ formatCurrency(getTotalBilled) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 hidden md:block">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-purple-100 p-2">
                        <Icon name="calendar" class="h-4 w-4 text-purple-600" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Moyenne période</p>
                        <p class="md:text-lg text-sm font-bold text-gray-900">{{ formatCurrency(getAverageMonthlyRevenue) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique -->
        <Card class="border border-gray-200 bg-white p-6 shadow-sm">

            <!-- Légende personnalisée -->
            <div class="flex gap-4 justify-center text-sm md:text-base">
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
                        'w-3 h-3 rounded-full transition-all duration-200 aspect-square',
                        datasetVisibility.revenus ? 'bg-green-500 ring-2 ring-green-200' : 'bg-gray-300'
                    ]"
                    ></div>
                    <span>Revenus réels</span>
                    <Icon
                        :name="datasetVisibility.revenus ? 'eye' : 'eye-off'"
                        class="w-4 h-4 opacity-70"
                    />
                </button>

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
                        'w-3 h-3 rounded-full transition-all duration-200 aspect-square',
                        datasetVisibility.facture ? 'bg-blue-500 ring-2 ring-blue-200' : 'bg-gray-300'
                    ]"
                    ></div>
                    <span>Revenus facturé</span>
                    <Icon
                        :name="datasetVisibility.facture ? 'eye' : 'eye-off'"
                        class="w-4 h-4 opacity-70"
                    />
                </button>
            </div>

            <div class="relative h-80">
                <!-- Canvas pour le graphique -->
                <canvas
                    v-if="hasChartData && !isLoading"
                    ref="chartCanvas"
                    class="h-full w-full"
                ></canvas>

                <!-- Loading State -->
                <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/90">
                    <div class="text-center">
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-purple-500 border-t-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Chargement du graphique...</p>
                    </div>
                </div>

                <!-- Error State -->
                <div v-if="error" class="absolute inset-0 flex items-center justify-center bg-red-50/90">
                    <div class="text-center">
                        <Icon name="alert-circle" class="mx-auto h-12 w-12 text-red-400" />
                        <h4 class="mt-2 text-sm font-medium text-red-900">Erreur de chargement</h4>
                        <p class="text-sm text-red-600">{{ error }}</p>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="refreshChartData"
                            class="mt-3"
                        >
                            <Icon name="refresh-cw" class="h-4 w-4 mr-1" />
                            Réessayer
                        </Button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!hasChartData && !isLoading && !error" class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <Icon name="chart-line" class="mx-auto h-12 w-12 text-gray-400" />
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Aucune donnée</h4>
                        <p class="text-sm text-gray-600">Aucune donnée disponible pour cette période</p>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch, nextTick } from 'vue'
import { Card } from '@/components/ui/card'
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
    chartData,
    isLoading,
    error,
    selectedPeriod,
    hasChartData,
    labels,
    datasets,
    chartOptions,
    granularity,
    availablePeriods,
    getTotalRevenue,
    getTotalBilled,
    getAverageMonthlyRevenue,
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

const toggleLineType = () => {
    isLineSmooth.value = !isLineSmooth.value

    if (chartInstance) {
        // Mettre à jour la tension des datasets
        const tension = isLineSmooth.value ? 0.4 : 0
        chartInstance.data.datasets.forEach((dataset: any) => {
            dataset.tension = tension
        })
        chartInstance.update('active')
    }
}

// Chart functions
const createChart = async (): Promise<void> => {
    if (!chartCanvas.value || !Chart || !hasChartData.value) return

    const labelsCount = labels.value.length

    const config = {
        type: 'line' as const,
        data: {
            labels: labels.value,
            datasets: datasets.value.map(dataset => ({
                ...dataset,
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 0,
                pointBackgroundColor: dataset.borderColor,
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                stepped: false,
                cubicInterpolationMode: 'default',
                tension: isLineSmooth.value ? 0.4 : 0
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
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(147, 51, 234, 0.3)',
                    borderWidth: 1,
                    cornerRadius: 8,
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
                        color: 'rgb(107, 114, 128)',
                        font: { size: 12 },
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: false
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        color: 'rgb(107, 114, 128)',
                        font: { size: 12 },
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
