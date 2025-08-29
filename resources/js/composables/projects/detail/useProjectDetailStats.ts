import { computed, type Ref } from 'vue'
import type { ProjectDetailData } from '@/types/projects/detail'
import type { Event } from '@/types/projects/events'
import { useProjectDetailFormatters } from './useProjectDetailFormatters'

/**
 * Composable pour la gestion des statistiques du projet
 */
export function useProjectDetailStats(project: Ref<ProjectDetailData['project'] | null>, events: Ref<Event[]>) {
    const { formatCurrency, getDaysUntil } = useProjectDetailFormatters()

    /**
     * Calcule le budget restant
     */
    const remainingBudget = computed(() => {
        if (!project.value?.budget) return 0
        return project.value.budget - (project.value.total_billed || 0)
    })

    /**
     * Vérifie si le budget est dépassé
     */
    const isBudgetExceeded = computed(() => {
        return project.value?.budget_exceeded || remainingBudget.value < 0
    })

    /**
     * Calcule le pourcentage d'utilisation du budget
     */
    const budgetUsagePercentage = computed(() => {
        if (!project.value?.budget || project.value.budget === 0) return 0
        return Math.min(((project.value.total_billed || 0) / project.value.budget) * 100, 100)
    })

    /**
     * Classes CSS pour le budget restant
     */
    const remainingBudgetClasses = computed(() => ({
        'text-gray-900': remainingBudget.value >= 0,
        'text-red-600': remainingBudget.value < 0
    }))

    /**
     * Classes CSS pour l'icône du budget restant
     */
    const remainingBudgetIconClasses = computed(() => ({
        'bg-blue-50': remainingBudget.value >= 0,
        'bg-red-50': remainingBudget.value < 0
    }))

    /**
     * Classes CSS pour l'icône du budget restant (couleur)
     */
    const remainingBudgetIconColorClasses = computed(() => ({
        'text-blue-600': remainingBudget.value >= 0,
        'text-red-600': remainingBudget.value < 0
    }))

    /**
     * Classes CSS pour la barre de progression
     */
    const progressBarClasses = computed(() => ({
        'bg-gradient-to-r from-emerald-500 to-emerald-600': !isBudgetExceeded.value,
        'bg-gradient-to-r from-red-500 to-red-600': isBudgetExceeded.value
    }))

    /**
     * Calcule le montant des factures à envoyer (créées mais pas envoyées)
     */
    const billsToSend = computed(() => {
        return events.value
            .filter(event => event.event_type === 'billing' && event.status === 'to_send')
            .reduce((total, event) => total + (event.amount || 0), 0)
    })

    /**
     * Calcule le montant des paiements à venir (factures envoyées non payées, échéance non dépassée)
     */
    const upcomingPayments = computed(() => {
        const today = new Date()
        today.setHours(0, 0, 0, 0)
        
        return events.value
            .filter(event => 
                event.event_type === 'billing' && 
                event.status === 'sent' && 
                event.payment_status === 'pending' &&
                event.payment_due_date &&
                getDaysUntil(event.payment_due_date) >= 0
            )
            .reduce((total, event) => total + (event.amount || 0), 0)
    })

    /**
     * Calcule le montant des factures impayées en retard (factures envoyées, échéance dépassée)
     */
    const overdueUnpaid = computed(() => {
        return events.value
            .filter(event => 
                event.event_type === 'billing' && 
                event.status === 'sent' && 
                event.payment_status === 'pending' &&
                event.payment_due_date &&
                getDaysUntil(event.payment_due_date) < 0
            )
            .reduce((total, event) => total + (event.amount || 0), 0)
    })

    /**
     * Données des statistiques formatées pour l'affichage
     */
    const statsData = computed(() => {
        if (!project.value) return null

        return {
            budget: {
                amount: project.value.budget,
                formatted: formatCurrency(project.value.budget)
            },
            totalBilled: {
                amount: project.value.total_billed || 0,
                formatted: formatCurrency(project.value.total_billed || 0)
            },
            totalPaid: {
                amount: project.value.total_paid || 0,
                formatted: formatCurrency(project.value.total_paid || 0)
            },
            totalUnpaid: {
                amount: project.value.total_unpaid || 0,
                formatted: formatCurrency(project.value.total_unpaid || 0)
            },
            remainingBudget: {
                amount: remainingBudget.value,
                formatted: formatCurrency(remainingBudget.value)
            },
            budgetUsage: {
                percentage: Math.round(budgetUsagePercentage.value),
                isExceeded: isBudgetExceeded.value
            },
            // Nouvelles métriques de paiement
            billsToSend: {
                amount: billsToSend.value,
                formatted: formatCurrency(billsToSend.value)
            },
            upcomingPayments: {
                amount: upcomingPayments.value,
                formatted: formatCurrency(upcomingPayments.value)
            },
            overdueUnpaid: {
                amount: overdueUnpaid.value,
                formatted: formatCurrency(overdueUnpaid.value)
            }
        }
    })

    return {
        // Données formatées
        statsData,
        
        // CSS classes
        remainingBudgetClasses,
        remainingBudgetIconClasses,
        remainingBudgetIconColorClasses,
        progressBarClasses
    }
}