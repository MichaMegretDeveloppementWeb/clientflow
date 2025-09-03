import { ref, computed } from 'vue'
import type { BillingData, BillingResponse } from '@/types/dashboard/billing'
import { route } from 'ziggy-js'

export function useBilling() {
    const billingData = ref<BillingData | null>(null)
    const isLoading = ref(false)
    const error = ref<string | null>(null)

    const hasBillingData = computed(() => billingData.value !== null)

    const totals = computed(() => billingData.value?.totals ?? {
        billed: 0,
        paid: 0,
        pending: 0,
        to_send: 0,
        upcoming_payment: 0,
        overdue_payment: 0,
    })

    const metrics = computed(() => billingData.value?.metrics ?? {
        payment_rate: 0,
        invoices_to_send_count: 0,
        unpaid_invoices_count: 0,
        overdue_invoices_count: 0,
    })

    const cards = computed(() => billingData.value?.cards ?? [])

    const totalBilled = computed(() => totals.value.billed)
    const totalPaid = computed(() => totals.value.paid)
    const totalPending = computed(() => totals.value.pending)
    const totalToSend = computed(() => totals.value.to_send)
    const paymentRate = computed(() => metrics.value.payment_rate)

    const loadBillingData = async (): Promise<void> => {
        isLoading.value = true
        error.value = null

        try {
            const response = await fetch(route('dashboard.billing'), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: BillingResponse = await response.json()
            billingData.value = data.billing

        } catch (err) {
            console.error('Error loading billing data:', err)
            error.value = err instanceof Error ? err.message : 'Failed to load billing data'
        } finally {
            isLoading.value = false
        }
    }

    const refreshBillingData = async (): Promise<void> => {
        await loadBillingData()
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

    const getCardColorClass = (color: string): string => {
        const colors: Record<string, string> = {
            purple: 'text-purple-600 bg-purple-100',
            orange: 'text-orange-600 bg-orange-100',
            blue: 'text-blue-600 bg-blue-100',
            green: 'text-green-600 bg-green-100',
            red: 'text-red-600 bg-red-100',
        }
        return colors[color] || 'text-gray-600 bg-gray-100'
    }

    const getPaymentStatusColor = (rate: number): string => {
        if (rate >= 80) return 'text-green-600'
        if (rate >= 60) return 'text-yellow-600'
        return 'text-red-600'
    }

    const hasOverduePayments = computed(() => {
        return totals.value.overdue_payment > 0
    })

    const hasInvoicesToSend = computed(() => {
        return metrics.value.invoices_to_send_count > 0
    })

    return {
        billingData,
        isLoading,
        error,
        hasBillingData,
        totals,
        metrics,
        cards,
        totalBilled,
        totalPaid,
        totalPending,
        totalToSend,
        paymentRate,
        hasOverduePayments,
        hasInvoicesToSend,
        loadBillingData,
        refreshBillingData,
        formatCurrency,
        formatPercentage,
        getCardColorClass,
        getPaymentStatusColor,
    }
}
