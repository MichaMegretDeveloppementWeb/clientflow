import { ref, computed } from 'vue'
import type { QuickStats, QuickStatsResponse } from '@/types/dashboard/quickStats'
import { route } from 'ziggy-js'

export function useQuickStats() {
    const quickStats = ref<QuickStats | null>(null)
    const isLoading = ref(false)
    const error = ref<string | null>(null)

    const hasQuickStats = computed(() => quickStats.value !== null)

    const loadQuickStats = async (): Promise<void> => {
        isLoading.value = true
        error.value = null

        try {
            const response = await fetch(route('dashboard.quick-stats'), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: QuickStatsResponse = await response.json()
            
            // Vérifier s'il y a une erreur dans les données retournées
            if (data.quick_stats?.error) {
                // Afficher l'erreur détaillée en console en mode développement
                if (import.meta.env.DEV) {
                    console.error('Quick Stats Backend Error:', data.quick_stats.error)
                }
                // Afficher une erreur générale à l'utilisateur
                error.value = 'Impossible de charger les statistiques rapides'
                quickStats.value = null
            } else {
                quickStats.value = data.quick_stats
            }
        } catch (err) {
            console.error('Error loading quick stats:', err)
            error.value = err instanceof Error ? err.message : 'Failed to load quick stats'
            quickStats.value = null
        } finally {
            isLoading.value = false
        }
    }

    const refreshQuickStats = async (): Promise<void> => {
        await loadQuickStats()
    }

    const formatPercentage = (value: number, decimals: number = 1): string => {
        return `${(value || 0).toFixed(decimals)}%`
    }

    const formatCurrency = (amount: number): string => {
        // Formatage personnalisé avec séparateurs très visibles
        const rounded = Math.round(amount || 0)
        const numberStr = rounded.toString()
        
        // Utiliser des espaces insécables pour qu'ils soient tous visibles
        const formatted = numberStr.replace(/\B(?=(\d{3})+(?!\d))/g, '\u00A0\u00A0\u00A0') // 3 espaces insécables
        
        return `${formatted}\u00A0€` // Espace insécable avant €
    }

    return {
        quickStats,
        isLoading,
        error,
        hasQuickStats,
        loadQuickStats,
        refreshQuickStats,
        formatPercentage,
        formatCurrency,
    }
}