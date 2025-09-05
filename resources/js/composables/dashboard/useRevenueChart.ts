import { ref, computed } from 'vue'
import type { RevenueChartData, RevenueChartResponse } from '@/types/dashboard/chart'
import { route } from 'ziggy-js'

export function useRevenueChart() {
    const chartData = ref<RevenueChartData | null>(null)
    const isLoading = ref(false)
    const error = ref<string | null>(null)
    const selectedPeriod = ref<string>('6months')

    const hasChartData = computed(() => chartData.value !== null)

    const labels = computed(() => chartData.value?.labels ?? [])
    const datasets = computed(() => chartData.value?.datasets ?? [])
    const chartOptions = computed(() => chartData.value?.chart_options ?? {})
    const granularity = computed(() => chartData.value?.granularity ?? 'month')
    const startDate = computed(() => chartData.value?.start_date ?? null)
    const endDate = computed(() => chartData.value?.end_date ?? null)

    const availablePeriods = [
        { value: 'current_month', label: 'Mois en cours' },
        { value: '7days', label: '7 derniers jours' },
        { value: '30days', label: '30 derniers jours' },
        { value: '3months', label: '3 derniers mois' },
        { value: '6months', label: '6 derniers mois' },
        { value: '12months', label: '12 derniers mois' },
        { value: 'all', label: 'Toute la période' },
    ]

    const loadChartData = async (period: string = selectedPeriod.value): Promise<void> => {
        console.log('loadChartData called with period:', period)
        isLoading.value = true
        error.value = null
        console.log(period);
        try {
            const url = route('dashboard.revenue-chart') + `?period=${period}`
            console.log('Fetching data from:', url)
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: RevenueChartResponse = await response.json()

            // Vérifier s'il y a une erreur dans les données retournées
            if (data.revenue_chart?.error) {
                // Afficher l'erreur détaillée en console en mode développement
                if (import.meta.env.DEV) {
                    console.error('Revenue Chart Backend Error:', data.revenue_chart.error)
                }
                // Afficher une erreur générale à l'utilisateur
                error.value = 'Impossible de charger les données de revenus'
                // Réinitialiser chartData avec des données vides
                chartData.value = {
                    labels: [],
                    datasets: [],
                    granularity: 'month',
                    period: period,
                    start_date: null,
                    end_date: null,
                    chart_options: {}
                }
            } else {
                chartData.value = data.revenue_chart
                selectedPeriod.value = period
            }
        } catch (err) {
            console.error('Error loading chart data:', err)
            error.value = err instanceof Error ? err.message : 'Failed to load chart data'
        } finally {
            isLoading.value = false
        }
    }

    const refreshChartData = async (): Promise<void> => {
        await loadChartData(selectedPeriod.value)
    }

    const changePeriod = async (period: string): Promise<void> => {
        console.log('changePeriod called with:', period, 'current:', selectedPeriod.value)
        if (period !== selectedPeriod.value) {
            console.log('Loading new chart data for period:', period)
            await loadChartData(period)
        }
    }

    const formatCurrency = (amount: number): string => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount).replace(/[\u202F\u00A0]/g, '\u2004')
    }

    const getTotalRevenue = computed(() => {
        console.log(datasets.value);
        if (!datasets.value.length) return 0
        const revenueDataset = datasets.value.find(d => d.label.includes('perçus'))
        return revenueDataset?.data.reduce((sum, value) => sum + value, 0) ?? 0
    })

    const getTotalBilled = computed(() => {
        if (!datasets.value.length) return 0
        const billedDataset = datasets.value.find(d => d.label.includes('Facturé'))
        return billedDataset?.data.reduce((sum, value) => sum + value, 0) ?? 0
    })

    const getAverageMonthlyRevenue = computed(() => {
        if (!labels.value.length || getTotalRevenue.value === 0) return 0
        return getTotalRevenue.value / labels.value.length
    })

    const getCurrentPeriodLabel = computed(() => {
        const period = availablePeriods.find(p => p.value === selectedPeriod.value)
        return period?.label ?? '6 derniers mois'
    })

    // Helper pour générer les tooltips détaillées selon la granularité
    const getTooltipTitle = (index: number): string => {
        if (!startDate.value || !chartData.value) return ''

        const start = new Date(startDate.value)

        if (granularity.value === 'day') {
            const date = new Date(start)
            date.setDate(start.getDate() + index)
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
        } else if (granularity.value === 'week') {
            const date = new Date(start)
            date.setDate(start.getDate() + (index * 7))
            return `Semaine du ${date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })}`
        } else {
            const date = new Date(start)
            date.setMonth(start.getMonth() + index)
            return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
        }
    }

    return {
        chartData,
        isLoading,
        error,
        selectedPeriod,
        hasChartData,
        labels,
        datasets,
        chartOptions,
        granularity,
        startDate,
        endDate,
        availablePeriods,
        getTotalRevenue,
        getTotalBilled,
        getAverageMonthlyRevenue,
        getCurrentPeriodLabel,
        getTooltipTitle,
        loadChartData,
        refreshChartData,
        changePeriod,
        formatCurrency,
    }
}
