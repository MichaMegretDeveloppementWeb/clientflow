import { computed, type Ref } from 'vue'

/**
 * Composable pour la gestion des statistiques du projet
 * NOUVEAU: Utilise les données brutes du backend et fait le formatage côté front
 */
export function useProjectDetailStats(financialStats: Ref<any | null>) {
    /**
     * Fonction de formatage des montants
     */
    const formatCurrency = (amount: number | null): string => {
        if (amount === null || amount === undefined) return '0,00 €'
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount).replace(/[\u202F\u00A0]/g, '\u2004')
    }

    /**
     * Données des statistiques avec formatage
     */
    const statsData = computed(() => {
        if (!financialStats.value) return null

        const stats = financialStats.value

        return {
            budget: {
                amount: stats.budget,
                formatted: formatCurrency(stats.budget)
            },
            totalBilled: {
                amount: stats.totalBilled,
                formatted: formatCurrency(stats.totalBilled)
            },
            totalPaid: {
                amount: stats.totalPaid,
                formatted: formatCurrency(stats.totalPaid)
            },
            totalUnpaid: {
                amount: stats.totalUnpaid,
                formatted: formatCurrency(stats.totalUnpaid)
            },
            billsToSend: {
                amount: stats.billsToSend,
                formatted: formatCurrency(stats.billsToSend)
            },
            upcomingPayments: {
                amount: stats.upcomingPayments,
                formatted: formatCurrency(stats.upcomingPayments)
            },
            overdueUnpaid: {
                amount: stats.overdueUnpaid,
                formatted: formatCurrency(stats.overdueUnpaid)
            },
            remainingBudget: {
                amount: stats.remainingBudget,
                formatted: formatCurrency(stats.remainingBudget)
            },
            budgetUsage: {
                percentage: stats.budgetUsage.percentage,
                isExceeded: stats.budgetUsage.isExceeded
            }
        }
    })

    /**
     * Classes CSS pour le budget restant
     */
    const remainingBudgetClasses = computed(() => {
        const remainingAmount = statsData.value?.remainingBudget?.amount ?? 0
        return {
            'text-gray-900': remainingAmount >= 0,
            'text-red-600': remainingAmount < 0
        }
    })

    /**
     * Classes CSS pour l'icône du budget restant
     */
    const remainingBudgetIconClasses = computed(() => {
        const remainingAmount = statsData.value?.remainingBudget?.amount ?? 0
        return {
            'bg-blue-50': remainingAmount >= 0,
            'bg-red-50': remainingAmount < 0
        }
    })

    /**
     * Classes CSS pour l'icône du budget restant (couleur)
     */
    const remainingBudgetIconColorClasses = computed(() => {
        const remainingAmount = statsData.value?.remainingBudget?.amount ?? 0
        return {
            'text-blue-600': remainingAmount >= 0,
            'text-red-600': remainingAmount < 0
        }
    })

    /**
     * Classes CSS pour la barre de progression
     */
    const progressBarClasses = computed(() => {
        const isExceeded = statsData.value?.budgetUsage?.isExceeded ?? false
        return {
            'bg-gradient-to-r from-emerald-500 to-emerald-600': !isExceeded,
            'bg-gradient-to-r from-red-500 to-red-600': isExceeded
        }
    })

    return {
        // Données formatées (formatage côté front)
        statsData,

        // CSS classes (basées sur les données backend)
        remainingBudgetClasses,
        remainingBudgetIconClasses,
        remainingBudgetIconColorClasses,
        progressBarClasses
    }
}
