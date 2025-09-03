import { ref, computed } from 'vue'
import type { DashboardStatistics, StatisticsResponse } from '@/types/dashboard/statistics'
import { route } from 'ziggy-js'

export function useStatistics() {
    const statistics = ref<DashboardStatistics | null>(null)
    const isLoading = ref(false)
    const error = ref<string | null>(null)

    const hasStatistics = computed(() => statistics.value !== null)

    // Only computed values for data that actually exists in our optimized response
    const clientsTotal = computed(() => statistics.value?.clients.total ?? 0)
    const activeProjects = computed(() => statistics.value?.projects.active ?? 0)

    const loadStatistics = async (): Promise<void> => {
        isLoading.value = true
        error.value = null

        try {
            const response = await fetch(route('dashboard.statistics'), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: StatisticsResponse = await response.json()
            statistics.value = data.statistics
        } catch (err) {
            console.error('Error loading statistics:', err)
            error.value = err instanceof Error ? err.message : 'Failed to load statistics'
        } finally {
            isLoading.value = false
        }
    }

    const refreshStatistics = async (): Promise<void> => {
        await loadStatistics()
    }

    const formatCurrency = (amount: number): string => {
        // Formatage personnalisé avec séparateurs très visibles
        const rounded = Math.round(amount)
        const numberStr = rounded.toString()
        
        // Utiliser des espaces insécables pour qu'ils soient tous visibles
        const formatted = numberStr.replace(/\B(?=(\d{3})+(?!\d))/g, '\u00A0\u00A0\u00A0') // 3 espaces insécables
        
        return `${formatted}\u00A0€` // Espace insécable avant €
    }

    const formatPercentage = (value: number): string => {
        return `${value.toFixed(1)}%`
    }

    const getProjectStatusColor = (status: 'active' | 'completed' | 'on_hold' | 'cancelled'): string => {
        const colors = {
            active: 'text-blue-600',
            completed: 'text-green-600',
            on_hold: 'text-yellow-600',
            cancelled: 'text-red-600',
        }
        return colors[status]
    }

    const getGrowthIndicator = (growth: number): { icon: string; color: string } => {
        if (growth > 0) {
            return { icon: 'trending-up', color: 'text-green-600' }
        } else if (growth < 0) {
            return { icon: 'trending-down', color: 'text-red-600' }
        }
        return { icon: 'minus', color: 'text-gray-600' }
    }

    return {
        statistics,
        isLoading,
        error,
        hasStatistics,
        clientsTotal,
        activeProjects,
        loadStatistics,
        refreshStatistics,
        formatCurrency,
        formatPercentage,
        getProjectStatusColor,
        getGrowthIndicator,
    }
}