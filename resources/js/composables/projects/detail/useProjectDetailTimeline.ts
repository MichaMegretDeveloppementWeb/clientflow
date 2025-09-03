import { computed, type Ref } from 'vue'
import type { Event } from '@/types/projects/events'
import { useProjectDetailFormatters } from './useProjectDetailFormatters'

/**
 * Composable pour la gestion de la timeline des événements
 */
export function useProjectDetailTimeline(events: Ref<Event[]>) {
    const { getDaysUntil, getEventDate, formatDate, formatCurrency, truncateText } = useProjectDetailFormatters()

    /**
     * Événements triés par ordre chronologique (plus récent en premier)
     */
    const timelineEvents = computed(() => {
        return [...events.value].sort((a, b) => {
            const dateA = getEventDate(a)
            const dateB = getEventDate(b)

            const fallbackDateA = dateA ? new Date(dateA) : new Date(a.created_date)
            const fallbackDateB = dateB ? new Date(dateB) : new Date(b.created_date)

            return fallbackDateB.getTime() - fallbackDateA.getTime()
        })
    })

    /**
     * Labels pour les types d'événements
     */
    const getEventTypeLabel = (eventType: string): string => {
        const labels = {
            step: 'Étape',
            billing: 'Facturation',
        }
        return labels[eventType as keyof typeof labels] || eventType
    }

    /**
     * Labels pour les statuts d'événements
     */
    const getEventStatusLabel = (status: string): string => {
        const labels = {
            todo: 'À faire',
            done: 'Fait',
            to_send: 'À envoyer',
            sent: 'Envoyé',
            cancelled: 'Annulé',
        }
        return labels[status as keyof typeof labels] || status
    }

    /**
     * Label de statut amélioré avec temporalité
     */
    const getEnhancedStatusLabel = (event: Event): string => {
        // Si déjà terminé ou annulé, afficher le statut simple
        if (['done', 'sent', 'cancelled'].includes(event.status)) {
            return getEventStatusLabel(event.status)
        }

        const referenceDate = getEventDate(event)
        if (!referenceDate) {
            return getEventStatusLabel(event.status)
        }

        const daysUntil = getDaysUntil(referenceDate)

        // Si en retard, afficher juste le statut
        if (daysUntil < 0) {
            return getEventStatusLabel(event.status)
        }

        // Si aujourd'hui
        if (daysUntil === 0) {
            return `${getEventStatusLabel(event.status)} aujourd'hui`
        }

        // Si demain
        if (daysUntil === 1) {
            return `${getEventStatusLabel(event.status)} demain`
        }

        return `${getEventStatusLabel(event.status)} dans ${daysUntil}j`
    }

    /**
     * Classes CSS pour les statuts
     */
    const getStatusClass = (status: string): string => {
        const classes = {
            todo: 'bg-orange-50 text-orange-700 ring-orange-600/20',
            done: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            to_send: 'bg-orange-50 text-orange-700 ring-orange-600/20',
            sent: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            cancelled: 'bg-red-50 text-red-700 ring-red-600/20'
        }
        return classes[status as keyof typeof classes] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
    }

    /**
     * Classes CSS pour les types d'événements
     */
    const getEventTypeClasses = (eventType: string): string => {
        const classes = {
            'step': 'text-blue-700',
            'billing': 'text-emerald-700'
        }
        return classes[eventType] || 'text-gray-700'
    }

    /**
     * Classes CSS pour les statuts de paiement
     */
    const getPaymentStatusClasses = (paymentStatus: string | undefined): string => {
        if (paymentStatus === 'paid') {
            return 'text-emerald-700'
        }
        return 'text-orange-700'
    }

    /**
     * Vérifie si l'événement est en retard
     */
    const shouldShowOverdueBadge = (event: Event): boolean => {
        if (['done', 'sent', 'cancelled'].includes(event.status)) {
            return false
        }

        const eventDate = getEventDate(event)
        if (!eventDate) return false

        return getDaysUntil(eventDate) < 0
    }

    /**
     * Calcule les jours de retard
     */
    const getOverdueDays = (event: Event): number => {
        const eventDate = getEventDate(event)
        if (!eventDate) return 0
        return Math.abs(getDaysUntil(eventDate))
    }

    /**
     * Vérifie si le paiement est en retard
     */
    const shouldShowPaymentOverdueBadge = (event: Event): boolean => {
        return event.payment_status === 'pending' &&
               event.payment_due_date &&
               getDaysUntil(event.payment_due_date) < 0
    }

    /**
     * Calcule les jours de retard de paiement
     */
    const getPaymentOverdueDays = (event: Event): number => {
        if (!event.payment_due_date) return 0
        return Math.abs(getDaysUntil(event.payment_due_date))
    }


    return {
        // Données
        timelineEvents,
        
        // Fonctions de labels
        getEventTypeLabel,
        getEnhancedStatusLabel,
        
        // Fonctions de classes CSS
        getStatusClass,
        getEventTypeClasses,
        getPaymentStatusClasses,
        
        // Fonctions de logique métier
        shouldShowOverdueBadge,
        shouldShowPaymentOverdueBadge,
        getOverdueDays,
        getPaymentOverdueDays,
        
        // Fonctions de formatage (réexportées)
        formatDate,
        formatCurrency,
        truncateText,
        getEventDate
    }
}