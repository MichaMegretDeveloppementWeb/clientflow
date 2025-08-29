<template>
    <div class="space-y-4">
        <!-- Header avec contrôles -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Évolution du chiffre d'affaires</h3>
        </div>

        <!-- Graphique -->
        <Card class="border border-gray-200 bg-white p-6 shadow-sm gap-0">

            <!-- Montant total de la période -->
            <div class="flex justify-center mb-3">
                <div class="inline-flex items-center gap-3 px-4 py-3">
                    <div class="rounded-full bg-purple-100 p-2">
                        <Icon name="banknote" class="h-4 w-4 text-purple-600" />
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total période</p>
                        <p class="text-xl font-bold text-gray-900">{{ formatCurrency(parseFloat(totalAmount)) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-6 items-center justify-center mb-16">
                <!-- Sélecteur de période -->
                <select
                    v-model="selectedPeriod"
                    @change="updateChart"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium shadow-sm transition-colors focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 w-max"
                >
                    <option value="current_month">Mois en cours</option>
                    <option value="7_days">7 derniers jours</option>
                    <option value="30_days">30 derniers jours</option>
                    <option value="3_months">3 derniers mois</option>
                    <option value="6_months">6 derniers mois</option>
                    <option value="12_months">12 derniers mois</option>
                    <option value="all_time">Toute la période</option>
                </select>

                <!-- Sélecteur de style de ligne avec icônes -->
                <div class="flex items-center rounded-lg bg-gray-100 p-1 w-max">
                    <button
                        @click="setLineStyle('straight')"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-all"
                        :class="lineStyle === 'straight' ? 'bg-white text-gray-900 shadow-sm' : 'cursor-pointer text-gray-500 hover:text-gray-700'"
                        title="Ligne brisée"
                    >
                        <Icon name="chart-line" class="h-4 w-4" />
                    </button>
                    <button
                        @click="setLineStyle('curved')"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-all"
                        :class="lineStyle === 'curved' ? 'bg-white text-gray-900 shadow-sm' : 'cursor-pointer text-gray-500 hover:text-gray-700'"
                        title="Ligne arrondie"
                    >
                        <Icon name="chart-line" class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <div class="relative h-90">
                <canvas ref="chartCanvas" class="h-full w-full"></canvas>

                <!-- Loading state -->
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/90">
                    <div class="text-center">
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-purple-500 border-t-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Chargement...</p>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="!loading && chartData.length === 0" class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <Icon name="chart-bar" class="mx-auto h-12 w-12 text-gray-400" />
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Aucune donnée</h4>
                        <p class="text-sm text-gray-500">Aucun paiement pour cette période</p>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</template>

<script setup lang="ts">
import { Card } from '@/components/ui/card';
import Icon from '@/components/Icon.vue';
import { onMounted, ref, onUnmounted, toRaw, nextTick } from 'vue';
import {
    Chart,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    LineController,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

// Enregistrer les composants Chart.js
Chart.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    LineController,
    Title,
    Tooltip,
    Legend,
    Filler
);

// Refs
const chartCanvas = ref<HTMLCanvasElement | null>(null);
const selectedPeriod = ref('current_month');
const lineStyle = ref<'straight' | 'curved'>('straight');
const loading = ref(false);
const chartData = ref<Array<{ date: string; label: string; amount: number }>>([]);
let chartInstance: Chart | null = null; // Variable normale au lieu de ref
const currentGroupBy = ref('day');
const totalAmount = ref(0);

// Fonction pour formater les montants
const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

// Fonction pour formater les dates pour le tooltip
const formatTooltipDate = (label: string, groupBy: string): string => {
    if (groupBy === 'day') {
        return `Le ${label}`;
    } else if (groupBy === 'week') {
        return `Semaine du ${label}`;
    } else if (groupBy === 'month') {
        return label;
    }
    return label;
};

// Charger les données du graphique
const loadChartData = async () => {

    loading.value = true;

    try {
        const url = `/dashboard/revenue-chart?period=${selectedPeriod.value}`;

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        chartData.value = result.data || [];
        currentGroupBy.value = result.groupBy || 'day';
        totalAmount.value = result.totalAmount || 0;

    } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
        chartData.value = [];
    } finally {
        loading.value = false;
    }

};



// Créer le graphique initial
const createChart = () => {
    if (!chartCanvas.value) return;

    const rawData = toRaw(chartData.value);
    const config = {
        type: 'line' as const,
        data: {
            labels: rawData.map(item => item.label),
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: rawData.map(item => item.amount),
                borderColor: 'rgb(26 115 232)',
                backgroundColor: 'rgba(241,245,255,0)',
                borderWidth: 2,
                pointBackgroundColor: 'rgb(147, 51, 234)',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 0,
                tension: lineStyle.value === 'curved' ? 0.4 : 0,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    padding: 10,
                    borderColor: 'rgba(147, 51, 234, 0.3)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    },
                    callbacks: {
                        title: (context: any) => {
                            const dataIndex = context[0].dataIndex;
                            const item = toRaw(chartData.value)[dataIndex];
                            return formatTooltipDate(item.tooltip_date, currentGroupBy.value);
                        },
                        label: (context: any) => {
                            return `Chiffre d'affaires : ${formatCurrency(context.parsed.y)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false, // Masquer les lignes verticales
                    },
                    ticks: {
                        color: 'rgb(107, 114, 128)', // gray-500
                        font: {
                            size: 12
                        },
                        maxRotation: 0,
                        minRotation: 0
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)', // Garder les lignes horizontales
                    },
                    ticks: {
                        color: 'rgb(107, 114, 128)', // gray-500
                        font: {
                            size: 12
                        },
                        callback: function(value: any) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index' as const,
            },
            hover: {
                animationDuration: 200
            },
            animation: {
                duration: 500,
                easing: 'easeInOutQuart'
            }
        }
    };

    chartInstance = new Chart(chartCanvas.value, config);
};



// Mettre à jour le graphique existant
const updateChartDisplay = () => {

    if (!chartInstance) {
        createChart();
        return;
    }

    const currentStyle = lineStyle.value; // Capturer la valeur pour éviter la réactivité

    // Mettre à jour la tension (style de ligne)
    chartInstance.data.datasets[0].tension = currentStyle === 'curved' ? 0.4 : 0;

    // Appeler update() pour redessiner le graphique
    chartInstance.update();

};


// Re-créer le graphique
const recreateChart = () => {

    chartInstance?.destroy();
    createChart();
    return;

};



// Mettre à jour le graphique
const updateChart = async () => {
    await loadChartData();
    recreateChart();
};

// Définir le style de ligne
const setLineStyle = (style: 'straight' | 'curved') => {
    if (lineStyle.value === style) {
        return;
    }

    lineStyle.value = style;

    // Mettre à jour le graphique avec le nouveau style
    updateChartDisplay();
};

// Lifecycle
onMounted(async () => {
    await loadChartData();
    createChart();
});

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>
