import { computed, unref, type MaybeRef } from 'vue'
import type { EventDTO } from '@/types/models'

export function useEventUtils(event: MaybeRef<EventDTO | null>) {

    // === FORMATAGE ===
    const formatDateTime = (date: string | null) => {
        if (!date) return ''
        const d = new Date(date)
        return d.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        })
    }

    const formatDate = (date: string | null) => {
        if (!date) return ''
        const d = new Date(date)
        return d.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        })
    }

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
        }).format(amount).replace(/[\u202F\u00A0]/g, '\u2004')

    }

    // === CALCULS DE DATES ===
    const getDaysOverdue = (date: string | null) => {
        if (!date) return 0

        const today = new Date()
        today.setHours(0, 0, 0, 0)

        const targetDate = new Date(date)
        targetDate.setHours(0, 0, 0, 0)

        const diffTime = today - targetDate
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

        return diffDays // Positif si en retard
    }

    const getDaysUntil = (date: string | null) => {
        if (!date) return 0

        const today = new Date()
        today.setHours(0, 0, 0, 0)

        const targetDate = new Date(date)
        targetDate.setHours(0, 0, 0, 0)

        const diffTime = targetDate - today
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

        return diffDays // Positif si dans le futur
    }

    // === PROGRESSION ===
    const getProgressPercentage = computed(() => {
        const eventValue = unref(event)
        if (!eventValue) return 0
        if (eventValue.status === 'done' || eventValue.status === 'sent') return 100
        if (eventValue.status === 'cancelled') return 0
        if (eventValue.completed_at) return 100
        return eventValue.status === 'to_send' ? 50 : 25
    })

    const getProgressBarClass = computed(() => {
        const percentage = getProgressPercentage.value
        if (percentage === 100) return 'bg-emerald-500'
        if (percentage === 0) return 'bg-gray-400'
        const eventValue = unref(event)
        if (eventValue?.is_overdue) return 'bg-red-400'
        return 'bg-amber-400'
    })

    // === CLASSES DE STATUT ===
    const getStatusClass = (status: string) => {
        const classes = {
            todo: 'bg-amber-50 text-amber-700 ring-amber-600/20',
            done: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            to_send: 'bg-blue-50 text-blue-700 ring-blue-600/20',
            sent: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            cancelled: 'bg-red-50 text-red-700 ring-red-600/20',
        }
        return classes[status as keyof typeof classes] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
    }

    const getStatusDotClass = (status: string) => {
        const classes = {
            todo: 'bg-amber-400',
            done: 'bg-emerald-400',
            to_send: 'bg-blue-400',
            sent: 'bg-emerald-400',
            cancelled: 'bg-red-400',
        }
        return classes[status as keyof typeof classes] || 'bg-gray-400'
    }

    // === BADGES DE PAIEMENT ===
    const getPaymentBadgeText = computed(() => {
        const eventValue = unref(event)
        if (!eventValue || eventValue.event_type !== 'billing') return ''
        return eventValue.payment_status === 'paid' ? 'Payé' : 'À payer'
    })

    const getPaymentBadgeClass = computed(() => {
        const eventValue = unref(event)
        if (!eventValue || eventValue.event_type !== 'billing') return ''
        if (eventValue.payment_status === 'paid') {
            return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
        } else {
            return 'bg-amber-50 text-amber-700 ring-amber-600/20'
        }
    })

    const getPaymentBadgeDotClass = computed(() => {
        const eventValue = unref(event)
        if (!eventValue || eventValue.event_type !== 'billing') return ''
        return eventValue.payment_status === 'paid' ? 'bg-emerald-400' : 'bg-amber-400'
    })

    return {
        // Formatage
        formatDateTime,
        formatDate,
        formatCurrency,

        // Calculs dates
        getDaysOverdue,
        getDaysUntil,

        // Progression
        getProgressPercentage,
        getProgressBarClass,

        // Classes statut
        getStatusClass,
        getStatusDotClass,

        // Badges paiement
        getPaymentBadgeText,
        getPaymentBadgeClass,
        getPaymentBadgeDotClass,
    }
}
